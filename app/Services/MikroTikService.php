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
        return count($this->client($router)->query('/system/identity/print')->read()) > 0;
    }

    public function onlineUsers(Router $router): array
    {
        return $this->client($router)->query('/ppp/active/print')->read();
    }

    private function secret(Router $router, string $username): ?array
    {
        $query = (new Query('/ppp/secret/print'))
            ->where('name', $username);

        $rows = $this->client($router)->query($query)->read();

        return $rows[0] ?? null;
    }

    public function createPppoeSecret(Router $router, Customer $customer): array
    {
        $query = (new Query('/ppp/secret/add'))
            ->equal('name', $customer->pppoe_username)
            ->equal('password', $customer->pppoe_password)
            ->equal('service', 'pppoe')
            ->equal('profile', $customer->internetPackage->mikrotik_profile)
            ->equal('comment', 'macbilling_v2 customer #'.$customer->id);
        return $this->client($router)->query($query)->read();
    }

    public function updatePppoeSecret(Router $router, Customer $customer): array
    {
        $secret = $this->secret($router, $customer->pppoe_username);
        if (!$secret) return $this->createPppoeSecret($router, $customer);
        $query = (new Query('/ppp/secret/set'))
            ->equal('.id', $secret['.id'])
            ->equal('name', $customer->pppoe_username)
            ->equal('service', 'pppoe')
            ->equal('profile', $customer->internetPackage->mikrotik_profile)
            ->equal('disabled', 'no');

        if (!empty($customer->pppoe_password)) {
            $query->equal('password', $customer->pppoe_password);
        }
        return $this->client($router)->query($query)->read();
    }

    public function isolate(Customer $customer): array
    {
        $secret = $this->secret($customer->router, $customer->pppoe_username);
        if (!$secret) throw new RuntimeException('PPPoE secret tidak ditemukan di MikroTik.');
        $query = (new Query('/ppp/secret/set'))
            ->equal('.id', $secret['.id'])
            ->equal('profile', config('mikrotik.isolation_profile'));
        return $this->client($customer->router)->query($query)->read();
    }

    public function activate(Customer $customer): array
    {
        return $this->updatePppoeSecret($customer->router, $customer);
    }

    public function syncProfile(Router $router, InternetPackage $package): array
    {
        $client = $this->client($router);

        $profileQuery = (new Query('/ppp/profile/print'))
            ->where('name', $package->mikrotik_profile);

        $profiles = $client->query($profileQuery)->read();
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