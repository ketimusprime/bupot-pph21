<?php

namespace App\Exports;

use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class TaxCutsExport implements FromQuery, WithHeadings, WithMapping, WithStyles, WithTitle
{
    protected $query;

    public function __construct($query)
    {
        $this->query = $query;
    }

    public function query()
    {
        return $this->query->with(['company', 'recipient']);
    }

    public function headings(): array
    {
        return [
            'No.',
            'Nama',
            'Keterangan',
            'PT / CV',
            'Bruto',
            'DPP',
            'PPH 21',
            'No Invoice',
            'Tgl Invoice',
            'NPWP',
            'NIK',
            'Alamat',
        ];
    }

    public function map($taxCut): array
    {
        return [
           // No.
        $taxCut->id,

        // Nama
        $taxCut->recipient->name,

        // Keterangan
        $taxCut->notes ?? '-',

        // PT / CV
        $taxCut->company->name,

        // Bruto (diambil dari Komisi)
        $taxCut->commission_amount,

        // DPP
        $taxCut->dpp_amount,

        // PPH 21
        $taxCut->tax_amount,

        // No Invoice
        $taxCut->invoice_number ?? '-',

        // Tgl Invoice
        $taxCut->invoice_date ? $taxCut->invoice_date->format('d/m/Y') : '-',

        // NPWP (recipient)
        $taxCut->recipient->npwp ?? '-',

        // NIK (recipient)
        $taxCut->recipient->nik ?? '-',

        // Alamat (recipient)
        $taxCut->recipient->address ?? '-',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }

    public function title(): string
    {
        return 'Bukti Potong PPh 21';
    }
}
