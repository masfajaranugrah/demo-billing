@extends('layouts/layoutMaster')

@section('title', 'Tagihan - Apps')


@section('vendor-style')
@vite([
  'resources/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.scss',
  'resources/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.scss',
  'resources/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.scss',
  'resources/assets/vendor/libs/select2/select2.scss',
  'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss',
  'resources/assets/vendor/libs/flatpickr/flatpickr.scss',
])
<style>
/* ========================================= */
/* MODERN CLEAN UI STYLES 2025 */
/* ========================================= */
:root {
  --card-shadow: 0 2px 8px rgba(0,0,0,0.08);
  --card-hover-shadow: 0 4px 16px rgba(0,0,0,0.12);
  --border-radius: 12px;
  --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.card {
  border: none;
  border-radius: var(--border-radius);
  box-shadow: var(--card-shadow);
  transition: var(--transition);
  overflow: hidden;
  background: #ffffff;
}

.card:hover {
  box-shadow: var(--card-hover-shadow);
}

.card-header-custom {
  color: black;
  border-radius: 12px 12px 0 0 !important;
  padding: 1.5rem;
  border-bottom: 2px solid #f0f0f0;
}

/* Modern Buttons */
.btn {
  border-radius: 8px;
  padding: 0.5rem 1.25rem;
  font-weight: 500;
  transition: var(--transition);
  border: none;
  font-size: 0.875rem;
}

.btn-primary {
  background: linear-gradient(135deg, #696cff 0%, #5a5dc9 100%);
  box-shadow: 0 2px 8px rgba(105, 108, 255, 0.3);
}

.btn-primary:hover {
  background: linear-gradient(135deg, #5a5dc9 0%, #4a4db9 100%);
  transform: translateY(-1px);
}

.btn-success {
  background: linear-gradient(135deg, #71dd37 0%, #5cb82e 100%);
  box-shadow: 0 2px 8px rgba(113, 221, 55, 0.3);
}

.btn-warning {
  background: linear-gradient(135deg, #ffab00 0%, #e09900 100%);
  box-shadow: 0 2px 8px rgba(255, 171, 0, 0.3);
}

.btn-sm {
  padding: 0.375rem 0.875rem;
  font-size: 0.8125rem;
}

.btn-outline-primary {
  border: 1.5px solid #696cff;
  color: #696cff;
  background: transparent;
}

.btn-outline-primary:hover {
  background: #696cff;
  color: #ffffff;
}

.btn-outline-danger {
  border: 1.5px solid #ff3e1d;
  color: #ff3e1d;
  background: transparent;
}

.btn-outline-danger:hover {
  background: #ff3e1d;
  color: #ffffff;
}

.btn-secondary {
  background: #e0e0e0;
  color: #5a5f7d;
}

/* Modern Badges */
.badge {
  padding: 0.4rem 0.75rem;
  border-radius: 6px;
  font-weight: 500;
  font-size: 0.75rem;
  letter-spacing: 0.3px;
}

.badge.bg-success {
  background: linear-gradient(135deg, #71dd37 0%, #5cb82e 100%) !important;
  color: #ffffff;
}

.badge.bg-warning {
  background: linear-gradient(135deg, #ffab00 0%, #e09900 100%) !important;
  color: #ffffff;
}

/* Clean Table Design */
.table-modern {
  border-radius: 8px;
  overflow: hidden;
}

.table-modern thead th {
  background: #f8f9fa;
  font-weight: 700;
  text-transform: uppercase;
  font-size: 0.75rem;
  letter-spacing: 0.5px;
  color: #6c757d;
  border: none;
  padding: 16px;
}

.table-modern tbody tr {
  transition: all 0.2s;
  border-bottom: 1px solid #f1f1f1;
  cursor: pointer;
}

.table-modern tbody tr:hover {
  background-color: #f8f9ff !important;
  transform: scale(1.001);
  box-shadow: 0 2px 8px rgba(0,0,0,0.08);
}

.table-modern tbody td {
  padding: 1rem;
  vertical-align: middle;
  font-size: 0.875rem;
}

.btn-icon-detail {
  width: 32px;
  height: 32px;
  padding: 0;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  border-radius: 8px;
 
  color: white;
  border: none;
  transition: all 0.3s;
}

.btn-icon-detail:hover {
  transform: scale(1.1);
  box-shadow: 0 4px 12px rgba(105, 108, 255, 0.4);
}

/* Modern Form Controls */
.form-select, 
.form-control {
  border-radius: 8px;
  border: 1.5px solid #e0e0e0;
  padding: 0.625rem 1rem;
  transition: var(--transition);
  font-size: 0.875rem;
}

.form-select:focus, 
.form-control:focus {
  border-color: #696cff;
  box-shadow: 0 0 0 3px rgba(105, 108, 255, 0.1);
}

.form-control[readonly] {
  background-color: #f8f9fa;
}

/* Modern Modal Design */
.modal-content {
  border-radius: 16px;
  border: none;
  box-shadow: 0 8px 32px rgba(0,0,0,0.15);
}

.modal-header {
  border-radius: 16px 16px 0 0;
  padding: 1.5rem;
  border-bottom: none;
  background: linear-gradient(135deg, #696cff 0%, #5a5dc9 100%) !important;
}

.modal-title {
  font-weight: 600;
  font-size: 1.125rem;
  color: #ffffff;
}

.modal-body {
  padding: 2rem;
  max-height: 70vh;
  overflow-y: auto;
}

.modal-footer {
  padding: 1.5rem;
  border-top: 1px solid #f0f0f0;
  background: #fafafa;
}

/* Detail Section in Modal */
.detail-section {
  background: #ffffff;
  border: 1px solid #e8e8e8;
  border-radius: 12px;
  padding: 1.25rem;
  margin-bottom: 1.25rem;
}

.detail-section h6 {
  color: #696cff;
  font-weight: 700;
  margin-bottom: 1rem;
  font-size: 0.9rem;
  text-transform: uppercase;
  letter-spacing: 0.8px;
  padding-bottom: 0.75rem;
  border-bottom: 2px solid #696cff;
}

.detail-item {
  padding: 0.75rem 0;
  border-bottom: 1px solid #f0f0f0;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.detail-item:last-child {
  border-bottom: none;
}

.detail-label {
  color: #5a5f7d;
  font-weight: 600;
  font-size: 0.875rem;
}

.detail-value {
  color: #2c3e50;
  font-size: 0.875rem;
  text-align: right;
  word-break: break-word;
}

/* Loading Overlay */
.loading-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0,0,0,0.5);
  display: none;
  align-items: center;
  justify-content: center;
  z-index: 9999;
}

.btn-close-white {
  filter: brightness(0) invert(1);
}

/* Image Preview */
.table img {
  border-radius: 8px;
  box-shadow: 0 2px 6px rgba(0,0,0,0.1);
  transition: var(--transition);
  cursor: pointer;
}

.table img:hover {
  transform: scale(1.5);
  box-shadow: 0 4px 12px rgba(0,0,0,0.2);
  z-index: 999;
}

/* Responsive */
@media (max-width: 768px) {
  .modal-body {
    padding: 1.5rem;
  }
  
  .card-header-custom {
    padding: 1.25rem;
  }
}

/* Scrollbar */
.modal-body::-webkit-scrollbar {
  width: 6px;
}

.modal-body::-webkit-scrollbar-thumb {
  background: #696cff;
  border-radius: 10px;
}
</style>
@endsection

@section('vendor-script')
@vite([
  'resources/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js',
  'resources/assets/vendor/libs/select2/select2.js',
  'resources/assets/vendor/libs/sweetalert2/sweetalert2.js',
  'resources/assets/vendor/libs/flatpickr/flatpickr.js',
])
@endsection

@section('page-script')
<script>
document.addEventListener("DOMContentLoaded", function () {
    // Helper functions
    function showLoading() {
        $('.loading-overlay').css('display', 'flex');
    }
    
    function hideLoading() {
        $('.loading-overlay').fadeOut(300);
    }

    // Inisialisasi Select2
    $('#pelangganSelect').select2({
        placeholder: '-- Pilih Pelanggan --',
        allowClear: true,
        width: '100%',
        dropdownParent: $('#modalTambahTagihan')
    });

    const tglMulai = document.getElementById('tanggal_mulai');
    const formatDate = d => d.toISOString().split('T')[0];
    if (tglMulai) {
        tglMulai.value = formatDate(new Date());
    }

    function fillFields(selected) {
        if (!selected || !selected.val()) {
            $('#nama_lengkap, #alamat_jalan, #rt, #rw, #desa, #kecamatan, #kabupaten, #provinsi, #kode_pos, #no_whatsapp, #nomer_id, #paket, #harga, #masa_pembayaran, #kecepatan, #pelanggan_id, #paket_id, #tanggal_berakhir').val('');
            return;
        }

        $('#nama_lengkap').val(selected.data('nama'));
        $('#no_whatsapp').val(selected.data('nowhatsapp'));
        $('#nomer_id').val(selected.data('nomorid'));
        $('#paket').val(selected.data('paket'));
        $('#harga').val(selected.data('harga'));
        $('#masa_pembayaran').val(selected.data('masa'));
        $('#kecepatan').val(selected.data('kecepatan'));
        $('#pelanggan_id').val(selected.val());
        $('#paket_id').val(selected.data('paket_id'));
        $('#alamat_jalan').val(selected.data('alamat_jalan'));
        $('#rt').val(selected.data('rt'));
        $('#rw').val(selected.data('rw'));
        $('#desa').val(selected.data('desa'));
        $('#kecamatan').val(selected.data('kecamatan'));
        $('#kabupaten').val(selected.data('kabupaten'));
        $('#provinsi').val(selected.data('provinsi'));
        $('#kode_pos').val(selected.data('kode_pos'));

        const startDate = new Date($('#tanggal_mulai').val());
        const endDate = new Date(startDate);
        const masa = selected.data('masa') || selected.data('durasi');
        if (masa) endDate.setDate(startDate.getDate() + parseInt(masa));
        $('#tanggal_berakhir').val(formatDate(endDate));
    }

    $('#pelangganSelect').on('change', function () {
        fillFields($(this).find('option:selected'));
    });

    if (tglMulai) {
        tglMulai.addEventListener('change', function () {
            fillFields($('#pelangganSelect').find('option:selected'));
        });
    }

    $('#modalTambahTagihan').on('shown.bs.modal', function () {
        const allOptions = $('#pelangganSelect option').filter(function () {
            return $(this).val() !== '';
        });
        if (allOptions.length === 1) {
            $('#pelangganSelect').val(allOptions.val()).trigger('change');
        }
    });

    // Inisialisasi DataTables
    const dtUserTable = $('.datatables-users').DataTable({
        paging: true,
        pageLength: 10,
        lengthMenu: [5, 10, 25, 50, 100],
        searching: true,
        ordering: true,
        info: true,
        responsive: true,
        dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>rtip',
        columnDefs: [
            { orderable: false, targets: [0] }
        ],
        language: {
            paginate: {
                previous: '<i class="ri-arrow-left-s-line"></i>',
                next: '<i class="ri-arrow-right-s-line"></i>'
            },
            search: "_INPUT_",
            searchPlaceholder: "Cari tagihan...",
            lengthMenu: "Tampilkan _MENU_ data",
            info: "Menampilkan _START_ - _END_ dari _TOTAL_ tagihan",
            infoEmpty: "Tidak ada data",
            infoFiltered: "(difilter dari _MAX_ total data)",
            zeroRecords: "Tidak ada data yang sesuai"
        }
    });

    // Event click icon detail
    $(document).on('click', '.btn-icon-detail', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        const tr = $(this).closest('tr');
        
        // Get data from tr attributes
        const tagihanId = tr.data('tagihan-id');
        const status = tr.data('status');
        const nomorId = tr.data('nomor-id');
        const nama = tr.data('nama');
        const whatsapp = tr.data('whatsapp');
        const alamat = tr.data('alamat');
        const kecamatan = tr.data('kecamatan');
        const kabupaten = tr.data('kabupaten');
        const provinsi = tr.data('provinsi');
        const paket = tr.data('paket');
        const harga = tr.data('harga');
        const kecepatan = tr.data('kecepatan');
        const tanggalMulai = tr.data('tanggal-mulai');
        const jatuhTempo = tr.data('jatuh-tempo');
        const bukti = tr.data('bukti');
        const kwitansi = tr.data('kwitansi');
        const catatan = tr.data('catatan');

        // Build bukti section
        let buktiSection = '<span class="text-muted">-</span>';
        if (bukti) {
            buktiSection = `<a href="${bukti}" target="_blank" class="btn btn-sm btn-outline-primary">
                <i class="ri-file-text-line me-1"></i>Lihat Bukti
            </a>`;
        }

        // Build kwitansi section
        let kwitansiSection = '<span class="text-muted">-</span>';
        if (kwitansi) {
            kwitansiSection = `<a href="${kwitansi}" target="_blank" class="btn btn-sm btn-outline-primary">
                <i class="ri-file-pdf-line me-1"></i>Lihat PDF
            </a>`;
        }

        const html = `
            <div class="detail-section">
                <h6><i class="ri-user-3-line me-2"></i>Informasi Pelanggan</h6>
                <div class="detail-item">
                    <span class="detail-label">No. ID</span>
                    <span class="detail-value"><strong>${nomorId}</strong></span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Nama Lengkap</span>
                    <span class="detail-value">${nama}</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">No. WhatsApp</span>
                    <span class="detail-value">
                        <a href="https://wa.me/${whatsapp}" target="_blank" class="text-success">
                            <strong>${whatsapp}</strong>
                        </a>
                    </span>
                </div>
            </div>

            <div class="detail-section">
                <h6><i class="ri-map-pin-line me-2"></i>Alamat</h6>
                <div class="detail-item">
                    <span class="detail-label">Alamat Lengkap</span>
                    <span class="detail-value">${alamat}</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Kecamatan</span>
                    <span class="detail-value">${kecamatan}</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Kabupaten</span>
                    <span class="detail-value">${kabupaten}</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Provinsi</span>
                    <span class="detail-value">${provinsi}</span>
                </div>
            </div>

            <div class="detail-section">
                <h6><i class="ri-box-3-line me-2"></i>Informasi Paket</h6>
                <div class="detail-item">
                    <span class="detail-label">Paket</span>
                    <span class="detail-value"><span class="badge bg-label-info">${paket}</span></span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Harga</span>
                    <span class="detail-value"><strong>${harga}</strong></span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Kecepatan</span>
                    <span class="detail-value">${kecepatan}</span>
                </div>
            </div>

            <div class="detail-section">
                <h6><i class="ri-calendar-check-line me-2"></i>Informasi Tagihan</h6>
                <div class="detail-item">
                    <span class="detail-label">Status</span>
                    <span class="detail-value">
                        <span class="badge ${status === 'lunas' ? 'bg-success' : 'bg-warning'}">${status.toUpperCase()}</span>
                    </span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Tanggal Mulai</span>
                    <span class="detail-value">${tanggalMulai}</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Jatuh Tempo</span>
                    <span class="detail-value"><strong class="text-danger">${jatuhTempo}</strong></span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Bukti Pembayaran</span>
                    <span class="detail-value">${buktiSection}</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Kwitansi</span>
                    <span class="detail-value">${kwitansiSection}</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Catatan</span>
                    <span class="detail-value">${catatan}</span>
                </div>
            </div>
        `;
        
        $('#detailModal .modal-body').html(html);
        $('#detailModal').data('tagihan-id', tagihanId);
        $('#detailModal').data('tagihan-nama', nama);
        $('#detailModal').data('tagihan-status', status);
        
        // Update footer buttons
        const footerButtons = `
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                <i class="ri-close-line me-1"></i>Tutup
            </button>
            <button type="button" class="btn btn-outline-primary btn-edit-detail" data-bs-toggle="modal" data-bs-target="#modalEditTagihan-${tagihanId}">
                <i class="ri-edit-2-line me-1"></i>Edit
            </button>
            <button type="button" class="btn btn-outline-danger btn-delete-detail" data-id="${tagihanId}">
                <i class="ri-delete-bin-line me-1"></i>Hapus
            </button>
            ${status === 'lunas' 
                ? '<button class="btn btn-secondary" disabled><i class="ri-check-circle-line me-1"></i>Sudah Lunas</button>' 
                : `<button class="btn btn-success btn-konfirmasi-detail" data-id="${tagihanId}" data-nama="${nama}"><i class="ri-check-circle-line me-1"></i>Sudah Lunas</button>`
            }
        `;
        
        $('#detailModal .modal-footer').html(footerButtons);
        $('#detailModal').modal('show');
    });

    // Konfirmasi Bayar dari Modal
    $(document).on('click', '.btn-konfirmasi-detail', function(e) {
        e.preventDefault();
        const tagihanId = $(this).data('id');
        const nama = $(this).data('nama');

        Swal.fire({
            title: 'Konfirmasi Pembayaran',
            html: `Apakah <strong>${nama}</strong> sudah melakukan pembayaran?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Ya, sudah lunas!',
            cancelButtonText: 'Batal',
            confirmButtonColor: '#71dd37',
            cancelButtonColor: '#8898aa',
            customClass: {
                confirmButton: 'btn btn-success me-2',
                cancelButton: 'btn btn-secondary'
            },
            buttonsStyling: false
        }).then((result) => {
            if (result.isConfirmed) {
                showLoading();
                
                $.ajax({
                    url: `/dashboard/admin/tagihan/${tagihanId}/bayar`,
                    method: 'POST',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                    },
                    success: function(response) {
                        hideLoading();
                        if(response.success) {
                            $('#detailModal').modal('hide');
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                html: `
                                    <p>Tagihan <strong>${nama}</strong> telah ditandai lunas.</p>
                                    <a href="${response.pdfUrl}" target="_blank" class="btn btn-primary mt-3">
                                        <i class="ri-printer-line me-1"></i> Cetak Kwitansi
                                    </a>
                                `,
                                showConfirmButton: false,
                                allowOutsideClick: true,
                                didClose: () => location.reload()
                            });
                        } else {
                            Swal.fire('Gagal!', response.message || 'Terjadi kesalahan.', 'error');
                        }
                    },
                    error: function() {
                        hideLoading();
                        Swal.fire('Gagal!', 'Terjadi kesalahan server.', 'error');
                    }
                });
            }
        });
    });

    // Delete dari Modal
    $(document).on('click', '.btn-delete-detail', function(e) {
        e.preventDefault();
        const tagihanId = $(this).data('id');

        Swal.fire({
            title: 'Konfirmasi Penghapusan',
            text: 'Yakin ingin menghapus tagihan ini? Data tidak dapat dikembalikan!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal',
            confirmButtonColor: '#ff3e1d',
            cancelButtonColor: '#8898aa',
            customClass: {
                confirmButton: 'btn btn-danger me-2',
                cancelButton: 'btn btn-secondary'
            },
            buttonsStyling: false
        }).then((result) => {
            if (result.isConfirmed) {
                const form = $('<form>', {
                    'method': 'POST',
                    'action': `/dashboard/admin/tagihan/${tagihanId}`
                });
                
                form.append($('<input>', {
                    'type': 'hidden',
                    'name': '_token',
                    'value': $('meta[name="csrf-token"]').attr('content')
                }));
                
                form.append($('<input>', {
                    'type': 'hidden',
                    'name': '_method',
                    'value': 'DELETE'
                }));
                
                $('body').append(form);
                form.submit();
            }
        });
    });

    // Edit Button
    $(document).on('click', '.btn-edit-detail', function(e) {
        $('#detailModal').modal('hide');
    });

    // Flatpickr
    flatpickr("#tanggal_mulai", {
        dateFormat: "Y-m-d",
        defaultDate: new Date(),
        onChange: function(selectedDates) {
            const tanggalMulai = selectedDates[0];
            const masaPembayaran = parseInt($('#masa_pembayaran').val()) || 0;
            if (tanggalMulai && masaPembayaran) {
                const tanggalBerakhir = new Date(tanggalMulai);
                tanggalBerakhir.setDate(tanggalMulai.getDate() + masaPembayaran);
                flatpickr("#tanggal_berakhir").setDate(tanggalBerakhir);
            }
        }
    });

    flatpickr("#tanggal_berakhir", {
        dateFormat: "Y-m-d",
    });
});
</script>
@endsection

@section('content')
<!-- Loading Overlay -->
<div class="loading-overlay">
    <div class="spinner-border text-light" style="width: 3rem; height: 3rem;" role="status">
        <span class="visually-hidden">Loading...</span>
    </div>
</div>

<div class="container-fluid px-4 py-4">
  <!-- Main Table Card -->
  <div class="card border-0 shadow-sm">
    <div class="card-header-custom">
        <div class="d-flex flex-wrap justify-content-between align-items-center">
            <div>
                <h4 class="mb-1 fw-bold">
                    <i class="ri-bill-line me-2"></i>Daftar Tagihan Lunas
                </h4>
                <p class="mb-0 opacity-75 small">Kelola seluruh tagihan pelanggan yang sudah lunas</p>
            </div>
           
        </div>
    </div>
    
    <div class="card-body p-0">
        <div class="card-datatable table-responsive p-3">
            <table class="datatables-users table table-modern table-hover">
                <thead>
                    <tr>
                        <th> Detail</th>
                        <th> No. ID</th>
                        <th>  Nama</th>
                        <th> WhatsApp</th>
                        <th>  Status</th>
                        <th>  Paket</th>
                        <th> Harga</th>
                        <th> Kecepatan</th>
                         
                    </tr>
                </thead>
                <tbody>


@foreach($tagihans as $item)
<tr 
    data-tagihan-id="{{ $item['id'] }}"
    data-status="{{ strtolower($item['status_pembayaran'] ?? '') }}"
    data-nomor-id="{{ $item['nomer_id'] }}"
    data-nama="{{ $item['nama_lengkap'] }}"
    data-whatsapp="{{ $item['no_whatsapp'] }}"
    data-alamat="{{ collect([$item['alamat_jalan'], ($item['rt'] || $item['rw']) ? 'RT '.$item['rt'].' / RW '.$item['rw'] : null, $item['desa'] ? 'Desa '.$item['desa'] : null])->filter()->implode(', ') }}"
    data-kecamatan="{{ $item['kecamatan'] ?? '-' }}"
    data-kabupaten="{{ $item['kabupaten'] ?? '-' }}"
    data-provinsi="{{ $item['provinsi'] ?? '-' }}"
    data-paket="{{ $item['paket']['nama_paket'] ?? '-' }}"
    data-harga="Rp {{ number_format($item['paket']['harga'] ?? 0, 0, ',', '.') }}"
    data-kecepatan="{{ $item['paket']['kecepatan'] ?? '-' }} Mbps"
    data-tanggal-mulai="{{ $item['tanggal_mulai'] ? \Carbon\Carbon::parse($item['tanggal_mulai'])->format('d M Y') : '-' }}"
    data-jatuh-tempo="{{ $item['tanggal_berakhir'] ? \Carbon\Carbon::parse($item['tanggal_berakhir'])->format('d M Y') : '-' }}"
    data-bukti="{{ asset('storage/' . $item['bukti_pembayaran']) }}"
    data-kwitansi="{{ !empty($item['kwitansi']) ? asset('storage/'. $item['kwitansi']) : '' }}"
    data-catatan="{{ $item['catatan'] ?? '-' }}"
>
                   

            <td>
              <button class="btn btn-sm btn-icon btn-outline-primary btn-icon-detail" title="Lihat Detail">
                <i class="ri-eye-line"></i>
              </button>
            </td>

                        <td><span class="badge bg-label-dark">{{ $item['nomer_id'] }}</span></td>
                        <td><strong>{{ $item['nama_lengkap'] }}</strong></td>
                        <td>{{ $item['no_whatsapp'] }}</td>
                        <td>
                            @php
                                $status = strtolower($item['status_pembayaran'] ?? '');
                                $badgeClass = match($status) {
                                    'lunas' => 'badge bg-success',
                                    'belum bayar' => 'badge bg-warning',
                                    default => 'badge bg-secondary',
                                };
                            @endphp
                            <span class="{{ $badgeClass }}">{{ ucfirst($status ?: '-') }}</span>
                        </td>
                        <td><span class="badge bg-label-info">{{ $item['paket']['nama_paket'] ?? '-' }}</span></td>
                        <td><strong>Rp {{ number_format($item['paket']['harga'] ?? 0, 0, ',', '.') }}</strong></td>
                        <td>{{ $item['paket']['kecepatan'] ?? '-' }} Mbps</td>
                         
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
  </div>
</div>

<!-- Modal Tambah Tagihan -->
<div class="modal fade" id="modalTambahTagihan" tabindex="-1">
  <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content">
      <form action="{{ route('tagihan.store') }}" method="POST">
        @csrf
        
        <div class="modal-header">
          <h5 class="modal-title text-white">
            <i class="ri-add-circle-line me-2"></i>Tambah Tagihan Manual
          </h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>

        <div class="modal-body">
          <div class="row g-3">
            <div class="col-12">
              <label class="form-label fw-semibold">Pilih Pelanggan <span class="text-danger">*</span></label>
              <select id="pelangganSelect" class="form-select" required>
                <option value="">-- Pilih Pelanggan --</option>
                @foreach($pelanggan as $p)
                  <option value="{{ $p->id }}"
                    data-paket_id="{{ optional($p->paket)->id }}"
                    data-nama="{{ $p->nama_lengkap }}"
                    data-alamat_jalan="{{ $p->alamat_jalan }}"
                    data-rt="{{ $p->rt }}"
                    data-rw="{{ $p->rw }}"
                    data-desa="{{ $p->desa }}"
                    data-kecamatan="{{ $p->kecamatan }}"
                    data-kabupaten="{{ $p->kabupaten }}"
                    data-provinsi="{{ $p->provinsi }}"
                    data-kode_pos="{{ $p->kode_pos }}"
                    data-nowhatsapp="{{ $p->no_whatsapp }}"
                    data-nomorid="{{ $p->nomer_id }}"
                    data-paket="{{ optional($p->paket)->nama_paket }}"
                    data-harga="{{ optional($p->paket)->harga }}"
                    data-masa="{{ optional($p->paket)->masa_pembayaran }}"
                    data-kecepatan="{{ optional($p->paket)->kecepatan }}"
                    data-durasi="{{ optional($p->paket)->durasi }}">
                    {{ $p->nomer_id }} - {{ $p->nama_lengkap }}
                  </option>
                @endforeach
              </select>
            </div>

            <input type="hidden" name="pelanggan_id" id="pelanggan_id">
            <input type="hidden" name="paket_id" id="paket_id">

            <div class="col-12 mt-4">
              <h6 class="text-primary fw-bold"><i class="ri-user-3-line me-2"></i>Informasi Pelanggan</h6>
            </div>

            <div class="col-md-6">
              <label class="form-label">Nama Lengkap</label>
              <input type="text" id="nama_lengkap" class="form-control bg-light" readonly>
            </div>

            <div class="col-md-6">
              <label class="form-label">Nomor ID</label>
              <input type="text" id="nomer_id" class="form-control bg-light" readonly>
            </div>

            <div class="col-md-6">
              <label class="form-label">Nomor WhatsApp</label>
              <input type="text" id="no_whatsapp" class="form-control bg-light" readonly>
            </div>

            <div class="col-md-6">
              <label class="form-label">Kode Pos</label>
              <input type="text" id="kode_pos" class="form-control bg-light" readonly>
            </div>

            <div class="col-12 mt-3">
              <h6 class="text-primary fw-bold"><i class="ri-map-pin-line me-2"></i>Alamat</h6>
            </div>

            <div class="col-12">
              <label class="form-label">Alamat Jalan</label>
              <input type="text" id="alamat_jalan" class="form-control bg-light" readonly>
            </div>

            <div class="col-md-3">
              <label class="form-label">RT</label>
              <input type="text" id="rt" class="form-control bg-light" readonly>
            </div>

            <div class="col-md-3">
              <label class="form-label">RW</label>
              <input type="text" id="rw" class="form-control bg-light" readonly>
            </div>

            <div class="col-md-6">
              <label class="form-label">Desa/Kelurahan</label>
              <input type="text" id="desa" class="form-control bg-light" readonly>
            </div>

            <div class="col-md-6">
              <label class="form-label">Kecamatan</label>
              <input type="text" id="kecamatan" class="form-control bg-light" readonly>
            </div>

            <div class="col-md-6">
              <label class="form-label">Kabupaten/Kota</label>
              <input type="text" id="kabupaten" class="form-control bg-light" readonly>
            </div>

            <div class="col-md-6">
              <label class="form-label">Provinsi</label>
              <input type="text" id="provinsi" class="form-control bg-light" readonly>
            </div>

            <div class="col-12 mt-3">
              <h6 class="text-primary fw-bold"><i class="ri-box-3-line me-2"></i>Paket</h6>
            </div>

            <div class="col-md-6">
              <label class="form-label">Nama Paket</label>
              <input type="text" id="paket" class="form-control bg-light" readonly>
            </div>

            <div class="col-md-6">
              <label class="form-label">Harga</label>
              <input type="text" id="harga" name="harga" class="form-control bg-light" readonly>
            </div>

            <div class="col-md-6">
              <label class="form-label">Masa Pembayaran</label>
              <input type="text" id="masa_pembayaran" class="form-control bg-light" readonly>
            </div>

            <div class="col-md-6">
              <label class="form-label">Kecepatan</label>
              <input type="text" id="kecepatan" class="form-control bg-light" readonly>
            </div>

            <div class="col-12 mt-3">
              <h6 class="text-primary fw-bold"><i class="ri-calendar-check-line me-2"></i>Detail Tagihan</h6>
            </div>

            <div class="col-md-6">
              <label class="form-label fw-semibold">Tanggal Mulai <span class="text-danger">*</span></label>
              <input type="date" id="tanggal_mulai" name="tanggal_mulai" class="form-control" required>
            </div>

            <div class="col-md-6">
              <label class="form-label fw-semibold">Tanggal Jatuh Tempo <span class="text-danger">*</span></label>
              <input type="date" id="tanggal_berakhir" name="tanggal_berakhir" class="form-control" required>
            </div>

            <div class="col-12">
              <label class="form-label">Catatan</label>
              <textarea class="form-control" id="catatan" name="catatan" rows="2" placeholder="Catatan tambahan..."></textarea>
            </div>
          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
            <i class="ri-close-line me-1"></i>Batal
          </button>
          <button type="submit" class="btn btn-primary">
            <i class="ri-save-line me-1"></i>Simpan
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Detail Modal -->
<div class="modal fade" id="detailModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title text-white">
          <i class="ri-information-line me-2"></i>Detail Tagihan Pelanggan
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body"></div>
      <div class="modal-footer"></div>
    </div>
  </div>
</div>

<!-- Modal Edit Tagihan -->
@foreach ($tagihans as $tagihan)
<div class="modal fade" id="modalEditTagihan-{{ $tagihan['id'] }}" tabindex="-1">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <form action="{{ route('tagihan.update', $tagihan['id']) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="modal-header bg-warning">
          <h5 class="modal-title text-white fw-bold">
            <i class="ri-edit-2-line me-2"></i>Edit Tagihan
          </h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>

        <div class="modal-body">
          <div class="row g-3">
            <div class="col-12">
              <label class="form-label fw-semibold">Nama Pelanggan</label>
              <input type="text" class="form-control bg-light" value="{{ $tagihan['nama_lengkap'] ?? '-' }}" readonly>
            </div>
            <input type="hidden" name="pelanggan_id" value="{{ $tagihan['pelanggan_id'] ?? '' }}">
            <input type="hidden" name="paket_id" value="{{ $tagihan['paket']['id'] ?? '' }}">

            <div class="col-md-6">
              <label class="form-label fw-semibold">Tanggal Mulai</label>
              <input type="date" name="tanggal_mulai" class="form-control" value="{{ $tagihan['tanggal_mulai'] }}" required>
            </div>

            <div class="col-md-6">
              <label class="form-label fw-semibold">Tanggal Jatuh Tempo</label>
              <input type="date" name="tanggal_berakhir" class="form-control" value="{{ $tagihan['tanggal_berakhir'] }}" required>
            </div>

            <div class="col-12">
              <label class="form-label">Catatan</label>
              <textarea class="form-control" name="catatan" rows="2">{{ $tagihan['catatan'] ?? '' }}</textarea>
            </div>

            <div class="col-12">
              <label class="form-label">Bukti Pembayaran</label>
              <input type="file" name="bukti_pembayaran" class="form-control" accept="image/*,.pdf">
              <small class="text-muted">Format: JPG, PNG, PDF (Max: 2MB)</small>
            </div>
          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-warning">
            <i class="ri-save-line me-1"></i>Simpan
          </button>
        </div>
      </form>
    </div>
  </div>
</div>
@endforeach

@endsection
