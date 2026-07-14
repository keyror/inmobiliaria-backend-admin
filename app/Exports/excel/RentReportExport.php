<?php

namespace App\Exports\excel;

use App\Support\ReportVariables;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class RentReportExport implements FromCollection, ShouldAutoSize, WithHeadings, WithStyles
{
    public function __construct(
        private readonly Collection $rents,
        private readonly array $columns
    ) {}

    public function collection(): Collection
    {
        return $this->rents->map(function ($rent) {
            return array_map(
                fn ($col) => ReportVariables::resolve($rent, $col['key']),
                $this->columns
            );
        });
    }

    public function headings(): array
    {
        return array_column($this->columns, 'label');
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => '374151']],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'E5E7EB'],
                ],
            ],
        ];
    }
}
