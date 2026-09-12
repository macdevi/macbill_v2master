<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class CustomerExcelTemplateExport implements FromCollection, WithHeadings, ShouldAutoSize, WithStyles
{
    public function collection(): Collection
    {
        return collect([
            [
                'Router Utama',
                'Paket 10 Mbps',
                'Budi Santoso',
                '081234567890',
                'Jl. Contoh No. 10',
                'budi01',
                'rahasia123',
                10,
                'active',
            ],
        ]);
    }

    public function headings(): array
    {
        return [
            'router',
            'package',
            'name',
            'phone',
            'address',
            'pppoe_username',
            'pppoe_password',
            'due_day',
            'status',
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        $sheet->freezePane('A2');

        $sheet->getStyle('A1:I1')->getFont()->setBold(true);

        $sheet->getStyle('A1:I1')->getFill()
            ->setFillType('solid')
            ->getStartColor()
            ->setRGB('06B6D4');

        $sheet->getStyle('A1:I1')->getFont()
            ->getColor()
            ->setRGB('0F172A');

        $sheet->getStyle('D:D')->getNumberFormat()->setFormatCode('@');
        $sheet->getStyle('F:G')->getNumberFormat()->setFormatCode('@');

        return [];
    }
}
