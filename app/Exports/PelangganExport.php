<?php

namespace App\Exports;

use App\Models\Pelanggan;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class PelangganExport implements FromCollection, WithHeadings, WithMapping
{
    public function collection()
    {
        return Pelanggan::select(
            'nama_lengkap',
            'nomer_id',
            'no_whatsapp',
            'created_at'
        )->get();
    }

    public function headings(): array
    {
        return [
            'Nama',
            'nomer_id',
	    'telp',
            'Tanggal Daftar'
        ];
    }

    public function map($row): array
    {
        return [
            $row->nama_lengkap,
            $row->nomer_id,
	    $row->no_whatsapp,
            $row->created_at->format('d-m-Y'),
        ];
    }
}
