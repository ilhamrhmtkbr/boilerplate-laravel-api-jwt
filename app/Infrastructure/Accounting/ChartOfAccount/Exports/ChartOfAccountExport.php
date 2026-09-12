<?php

declare(strict_types=1);

namespace App\Infrastructure\Accounting\ChartOfAccount\Exports;

use App\Infrastructure\Accounting\ChartOfAccount\Persistence\Eloquent\Models\ChartOfAccountModel;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ChartOfAccountExport implements FromQuery, WithHeadings, WithMapping, WithStyles, ShouldAutoSize, WithTitle
{
    private int $rowIndex = 0;

    public function __construct(private readonly array $filters = []) {}

    public function query(): Builder
    {
        $query = ChartOfAccountModel::query();
        if (!empty($this->filters['search'])) {
            $query->where('name', 'like', '%' . $this->filters['search'] . '%');
        }
        return $query->latest();
    }

    public function title(): string { return 'Chart Of Accounts'; }

    public function headings(): array
    {
        return ['No.', 'Name', 'Created At'];
    }

    public function map($row): array
    {
        $this->rowIndex++;
        return [$this->rowIndex, $row->name, $row->created_at_formatted];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill'      => ['fillType' => 'solid', 'color' => ['rgb' => '1F4E79']],
                'alignment' => ['horizontal' => 'center'],
            ],
        ];
    }
}
