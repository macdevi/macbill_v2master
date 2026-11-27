<?php

namespace App\Rules;

use App\Models\InternetPackage;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class PackageAvailableOnRouter implements ValidationRule
{
    public function __construct(private mixed $routerId)
    {
    }

    public function validate(
        string $attribute,
        mixed $value,
        Closure $fail
    ): void {
        $routerId = $this->positiveId($this->routerId);
        $packageId = $this->positiveId($value);

        if (
            $routerId === null ||
            $packageId === null ||
            !InternetPackage::query()
                ->where('active', true)
                ->availableForRouter($routerId)
                ->whereKey($packageId)
                ->exists()
        ) {
            $fail('Paket tidak aktif atau tidak tersedia pada router yang dipilih.');
        }
    }

    private function positiveId(mixed $value): ?int
    {
        if (!is_int($value) && !is_string($value)) {
            return null;
        }

        $id = filter_var(
            $value,
            FILTER_VALIDATE_INT,
            ['options' => ['min_range' => 1]]
        );

        return $id === false ? null : $id;
    }
}
