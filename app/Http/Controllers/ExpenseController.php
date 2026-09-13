<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Expense;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ExpenseController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->query('search', ''));
        $month = (string) $request->query('month', now()->format('Y-m'));
        $category = (string) $request->query('category', '');
        $paymentMethod = (string) $request->query('payment_method', '');
        $status = (string) $request->query('status', 'posted');

        if (! preg_match('/^\d{4}-\d{2}$/', $month)) {
            $month = now()->format('Y-m');
        }

        if (! in_array($category, Expense::CATEGORIES, true)) {
            $category = '';
        }

        if (! array_key_exists($paymentMethod, Expense::PAYMENT_METHODS)) {
            $paymentMethod = '';
        }

        if (! in_array($status, ['posted', 'voided', 'all'], true)) {
            $status = 'posted';
        }

        [$year, $monthNumber] = array_map('intval', explode('-', $month));
        $monthStart = Carbon::create($year, $monthNumber, 1)->startOfMonth();
        $monthEnd = $monthStart->copy()->endOfMonth();

        $user = $request->user();

        $expenses = Expense::query()
            ->with('createdBy:id,name,username,role')
            ->when(in_array($user?->role, ['admin', 'kasir'], true), function ($query) use ($user) {
                $query->whereIn('area_id', $user->activeAreaIds());
            })
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($expenseQuery) use ($search) {
                    $expenseQuery
                        ->where('title', 'like', '%' . $search . '%')
                        ->orWhere('vendor', 'like', '%' . $search . '%')
                        ->orWhere('description', 'like', '%' . $search . '%');
                });
            })
            ->whereBetween('expense_date', [$monthStart->toDateString(), $monthEnd->toDateString()])
            ->when($category !== '', fn ($query) => $query->where('category', $category))
            ->when($paymentMethod !== '', fn ($query) => $query->where('payment_method', $paymentMethod))
            ->when($status !== 'all', fn ($query) => $query->where('status', $status))
            ->latest('expense_date')
            ->latest('id')
            ->paginate(20)
            ->withQueryString();

        $monthPostedQuery = Expense::posted()
            ->when(in_array($user?->role, ['admin', 'kasir'], true), function ($query) use ($user) {
                $query->whereIn('area_id', $user->activeAreaIds());
            })
            ->whereBetween('expense_date', [$monthStart->toDateString(), $monthEnd->toDateString()]);

        $today = now()->toDateString();

        return view('expenses.index', [
            'expenses' => $expenses,
            'search' => $search,
            'month' => $month,
            'category' => $category,
            'paymentMethod' => $paymentMethod,
            'status' => $status,
            'categories' => Expense::CATEGORIES,
            'paymentMethods' => Expense::PAYMENT_METHODS,
            'monthTotal' => (float) (clone $monthPostedQuery)->sum('amount'),
            'monthCount' => (clone $monthPostedQuery)->count(),
            'todayTotal' => (float) Expense::posted()
                ->when(in_array($user?->role, ['admin', 'kasir'], true), function ($query) use ($user) {
                    $query->whereIn('area_id', $user->activeAreaIds());
                })
                ->whereDate('expense_date', $today)
                ->sum('amount'),
        ]);
    }

    public function create()
    {
        return view('expenses.create', [
            'expense' => new Expense([
                'expense_date' => now()->toDateString(),
                'payment_method' => 'cash',
                'category' => 'Operasional',
            ]),
            'categories' => Expense::CATEGORIES,
            'paymentMethods' => Expense::PAYMENT_METHODS,
        ]);
    }

    public function store(Request $request)
    {
        $user = $request->user();
        $data = $this->validatedData($request);

        if (in_array($user?->role, ['admin', 'kasir'], true)) {
            $areaId = $user->activeAreaIds()->first();

            abort_unless(
                $areaId,
                403,
                'Anda tidak memiliki area aktif untuk mencatat pengeluaran.'
            );

            $data['area_id'] = (int) $areaId;
        }

        $expense = Expense::create(array_merge(
            $data,
            ['created_by' => $user->id]
        ));

        $this->writeLog(
            'expense.created',
            'Pengeluaran "' . $expense->title . '" sebesar Rp '
                . number_format((float) $expense->amount, 0, ',', '.')
                . ' ditambahkan.'
        );

        return redirect()
            ->route('expenses.index')
            ->with('success', 'Pengeluaran berhasil disimpan.');
    }

    public function edit(Expense $expense)
    {
        $this->authorizeExpenseArea($expense);

        if ($expense->status === 'voided') {
            return redirect()
                ->route('expenses.index')
                ->with('error', 'Pengeluaran yang sudah dibatalkan tidak dapat diedit.');
        }

        return view('expenses.create', [
            'expense' => $expense,
            'categories' => Expense::CATEGORIES,
            'paymentMethods' => Expense::PAYMENT_METHODS,
        ]);
    }

    public function update(Request $request, Expense $expense)
    {
        $this->authorizeExpenseArea($expense);

        if ($expense->status === 'voided') {
            return redirect()
                ->route('expenses.index')
                ->with('error', 'Pengeluaran yang sudah dibatalkan tidak dapat diperbarui.');
        }

        $oldAmount = (float) $expense->amount;
        $expense->update($this->validatedData($request));

        $this->writeLog(
            'expense.updated',
            'Pengeluaran "' . $expense->title . '" diperbarui. Nominal Rp '
                . number_format($oldAmount, 0, ',', '.')
                . ' menjadi Rp ' . number_format((float) $expense->amount, 0, ',', '.') . '.'
        );

        return redirect()
            ->route('expenses.index')
            ->with('success', 'Pengeluaran berhasil diperbarui.');
    }

    public function destroy(Expense $expense)
    {
        $this->authorizeExpenseArea($expense);

        if ($expense->status === 'voided') {
            return back()->with('error', 'Pengeluaran ini sudah dibatalkan sebelumnya.');
        }

        $expense->update([
            'status' => 'voided',
            'voided_at' => now(),
        ]);

        $this->writeLog(
            'expense.voided',
            'Pengeluaran "' . $expense->title . '" sebesar Rp '
                . number_format((float) $expense->amount, 0, ',', '.')
                . ' dibatalkan.'
        );

        return back()->with('success', 'Pengeluaran berhasil dibatalkan dan tetap tersimpan sebagai riwayat.');
    }

    private function authorizeExpenseArea(Expense $expense): void
    {
        $user = request()->user();

        if ($user?->isSuperAdmin()) {
            return;
        }

        abort_unless(
            in_array($user?->role, ['admin', 'kasir'], true),
            403,
            'Anda tidak memiliki akses ke pengeluaran ini.'
        );

        abort_unless(
            $expense->area_id
                && $user->activeAreaIds()->contains((int) $expense->area_id),
            403,
            'Anda tidak memiliki akses ke pengeluaran di luar area penugasan.'
        );
    }

    private function validatedData(Request $request): array
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'category' => ['required', Rule::in(Expense::CATEGORIES)],
            'vendor' => ['nullable', 'string', 'max:255'],
            'payment_method' => ['required', Rule::in(array_keys(Expense::PAYMENT_METHODS))],
            'amount' => ['required', 'numeric', 'min:1', 'max:999999999999.99'],
            'expense_date' => ['required', 'date'],
            'description' => ['nullable', 'string', 'max:5000'],
        ], [
            'title.required' => 'Judul pengeluaran wajib diisi.',
            'category.required' => 'Kategori pengeluaran wajib dipilih.',
            'category.in' => 'Kategori pengeluaran tidak valid.',
            'vendor.max' => 'Nama penerima/vendor maksimal 255 karakter.',
            'payment_method.required' => 'Metode pembayaran wajib dipilih.',
            'payment_method.in' => 'Metode pembayaran tidak valid.',
            'amount.required' => 'Nominal pengeluaran wajib diisi.',
            'amount.numeric' => 'Nominal pengeluaran harus berupa angka.',
            'amount.min' => 'Nominal pengeluaran minimal Rp 1.',
            'amount.max' => 'Nominal pengeluaran terlalu besar.',
            'expense_date.required' => 'Tanggal pengeluaran wajib diisi.',
            'expense_date.date' => 'Tanggal pengeluaran tidak valid.',
            'description.max' => 'Keterangan maksimal 5.000 karakter.',
        ]);

        $data['title'] = trim($data['title']);
        $data['vendor'] = isset($data['vendor']) && trim($data['vendor']) !== ''
            ? trim($data['vendor'])
            : null;
        $data['description'] = isset($data['description']) && trim($data['description']) !== ''
            ? trim($data['description'])
            : null;
        $data['amount'] = round((float) $data['amount'], 2);

        return $data;
    }

    private function writeLog(string $action, string $description): void
    {
        ActivityLog::create([
            'action' => $action,
            'description' => $description,
            'ip_address' => request()->ip(),
        ]);
    }
}
