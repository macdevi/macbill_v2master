<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Payment;
use App\Services\BillingTaxService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InvoiceController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->query('search', ''));
        $tab = (string) $request->query('tab', 'active');
        $status = (string) $request->query('status', '');
        $period = (string) $request->query('period', '');

        $allowedTabs = ['active', 'history', 'all'];

        if (! in_array($tab, $allowedTabs, true)) {
            $tab = 'active';
        }

        $allowedStatuses = ['unpaid', 'paid', 'isolated'];

        if (! in_array($status, $allowedStatuses, true)) {
            $status = '';
        }

        if (! preg_match('/^\d{4}-\d{2}$/', $period)) {
            $period = '';
        }

        $user = $request->user();

        $invoices = Invoice::query()
            ->with('customer')
            ->when($user?->role === 'admin', function ($query) use ($user) {
                $query->whereHas('customer', function ($customerQuery) use ($user) {
                    $customerQuery->whereIn('area_id', $user->activeAreaIds());
                });
            })
            ->when($search !== '', function ($query) use ($search) {
                $normalizedAmount = preg_replace('/[^0-9.]/', '', str_replace(',', '.', $search));

                $query->where(function ($invoiceQuery) use ($search, $normalizedAmount) {
                    $invoiceQuery
                        ->where('invoices.invoice_number', 'like', '%' . $search . '%')
                        ->orWhere('invoices.status', 'like', '%' . $search . '%')
                        ->orWhereHas('customer', function ($customerQuery) use ($search) {
                            $customerQuery
                                ->where('name', 'like', '%' . $search . '%')
                                ->orWhere('customer_code', 'like', '%' . $search . '%')
                                ->orWhere('phone', 'like', '%' . $search . '%');
                        });

                    if ($normalizedAmount !== '' && is_numeric($normalizedAmount)) {
                        $invoiceQuery->orWhere('invoices.amount', '=', (float) $normalizedAmount);
                    }
                });
            })
            ->when($status !== '', function ($query) use ($status) {
                $query->where('invoices.status', $status);
            })
            ->when($status === '' && $tab === 'active', function ($query) {
                $query->whereIn('invoices.status', ['unpaid', 'isolated']);
            })
            ->when($status === '' && $tab === 'history', function ($query) {
                $query->where('invoices.status', 'paid');
            })
            ->when($period !== '', function ($query) use ($period) {
                [$year, $month] = explode('-', $period);

                $query
                    ->whereYear('invoices.billing_date', (int) $year)
                    ->whereMonth('invoices.billing_date', (int) $month);
            })
            ->join('customers', 'invoices.customer_id', '=', 'customers.id')
            ->select('invoices.*')
            ->orderByRaw('LOWER(customers.name) ASC')
            ->orderBy('invoices.id')
            ->get();

        return view('invoices.index', [
            'invoices' => $invoices,
            'search' => $search,
            'tab' => $tab,
            'status' => $status,
            'period' => $period,
        ]);
    }

    public function createPage()
    {
        $user = request()->user();

        return view('invoices.create', [
            'customers' => Customer::query()
                ->when($user?->role === 'admin', function ($query) use ($user) {
                    $query->whereIn('area_id', $user->activeAreaIds());
                })
                ->orderBy('name')
                ->get(['id', 'name', 'customer_code']),
        ]);
    }

    public function creditBalancePage()
    {
        $user = request()->user();

        abort_unless(
            in_array($user?->role, ['super_admin', 'admin'], true),
            403,
            'Anda tidak memiliki akses ke fitur titip saldo.'
        );

        return view('invoices.credit-balance', [
            'customers' => Customer::query()
                ->when($user?->role === 'admin', function ($query) use ($user) {
                    $query->whereIn('area_id', $user->activeAreaIds());
                })
                ->orderBy('name')
                ->get([
                    'id',
                    'name',
                    'customer_code',
                    'phone',
                    'credit_balance',
                    'status',
                ]),
        ]);
    }

    public function show(Invoice $invoice)
    {
        $this->authorizeInvoiceArea($invoice);

        $invoice->load(['customer', 'payments']);

        return view('invoices.show', compact('invoice'));
    }

    public function pay(Invoice $invoice)
    {
        $this->authorizeInvoiceArea($invoice);

        $invoice->load(['customer', 'payments']);

        return view('invoices.pay', compact('invoice'));
    }

    public function print(Invoice $invoice)
    {
        $this->authorizeInvoiceArea($invoice);

        $invoice->load('customer');

        return view('invoices.print', compact('invoice'));
    }

    public function generateManual(Request $request, BillingTaxService $billingTax)
    {
        $data = $request->validate([
            'customer_id' => ['required', 'exists:customers,id'],
            'billing_period' => ['required', 'date_format:Y-m', 'before_or_equal:' . now()->format('Y-m')],
        ], [
            'customer_id.required' => 'Pelanggan wajib dipilih.',
            'customer_id.exists' => 'Pelanggan yang dipilih tidak ditemukan.',
            'billing_period.required' => 'Bulan tagihan wajib dipilih.',
            'billing_period.date_format' => 'Format bulan tagihan tidak valid.',
            'billing_period.before_or_equal' => 'Bulan tagihan tidak boleh melebihi bulan berjalan.',
        ]);

        $customer = Customer::with('internetPackage')->findOrFail($data['customer_id']);

        $this->authorizeCustomerAreaForInvoice($customer);

        $billingMonth = Carbon::createFromFormat('!Y-m', $data['billing_period']);
        $period = $billingMonth->format('Ym');

        $exists = Invoice::where('customer_id', $customer->id)
            ->whereRaw("strftime('%Y%m', billing_date) = ?", [$period])
            ->exists();

        if ($exists) {
            return back()->withErrors([
                'invoice' => 'Tagihan ' . $billingMonth->translatedFormat('F Y') . ' untuk pelanggan tersebut sudah ada.',
            ])->withInput();
        }

        [$invoice, $creditUsed] = $this->createInvoiceWithCreditBalance(
            $customer,
            $billingMonth,
            $period,
            $billingTax
        );

        $message = 'Tagihan ' . $billingMonth->translatedFormat('F Y') . ' berhasil dibuat.';

        if ($creditUsed > 0) {
            $message .= ' Titip saldo terpakai Rp ' . number_format((float) $creditUsed, 0, ',', '.') . '.';
        }

        if ($invoice->status === 'paid') {
            $message .= ' Invoice otomatis lunas oleh titip saldo.';
        } else {
            $message .= ' Sisa tagihan Rp ' . number_format((float) $invoice->amount, 0, ',', '.') . '.';
        }

        return redirect()
            ->route('invoices.index', ['period' => $data['billing_period']])
            ->with('success', $message);
    }

    public function generateMass(Request $request, BillingTaxService $billingTax)
    {
        $this->authorizeSuperAdmin();

        $data = $request->validate([
            'billing_period' => ['required', 'date_format:Y-m', 'before_or_equal:' . now()->format('Y-m')],
        ], [
            'billing_period.required' => 'Bulan tagihan wajib dipilih.',
            'billing_period.date_format' => 'Format bulan tagihan tidak valid.',
            'billing_period.before_or_equal' => 'Bulan tagihan tidak boleh melebihi bulan berjalan.',
        ]);

        $billingMonth = Carbon::createFromFormat('!Y-m', $data['billing_period']);
        $period = $billingMonth->format('Ym');
        $count = 0;
        $skippedCount = 0;
        $creditAppliedCount = 0;
        $autoPaidCount = 0;

        foreach (
            Customer::with('internetPackage')
                ->where('status', '!=', 'inactive')
                ->get() as $customer
        ) {
            $exists = Invoice::where('customer_id', $customer->id)
                ->whereRaw("strftime('%Y%m', billing_date) = ?", [$period])
                ->exists();

            if ($exists) {
                $skippedCount++;
                continue;
            }

            [$invoice, $creditUsed] = $this->createInvoiceWithCreditBalance(
                $customer,
                $billingMonth,
                $period,
                $billingTax
            );

            $count++;

            if ($creditUsed > 0) {
                $creditAppliedCount++;
            }

            if ($invoice->status === 'paid') {
                $autoPaidCount++;
            }
        }

        $periodLabel = $billingMonth->translatedFormat('F Y');
        $message = "Generate massal {$periodLabel} selesai. {$count} tagihan dibuat.";

        if ($skippedCount > 0) {
            $message .= " {$skippedCount} pelanggan dilewati karena tagihannya sudah ada.";
        }

        if ($creditAppliedCount > 0) {
            $message .= " {$creditAppliedCount} tagihan memakai titip saldo.";
        }

        if ($autoPaidCount > 0) {
            $message .= " {$autoPaidCount} tagihan otomatis lunas penuh dari titip saldo.";
        }

        return redirect()
            ->route('invoices.index', ['period' => $data['billing_period']])
            ->with('success', $message);
    }

    private function authorizeSuperAdmin(): void
    {
        abort_unless(
            request()->user()?->role === 'super_admin',
            403,
            'Hanya Super Admin yang dapat mengakses fitur ini.'
        );
    }

    private function authorizeCustomerAreaForInvoice(Customer $customer): void
    {
        $user = request()->user();

        if ($user?->role !== 'admin') {
            return;
        }

        abort_unless(
            $user->activeAreaIds()->contains((int) $customer->area_id),
            403,
            'Anda tidak memiliki akses untuk membuat invoice pelanggan di luar area penugasan.'
        );
    }

    private function authorizeInvoiceArea(Invoice $invoice): void
    {
        $user = request()->user();

        if ($user?->role !== 'admin') {
            return;
        }

        $invoice->loadMissing('customer');

        abort_unless(
            $user->activeAreaIds()->contains((int) $invoice->customer->area_id),
            403,
            'Anda tidak memiliki akses ke invoice di luar area penugasan.'
        );
    }

    private function createInvoiceWithCreditBalance(
        Customer $customer,
        Carbon $now,
        string $period,
        BillingTaxService $billingTax
    ): array {
        return DB::transaction(function () use ($customer, $now, $period, $billingTax) {
            $customer = Customer::with('internetPackage')
                ->lockForUpdate()
                ->findOrFail($customer->id);

            $billingDate = $now->copy()->startOfMonth();
            $dueDate = $billingDate->copy()
                ->day(min($customer->due_day, $billingDate->daysInMonth));

            $tax = $billingTax->calculate($customer);
            $availableCredit = (float) $customer->credit_balance;
            $creditUsed = min($availableCredit, (float) $tax['gross_amount']);
            $finalAmount = max((float) $tax['gross_amount'] - $creditUsed, 0);

            $invoice = Invoice::create([
                'customer_id' => $customer->id,
                'invoice_number' => 'INV-' . $period . '-' . str_pad((string) $customer->id, 6, '0', STR_PAD_LEFT),
                'billing_date' => $billingDate,
                'due_date' => $dueDate,
                'service_amount' => $tax['service_amount'],
                'subtotal' => $tax['subtotal'],
                'tax_name' => $tax['tax_name'],
                'tax_rate' => $tax['tax_rate'],
                'tax_amount' => $tax['tax_amount'],
                'tax_mode' => $tax['tax_mode'],
                'gross_amount' => $tax['gross_amount'],
                'credit_used' => $creditUsed,
                'amount' => $finalAmount,
                'status' => $finalAmount <= 0 ? 'paid' : 'unpaid',
            ]);

            if ($creditUsed > 0) {
                $customer->decrement('credit_balance', $creditUsed);

                if ($finalAmount <= 0) {
                    Payment::create([
                        'invoice_id' => $invoice->id,
                        'method' => 'cash',
                        'amount' => $creditUsed,
                        'proof_path' => null,
                        'status' => 'verified',
                        'notes' => 'Pembayaran otomatis dari titip saldo pelanggan.',
                        'paid_at' => now(),
                        'verified_at' => now(),
                    ]);
                }
            }

            return [$invoice->fresh(), $creditUsed];
        });
    }
}
