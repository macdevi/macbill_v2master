<?php

namespace App\Console\Commands;

use App\Models\Router;
use App\Services\MikroTikService;
use Illuminate\Console\Command;
use Throwable;

class CheckMikrotikInterfaces extends Command
{
    protected $signature = 'mikrotik:interfaces';

    protected $description = 'Menampilkan daftar interface dari MikroTik aktif';

    public function handle(MikroTikService $mikrotik): int
    {
        $router = Router::query()
            ->where('active', true)
            ->first();

        if (! $router) {
            $this->error('Router aktif tidak ditemukan.');

            return self::FAILURE;
        }

        try {
            $interfaces = $mikrotik->interfaces($router);
        } catch (Throwable $exception) {
            report($exception);

            $this->error('Gagal mengambil interface MikroTik.');

            return self::FAILURE;
        }

        $rows = collect($interfaces)
            ->map(fn (array $interface) => [
                $interface['name'] ?? '-',
                $interface['type'] ?? '-',
                ($interface['running'] ?? 'false') === 'true' ? 'yes' : 'no',
                ($interface['disabled'] ?? 'false') === 'true' ? 'yes' : 'no',
            ])
            ->values()
            ->all();

        $this->table(
            ['Interface', 'Type', 'Running', 'Disabled'],
            $rows
        );

        return self::SUCCESS;
    }
}
