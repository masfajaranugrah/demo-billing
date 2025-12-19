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
/* MODERN CLEAN STYLES */
/* ========================================= */
:root {
  --card-shadow: 0 2px 8px rgba(0,0,0,0.08);
  --card-hover-shadow: 0 4px 16px rgba(0,0,0,0.12);
  --border-radius: 12px;
  --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

/* Card Design */
.card {
  border: none;
  border-radius: var(--border-radius);
  box-shadow: var(--card-shadow);
  transition: var(--transition);
  overflow: hidden;
}

.card:hover {
  box-shadow: var(--card-hover-shadow);
  transform: translateY(-2px);
}

/* Dashboard Cards with Border Accent */
.card-border-shadow-primary::before,
.card-border-shadow-success::before,
.card-border-shadow-warning::before,
.card-border-shadow-info::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  width: 4px;
  height: 100%;
}

.card-border-shadow-primary::before {
  background: linear-gradient(180deg, #696cff 0%, #5a5dc9 100%);
}

.card-border-shadow-success::before {
  background: linear-gradient(180deg, #71dd37 0%, #5cb82e 100%);
}

.card-border-shadow-warning::before {
  background: linear-gradient(180deg, #ffab00 0%, #e09900 100%);
}

.card-border-shadow-info::before {
  background: linear-gradient(180deg, #03c3ec 0%, #02a8cc 100%);
}

/* Avatar */
.avatar-initial {
  border-radius: 12px;
  transition: var(--transition);
}

.card:hover .avatar-initial {
  transform: scale(1.05);
}

/* Buttons */
.btn {
  border-radius: 8px;
  padding: 0.5rem 1.25rem;
  font-weight: 500;
  transition: var(--transition);
  border: none;
}

.btn-primary {
  background: linear-gradient(135deg, #696cff 0%, #5a5dc9 100%);
  box-shadow: 0 2px 8px rgba(105, 108, 255, 0.3);
}

.btn-primary:hover {
  background: linear-gradient(135deg, #5a5dc9 0%, #4a4db9 100%);
  box-shadow: 0 4px 12px rgba(105, 108, 255, 0.4);
  transform: translateY(-1px);
}

.btn-warning {
  background: linear-gradient(135deg, #ffab00 0%, #e09900 100%);
  box-shadow: 0 2px 8px rgba(255, 171, 0, 0.3);
}

.btn-warning:hover {
  background: linear-gradient(135deg, #e09900 0%, #c78800 100%);
  box-shadow: 0 4px 12px rgba(255, 171, 0, 0.4);
  transform: translateY(-1px);
}

.btn-success {
  background: linear-gradient(135deg, #71dd37 0%, #5cb82e 100%);
  box-shadow: 0 2px 8px rgba(113, 221, 55, 0.3);
}

.btn-sm {
  padding: 0.375rem 0.875rem;
  font-size: 0.875rem;
}

/* Badges */
.badge {
  padding: 0.4rem 0.75rem;
  border-radius: 6px;
  font-weight: 500;
  font-size: 0.75rem;
  letter-spacing: 0.3px;
}

/* Form Controls */
.form-select, .form-control {
  border-radius: 8px;
  border: 1px solid #e0e0e0;
  padding: 0.625rem 1rem;
  transition: var(--transition);
}

.form-select:focus, .form-control:focus {
  border-color: #696cff;
  box-shadow: 0 0 0 3px rgba(105, 108, 255, 0.1);
}

/* Modal */
.modal-content {
  border-radius: 16px;
  border: none;
  box-shadow: 0 8px 32px rgba(0,0,0,0.15);
}

.modal-header {
  border-radius: 16px 16px 0 0;
  padding: 1.5rem;
  border-bottom: none;
}

.modal-header.bg-primary {
  background: linear-gradient(135deg, #696cff 0%, #5a5dc9 100%) !important;
}

.modal-header.bg-warning {
  background: linear-gradient(135deg, #ffab00 0%, #e09900 100%) !important;
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

.btn-close-white {
  filter: brightness(0) invert(1);
}

/* Table */
.table {
  border-collapse: separate;
  border-spacing: 0;
}

.table thead th {
  background: #f8f9fa;
  border: none;
  padding: 1rem;
  font-weight: 600;
  color: #5a5f7d;
  font-size: 0.875rem;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  white-space: nowrap;
}

.table tbody tr {
  transition: var(--transition);
}

.table tbody tr:hover {
  background: #f8f9fa;
  transform: scale(1.001);
}

.table tbody td {
  padding: 1rem;
  border-bottom: 1px solid #f0f0f0;
  vertical-align: middle;
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

/* ========================================= */
/* DETAIL MODAL STYLES */
/* ========================================= */
.customer-header-info {
  text-align: center;
  padding: 1.5rem;
  background: linear-gradient(135deg, #f8f9ff 0%, #ffffff 100%);
  border-radius: 12px;
  margin-bottom: 1.5rem;
  border: 1px solid #e8e8e8;
}

.customer-avatar {
  width: 100px;
  height: 100px;
  border-radius: 50%;
  background: linear-gradient(135deg, #696cff 0%, #5a5dc9 100%);
  display: inline-flex;
  align-items: center;
  justify-content: center;
  color: white;
  font-weight: 700;
  font-size: 2.5rem;
  margin-bottom: 1rem;
  box-shadow: 0 4px 16px rgba(105, 108, 255, 0.4);
  border: 4px solid white;
}

.customer-name {
  font-size: 1.5rem;
  font-weight: 700;
  color: #2c3e50;
  margin-bottom: 0.5rem;
}

.customer-status {
  display: inline-block;
  padding: 0.5rem 1.5rem;
  border-radius: 20px;
  font-weight: 600;
  font-size: 0.875rem;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
}

.detail-section {
  background: #ffffff;
  border: 1px solid #e8e8e8;
  border-radius: 12px;
  padding: 1.25rem;
  margin-bottom: 1.25rem;
  transition: all 0.2s;
}

.detail-section:hover {
  box-shadow: 0 2px 8px rgba(0,0,0,0.08);
  border-color: #696cff;
}

.detail-section h6 {
  color: #696cff;
  font-weight: 700;
  margin-bottom: 1.25rem;
  font-size: 0.9rem;
  text-transform: uppercase;
  letter-spacing: 0.8px;
  display: flex;
  align-items: center;
  padding-bottom: 0.75rem;
  border-bottom: 2px solid #696cff;
}

.detail-section h6 i {
  margin-right: 0.5rem;
  font-size: 1.1rem;
}

.detail-item {
  display: flex;
  padding: 0.875rem 0;
  border-bottom: 1px solid #f0f0f0;
  align-items: flex-start;
}

.detail-item:last-child {
  border-bottom: none;
  padding-bottom: 0;
}

.detail-label {
  color: #5a5f7d;
  font-weight: 600;
  min-width: 150px;
  font-size: 0.875rem;
  display: flex;
  align-items: center;
}

.detail-label i {
  margin-right: 0.5rem;
  color: #a8afc7;
  font-size: 1rem;
}

.detail-value {
  color: #2c3e50;
  font-size: 0.875rem;
  flex: 1;
  word-break: break-word;
}

/* Card Header */
.card-header {
  background: transparent;
  padding: 1.5rem;
  border-bottom: 1px solid #f0f0f0;
}

/* Input Groups */
.input-group-text {
  border-radius: 8px 0 0 8px;
  background: #f8f9fa;
  border: 1px solid #e0e0e0;
  color: #5a5f7d;
  font-weight: 500;
}

/* Animations */
@keyframes fadeIn {
  from { opacity: 0; transform: translateY(10px); }
  to { opacity: 1; transform: translateY(0); }
}

.card {
  animation: fadeIn 0.3s ease-out;
}

/* Responsive */
@media (max-width: 768px) {
  .modal-body {
    padding: 1.5rem;
  }
  .card-body {
    padding: 1.25rem;
  }
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
    // ========================================
    // HELPER FUNCTIONS
    // ========================================
    function showLoading() {
        $('.loading-overlay').css('display', 'flex');
    }
    
    function hideLoading() {
        $('.loading-overlay').fadeOut(300);
    }

    const formatDate = d => d.toISOString().split('T')[0];

    // ========================================
    // FLATPICKR INITIALIZATION
    // ========================================
    $(document).on('shown.bs.modal', '[id^="modalEditTagihan-"]', function () {
        flatpickr($(this).find('.flatpickr-edit-start'), {
            dateFormat: "Y-m-d",
            allowInput: true
        });
        flatpickr($(this).find('.flatpickr-edit-end'), {
            dateFormat: "Y-m-d",
            allowInput: true
        });
    });

    flatpickr("#tanggal_mulai", { 
        dateFormat: "Y-m-d", 
        defaultDate: new Date(), 
        allowInput: true 
    });
    
    flatpickr("#tanggal_berakhir", { 
        dateFormat: "Y-m-d", 
        allowInput: false 
    });

    // ========================================
    // SELECT2 PELANGGAN
    // ========================================
    $('#pelangganSelect').select2({
        placeholder: '-- Pilih Pelanggan --',
        allowClear: true,
        width: '100%',
        dropdownParent: $('#modalTambahTagihan')
    });

    const tglMulai = document.getElementById('tanggal_mulai');
    if (tglMulai) {
        tglMulai.value = formatDate(new Date());
    }

    function fillFields(selected) {
        if (!selected || !selected.val()) {
            $('#nama_lengkap, #alamat_jalan, #rt, #rw, #desa, #kecamatan, #kabupaten, #provinsi, #kode_pos, #no_whatsapp, #nomer_id, #paket, #harga, #masa_pembayaran, #kecepatan, #pelanggan_id, #paket_id, #tanggal_berakhir').val('');
            return;
        }

        const fields = [
            'nama','alamat_jalan','rt','rw','desa','kecamatan','kabupaten','provinsi',
            'kode_pos','nowhatsapp','nomorid','paket','harga','masa','kecepatan','paket_id'
        ];

        fields.forEach(f => {
            const el = $('#' + (f === 'masa' ? 'masa_pembayaran' : f === 'nama' ? 'nama_lengkap' : f === 'nowhatsapp' ? 'no_whatsapp' : f === 'nomorid' ? 'nomer_id' : f));
            el.val(selected.data(f));
        });

        $('#pelanggan_id').val(selected.val());

        const startDate = new Date($('#tanggal_mulai').val());
        const masa = selected.data('masa') || selected.data('durasi');
        if (masa) {
            const endDate = new Date(startDate);
            endDate.setDate(startDate.getDate() + parseInt(masa));
            $('#tanggal_berakhir').val(formatDate(endDate));
        }
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
        const list = $('#pelangganSelect option').filter((_, el) => el.value);
        if (list.length === 1) {
            $('#pelangganSelect').val(list.val()).trigger('change');
        }
    });

    // ========================================
    // DATATABLES
    // ========================================
    const dtUserTable = $('.datatables-users').DataTable({
        paging: true,
        pageLength: 10,
        lengthMenu: [5, 10, 25, 50, 100],
        ordering: true,
        searching: true,
        info: true,
        responsive: false,
        scrollX: false,
        autoWidth: false,
        dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>rtip',
        columnDefs: [
            { orderable: false, targets: [0, -1], width: '80px' },
            { width: '100px', targets: 1 },
            { width: 'auto', targets: [2, 3, 4, 5, 6] }
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

    // ========================================
    // DETAIL MODAL
    // ========================================
    $(document).on('click', '.btn-detail', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        const tr = $(this).closest('tr');
        const row = dtUserTable.row(tr).data();
        if (!row) return;

        // Get all data from row
        const nomorId = row[1] || '-';
        const namaLengkap = row[2] || '-';
        const noWhatsapp = row[3] || '-';
        const statusPembayaran = row[4] || '-';
        const paket = row[5] || '-';
        const harga = row[6] || '-';
        
        // Get hidden data from tr attributes
        const $tr = $(tr);
        const alamatLengkap = $tr.data('alamat') || '-';
        const kecamatan = $tr.data('kecamatan') || '-';
        const kabupaten = $tr.data('kabupaten') || '-';
        const provinsi = $tr.data('provinsi') || '-';
        const kecepatan = $tr.data('kecepatan') || '-';
        const tanggalMulai = $tr.data('tanggal-mulai') || '-';
        const jatuhTempo = $tr.data('jatuh-tempo') || '-';
        const catatan = $tr.data('catatan') || '-';
        const buktiPembayaran = $tr.data('bukti') || '';
        
        const initial = namaLengkap ? namaLengkap.charAt(0).toUpperCase() : '?';
        const statusLower = statusPembayaran.toLowerCase();
        const statusClass = statusLower.includes('lunas') ? 'bg-success' : 'bg-danger';
        
        // Build bukti pembayaran section
        let buktiSection = '-';
        if (buktiPembayaran) {
            buktiSection = `<a href="${buktiPembayaran}" target="_blank" class="btn btn-sm btn-outline-primary">
                <i class="ri-file-text-line me-1"></i>Lihat Bukti
            </a>`;
        }
        
        const html = `
            <div class="customer-header-info">
                <div class="customer-avatar mx-auto">${initial}</div>
                <div class="customer-name">${namaLengkap}</div>
                <div class="customer-status ${statusClass}">
                    <i class="ri-checkbox-circle-line me-2"></i>${statusPembayaran}
                </div>
            </div>

            <div class="detail-section">
                <h6><i class="ri-user-3-line"></i>Informasi Pelanggan</h6>
                <div class="detail-item">
                    <span class="detail-label"><i class="ri-id-card-line"></i>No. ID</span>
                    <span class="detail-value"><strong>${nomorId}</strong></span>
                </div>
                <div class="detail-item">
                    <span class="detail-label"><i class="ri-user-line"></i>Nama Lengkap</span>
                    <span class="detail-value">${namaLengkap}</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label"><i class="ri-whatsapp-line"></i>No. WhatsApp</span>
                    <span class="detail-value">
                        <a href="https://wa.me/${noWhatsapp}" target="_blank" class="text-success text-decoration-none">
                            <strong>${noWhatsapp}</strong>
                        </a>
                    </span>
                </div>
            </div>

            <div class="detail-section">
                <h6><i class="ri-map-pin-line"></i>Alamat Lengkap</h6>
                <div class="detail-item">
                    <span class="detail-label"><i class="ri-map-2-line"></i>Alamat</span>
                    <span class="detail-value">${alamatLengkap}</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label"><i class="ri-building-line"></i>Kecamatan</span>
                    <span class="detail-value">${kecamatan}</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label"><i class="ri-community-line"></i>Kabupaten</span>
                    <span class="detail-value">${kabupaten}</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label"><i class="ri-map-pin-2-line"></i>Provinsi</span>
                    <span class="detail-value">${provinsi}</span>
                </div>
            </div>

            <div class="detail-section">
                <h6><i class="ri-box-3-line"></i>Informasi Paket</h6>
                <div class="detail-item">
                    <span class="detail-label"><i class="ri-box-line"></i>Nama Paket</span>
                    <span class="detail-value"><span class="badge bg-label-info">${paket}</span></span>
                </div>
                <div class="detail-item">
                    <span class="detail-label"><i class="ri-money-dollar-circle-line"></i>Harga</span>
                    <span class="detail-value"><strong>${harga}</strong></span>
                </div>
                <div class="detail-item">
                    <span class="detail-label"><i class="ri-speed-line"></i>Kecepatan</span>
                    <span class="detail-value">${kecepatan}</span>
                </div>
            </div>

            <div class="detail-section">
                <h6><i class="ri-calendar-check-line"></i>Informasi Tagihan</h6>
                <div class="detail-item">
                    <span class="detail-label"><i class="ri-calendar-line"></i>Tanggal Mulai</span>
                    <span class="detail-value">${tanggalMulai}</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label"><i class="ri-calendar-event-line"></i>Jatuh Tempo</span>
                    <span class="detail-value"><strong class="text-danger">${jatuhTempo}</strong></span>
                </div>
            
                <div class="detail-item">
                    <span class="detail-label"><i class="ri-file-text-line"></i>Catatan</span>
                    <span class="detail-value">${catatan}</span>
                </div>
            </div>
        `;
        
        $('#detailModal .modal-body').html(html);
        $('#detailModal').modal('show');
    });

    // ========================================
    // FILTER DROPDOWN
    // ========================================
    $('#statusPembayaranFilter').on('change', function() {
        dtUserTable.column(4).search($(this).val()).draw();
    });
    $('#kabupatenFilter').on('change', function() {
        dtUserTable.search($(this).val()).draw();
    });
    $('#kecamatanFilter').on('change', function() {
        dtUserTable.search($(this).val()).draw();
    });

  
// ========================================
// SWEETALERT DELETE - ONLY 2 BUTTONS
// ========================================
$(document).on('submit', '.delete-form', function(e) {
    e.preventDefault();
    e.stopPropagation();
    const form = this;

    Swal.fire({
        title: 'Konfirmasi Penghapusan',
        html: '<p class="mb-0">Yakin ingin menghapus tagihan ini?<br><strong class="text-danger">Data tidak dapat dikembalikan!</strong></p>',
        icon: 'warning',
        showCancelButton: true,
        showConfirmButton: true,
        showDenyButton: false,  // ? Explicitly disable deny button (No button)
        confirmButtonColor: '#f5365c',
        cancelButtonColor: '#8898aa',
        confirmButtonText: '<i class="ri-delete-bin-line me-1"></i>Hapus',
        cancelButtonText: 'Batal',
        allowOutsideClick: false,
        allowEscapeKey: false,
        reverseButtons: true,
        customClass: {
            confirmButton: 'btn btn-danger me-2',
            cancelButton: 'btn btn-secondary'
        },
        buttonsStyling: false
    }).then((result) => {
        if (result.isConfirmed) {
            showLoading();
            setTimeout(() => form.submit(), 500);
        }
    });
});


    // ========================================
    // KONFIRMASI PEMBAYARAN
    // ========================================
    $(document).on('click', '.btn-konfirmasi', function(e) {
        e.preventDefault();
        e.stopPropagation();
        const id = $(this).data('id');
        const nama = $(this).data('nama');

        Swal.fire({
            title: 'Konfirmasi Pembayaran',
            html: `<p class="mb-0">Apakah <strong>${nama}</strong> sudah membayar?</p>`,
            icon: 'question',
            showCancelButton: true,
            showConfirmButton: true,
            confirmButtonColor: '#2dce89',
            cancelButtonColor: '#8898aa',
            confirmButtonText: '<i class="ri-check-line me-1"></i>Ya, Lunas',
            cancelButtonText: 'Batal',
            buttonsStyling: true,
            allowOutsideClick: false,
            allowEscapeKey: false,
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                showLoading();

                $.post(`/dashboard/admin/tagihan/${id}/bayar`, {
                    _token: $('meta[name="csrf-token"]').attr('content')
                })
                .done(resp => {
                    hideLoading();
                    if (resp.success) {
                        Swal.fire({ 
                            icon: 'success', 
                            title: 'Berhasil!',
                            text: 'Pembayaran berhasil dikonfirmasi',
                            timer: 1500,
                            showConfirmButton: false,
                            allowOutsideClick: false
                        }).then(() => location.reload());
                    }
                })
                .fail(() => {
                    hideLoading();
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: 'Terjadi kesalahan server',
                        confirmButtonText: 'OK'
                    });
                });
            }
        });
    });

    // ========================================
    // MASS TAGIHAN
    // ========================================
    $('#modalMassTagihan').on('shown.bs.modal', function () {
        flatpickr(".flatpickr-select-start-all", { 
            dateFormat: "Y-m-d", 
            defaultDate: new Date(), 
            minDate: "today", 
            allowInput: true 
        });
        flatpickr(".flatpickr-select-start-end", { 
            dateFormat: "Y-m-d", 
            defaultDate: new Date().fp_incr(7), 
            minDate: "today", 
            allowInput: true 
        });
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
  <!-- ========================================= -->
  <!-- DASHBOARD CARDS -->
  <!-- ========================================= -->
  <div class="row g-4 mb-4">
    <!-- Total Customer -->
    <div class="col-xl-3 col-md-6">
      <div class="card card-border-shadow-primary h-100">
        <div class="card-body">
          <div class="d-flex align-items-start justify-content-between mb-3">
            <div class="avatar me-3">
              <span class="avatar-initial rounded bg-label-primary d-flex justify-content-center align-items-center" style="width:48px; height:48px;">
                <i class="ri-group-line" style="font-size:24px;"></i>
              </span>
            </div>
            <div class="flex-grow-1">
              <p class="mb-1 text-muted fw-medium" style="font-size: 0.875rem;">Total Customer</p>
              <h3 class="mb-0 fw-bold">{{ $totalCustomer }}</h3>
            </div>
          </div>
          <small class="text-muted">
            <span class="badge bg-label-primary me-1">100%</span>
            dari total pelanggan
          </small>
        </div>
      </div>
    </div>

    <!-- Lunas -->
    <div class="col-xl-3 col-md-6">
      <div class="card card-border-shadow-success h-100">
        <div class="card-body">
          <div class="d-flex align-items-start justify-content-between mb-3">
            <div class="avatar me-3">
              <span class="avatar-initial rounded bg-label-success d-flex justify-content-center align-items-center" style="width:48px; height:48px;">
                <i class="ri-checkbox-circle-line" style="font-size:24px;"></i>
              </span>
            </div>
            <div class="flex-grow-1">
              <p class="mb-1 text-muted fw-medium" style="font-size: 0.875rem;">Pembayaran Lunas</p>
              <h3 class="mb-0 fw-bold">{{ $lunas }}</h3>
            </div>
          </div>
          <small class="text-muted">
            <span class="badge bg-label-success me-1">{{ round($lunas / max($totalCustomer,1) * 100) }}%</span>
            dari total customer
          </small>
        </div>
      </div>
    </div>

    <!-- Belum Lunas -->
    <div class="col-xl-3 col-md-6">
      <div class="card card-border-shadow-warning h-100">
        <div class="card-body">
          <div class="d-flex align-items-start justify-content-between mb-3">
            <div class="avatar me-3">
              <span class="avatar-initial rounded bg-label-warning d-flex justify-content-center align-items-center" style="width:48px; height:48px;">
                <i class="ri-error-warning-line" style="font-size:24px;"></i>
              </span>
            </div>
            <div class="flex-grow-1">
              <p class="mb-1 text-muted fw-medium" style="font-size: 0.875rem;">Belum Lunas</p>
              <h3 class="mb-0 fw-bold">{{ $belumLunas }}</h3>
            </div>
          </div>
          <small class="text-muted">
            <span class="badge bg-label-warning me-1">{{ round($belumLunas / max($totalCustomer,1) * 100) }}%</span>
            dari total customer
          </small>
        </div>
      </div>
    </div>

    <!-- Total Paket -->
    <div class="col-xl-3 col-md-6">
      <div class="card card-border-shadow-info h-100">
        <div class="card-body">
          <div class="d-flex align-items-start justify-content-between mb-3">
            <div class="avatar me-3">
              <span class="avatar-initial rounded bg-label-info d-flex justify-content-center align-items-center" style="width:48px; height:48px;">
                <i class="ri-box-3-line" style="font-size:24px;"></i>
              </span>
            </div>
            <div class="flex-grow-1">
              <p class="mb-1 text-muted fw-medium" style="font-size: 0.875rem;">Total Paket</p>
              <h3 class="mb-0 fw-bold">{{ $totalPaket }}</h3>
            </div>
          </div>
          <small class="text-muted">
            <span class="badge bg-label-info me-1">Aktif</span>
            paket tersedia
          </small>
        </div>
      </div>
    </div>
  </div>

  <!-- ========================================= -->
  <!-- DAFTAR TAGIHAN -->
  <!-- ========================================= -->
  <div class="card">
    <div class="card-header">
      <div class="row align-items-center">
        <div class="col-md-6">
          <h5 class="mb-0 fw-bold">
            <i class="ri-file-list-3-line me-2 text-primary"></i>
            Daftar Tagihan
          </h5>
          <small class="text-muted">Kelola seluruh tagihan pelanggan</small>
        </div>
        <div class="col-md-6 text-md-end mt-3 mt-md-0">
          <button type="button" class="btn btn-primary me-2" data-bs-toggle="modal" data-bs-target="#modalTambahTagihan">
            <i class="ri-add-line me-1"></i>
            Tambah Tagihan
          </button>
          <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#modalMassTagihan">
            <i class="ri-group-line me-1"></i>
            Tagihan Massal
          </button>
        </div>
      </div>

      <!-- Filters -->
      <div class="row g-3 mt-3">
        <div class="col-md-4">
          <label class="form-label small text-muted mb-1">Status Pembayaran</label>
          <select id="statusPembayaranFilter" class="form-select">
            <option value="">Semua Status</option>
            <option value="lunas">Sudah Lunas</option>
            <option value="belum bayar">Belum Bayar</option>
          </select>
        </div>

        <div class="col-md-4">
          <label class="form-label small text-muted mb-1">Kabupaten</label>
          <select id="kabupatenFilter" class="form-select">
            <option value="">Semua Kabupaten</option>
            @foreach($kabupatenList as $kab)
              <option value="{{ strtolower($kab) }}">{{ $kab }}</option>
            @endforeach
          </select>
        </div>

        <div class="col-md-4">
          <label class="form-label small text-muted mb-1">Kecamatan</label>
          <select id="kecamatanFilter" class="form-select">
            <option value="">Semua Kecamatan</option>
            @foreach($kecamatanList as $kec)
              <option value="{{ strtolower($kec) }}">{{ $kec }}</option>
            @endforeach
          </select>
        </div>
      </div>
    </div>

    <div class="card-datatable table-responsive">
      <table class="datatables-users table">
        <thead>
          <tr>
            <th>Detail</th>
            <th>No. ID</th>
            <th>Nama</th>
            <th>No. WA</th>
            <th>Status</th>
            <th>Paket</th>
            <th>Harga</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          @foreach($tagihans as $item)
          @php
            $status = strtolower($item['status_pembayaran'] ?? '');
            $badgeClass = match($status) {
              'lunas' => 'badge bg-success',
              'belum bayar' => 'badge bg-danger',
              default => 'badge bg-secondary',
            };
            
            $alamatParts = [];
            if($item['alamat_jalan']) $alamatParts[] = $item['alamat_jalan'];
            if($item['rt'] || $item['rw']) $alamatParts[] = 'RT '.$item['rt'].' / RW '.$item['rw'];
            if($item['desa']) $alamatParts[] = 'Desa '.$item['desa'];
            if($item['kecamatan']) $alamatParts[] = 'Kecamatan '.$item['kecamatan'];
            if($item['kabupaten']) $alamatParts[] = 'Kabupaten '.$item['kabupaten'];
            if($item['provinsi']) $alamatParts[] = $item['provinsi'];
            $alamatLengkap = implode(', ', $alamatParts);
            
            $buktiUrl = !empty($item['bukti_pembayaran']) ? asset('storage/kwitansi/' . $item['bukti_pembayaran']) : '';
          @endphp
          <tr 
            data-alamat="{{ $alamatLengkap }}"
            data-kecamatan="{{ $item['kecamatan'] ?? '-' }}"
            data-kabupaten="{{ $item['kabupaten'] ?? '-' }}"
            data-provinsi="{{ $item['provinsi'] ?? '-' }}"
            data-kecepatan="{{ $item['paket']['kecepatan'] ?? '-' }} Mbps"
            data-tanggal-mulai="{{ $item['tanggal_mulai'] ? \Carbon\Carbon::parse($item['tanggal_mulai'])->format('d M Y') : '-' }}"
            data-jatuh-tempo="{{ $item['tanggal_berakhir'] ? \Carbon\Carbon::parse($item['tanggal_berakhir'])->format('d M Y') : '-' }}"
            data-catatan="{{ $item['catatan'] ?? '-' }}"
            data-bukti="{{ $buktiUrl }}"
          >
            <td>
              <button class="btn btn-sm btn-icon btn-outline-primary btn-detail" title="Lihat Detail">
                <i class="ri-eye-line"></i>
              </button>
            </td>
            <td><span class="badge bg-label-dark">{{ $item['nomer_id'] }}</span></td>
            <td><strong>{{ $item['nama_lengkap'] }}</strong></td>
            <td>{{ $item['no_whatsapp'] }}</td>
            <td>
              <span class="{{ $badgeClass }}">{{ ucfirst($status ?: '-') }}</span>
            </td>
            <td>{{ $item['paket']['nama_paket'] ?? '-' }}</td>
            <td><strong>Rp {{ number_format($item['paket']['harga'] ?? 0, 0, ',', '.') }}</strong></td>
            <td>
              <div class="d-flex gap-2">
                <button type="button"
                  class="btn btn-sm btn-outline-primary"
                  data-bs-toggle="modal"
                  data-bs-target="#modalEditTagihan-{{ $item['id'] }}"
                  title="Edit">
                  <i class="ri-edit-2-line"></i>
                </button>

                <form action="{{ route('tagihan.destroy', $item['id']) }}" method="POST" class="delete-form d-inline">
                  @csrf
                  @method('DELETE')
                  <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus">
                    <i class="ri-delete-bin-line"></i>
                  </button>
                </form>

        
              </div>
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- ========================================= -->
<!-- MODAL: DETAIL PELANGGAN -->
<!-- ========================================= -->
<div class="modal fade" id="detailModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">
      <div class="modal-header bg-primary">
        <h5 class="modal-title text-white fw-bold">
          <i class="ri-information-line me-2"></i>Detail Pelanggan
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <!-- Content will be inserted via JavaScript -->
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
          <i class="ri-close-line me-1"></i>Tutup
        </button>
      </div>
    </div>
  </div>
</div>

<!-- ========================================= -->
<!-- MODAL: TAMBAH TAGIHAN -->
<!-- ========================================= -->
<div class="modal fade" id="modalTambahTagihan" tabindex="-1">
  <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content">
      <form action="{{ route('tagihan.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="modal-header bg-primary">
          <h5 class="modal-title text-white fw-bold">
            <i class="ri-add-circle-line me-2"></i>Tambah Tagihan Baru
          </h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>

        <div class="modal-body">
          <div class="row g-3">
            <!-- Pilih Pelanggan -->
            <div class="col-12">
              <label class="form-label fw-semibold">Pilih Pelanggan <span class="text-danger">*</span></label>
              <select id="pelangganSelect" name="pelanggan_id" class="form-select select2" required>
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

            <input type="hidden" name="paket_id" id="paket_id">

            <!-- Info Pelanggan -->
            <div class="col-12 mt-4">
              <h6 class="text-primary fw-bold mb-3">
                <i class="ri-user-3-line me-2"></i>Informasi Pelanggan
              </h6>
            </div>

            <div class="col-md-6">
              <label class="form-label small text-muted">Nama Lengkap</label>
              <input type="text" id="nama_lengkap" class="form-control bg-light" readonly>
            </div>

            <div class="col-md-6">
              <label class="form-label small text-muted">Nomor ID</label>
              <input type="text" id="nomer_id" class="form-control bg-light" readonly>
            </div>

            <div class="col-md-6">
              <label class="form-label small text-muted">Nomor WhatsApp</label>
              <input type="text" id="no_whatsapp" class="form-control bg-light" readonly>
            </div>

            <div class="col-md-6">
              <label class="form-label small text-muted">Kode Pos</label>
              <input type="text" id="kode_pos" class="form-control bg-light" readonly>
            </div>

            <!-- Alamat -->
            <div class="col-12 mt-4">
              <h6 class="text-primary fw-bold mb-3">
                <i class="ri-map-pin-line me-2"></i>Alamat Lengkap
              </h6>
            </div>

            <div class="col-12">
              <label class="form-label small text-muted">Alamat Jalan</label>
              <input type="text" id="alamat_jalan" class="form-control bg-light" readonly>
            </div>

            <div class="col-md-3">
              <label class="form-label small text-muted">RT</label>
              <input type="text" id="rt" class="form-control bg-light" readonly>
            </div>

            <div class="col-md-3">
              <label class="form-label small text-muted">RW</label>
              <input type="text" id="rw" class="form-control bg-light" readonly>
            </div>

            <div class="col-md-6">
              <label class="form-label small text-muted">Desa/Kelurahan</label>
              <input type="text" id="desa" class="form-control bg-light" readonly>
            </div>

            <div class="col-md-4">
              <label class="form-label small text-muted">Kecamatan</label>
              <input type="text" id="kecamatan" class="form-control bg-light" readonly>
            </div>

            <div class="col-md-4">
              <label class="form-label small text-muted">Kabupaten/Kota</label>
              <input type="text" id="kabupaten" class="form-control bg-light" readonly>
            </div>

            <div class="col-md-4">
              <label class="form-label small text-muted">Provinsi</label>
              <input type="text" id="provinsi" class="form-control bg-light" readonly>
            </div>

            <!-- Paket -->
            <div class="col-12 mt-4">
              <h6 class="text-primary fw-bold mb-3">
                <i class="ri-box-3-line me-2"></i>Informasi Paket
              </h6>
            </div>

            <div class="col-md-6">
              <label class="form-label small text-muted">Nama Paket</label>
              <input type="text" id="paket" class="form-control bg-light" readonly>
            </div>

            <div class="col-md-6">
              <label class="form-label small text-muted">Harga Paket</label>
              <div class="input-group">
                <span class="input-group-text">Rp</span>
                <input type="text" id="harga" name="harga" class="form-control bg-light" readonly>
              </div>
            </div>

            <div class="col-md-6">
              <label class="form-label small text-muted">Kecepatan</label>
              <div class="input-group">
                <input type="text" id="kecepatan" class="form-control bg-light" readonly>
                <span class="input-group-text">Mbps</span>
              </div>
            </div>

            <div class="col-md-6">
              <label class="form-label small text-muted">Masa Pembayaran</label>
              <div class="input-group">
                <input type="text" id="masa_pembayaran" class="form-control bg-light" readonly>
                <span class="input-group-text">Hari</span>
              </div>
            </div>

            <!-- Tagihan -->
            <div class="col-12 mt-4">
              <h6 class="text-primary fw-bold mb-3">
                <i class="ri-calendar-check-line me-2"></i>Detail Tagihan
              </h6>
            </div>

            <div class="col-md-6">
              <label class="form-label fw-semibold">Tanggal Mulai <span class="text-danger">*</span></label>
              <input type="date" id="tanggal_mulai" name="tanggal_mulai" class="form-control" required>
            </div>

            <div class="col-md-6">
              <label class="form-label fw-semibold">Tanggal Jatuh Tempo <span class="text-danger">*</span></label>
              <input type="date" id="tanggal_berakhir" name="tanggal_berakhir" class="form-control bg-light" readonly required>
            </div>

            <div class="col-12">
              <label class="form-label">Catatan (Opsional)</label>
              <textarea class="form-control" id="catatan" name="catatan" rows="3" placeholder="Tambahkan catatan jika diperlukan..."></textarea>
            </div>

            <div class="col-12">
              <label class="form-label">Upload Bukti Pembayaran (Opsional)</label>
              <input type="file" name="bukti_pembayaran" class="form-control" accept="image/*,.pdf">
              <small class="text-muted">Format: JPG, PNG, PDF (Max: 2MB)</small>
            </div>
          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
            <i class="ri-close-line me-1"></i>Batal
          </button>
          <button type="submit" class="btn btn-primary">
            <i class="ri-save-line me-1"></i>Simpan Tagihan
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- ========================================= -->
<!-- MODAL: EDIT TAGIHAN (FOREACH) -->
<!-- ========================================= -->
@foreach ($tagihans as $tagihan)
<div class="modal fade" id="modalEditTagihan-{{ $tagihan['id'] }}" tabindex="-1">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <form action="{{ route('tagihan.update', $tagihan['id']) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="modal-header bg-warning text-dark">
          <h5 class="modal-title fw-bold">
            <i class="ri-edit-2-line me-2"></i>Edit Tagihan
          </h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
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
              <input type="text" name="tanggal_mulai" class="form-control flatpickr-edit-start" value="{{ $tagihan['tanggal_mulai'] }}" required>
            </div>

            <div class="col-md-6">
              <label class="form-label fw-semibold">Tanggal Jatuh Tempo</label>
              <input type="text" name="tanggal_berakhir" class="form-control flatpickr-edit-end" value="{{ $tagihan['tanggal_berakhir'] }}" required>
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
            <i class="ri-save-line me-1"></i>Simpan Perubahan
          </button>
        </div>
      </form>
    </div>
  </div>
</div>
@endforeach

<!-- ========================================= -->
<!-- MODAL: MASS TAGIHAN -->
<!-- ========================================= -->
<div class="modal fade" id="modalMassTagihan" tabindex="-1">
  <div class="modal-dialog modal-md modal-dialog-centered">
    <div class="modal-content">
      <form action="{{ route('tagihan.massStore') }}" method="POST">
        @csrf

        <div class="modal-header bg-warning text-dark">
          <h5 class="modal-title fw-bold">
            <i class="ri-group-line me-2"></i>Buat Tagihan Massal
          </h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>

        <div class="modal-body">
          <div class="alert alert-info d-flex align-items-center">
            <i class="ri-information-line me-2" style="font-size: 1.5rem;"></i>
            <div>
              <strong>{{ count($pelanggan) }} pelanggan</strong> akan dibuatkan tagihan secara otomatis
            </div>
          </div>

          <div class="border rounded p-3 mb-3" style="max-height: 200px; overflow-y: auto; background: #f8f9fa;">
            @foreach ($pelanggan as $p)
              <div class="py-2 border-bottom">
                <span class="badge bg-dark me-2">{{ $p->nomer_id }}</span>
                <strong>{{ $p->nama_lengkap }}</strong>
              </div>
            @endforeach
          </div>

          <div class="mb-3">
            <label class="form-label fw-semibold">Tanggal Mulai</label>
            <input type="text" name="tanggal_mulai" class="form-control flatpickr-select-start-all" required>
          </div>

          <div class="mb-3">
            <label class="form-label fw-semibold">Tanggal Jatuh Tempo</label>
            <input type="text" name="tanggal_berakhir" class="form-control flatpickr-select-start-end" required>
          </div>

          <div class="alert alert-warning small mb-0">
            <i class="ri-error-warning-line me-1"></i>
            Semua pelanggan di atas akan otomatis dibuatkan tagihan baru
          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-warning">
            <i class="ri-check-circle-line me-1"></i>Buat Semua Tagihan
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

@endsection