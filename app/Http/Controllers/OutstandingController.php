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
use Illuminate\Support\Facades\Cache;

class OutstandingController extends Controller
{
    // =====================================================
    // GET DATA JSON (OUTSTANDING + STATISTIK)
    // =====================================================
    public function indexGetJson()
    {
        // Cache 30 detik untuk response JSON
        $data = Cache::remember('tagihan_index_json', 30, function () {
            // Ambil semua pelanggan & paket untuk dropdown modal
            $pelanggan = Pelanggan::all();
            $paket = Paket::all();

            // Ambil semua tagihan beserta relasinya
            $tagihans = Tagihan::with(['pelanggan', 'paket'])
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function ($item) {
                    $pelanggan = $item->pelanggan;
                    $paket = $item->paket;

                    return [
                        'id' => $item->id,
                        'nomer_id' => $pelanggan->nomer_id ?? '-',
                        'nama_lengkap' => $pelanggan->nama_lengkap ?? '-',
                        'alamat_jalan' => $pelanggan->alamat_jalan ?? '-',
                        'rt' => $pelanggan->rt ?? '-',
                        'rw' => $pelanggan->rw ?? '-',
                        'desa' => $pelanggan->desa ?? '-',
                        'kecamatan' => $pelanggan->kecamatan ?? '-',
                        'kabupaten' => $pelanggan->kabupaten ?? '-',
                        'provinsi' => $pelanggan->provinsi ?? '-',
                        'kode_pos' => $pelanggan->kode_pos ?? '-',
                        'paket' => [
                            'id' => $paket->id ?? null,
                            'nama_paket' => $paket->nama_paket ?? '-',
                            'harga' => $paket->harga ?? 0,
                            'kecepatan' => $paket->kecepatan ?? 0,
                            'masa_pembayaran' => $paket->masa_pembayaran ?? 0,
                            'durasi' => $paket->durasi ?? 0,
                        ],
                        'tanggal_mulai' => $item->tanggal_mulai,
                        'tanggal_berakhir' => $item->tanggal_berakhir,
                        'status_pembayaran' => $item->status_pembayaran,
                        'tanggal_pembayaran' => $item->tanggal_pembayaran ?? '-',
                        'bukti_pembayaran' => $item->bukti_pembayaran ?? '-',
                        'no_whatsapp' => $pelanggan->no_whatsapp ?? '08xxxxxxxxxx',
                        'catatan' => $item->catatan ?? '-',
                    ];
                });

            // Ambil list unik untuk dropdown
            $kabupatenList = $pelanggan->pluck('kabupaten')->unique()->values();
            $kecamatanList = $pelanggan->pluck('kecamatan')->unique()->values();

            // Statistik
            $totalCustomer = $pelanggan->count();
            $lunas = 0;
            $belumLunas = $tagihans->count();
            $totalPaket = $paket->count();

            return [
                'tagihans' => $tagihans,
                'pelanggan' => $pelanggan,
                'paket' => $paket,
                'statistics' => [
                    'total_customer' => $totalCustomer,
                    'lunas' => $lunas,
                    'belum_lunas' => $belumLunas,
                    'total_paket' => $totalPaket,
                ],
                'filters' => [
                    'kabupaten' => $kabupatenList,
                    'kecamatan' => $kecamatanList,
                ],
            ];
        });

        return response()->json([
            'status' => true,
            'message' => 'Data tagihan berhasil diambil.',
            'data' => $data,
        ]);
    }

    // =====================================================
    // GET DETAIL BY ID (JSON)
    // =====================================================
    public function getByIdJson($id)
    {
        $item = Tagihan::with(['pelanggan', 'paket'])->find($id);

        if (!$item) {
            return response()->json([
                'status' => false,
                'message' => 'Tagihan tidak ditemukan.',
                'data' => null,
            ], 404);
        }

        $pelanggan = $item->pelanggan;
        $paket = $item->paket;

        $tagihanDetail = [
            'id' => $item->id,
            'nomer_id' => $pelanggan->nomer_id ?? '-',
            'nama_lengkap' => $pelanggan->nama_lengkap ?? '-',
            'alamat_jalan' => $pelanggan->alamat_jalan ?? '-',
            'rt' => $pelanggan->rt ?? '-',
            'rw' => $pelanggan->rw ?? '-',
            'desa' => $pelanggan->desa ?? '-',
            'kecamatan' => $pelanggan->kecamatan ?? '-',
            'kabupaten' => $pelanggan->kabupaten ?? '-',
            'provinsi' => $pelanggan->provinsi ?? '-',
            'kode_pos' => $pelanggan->kode_pos ?? '-',
            'paket' => [
                'id' => $paket->id ?? null,
                'nama_paket' => $paket->nama_paket ?? '-',
                'harga' => $paket->harga ?? 0,
                'kecepatan' => $paket->kecepatan ?? 0,
                'masa_pembayaran' => $paket->masa_pembayaran ?? 0,
                'durasi' => $paket->durasi ?? 0,
            ],
            'tanggal_mulai' => $item->tanggal_mulai,
            'tanggal_berakhir' => $item->tanggal_berakhir,
            'status_pembayaran' => $item->status_pembayaran,
            'tanggal_pembayaran' => $item->tanggal_pembayaran ?? '-',
            'bukti_pembayaran' => $item->bukti_pembayaran ?? '-',
            'no_whatsapp' => $pelanggan->no_whatsapp ?? '08xxxxxxxxxx',
            'catatan' => $item->catatan ?? '-',
        ];

        return response()->json([
            'status' => true,
            'message' => 'Detail tagihan berhasil diambil.',
            'data' => $tagihanDetail,
        ]);
    }

    // =====================================================
    // KONFIRMASI BAYAR + GENERATE PDF + PUSH NOTIF
    // =====================================================
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

            // Update status tagihan
            $tagihan->status_pembayaran = 'lunas';
            $tagihan->tanggal_pembayaran = now();

            // Generate PDF kwitansi
            $pdf = Pdf::loadView('content.apps.pdf.kwitansi', ['tagihan' => $tagihan]);
            $filename = 'kwitansi-' . $tagihan->id . '.pdf';
            $pdfPath = 'kwitansi/' . $filename;
            Storage::disk('public')->put($pdfPath, $pdf->output());

            // Simpan path PDF ke field kwitansi
            $tagihan->kwitansi = $pdfPath;
            $tagihan->save();

            // Hapus cache terkait tagihan
            Cache::forget('tagihan_outstanding');
            Cache::forget('tagihan_index_json');

            // Buat link publik PDF
            $pdfUrl = asset('storage/' . $pdfPath);

            // Record Income
            Income::create([
                'kode' => $this->getKode('penjualan'),
                'kategori' => 'penjualan',
                'jumlah' => $tagihan->jumlah_tagihan ?? $tagihan->paket->harga,
                'keterangan' => 'Pembayaran paket ' . $tagihan->paket->nama_paket . ' dari ' . $tagihan->pelanggan->nama_lengkap,
                'tanggal_masuk' => now(),
            ]);

            // Kirim push notif Webpushr
            $pelanggan = $tagihan->pelanggan;
            if ($pelanggan && $pelanggan->webpushr_sid) {
                $end_point = 'https://api.webpushr.com/v1/notification/send/sid';

                $http_header = [
                    'Content-Type: Application/Json',
                    'webpushrKey: 2ee12b373a17d9ba5f44683cb42d4279',
                    'webpushrAuthToken: 116294',
                ];

                $req_data = [
                    'title' => 'Pembayaran Berhasil',
                    'message' => "Terima kasih, {$pelanggan->nama_lengkap}. Pembayaran Anda telah kami terima dan dikonfirmasi.",
                    'target_url' => url('https://layanan.jernih.net.id/dashboard/customer/tagihan/selesai'),
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

    // Dummy WA helper
    private function sendWA($nomor, $pesan)
    {
        return true;
    }

    // =====================================================
    // HALAMAN OUTSTANDING (BELUM BAYAR SAJA)
    // =====================================================
    public function index()
    {
        $pelanggan = Pelanggan::where('status', 'approve')->get();
        $paket = Paket::all();

        // Cache data outstanding 30 detik
        $tagihans = Cache::remember('tagihan_outstanding', 30, function () {
            return Tagihan::with(['pelanggan', 'paket'])
                ->where('status_pembayaran', 'belum bayar')
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function ($item) {
                    $pelanggan = $item->pelanggan;
                    $paket = $item->paket;

                    return [
                        'id' => $item->id,
                        'nomer_id' => $pelanggan->nomer_id ?? '-',
                        'nama_lengkap' => $pelanggan->nama_lengkap ?? '-',
                        'alamat_jalan' => $pelanggan->alamat_jalan ?? '-',
                        'rt' => $pelanggan->rt ?? '-',
                        'rw' => $pelanggan->rw ?? '-',
                        'desa' => $pelanggan->desa ?? '-',
                        'kecamatan' => $pelanggan->kecamatan ?? '-',
                        'kabupaten' => $pelanggan->kabupaten ?? '-',
                        'provinsi' => $pelanggan->provinsi ?? '-',
                        'kode_pos' => $pelanggan->kode_pos ?? '-',
                        'paket' => [
                            'id' => $paket->id ?? null,
                            'nama_paket' => $paket->nama_paket ?? '-',
                            'harga' => $paket->harga ?? 0,
                            'kecepatan' => $paket->kecepatan ?? 0,
                            'masa_pembayaran' => $paket->masa_pembayaran ?? 0,
                            'durasi' => $paket->durasi ?? 0,
                        ],
                        'tanggal_mulai' => $item->tanggal_mulai ?? null,
                        'tanggal_berakhir' => $item->tanggal_berakhir ?? null,
                        'status_pembayaran' => $item->status_pembayaran ?? 'belum bayar',
                        'tanggal_pembayaran' => $item->tanggal_pembayaran ?? '-',
                        'bukti_pembayaran' => $item->bukti_pembayaran ?? '-',
                        'no_whatsapp' => $pelanggan->no_whatsapp ?? '08xxxxxxxxxx',
                        'catatan' => $item->catatan ?? '-',
                    ];
                });
        });

        $kabupatenList = $pelanggan->pluck('kabupaten')->unique();
        $kecamatanList = $pelanggan->pluck('kecamatan')->unique();

        $totalCustomer = $pelanggan->count();
        $lunas = 0;
        $belumLunas = $tagihans->count();
        $totalPaket = $paket->count();

        return view('content.apps.Outstanding.tagihan', compact(
            'tagihans',
            'pelanggan',
            'paket',
            'totalCustomer',
            'lunas',
            'belumLunas',
            'totalPaket',
            'kabupatenList',
            'kecamatanList'
        ));
    }

    // =====================================================
    // HALAMAN PROSES VERIFIKASI
    // =====================================================
    public function proses()
    {
        $pelanggan = Pelanggan::all();
        $paket = Paket::all();

        $tagihans = Tagihan::with(['pelanggan', 'paket'])
            ->where('status_pembayaran', 'proses_verifikasi')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($item) {
                $pelanggan = $item->pelanggan;
                $paket = $item->paket;

                return [
                    'id' => $item->id,
                    'nomer_id' => $pelanggan->nomer_id ?? '-',
                    'nama_lengkap' => $pelanggan->nama_lengkap ?? '-',
                    'alamat_jalan' => $pelanggan->alamat_jalan ?? '-',
                    'rt' => $pelanggan->rt ?? '-',
                    'rw' => $pelanggan->rw ?? '-',
                    'desa' => $pelanggan->desa ?? '-',
                    'kecamatan' => $pelanggan->kecamatan ?? '-',
                    'kabupaten' => $pelanggan->kabupaten ?? '-',
                    'provinsi' => $pelanggan->provinsi ?? '-',
                    'kode_pos' => $pelanggan->kode_pos ?? '-',
                    'paket' => [
                        'id' => $paket->id ?? null,
                        'nama_paket' => $paket->nama_paket ?? '-',
                        'harga' => $paket->harga ?? 0,
                        'kecepatan' => $paket->kecepatan ?? 0,
                        'masa_pembayaran' => $paket->masa_pembayaran ?? 0,
                        'durasi' => $paket->durasi ?? 0,
                    ],
                    'tanggal_mulai' => $item->tanggal_mulai ?? null,
                    'tanggal_berakhir' => $item->tanggal_berakhir ?? null,
                    'status_pembayaran' => $item->status_pembayaran ?? 'belum bayar',
                    'tanggal_pembayaran' => $item->tanggal_pembayaran ?? '-',
                    'bukti_pembayaran' => $item->bukti_pembayaran ?? '-',
                    'no_whatsapp' => $pelanggan->no_whatsapp ?? '08xxxxxxxxxx',
                    'catatan' => $item->catatan ?? '-',
                ];
            });

        $kabupatenList = $pelanggan->pluck('kabupaten')->unique();
        $kecamatanList = $pelanggan->pluck('kecamatan')->unique();

        $totalCustomer = $pelanggan->count();
        $lunas = 0;
        $belumLunas = $tagihans->count();
        $totalPaket = $paket->count();

        return view('content.apps.Tagihan.proses-tagihan', compact(
            'tagihans',
            'pelanggan',
            'paket',
            'totalCustomer',
            'lunas',
            'belumLunas',
            'totalPaket',
            'kabupatenList',
            'kecamatanList'
        ));
    }

    // =====================================================
    // HALAMAN TAGIHAN LUNAS
    // =====================================================
    public function lunas()
    {
        $pelanggan = Pelanggan::all();
        $paket = Paket::all();

        $tagihans = Tagihan::with(['pelanggan', 'paket'])
            ->where('status_pembayaran', 'lunas')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($item) {
                $pelanggan = $item->pelanggan;
                $paket = $item->paket;

                $kwitansiUrl = null;
                if (!empty($item->kwitansi)) {
                    $kwitansiUrl = $item->kwitansi;
                }

                return [
                    'id' => $item->id,
                    'nomer_id' => $pelanggan->nomer_id ?? '-',
                    'nama_lengkap' => $pelanggan->nama_lengkap ?? '-',
                    'alamat_jalan' => $pelanggan->alamat_jalan ?? '-',
                    'rt' => $pelanggan->rt ?? '-',
                    'rw' => $pelanggan->rw ?? '-',
                    'desa' => $pelanggan->desa ?? '-',
                    'kecamatan' => $pelanggan->kecamatan ?? '-',
                    'kabupaten' => $pelanggan->kabupaten ?? '-',
                    'provinsi' => $pelanggan->provinsi ?? '-',
                    'kode_pos' => $pelanggan->kode_pos ?? '-',
                    'paket' => [
                        'id' => $paket->id ?? null,
                        'nama_paket' => $paket->nama_paket ?? '-',
                        'harga' => $paket->harga ?? 0,
                        'kecepatan' => $paket->kecepatan ?? 0,
                        'masa_pembayaran' => $paket->masa_pembayaran ?? 0,
                        'durasi' => $paket->durasi ?? 0,
                    ],
                    'tanggal_mulai' => $item->tanggal_mulai ?? null,
                    'tanggal_berakhir' => $item->tanggal_berakhir ?? null,
                    'status_pembayaran' => $item->status_pembayaran ?? 'belum bayar',
                    'tanggal_pembayaran' => $item->tanggal_pembayaran ?? '-',
                    'bukti_pembayaran' => $item->bukti_pembayaran ?? '-',
                    'kwitansi' => $kwitansiUrl,
                    'no_whatsapp' => $pelanggan->no_whatsapp ?? '08xxxxxxxxxx',
                    'catatan' => $item->catatan ?? '-',
                ];
            });

        $kabupatenList = $pelanggan->pluck('kabupaten')->unique();
        $kecamatanList = $pelanggan->pluck('kecamatan')->unique();

        $totalCustomer = $pelanggan->count();
        $lunas = $tagihans->count();
        $belumLunas = Tagihan::where('status_pembayaran', '!=', 'lunas')->count();
        $totalPaket = $paket->count();

        return view('content.apps.Tagihan.tagihan-lunas', compact(
            'tagihans',
            'pelanggan',
            'paket',
            'totalCustomer',
            'lunas',
            'belumLunas',
            'totalPaket',
            'kabupatenList',
            'kecamatanList'
        ));
    }

    // =====================================================
    // UPDATE TAGIHAN
    // =====================================================
    public function update(Request $request, $id)
    {
        $request->validate([
            'tanggal_mulai' => 'required|date',
            'tanggal_berakhir' => 'nullable|date',
            'catatan' => 'nullable|string',
            'paket_id' => 'required|exists:pakets,id',
            'bukti_pembayaran' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:2048',
            'kwitansi' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:2048',
        ]);

        $tagihan = Tagihan::findOrFail($id);
        $paket = Paket::findOrFail($request->paket_id);

        $tanggalMulai = \Carbon\Carbon::parse($request->tanggal_mulai);
        $tanggalBerakhir = $request->tanggal_berakhir
            ? \Carbon\Carbon::parse($request->tanggal_berakhir)
            : $tanggalMulai->copy()->addDays($paket->masa_pembayaran);

        if ($request->hasFile('bukti_pembayaran')) {
            if ($tagihan->bukti_pembayaran && Storage::disk('public')->exists($tagihan->bukti_pembayaran)) {
                Storage::disk('public')->delete($tagihan->bukti_pembayaran);
            }

            $tagihan->bukti_pembayaran = $request->file('bukti_pembayaran')
                ->store('bukti_pembayaran', 'public');
        }

        if ($request->hasFile('kwitansi')) {
            if ($tagihan->kwitansi && Storage::disk('public')->exists($tagihan->kwitansi)) {
                Storage::disk('public')->delete($tagihan->kwitansi);
            }

            $tagihan->kwitansi = $request->file('kwitansi')
                ->store('kwitansi', 'public');
        }

        $tagihan->update([
            'paket_id' => $request->paket_id,
            'tanggal_mulai' => $tanggalMulai->format('Y-m-d'),
            'tanggal_berakhir' => $tanggalBerakhir->format('Y-m-d'),
            'catatan' => $request->catatan,
        ]);

        Cache::forget('tagihan_outstanding');
        Cache::forget('tagihan_index_json');

        return redirect()->back()->with('success', 'Tagihan berhasil diperbarui!');
    }

    // =====================================================
    // STORE TAGIHAN BARU
    // =====================================================
    public function store(Request $request)
    {
        $request->validate([
            'pelanggan_id' => 'required|exists:pelanggans,id',
            'paket_id' => 'required|exists:pakets,id',
            'tanggal_mulai' => 'required|date',
            'tanggal_berakhir' => 'nullable|date',
            'catatan' => 'nullable|string',
        ]);

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

        $pelanggan = Pelanggan::find($request->pelanggan_id);

        if ($pelanggan && $pelanggan->webpushr_sid) {
            $ch = curl_init('https://api.webpushr.com/v1/notification/send/sid');

            $payload = [
                'title' => 'Pemberitahuan untuk Anda',
                'message' => "Halo {$pelanggan->nama}, kami baru saja menerbitkan tagihan untuk Anda. Silakan cek detailnya.",
                'target_url' => url('https://layanan.jernih.net.id/dashboard/customer/tagihan'),
                'sid' => $pelanggan->webpushr_sid,
            ];

            $headers = [
                'Content-Type: application/json',
                'webpushrKey: 2ee12b373a17d9ba5f44683cb42d4279',
                'webpushrAuthToken: 116294',
            ];

            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            $response = curl_exec($ch);
            curl_close($ch);
        }

        Cache::forget('tagihan_outstanding');
        Cache::forget('tagihan_index_json');

        return redirect()->back()->with('success', 'Tagihan berhasil ditambahkan dan notifikasi terkirim!');
    }

    // OneSignal helper
    private function sendOneSignalNotification($playerId, $title, $message)
    {
        $content = [
            'en' => $message,
        ];

        $fields = [
            'app_id' => env('ONESIGNAL_APP_ID'),
            'include_player_ids' => [$playerId],
            'headings' => ['en' => $title],
            'contents' => $content,
        ];

        $fields = json_encode($fields);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://onesignal.com/api/v1/notifications');
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json; charset=utf-8',
            'Authorization: Basic ' . env('ONESIGNAL_REST_API_KEY'),
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);

        $result = curl_exec($ch);
        curl_close($ch);

        return $result;
    }

    // Generate kode otomatis per kategori
    private function getKode($kategori)
    {
        return match (strtolower($kategori)) {
            'internet' => '01',
            'penjualan' => '02',
            'piutang' => '03',
            default => 'O4',
        };
    }

    // =====================================================
    // UPDATE STATUS LUNAS (SIMPLE JSON)
    // =====================================================
    public function updateStatus($id)
    {
        $tagihan = Tagihan::with('pelanggan', 'paket')->find($id);

        if (!$tagihan) {
            return response()->json([
                'success' => false,
                'message' => 'Tagihan tidak ditemukan.',
            ], 404);
        }

        $tagihan->status_pembayaran = 'lunas';
        $tagihan->tanggal_pembayaran = now();
        $tagihan->save();

        Income::create([
            'kode' => $this->getKode('penjualan'),
            'kategori' => 'Tagihan',
            'jumlah' => $tagihan->jumlah_tagihan ?? $tagihan->paket->harga,
            'keterangan' => 'Pembayaran paket ' . $tagihan->paket->nama_paket . ' dari ' . $tagihan->pelanggan->nama_lengkap,
            'tanggal_masuk' => now(),
        ]);

        Cache::forget('tagihan_outstanding');
        Cache::forget('tagihan_index_json');

        return response()->json([
            'success' => true,
            'message' => 'Status pembayaran berhasil diperbarui menjadi lunas dan income tercatat.',
        ]);
    }

    // =====================================================
    // HAPUS TAGIHAN
    // =====================================================
    public function destroy($id)
    {
        $tagihan = Tagihan::findOrFail($id);
        $tagihan->delete();

        Cache::forget('tagihan_outstanding');
        Cache::forget('tagihan_index_json');

        return redirect()->back()->with('success', 'Tagihan berhasil dihapus!');
    }

    // =====================================================
    // MASS STORE TAGIHAN
    // =====================================================
    public function massStore(Request $request)
    {
        $request->validate([
            'tanggal_mulai' => 'required|date',
            'tanggal_berakhir' => 'required|date|after_or_equal:tanggal_mulai',
        ]);

        $pelanggan = Pelanggan::with('paket')
            ->whereDoesntHave('tagihans', function ($q) {
                $q->where('status_pembayaran', 'belum bayar');
            })
            ->get();

        if ($pelanggan->count() == 0) {
            return back()->with('error', 'Tidak ada pelanggan untuk dibuatkan tagihan.');
        }

        DB::beginTransaction();

        try {
            foreach ($pelanggan as $p) {
                Tagihan::create([
                    'pelanggan_id' => $p->id,
                    'paket_id' => $p->paket_id,
                    'nama_lengkap' => $p->nama_lengkap,
                    'nomer_id' => $p->nomer_id,
                    'no_whatsapp' => $p->no_whatsapp,
                    'alamat_jalan' => $p->alamat_jalan,
                    'rt' => $p->rt,
                    'rw' => $p->rw,
                    'desa' => $p->desa,
                    'kecamatan' => $p->kecamatan,
                    'kabupaten' => $p->kabupaten,
                    'provinsi' => $p->provinsi,
                    'kode_pos' => $p->kode_pos,
                    'harga' => $p->paket->harga,
                    'kecepatan' => $p->paket->kecepatan,
                    'masa_pembayaran' => $p->paket->masa_pembayaran,
                    'tanggal_mulai' => $request->tanggal_mulai,
                    'tanggal_berakhir' => $request->tanggal_berakhir,
                    'status_pembayaran' => 'belum bayar',
                ]);
            }

            DB::commit();

            Cache::forget('tagihan_outstanding');
            Cache::forget('tagihan_index_json');

            return back()->with('success', 'Tagihan berhasil dibuat untuk semua pelanggan!');
        } catch (\Throwable $th) {
            DB::rollBack();

            return back()->with('error', 'Gagal membuat tagihan massal: ' . $th->getMessage());
        }
    }

    // =====================================================
    // OUTSTANDING (SEMUA STATUS + FILTER BULAN/TAHUN)
    // =====================================================
    public function outstanding(Request $request)
    {
        $query = Tagihan::with(['pelanggan', 'paket']);

        if ($request->filled('bulan')) {
            $query->whereMonth('tanggal_mulai', $request->bulan);
        }

        if ($request->filled('tahun')) {
            $query->whereYear('tanggal_mulai', $request->tahun);
        }

        if ($request->filled('status_filter') && $request->status_filter !== 'semua') {
            $query->where('status_pembayaran', $request->status_filter);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('pelanggan', function ($subQ) use ($search) {
                    $subQ->where('nama_lengkap', 'LIKE', "%{$search}%")
                        ->orWhere('nomer_id', 'LIKE', "%{$search}%")
                        ->orWhere('no_whatsapp', 'LIKE', "%{$search}%");
                })
                ->orWhereHas('paket', function ($subQ) use ($search) {
                    $subQ->where('nama_paket', 'LIKE', "%{$search}%");
                });
            });
        }

        $sortBy = $request->input('sort_by', 'created_at');
        $sortOrder = $request->input('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $perPage = $request->input('per_page', 20);
        $tagihans = $query->paginate($perPage)->withQueryString();

        $tagihans->getCollection()->transform(function ($item) {
            $pelanggan = $item->pelanggan;
            $paket = $item->paket;

            return (object) [
                'id' => $item->id,
                'nomer_id' => $pelanggan->nomer_id ?? '-',
                'nama_lengkap' => $pelanggan->nama_lengkap ?? '-',
                'alamat_jalan' => $pelanggan->alamat_jalan ?? '-',
                'rt' => $pelanggan->rt ?? '-',
                'rw' => $pelanggan->rw ?? '-',
                'desa' => $pelanggan->desa ?? '-',
                'kecamatan' => $pelanggan->kecamatan ?? '-',
                'kabupaten' => $pelanggan->kabupaten ?? '-',
                'provinsi' => $pelanggan->provinsi ?? '-',
                'kode_pos' => $pelanggan->kode_pos ?? '-',
                'paket' => [
                    'id' => $paket->id ?? null,
                    'nama_paket' => $paket->nama_paket ?? '-',
                    'harga' => $paket->harga ?? 0,
                    'kecepatan' => $paket->kecepatan ?? 0,
                    'masa_pembayaran' => $paket->masa_pembayaran ?? 0,
                    'durasi' => $paket->durasi ?? 0,
                ],
                'tanggal_mulai' => $item->tanggal_mulai ?? null,
                'tanggal_berakhir' => $item->tanggal_berakhir ?? null,
                'status_pembayaran' => $item->status_pembayaran ?? 'belum bayar',
                'tanggal_pembayaran' => $item->tanggal_pembayaran ?? '-',
                'bukti_pembayaran' => $item->bukti_pembayaran ?? '-',
                'kwitansi' => $item->kwitansi ?? null,
                'no_whatsapp' => $pelanggan->no_whatsapp ?? '08xxxxxxxxxx',
                'catatan' => $item->catatan ?? '-',
            ];
        });

        $pelanggan = Pelanggan::where('status', 'approve')->get();
        $paket = Paket::all();

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

        $kabupatenList = Pelanggan::pluck('kabupaten')->unique()->filter();
        $kecamatanList = Pelanggan::pluck('kecamatan')->unique()->filter();

        $bulanList = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret',
            4 => 'April', 5 => 'Mei', 6 => 'Juni',
            7 => 'Juli', 8 => 'Agustus', 9 => 'September',
            10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];

        $tahunList = range(date('Y'), date('Y') - 4);

        return view('content.apps.Tagihan.outstanding', compact(
            'tagihans',
            'pelanggan',
            'paket',
            'statistics',
            'kabupatenList',
            'kecamatanList',
            'bulanList',
            'tahunList'
        ));
    }
}
