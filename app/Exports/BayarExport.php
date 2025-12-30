<?php

namespace App\Exports;

use App\Models\Tagihan;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class BayarExport implements FromQuery, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    protected $search;
    protected $status;

    public function __construct($search = null, $status = null)
    {
        $this->search = $search;
        $this->status = $status;
    }

    /**
     * Query builder untuk data export
     */
    public function query()
    {
        // TAMBAHKAN RELASI 'rekening' untuk type_pembayaran
        $query = Tagihan::with(['pelanggan', 'paket', 'rekening']);

        // Filter berdasarkan status jika ada
        if ($this->status) {
            $query->where('status_pembayaran', $this->status);
        }

        // Filter search jika ada
        if ($this->search) {
            $query->where(function($q) {
                $q->whereHas('pelanggan', function($subQ) {
                    $subQ->where('nama_lengkap', 'LIKE', "%{$this->search}%")
                         ->orWhere('nomer_id', 'LIKE', "%{$this->search}%")
                         ->orWhere('no_whatsapp', 'LIKE', "%{$this->search}%")
                         ->orWhere('kecamatan', 'LIKE', "%{$this->search}%")
                         ->orWhere('kabupaten', 'LIKE', "%{$this->search}%");
                })
                ->orWhereHas('paket', function($subQ) {
                    $subQ->where('nama_paket', 'LIKE', "%{$this->search}%");
                });
            });
        }

        return $query->orderBy('created_at', 'desc');
    }

    /**
     * Header kolom Excel (TANPA ALAMAT)
     */
    public function headings(): array
    {
        return [
            'NO',
            'NO. ID PELANGGAN',
            'NAMA LENGKAP',
            'NO. WHATSAPP',
            'NAMA PAKET',
            'HARGA PAKET',
            'KECEPATAN',
            'TANGGAL MULAI',
            'JATUH TEMPO',
            'STATUS PEMBAYARAN',
            'TYPE PEMBAYARAN',
            'TANGGAL PEMBAYARAN',
            'CATATAN'
        ];
    }

    /**
     * Mapping data ke format Excel (TANPA ALAMAT)
     */
    public function map($tagihan): array
    {
        static $no = 0;
        $no++;

        return [
            $no,
            $tagihan->pelanggan->nomer_id ?? '-',
            $tagihan->pelanggan->nama_lengkap ?? '-',
            $tagihan->pelanggan->no_whatsapp ?? '-',
            $tagihan->paket->nama_paket ?? '-',
            'Rp ' . number_format($tagihan->paket->harga ?? 0, 0, ',', '.'),
            ($tagihan->paket->kecepatan ?? '-') . ' Mbps',
            $tagihan->tanggal_mulai ? \Carbon\Carbon::parse($tagihan->tanggal_mulai)->format('d/m/Y') : '-',
            $tagihan->tanggal_berakhir ? \Carbon\Carbon::parse($tagihan->tanggal_berakhir)->format('d/m/Y') : '-',
            strtoupper($tagihan->status_pembayaran ?? 'BELUM BAYAR'),
            $tagihan->rekening->nama_bank ?? '-',
            $tagihan->tanggal_pembayaran ? \Carbon\Carbon::parse($tagihan->tanggal_pembayaran)->format('d/m/Y H:i') : '-',
            $tagihan->catatan ?? '-'
        ];
    }

    /**
     * Styling Excel
     */
    public function styles(Worksheet $sheet)
    {
        return [
            // Style header row
            1 => [
                'font' => [
                    'bold' => true,
                    'size' => 12,
                    'color' => ['rgb' => 'FFFFFF']
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '696CFF']
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => '000000']
                    ]
                ]
            ],
        ];
    }
}
