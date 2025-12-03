<?php

namespace App\Exports;

use App\Models\Member;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class CustomerExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    protected $outletFilter;
    protected $tipeFilter;

    public function __construct($outletFilter = 'all', $tipeFilter = 'all')
    {
        $this->outletFilter = $outletFilter;
        $this->tipeFilter = $tipeFilter;
    }

    public function collection()
    {
        $query = Member::with(['tipe', 'outlet'])->withTotalPiutang();

        if ($this->outletFilter !== 'all') {
            $query->where('id_outlet', $this->outletFilter);
        }

        if ($this->tipeFilter !== 'all') {
            $query->where('id_tipe', $this->tipeFilter);
        }

        return $query->get();
    }

    public function headings(): array
    {
        return [
            'No',
            'Kode Member',
            'Nama',
            'Telepon',
            'Alamat',
            'Tipe Customer',
            'Outlet',
            'Total Piutang',
        ];
    }

    public function map($member): array
    {
        static $no = 0;
        $no++;

        return [
            $no,
            $member->getMemberCodeWithPrefix() ?? $member->kode_member ?? '-',
            $member->nama,
            $member->telepon,
            $member->alamat,
            $member->tipe ? $member->tipe->nama_tipe : '-',
            $member->outlet ? $member->outlet->nama : '-',
            'Rp ' . number_format($member->total_piutang ?? 0, 0, ',', '.'),
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
