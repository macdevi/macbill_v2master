<?php

namespace App\Console\Commands;

use App\Models\Invoice;
use Illuminate\Console\Command;

class IsolateOverdueInvoices extends Command
{
    protected $signature = 'billing:isolate-overdue';

    protected $description = 'Memeriksa tagihan lewat jatuh tempo tanpa mengisolir pelanggan otomatis';

    public function handle(): int
    {
        $overdueInvoices = Invoice::query()
            ->where('status', 'unpaid')
            ->whereDate('due_date', '<', now()->toDateString())
            ->count();

        $this->info("Ditemukan {$overdueInvoices} tagihan belum dibayar yang telah melewati jatuh tempo.");
        $this->info('Tidak ada pelanggan yang diisolir otomatis. Gunakan tombol Isolir pada halaman pelanggan bila diperlukan.');

        return self::SUCCESS;
    }
}
