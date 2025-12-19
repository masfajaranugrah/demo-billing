<?php

namespace App\Http\Controllers;

use App\Models\Income;
use App\Models\Paket;
use App\Models\Pelanggan;
use App\Models\Tagihan;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class OutstandingController extends Controller
{
    /**
     * Display listing of all outstanding bills
     */
    public function index(Request $request)
    {
        // ? Base query dengan eager loading
        $query = Tagihan::with(['pelanggan', 'paket']);

        // ? Filter berdasarkan bulan/tahun
        if ($request->filled('bulan')) {
            $query->whereMonth('tanggal_mulai', $request->bulan);
        }

        if ($request->filled('tahun')) {
            $query->whereYear('tanggal_mulai', $request->tahun);
        }

        // ? Filter berdasarkan status
        if ($request->filled('status_filter') && $request->status_filter !== 'semua') {
            $query->where('status_pembayaran', $request->status_filter);
        }

        // ? Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->whereHas('pelanggan', function($subQ) use ($search) {
                    $subQ->where('nama_lengkap', 'LIKE', "%{$search}%")
                         ->orWhere('nomer_id', 'LIKE', "%{$search}%")
                         ->orWhere('no_whatsapp', 'LIKE', "%{$search}%");
                })
                ->orWhereHas('paket', function($subQ) use ($search) {
                    $subQ->where('nama_paket', 'LIKE', "%{$search}%");
                });
            });
        }

        // ? Sorting
        $query->orderBy('created_at', 'desc');

        // ? Pagination
        $tagihans = $query->paginate(20)->withQueryString();

        // ? Ambil pelanggan yang approved untuk dropdown
        $pelanggan = Pelanggan::where('status', 'approve')->get();
        $paket = Paket::all();

        // ? Statistik Outstanding
        try {
            $totalTagihan = Tagihan::count();
            $totalBelumBayar = Tagihan::where('status_pembayaran', 'belum bayar')->count();
            $totalProses = Tagihan::where('status_pembayaran', 'proses_verifikasi')->count();
            $totalLunas = Tagihan::where('status_pembayaran', 'lunas')->count();
            
            $totalOverdue = Tagihan::where('status_pembayaran', '!=', 'lunas')
                ->where('tanggal_berakhir', '<', now())
                ->count();

            $nilaiOutstanding = Tagihan::where('status_pembayaran', 'belum bayar')
                ->join('pakets', 'tagihans.paket_id', '=', 'pakets.id')
                ->sum('pakets.harga');

            $statistics = [
                'total' => $totalTagihan,
                'belum_bayar' => $totalBelumBayar,
                'proses' => $totalProses,
                'lunas' => $totalLunas,
                'overdue' => $totalOverdue,
                'nilai_outstanding' => $nilaiOutstanding,
            ];
        } catch (\Exception $e) {
            $statistics = [
                'total' => 0,
                'belum_bayar' => 0,
                'proses' => 0,
                'lunas' => 0,
                'overdue' => 0,
                'nilai_outstanding' => 0,
            ];
        }

        // ? Filter dropdown lists
        $bulanList = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 
            4 => 'April', 5 => 'Mei', 6 => 'Juni',
            7 => 'Juli', 8 => 'Agustus', 9 => 'September',
            10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];

        $tahunList = range(date('Y'), date('Y') - 4);

        return view('content.apps.Outstanding.index', compact(
            'tagihans',
            'pelanggan',
            'paket',
            'statistics',
            'bulanList',
            'tahunList'
        ));
    }

    /**
     * Store a new outstanding bill
     */
    public function store(Request $request)
    {
        $request->validate([
            'pelanggan_id' => 'required|exists:pelanggans,id',
            'paket_id' => 'required|exists:pakets,id',
            'tanggal_mulai' => 'required|date',
            'tanggal_berakhir' => 'nullable|date',
            'catatan' => 'nullable|string',
        ]);

        // ? Cek duplikat - Cegah create jika sudah ada tagihan belum bayar
        $existingTagihan = Tagihan::where('pelanggan_id', $request->pelanggan_id)
            ->where('status_pembayaran', 'belum bayar')
            ->exists();

        if ($existingTagihan) {
            return redirect()->back()
                ->with('error', 'Pelanggan ini sudah memiliki tagihan yang belum dibayar!')
                ->withInput();
        }

        DB::beginTransaction();
        try {
            $paket = Paket::findOrFail($request->paket_id);
            $tanggalMulai = \Carbon\Carbon::parse($request->tanggal_mulai);
            $tanggalBerakhir = $request->tanggal_berakhir
                ? \Carbon\Carbon::parse($request->tanggal_berakhir)
                : $tanggalMulai->copy()->addDays($paket->masa_pembayaran);

            $tagihan = Tagihan::create([
                'pelanggan_id' => $request->pelanggan_id,
                'paket_id' => $request->paket_id,
                'harga' => $paket->harga,
                'tanggal_mulai' => $tanggalMulai->format('Y-m-d'),
                'tanggal_berakhir' => $tanggalBerakhir->format('Y-m-d'),
                'status_pembayaran' => 'belum bayar',
                'catatan' => $request->catatan,
            ]);

            // ? Kirim push notification
            $pelanggan = Pelanggan::find($request->pelanggan_id);
            if ($pelanggan && $pelanggan->webpushr_sid) {
                $this->sendPushNotification($pelanggan, 'Tagihan Baru', 
                    "Halo {$pelanggan->nama_lengkap}, kami baru saja menerbitkan tagihan untuk Anda. Silakan cek detailnya.");
            }

            DB::commit();
            return redirect()->back()->with('success', 'Tagihan outstanding berhasil ditambahkan!');
            
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Gagal membuat tagihan: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Update outstanding bill
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'tanggal_mulai' => 'required|date',
            'tanggal_berakhir' => 'required|date',
            'catatan' => 'nullable|string',
            'paket_id' => 'required|exists:pakets,id',
        ]);

        DB::beginTransaction();
        try {
            $tagihan = Tagihan::findOrFail($id);
            $paket = Paket::findOrFail($request->paket_id);

            $tanggalMulai = \Carbon\Carbon::parse($request->tanggal_mulai);
            $tanggalBerakhir = \Carbon\Carbon::parse($request->tanggal_berakhir);

            $tagihan->update([
                'paket_id' => $request->paket_id,
                'harga' => $paket->harga,
                'tanggal_mulai' => $tanggalMulai->format('Y-m-d'),
                'tanggal_berakhir' => $tanggalBerakhir->format('Y-m-d'),
                'catatan' => $request->catatan,
            ]);

            DB::commit();
            return redirect()->back()->with('success', 'Tagihan berhasil diperbarui!');
            
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal update tagihan: ' . $e->getMessage());
        }
    }

    /**
     * Delete outstanding bill
     */
    public function destroy($id)
    {
        try {
            $tagihan = Tagihan::findOrFail($id);
            
            // ? Hapus file terkait jika ada
            if ($tagihan->bukti_pembayaran && Storage::disk('public')->exists($tagihan->bukti_pembayaran)) {
                Storage::disk('public')->delete($tagihan->bukti_pembayaran);
            }
            if ($tagihan->kwitansi && Storage::disk('public')->exists($tagihan->kwitansi)) {
                Storage::disk('public')->delete($tagihan->kwitansi);
            }

            $tagihan->delete();

            return redirect()->back()->with('success', 'Tagihan berhasil dihapus!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menghapus tagihan: ' . $e->getMessage());
        }
    }

    /**
     * Konfirmasi pembayaran
     */
    public function konfirmasiBayar(Request $request, $id)
    {
        $tagihan = Tagihan::with('pelanggan', 'paket')->findOrFail($id);

        DB::beginTransaction();
        try {
            // Upload bukti pembayaran (opsional)
            if ($request->hasFile('bukti_pembayaran')) {
                $file = $request->file('bukti_pembayaran');
                $path = $file->store('bukti_pembayaran', 'public');
                $tagihan->bukti_pembayaran = $path;
            }

            // Update status tagihan menjadi lunas
            $tagihan->status_pembayaran = 'lunas';
            $tagihan->tanggal_pembayaran = now();

            // Generate PDF kwitansi
            $pdf = Pdf::loadView('content.apps.pdf.kwitansi', ['tagihan' => $tagihan]);
            $filename = 'kwitansi-'.$tagihan->id.'.pdf';
            $pdfPath = 'kwitansi/'.$filename;
            Storage::disk('public')->put($pdfPath, $pdf->output());

            $tagihan->kwitansi = $pdfPath;
            $tagihan->save();

            $pdfUrl = asset('storage/'.$pdfPath);

            // Buat record Income
            Income::create([
                'kode' => $this->getKode('penjualan'),
                'kategori' => 'penjualan',
                'jumlah' => $tagihan->harga ?? $tagihan->paket->harga,
                'keterangan' => 'Pembayaran paket '.$tagihan->paket->nama_paket.' dari '.$tagihan->pelanggan->nama_lengkap,
                'tanggal_masuk' => now(),
            ]);

            // Kirim push notification
            $pelanggan = $tagihan->pelanggan;
            if ($pelanggan && $pelanggan->webpushr_sid) {
                $this->sendPushNotification($pelanggan, 'Pembayaran Berhasil', 
                    "Terima kasih, {$pelanggan->nama_lengkap}. Pembayaran Anda telah kami terima dan dikonfirmasi.");
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'pdfUrl' => $pdfUrl,
                'message' => 'Pembayaran berhasil dikonfirmasi dan notifikasi terkirim!',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    /**
     * Helper: Send push notification
     */
    private function sendPushNotification($pelanggan, $title, $message)
    {
        if (!$pelanggan->webpushr_sid) {
            return false;
        }

        $end_point = 'https://api.webpushr.com/v1/notification/send/sid';

        $http_header = [
            'Content-Type: Application/Json',
            'webpushrKey: 2ee12b373a17d9ba5f44683cb42d4279',
            'webpushrAuthToken: 116294',
        ];

        $req_data = [
            'title' => $title,
            'message' => $message,
            'target_url' => url('https://layanan.jernih.net.id/dashboard/customer/tagihan'),
            'sid' => $pelanggan->webpushr_sid,
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HTTPHEADER, $http_header);
        curl_setopt($ch, CURLOPT_URL, $end_point);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($req_data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);

        return $response;
    }

    /**
     * Helper: Generate kode
     */
    private function getKode($kategori)
    {
        return match (strtolower($kategori)) {
            'internet' => '01',
            'penjualan' => '02',
            'piutang' => '03',
            default => '04',
        };
    }
}
