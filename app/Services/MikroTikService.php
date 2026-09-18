<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\InternetPackage;
use App\Models\Router;
use RouterOS\Client;
use RouterOS\Query;
use RuntimeException;

class MikroTikService
{
    private function client(Router $router): Client
    {
        return new Client([
            'host' => $router->host,
            'user' => $router->username,
            'pass' => $router->password,
            'port' => $router->port,
            'timeout' => config('mikrotik.timeout', 5),
        ]);
    }

    public function testConnection(Router $router): bool
    {
        return count(
            $this->client($router)
                ->query('/system/identity/print')
                ->read()
        ) > 0;
    }

    public function onlineUsers(Router $router): array
    {
        return $this->client($router)
            ->query('/ppp/active/print')
            ->read();
    }

    /**
     * Membaca status dan resource MikroTik untuk dashboard.
     *
     * @return array<string, mixed>|null
     */
    public function systemResource(Router $router): ?array
    {
        $rows = $this->client($router)
            ->query('/system/resource/print')
            ->read();

        return $rows[0] ?? null;
    }

    private function secret(Router $router, string $username): ?array
    {
        $username = trim($username);

        if ($username === '') {
            return null;
        }

        $query = (new Query('/ppp/secret/print'))
            ->where('name', $username);

        $rows = $this->client($router)->query($query)->read();

        return $rows[0] ?? null;
    }

    private function secretByCustomerId(Router $router, int $customerId): ?array
    {
        $query = (new Query('/ppp/secret/print'))
            ->where('comment', 'macbilling_v2 customer #'.$customerId);

        $rows = $this->client($router)->query($query)->read();

        return $rows[0] ?? null;
    }

    private function ensureCustomerPppoeData(Customer $customer): void
    {
        $customer->loadMissing('internetPackage');

        if (! $customer->internetPackage) {
            throw new RuntimeException('Paket internet pelanggan tidak ditemukan.');
        }

        if (blank($customer->pppoe_username)) {
            throw new RuntimeException('Username PPPoE pelanggan belum diisi.');
        }

        if (blank($customer->pppoe_password)) {
            throw new RuntimeException('Password PPPoE pelanggan belum diisi.');
        }

        if (blank($customer->internetPackage->mikrotik_profile)) {
            throw new RuntimeException('MikroTik profile pada paket internet belum diisi.');
        }
    }

    public function createPppoeSecret(Router $router, Customer $customer): array
    {
        $this->ensureCustomerPppoeData($customer);

        $query = (new Query('/ppp/secret/add'))
            ->equal('name', trim($customer->pppoe_username))
            ->equal('password', $customer->pppoe_password)
            ->equal('service', 'pppoe')
            ->equal('profile', $customer->internetPackage->mikrotik_profile)
            ->equal('disabled', 'no')
            ->equal('comment', 'macbilling_v2 customer #'.$customer->id);

        return $this->client($router)->query($query)->read();
    }

    public function updatePppoeSecret(Router $router, Customer $customer): array
    {
        $this->ensureCustomerPppoeData($customer);

        /*
         * Cari berdasarkan comment agar perubahan username PPPoE
         * tidak membuat secret lama tertinggal di MikroTik.
         */
        $secret = $this->secretByCustomerId($router, $customer->id);

        if (! $secret) {
            $secret = $this->secret($router, $customer->pppoe_username);
        }

        if (! $secret) {
            return $this->createPppoeSecret($router, $customer);
        }

        $query = (new Query('/ppp/secret/set'))
            ->equal('.id', $secret['.id'])
            ->equal('name', trim($customer->pppoe_username))
            ->equal('password', $customer->pppoe_password)
            ->equal('service', 'pppoe')
            ->equal('profile', $customer->internetPackage->mikrotik_profile)
            ->equal('disabled', 'no')
            ->equal('comment', 'macbilling_v2 customer #'.$customer->id);

        return $this->client($router)->query($query)->read();
    }

    public function isolate(Customer $customer): array
    {
        $customer->loadMissing(['router', 'internetPackage']);

        if (! $customer->router) {
            throw new RuntimeException('Router pelanggan tidak ditemukan.');
        }

        $isolationProfile = trim((string) $customer->router->isolation_profile);

        if ($isolationProfile === '') {
            throw new RuntimeException(
                "Profile isolasi belum diatur untuk router {$customer->router->name}."
            );
        }

        if (blank($customer->pppoe_username)) {
            throw new RuntimeException('Username PPPoE pelanggan belum diisi.');
        }

        $client = $this->client($customer->router);

        /*
         * Utamakan pencarian comment agar username yang pernah berubah
         * tetap mengarah ke PPPoE secret milik pelanggan yang sama.
         */
        $secret = $this->secretByCustomerId($customer->router, $customer->id);

        if (! $secret) {
            $secretQuery = (new Query('/ppp/secret/print'))
                ->where('name', trim($customer->pppoe_username));

            $secrets = $client->query($secretQuery)->read();
            $secret = $secrets[0] ?? null;
        }

        if (! $secret) {
            throw new RuntimeException('PPPoE secret tidak ditemukan di MikroTik.');
        }

        $isolateQuery = (new Query('/ppp/secret/set'))
            ->equal('.id', $secret['.id'])
            ->equal('profile', $isolationProfile)
            ->equal('disabled', 'no')
            ->equal('comment', 'macbilling_v2 customer #'.$customer->id);

        $client->query($isolateQuery)->read();

        $activeQuery = (new Query('/ppp/active/print'))
            ->where('name', $secret['name'] ?? trim($customer->pppoe_username));

        $activeSessions = $client->query($activeQuery)->read();

        foreach ($activeSessions as $session) {
            if (! empty($session['.id'])) {
                $disconnectQuery = (new Query('/ppp/active/remove'))
                    ->equal('.id', $session['.id']);

                $client->query($disconnectQuery)->read();
            }
        }

        return $activeSessions;
    }

    public function activate(Customer $customer): array
    {
        $customer->loadMissing(['router', 'internetPackage']);

        if (! $customer->router) {
            throw new RuntimeException('Router pelanggan tidak ditemukan.');
        }

        return $this->updatePppoeSecret($customer->router, $customer);
    }

    public function syncProfile(Router $router, InternetPackage $package): array
    {
        $client = $this->client($router);

        $profileQuery = (new Query('/ppp/profile/print'))
            ->where('name', $package->mikrotik_profile);

        $profiles = $client->query($profileQuery)->read();

        /*
         * Jika hasil speed test terbalik, tukar urutan menjadi:
         * $package->upload_speed.'M/'.$package->download_speed.'M'
         */
        $rate = $package->download_speed.'M/'.$package->upload_speed.'M';

        $query = $profiles
            ? (new Query('/ppp/profile/set'))->equal('.id', $profiles[0]['.id'])
            : (new Query('/ppp/profile/add'));

        $query
            ->equal('name', $package->mikrotik_profile)
            ->equal('rate-limit', $rate);

        if ($package->burst_limit) {
            $query->equal('burst-limit', $package->burst_limit);
        }

        if ($package->burst_threshold) {
            $query->equal('burst-threshold', $package->burst_threshold);
        }

        if ($package->burst_time) {
            $query->equal('burst-time', $package->burst_time);
        }

        return $client->query($query)->read();
    }

    public function profiles(Router $router): array
    {
        return $this->client($router)
            ->query('/ppp/profile/print')
            ->read();
    }

    public function allPppoeUsers(Router $router): array
    {
        return $this->client($router)
            ->query('/ppp/secret/print')
            ->read();
    }
}
