<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class GenerateInvoices extends Command
{
    protected $signature = 'billing:generate';

    protected $description =
        'Membuat invoice menggunakan aturan generator harian yang sama';

    public function handle(): int
    {
        return $this->call('billing:generate-daily');
    }
}
