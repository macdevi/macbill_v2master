<?php

namespace App\Exports;

use App\Models\Customer;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class CustomerExcelExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    public function collection(): Collection
    {
        return Customer::query()
            ->with([
                'area:id,code,name',
                'router:id,name,host',
                'internetPackage:id,name,monthly_price',
            ])
            ->orderBy('name')
            ->get();
    }

    public function headings(): array
    {
        return [
            'Kode Pelanggan',
            'Kode Wilayah',
            'Nama Wilayah',
            'Router',
            'Paket',
            'Harga Custom',
            'Mode Pajak',
            'Nama Pelanggan',
            'No. Telepon',
            'Alamat',
            'Username PPPoE',
            'Password PPPoE',
            'Tanggal Jatuh Tempo',
            'Status',
        ];
    }

    public function map($customer): array
    {
        return [
            $customer->customer_code,
            $customer->area?->code,
            $customer->area?->name,
            $customer->router?->name,
            $customer->internetPackage?->name,
            $customer->monthly_price_override,
            $customer->tax_mode,
            $customer->name,
            $customer->phone,
            $customer->address,
            $customer->pppoe_username,
            $customer->getRawOriginal('pppoe_password'),
            $customer->due_day,
            $customer->status,
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        $sheet->freezePane('A2');

        $sheet->getStyle('A1:N1')->getFont()->setBold(true);

        $sheet->getStyle('A1:N1')->getFill()
            ->setFillType('solid')
            ->getStartColor()
            ->setRGB('06B6D4');

        $sheet->getStyle('A1:N1')->getFont()
            ->getColor()
            ->setRGB('0F172A');

        $sheet->getStyle('I:I')->getNumberFormat()->setFormatCode('@');
        $sheet->getStyle('K:K')->getNumberFormat()->setFormatCode('@');

        return [];
    }
}
