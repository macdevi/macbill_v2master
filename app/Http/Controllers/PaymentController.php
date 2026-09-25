<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    public function store(Request $request, Invoice $invoice)
    {
        $this->authorizeInvoiceAreaForPayment($invoice);

        $data = $request->validate([
            'method' => ['required', 'in:cash,bank_transfer'],
            'amount' => ['required', 'numeric', 'min:1'],
            'proof' => ['nullable', 'image', 'max:5120'],
            'notes' => ['nullable', 'string'],
        ], [
            'amount.required' => 'Jumlah pembayaran wajib diisi.',
            'amount.numeric' => 'Jumlah pembayaran harus berupa angka.',
            'amount.min' => 'Jumlah pembayaran minimal Rp 1.',
        ]);

        if ($invoice->status === 'paid' || (float) $invoice->amount <= 0) {
            return back()->withErrors([
                'payment' => 'Invoice ini sudah lunas dan tidak dapat dibayar ulang.',
            ]);
        }

        return DB::transaction(function () use ($request, $invoice, $data) {
            $invoice = Invoice::query()
                ->lockForUpdate()
                ->findOrFail($invoice->id);

            $customer = Customer::query()
                ->lockForUpdate()
                ->findOrFail($invoice->customer_id);

            $remainingBefore = round((float) $invoice->amount, 2);
            $receivedAmount = round((float) $data['amount'], 2);

            if ($invoice->status === 'paid' || $remainingBefore <= 0) {
                return back()->withErrors([
                    'payment' => 'Invoice ini sudah lunas dan tidak dapat dibayar ulang.',
                ]);
            }

            $allocatedAmount = round(min($receivedAmount, $remainingBefore), 2);
            $creditAmount = round(max($receivedAmount - $allocatedAmount, 0), 2);
            $newRemaining = round(max($remainingBefore - $allocatedAmount, 0), 2);

            if ($request->hasFile('proof')) {
                $data['proof_path'] = $request->file('proof')->store('payment-proofs', 'public');
            }

            $notes = trim((string) ($data['notes'] ?? ''));

            if ($creditAmount > 0) {
                $overpaymentNote = 'Pembayaran diterima Rp ' . number_format($receivedAmount, 0, ',', '.')
                    . '. Dialokasikan ke invoice Rp ' . number_format($allocatedAmount, 0, ',', '.')
                    . '. Kelebihan Rp ' . number_format($creditAmount, 0, ',', '.')
                    . ' ditambahkan ke titip saldo pelanggan.';

                $notes = $notes !== ''
                    ? $notes . "\n" . $overpaymentNote
                    : $overpaymentNote;
            }

            Payment::create([
                'invoice_id' => $invoice->id,
                'method' => $data['method'],
                'amount' => $allocatedAmount,
                'proof_path' => $data['proof_path'] ?? null,
                'status' => 'verified',
                'notes' => $notes !== '' ? $notes : null,
                'paid_at' => now(),
                'verified_at' => now(),
            ]);

            $invoice->update([
                'amount' => $newRemaining,
                'status' => $newRemaining <= 0 ? 'paid' : 'unpaid',
            ]);

            if ($creditAmount > 0) {
                $customer->increment('credit_balance', $creditAmount);
            }

            $phoneDigits = preg_replace('/\D+/', '', (string) ($customer->phone ?? ''));
            $whatsappNumber = str_starts_with($phoneDigits, '0')
                ? '62' . substr($phoneDigits, 1)
                : $phoneDigits;

            $message = $newRemaining <= 0
                ? 'Pembayaran dicatat. Invoice dinyatakan lunas.'
                : 'Pembayaran cicilan dicatat. Sisa tagihan Rp ' . number_format($newRemaining, 0, ',', '.') . '.';

            if ($creditAmount > 0) {
                $message .= ' Kelebihan Rp ' . number_format($creditAmount, 0, ',', '.')
                    . ' ditambahkan ke titip saldo pelanggan.';
            }

            $redirect = redirect()
                ->route('invoices.pay', $invoice)
                ->with('success', $message);

            if (strlen($whatsappNumber) >= 10) {
                $redirect->with('payment_receipt_whatsapp', [
                    'customer_name' => $customer->name,
                    'invoice_number' => $invoice->invoice_number,
                    'amount' => $allocatedAmount,
                    'received_amount' => $receivedAmount,
                    'credit_amount' => $creditAmount,
                    'remaining_amount' => $newRemaining,
                    'paid_at' => now()->format('d/m/Y H:i'),
                    'invoice_url' => route('invoices.print', $invoice),
                    'whatsapp_number' => $whatsappNumber,
                ]);
            }

            return $redirect;
        });
    }

    private function authorizeInvoiceAreaForPayment(Invoice $invoice): void
    {
        $user = request()->user();

        if ($user?->isSuperAdmin()) {
            return;
        }

        abort_unless(
            in_array($user?->role, ['admin', 'kasir'], true),
            403,
            'Anda tidak memiliki akses untuk mencatat pembayaran.'
        );

        $invoice->loadMissing('customer');

        abort_unless(
            $user->activeAreaIds()->contains((int) $invoice->customer->area_id),
            403,
            'Anda tidak memiliki akses ke invoice di luar area penugasan.'
        );
    }

}