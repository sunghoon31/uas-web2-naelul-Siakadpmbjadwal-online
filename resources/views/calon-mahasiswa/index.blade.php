@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="card shadow-lg border-0">
        <div class="card-header bg-gradient-primary text-white py-3">
            <div class="d-flex justify-content-between align-items-center">
                <h4 class="mb-0 font-weight-bold">
                    <i class="fas fa-users mr-2"></i> Daftar Calon Mahasiswa
                </h4>
                <div class="btn-group-header">
                    <button type="button" class="btn btn-light btn-lg shadow-sm" onclick="showModalCreate()">
                        <i class="fas fa-plus-circle mr-2"></i> Tambah Pendaftar
                    </button>
                    <!-- TOMBOL EXPORT LANGSUNG TANPA DROPDOWN -->
                    <button type="button" class="btn btn-danger btn-lg shadow-sm ml-2" onclick="exportData('pdf')">
                        <i class="fas fa-file-pdf mr-2"></i> Export PDF
                    </button>
                    <button type="button" class="btn btn-success btn-lg shadow-sm ml-2" onclick="exportData('excel')">
                        <i class="fas fa-file-excel mr-2"></i> Export Excel
                    </button>
                </div>
            </div>
        </div>

        <div class="card-body bg-light">
            <!-- Filter Section -->
            <div class="card mb-4 border-0 shadow-sm">
                <div class="card-body">
                    <h6 class="card-title text-primary mb-3">
                        <i class="fas fa-filter mr-2"></i>Filter Data
                    </h6>
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <label class="small font-weight-bold text-muted">Pencarian</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text bg-white">
                                        <i class="fas fa-search text-primary"></i>
                                    </span>
                                </div>
                                <input type="text" id="searchInput" class="form-control" placeholder="Cari nama/no pendaftaran...">
                            </div>
                        </div>
                        <div class="col-md-2 mb-3">
                            <label class="small font-weight-bold text-muted">Status</label>
                            <select id="filterStatus" class="form-control custom-select">
                                <option value="">Semua Status</option>
                                <option value="pending">Pending</option>
                                <option value="diterima">Diterima</option>
                                <option value="ditolak">Ditolak</option>
                            </select>
                        </div>
                        <div class="col-md-2 mb-3">
                            <label class="small font-weight-bold text-muted">Jalur Masuk</label>
                            <select id="filterJalur" class="form-control custom-select">
                                <option value="">Semua Jalur</option>
                                <option value="reguler">Reguler</option>
                                <option value="prestasi">Prestasi</option>
                                <option value="beasiswa">Beasiswa</option>
                                <option value="pindahan">Pindahan</option>
                            </select>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="small font-weight-bold text-muted">Program Studi</label>
                            <select id="filterProdi" class="form-control custom-select">
                                <option value="">Semua Prodi</option>
                                @foreach($prodis as $prodi)
                                <option value="{{ $prodi->id }}">{{ $prodi->nama_prodi }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2 mb-3">
                            <label class="small font-weight-bold text-muted">&nbsp;</label>
                            <button type="button" id="btnFilter" class="btn btn-primary btn-block">
                                <i class="fas fa-filter mr-2"></i> Terapkan Filter
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Table -->
            <div class="card border-0 shadow-sm">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-striped mb-0" id="tableCalonMahasiswa">
                            <thead class="thead-dark">
                                <tr>
                                    <th width="60" class="text-center">No</th>
                                    <th width="140">No Pendaftaran</th>
                                    <th>Nama Lengkap</th>
                                    <th width="80" class="text-center">L/P</th>
                                    <th width="200">Prodi</th>
                                    <th width="120" class="text-center">Jalur</th>
                                    <th width="120" class="text-center">Status</th>
                                    <th width="350" class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td colspan="8" class="text-center py-5">
                                        <i class="fas fa-spinner fa-spin fa-2x text-primary mb-3"></i>
                                        <p class="text-muted">Memuat data...</p>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Pagination -->
            <div id="paginationContainer" class="mt-4 d-flex justify-content-center"></div>
        </div>
    </div>
</div>

<!-- Include Modal Create/Edit -->
@include('calon-mahasiswa.tambah')

<!-- Modal Detail -->
<div class="modal fade" id="modalDetail" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-gradient-info text-white">
                <h5 class="modal-title font-weight-bold">
                    <i class="fas fa-info-circle mr-2"></i> Detail Calon Mahasiswa
                </h5>
                <button type="button" class="close text-white" onclick="closeDetailModal()">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body" id="detailContent">
                <div class="text-center py-5">
                    <i class="fas fa-spinner fa-spin fa-3x text-primary mb-3"></i>
                    <p class="text-muted">Memuat data...</p>
                </div>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary btn-lg" onclick="closeDetailModal()">
                    <i class="fas fa-times mr-2"></i> Tutup
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
/* Gradient Background */
.bg-gradient-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.bg-gradient-info {
    background: linear-gradient(135deg, #36d1dc 0%, #5b86e5 100%);
}

/* Card Styling */
.card {
    border-radius: 12px;
    overflow: hidden;
}

.card-header {
    border: none;
}

/* Table Styling */
.table {
    font-size: 0.95rem;
}

.table thead th {
    vertical-align: middle;
    text-align: center;
    font-weight: 600;
    letter-spacing: 0.5px;
    background: #2c3e50;
    color: white;
    border: none;
    padding: 15px 10px;
}

.table tbody td {
    vertical-align: middle;
    padding: 12px 10px;
    border-color: #e9ecef;
}

.table-hover tbody tr:hover {
    background-color: #f8f9fa;
    cursor: pointer;
}

/* Badge Styling */
.badge {
    padding: 0.6em 1em;
    font-size: 0.85rem;
    font-weight: 600;
    border-radius: 6px;
}

.badge-warning {
    background: #ffc107;
    color: #000;
}

.badge-success {
    background: #28a745;
}

.badge-danger {
    background: #dc3545;
}

.badge-info {
    background: #17a2b8;
}

.badge-primary {
    background: #007bff;
}

/* Button Styling */
.btn {
    border-radius: 8px;
    font-weight: 600;
    transition: all 0.3s ease;
    border: none;
}

.btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

.btn-lg {
    padding: 0.6rem 1.5rem;
    font-size: 1rem;
}

/* Action Buttons - LEBIH BESAR DAN JELAS */
.btn-action {
    padding: 0.5rem 1rem !important;
    font-size: 0.9rem !important;
    margin: 2px !important;
    min-width: 85px;
    white-space: nowrap;
    display: inline-block;
}

.btn-action i {
    margin-right: 5px;
}

.btn-group-action {
    white-space: nowrap;
    display: flex;
    flex-wrap: wrap;
    gap: 5px;
    justify-content: center;
}

/* Form Styling */
.form-control, .custom-select {
    border-radius: 8px;
    border: 2px solid #e9ecef;
    padding: 0.6rem 1rem;
    transition: all 0.3s ease;
}

.form-control:focus, .custom-select:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
}

.form-control-lg {
    padding: 0.75rem 1.25rem;
    font-size: 1rem;
}

/* Modal Styling */
.modal-content {
    border-radius: 12px;
}

.modal-header {
    border-top-left-radius: 12px;
    border-top-right-radius: 12px;
    border: none;
    padding: 1.5rem;
}

.modal-footer {
    border: none;
    padding: 1.5rem;
}

/* Input Group */
.input-group-text {
    border-radius: 8px 0 0 8px;
    border: 2px solid #e9ecef;
    border-right: none;
}

.input-group .form-control {
    border-left: none;
    border-radius: 0 8px 8px 0;
}

/* Pagination */
.pagination {
    margin-bottom: 0;
}

.page-item .page-link {
    border-radius: 8px;
    margin: 0 3px;
    border: 2px solid #e9ecef;
    color: #667eea;
    font-weight: 600;
    padding: 0.5rem 1rem;
}

.page-item.active .page-link {
    background: #667eea;
    border-color: #667eea;
}

.page-item:hover .page-link {
    background: #f8f9fa;
}

/* Shadow Effects */
.shadow-sm {
    box-shadow: 0 2px 4px rgba(0,0,0,0.1) !important;
}

.shadow {
    box-shadow: 0 4px 8px rgba(0,0,0,0.1) !important;
}

.shadow-lg {
    box-shadow: 0 8px 16px rgba(0,0,0,0.15) !important;
}

/* Responsive */
@media (max-width: 768px) {
    .btn-action {
        min-width: auto;
        padding: 0.4rem 0.8rem !important;
        font-size: 0.85rem !important;
    }
    
    .table {
        font-size: 0.85rem;
    }
}
</style>
@endpush

@push('scripts')
<script>
$(document).ready(function() {
    console.log('=== Page Initialized ===');
    loadData();

    $('#btnFilter').on('click', function() {
        loadData();
    });

    $('#searchInput').on('keypress', function(e) {
        if(e.which == 13) {
            e.preventDefault();
            loadData();
        }
    });
});

// Fungsi untuk export dengan parameter filter - SEDERHANA & LANGSUNG
function exportData(type) {
    console.log('=== EXPORT FUNCTION CALLED ===');
    console.log('Type:', type);
    
    // Ambil semua parameter filter
    const search = $('#searchInput').val() || '';
    const status = $('#filterStatus').val() || '';
    const jalur = $('#filterJalur').val() || '';
    const prodi_id = $('#filterProdi').val() || '';
    
    console.log('Filters:', { search, status, jalur, prodi_id });
    
    // Build query string
    let queryParams = [];
    if (search) queryParams.push('search=' + encodeURIComponent(search));
    if (status) queryParams.push('status=' + encodeURIComponent(status));
    if (jalur) queryParams.push('jalur=' + encodeURIComponent(jalur));
    if (prodi_id) queryParams.push('prodi_id=' + encodeURIComponent(prodi_id));
    
    const queryString = queryParams.length > 0 ? '?' + queryParams.join('&') : '';
    console.log('Query String:', queryString);
    
    // Build URL menggunakan window.location.origin
    const baseUrl = window.location.origin;
    let url;
    
    if (type === 'pdf') {
        url = baseUrl + '/pmb/calon-mahasiswa/export/pdf' + queryString;
        console.log('=== OPENING PDF ===');
        console.log('Full URL:', url);
        
        // Tampilkan loading alert
        Swal.fire({
            title: 'Memproses...',
            text: 'Sedang membuat file PDF',
            icon: 'info',
            allowOutsideClick: false,
            showConfirmButton: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
        
        // Buka di tab baru
        const newWindow = window.open(url, '_blank');
        console.log('New window opened:', newWindow !== null);
        
        // Tutup loading setelah 1 detik
        setTimeout(() => {
            Swal.close();
        }, 1000);
        
    } else if (type === 'excel') {
        url = baseUrl + '/pmb/calon-mahasiswa/export/excel' + queryString;
        console.log('=== DOWNLOADING EXCEL ===');
        console.log('Full URL:', url);
        
        // Tampilkan loading alert
        Swal.fire({
            title: 'Memproses...',
            text: 'Sedang membuat file Excel',
            icon: 'info',
            allowOutsideClick: false,
            showConfirmButton: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
        
        // Download file
        window.location.href = url;
        console.log('Download initiated');
        
        // Tutup loading setelah 2 detik
        setTimeout(() => {
            Swal.close();
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: 'File Excel sedang diunduh',
                timer: 2000,
                showConfirmButton: false
            });
        }, 2000);
    }
    
    console.log('=== EXPORT FUNCTION COMPLETED ===');
}

function closeDetailModal() {
    $('#modalDetail').modal('hide');
    setTimeout(function() {
        $('.modal-backdrop').remove();
        $('body').removeClass('modal-open').css('padding-right', '');
    }, 300);
}

function loadData(page = 1) {
    const params = {
        page: page,
        search: $('#searchInput').val(),
        status: $('#filterStatus').val(),
        jalur: $('#filterJalur').val(),
        prodi_id: $('#filterProdi').val()
    };

    $.ajax({
        url: '{{ route("pmb.calon-mahasiswa.index") }}',
        data: params,
        headers: {'X-Requested-With': 'XMLHttpRequest'},
        success: function(response) {
            let html = '';
            if(response.data && response.data.length > 0) {
                response.data.forEach((item, index) => {
                    const statusBadge = item.status_seleksi == 'pending' ? 'warning' : 
                                       item.status_seleksi == 'diterima' ? 'success' : 'danger';
                    const statusText = item.status_seleksi == 'pending' ? 'Pending' : 
                                      item.status_seleksi == 'diterima' ? 'Diterima' : 'Ditolak';
                    
                    html += `
                        <tr>
                            <td class="text-center font-weight-bold">${response.from + index}</td>
                            <td><strong class="text-primary">${item.no_pendaftaran}</strong></td>
                            <td class="font-weight-bold">${item.nama}</td>
                            <td class="text-center">
                                <span class="badge badge-${item.jenis_kelamin == 'L' ? 'primary' : 'danger'}">
                                    ${item.jenis_kelamin}
                                </span>
                            </td>
                            <td>${item.prodi ? item.prodi.nama_prodi : '-'}</td>
                            <td class="text-center">
                                <span class="badge badge-info">${item.jalur_masuk}</span>
                            </td>
                            <td class="text-center">
                                <span class="badge badge-${statusBadge}">${statusText}</span>
                                <br>
                                <small>
                                    <span class="badge badge-${item.status_verifikasi_badge || 'secondary'} mt-1">
                                        ${item.status_verifikasi_berkas_text || 'Belum Upload'}
                                    </span>
                                </small>
                            </td>
                            <td>
                                <div class="btn-group-action">
                                    <button type="button" onclick="showDetail(${item.id})" class="btn btn-sm btn-info btn-action" title="Detail">
                                        <i class="fas fa-eye"></i> Detail
                                    </button>
                                    <a href="${window.location.origin}/pmb/dokumen/${item.id}" class="btn btn-sm btn-warning btn-action" title="Dokumen">
                                        <i class="fas fa-folder-open"></i> Dokumen
                                    </a>
                                    <button type="button" onclick="editData(${item.id})" class="btn btn-sm btn-primary btn-action" title="Edit">
                                        <i class="fas fa-edit"></i> Edit
                                    </button>
                                    <button type="button" onclick="deleteData(${item.id})" class="btn btn-sm btn-danger btn-action" title="Hapus">
                                        <i class="fas fa-trash"></i> Hapus
                                    </button>
                                    <a href="${window.location.origin}/pmb/keuangan/${item.id}" class="btn btn-sm btn-success btn-action" title="Keuangan">
                                        <i class="fas fa-money-bill-wave"></i> Keuangan
                                    </a>
                                </div>
                            </td>
                        </tr>
                    `;
                });
            } else {
                html = `
                    <tr>
                        <td colspan="8" class="text-center py-5">
                            <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                            <p class="text-muted mb-0">Tidak ada data calon mahasiswa</p>
                        </td>
                    </tr>
                `;
            }
            
            $('#tableCalonMahasiswa tbody').html(html);
            renderPagination(response);
        },
        error: function(xhr) {
            console.error('Error loading data:', xhr);
            $('#tableCalonMahasiswa tbody').html(`
                <tr>
                    <td colspan="8" class="text-center text-danger py-5">
                        <i class="fas fa-exclamation-triangle fa-3x mb-3"></i>
                        <p>Error loading data</p>
                    </td>
                </tr>
            `);
        }
    });
}

function showDetail(id) {
    $('#modalDetail').modal('show');
    $('#detailContent').html(`
        <div class="text-center py-5">
            <i class="fas fa-spinner fa-spin fa-3x text-primary mb-3"></i>
            <p class="text-muted">Memuat data...</p>
        </div>
    `);
    
    $.ajax({
        url: '{{ url("pmb/calon-mahasiswa") }}/' + id,
        headers: {'X-Requested-With': 'XMLHttpRequest'},
        success: function(response) {
            const data = response.data || response;
            let html = `
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="text-primary border-bottom pb-2 mb-3 font-weight-bold">
                            <i class="fas fa-user mr-2"></i> Data Pribadi
                        </h6>
                        <table class="table table-sm table-borderless">
                            <tr>
                                <th width="40%">No Pendaftaran</th>
                                <td><strong class="text-primary">${data.no_pendaftaran}</strong></td>
                            </tr>
                            <tr>
                                <th>Nama Lengkap</th>
                                <td><strong>${data.nama}</strong></td>
                            </tr>
                            <tr>
                                <th>Jenis Kelamin</th>
                                <td>${data.jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan'}</td>
                            </tr>
                            <tr>
                                <th>Alamat</th>
                                <td>${data.alamat}</td>
                            </tr>
                            <tr>
                                <th>No HP</th>
                                <td>${data.no_hp}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-primary border-bottom pb-2 mb-3 font-weight-bold">
                            <i class="fas fa-graduation-cap mr-2"></i> Data Pendaftaran
                        </h6>
                        <table class="table table-sm table-borderless">
                            <tr>
                                <th width="40%">Program Studi</th>
                                <td><strong>${data.prodi ? data.prodi.nama_prodi : '-'}</strong></td>
                            </tr>
                            <tr>
                                <th>Jalur Masuk</th>
                                <td><span class="badge badge-info">${data.jalur_masuk}</span></td>
                            </tr>
                            <tr>
                                <th>Gelombang</th>
                                <td>${data.gelombang || '-'}</td>
                            </tr>
                            <tr>
                                <th>Status Seleksi</th>
                                <td>
                                    <span class="badge badge-${data.status_seleksi == 'pending' ? 'warning' : data.status_seleksi == 'diterima' ? 'success' : 'danger'}">
                                        ${data.status_seleksi == 'pending' ? 'Pending' : data.status_seleksi == 'diterima' ? 'Diterima' : 'Ditolak'}
                                    </span>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
                
                ${data.status_seleksi == 'pending' ? `
                <hr>
                <div class="text-center py-3">
                    <h6 class="mb-4 font-weight-bold">Proses Seleksi</h6>
                    <button type="button" onclick="approveData(${data.id})" class="btn btn-success btn-lg mr-2">
                        <i class="fas fa-check-circle mr-2"></i> ACC / Terima
                    </button>
                    <button type="button" onclick="rejectData(${data.id})" class="btn btn-danger btn-lg">
                        <i class="fas fa-times-circle mr-2"></i> Tolak
                    </button>
                </div>
                ` : ''}
            `;
            $('#detailContent').html(html);
        },
        error: function(xhr) {
            $('#detailContent').html(`
                <div class="text-center text-danger py-5">
                    <i class="fas fa-exclamation-triangle fa-3x mb-3"></i>
                    <p>Error loading data</p>
                </div>
            `);
        }
    });
}

function deleteData(id) {
    Swal.fire({
        title: 'Yakin hapus data?',
        text: 'Data yang dihapus tidak dapat dikembalikan!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '{{ url("pmb/calon-mahasiswa") }}/' + id,
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                    'X-Requested-With': 'XMLHttpRequest'
                },
                success: function(response) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Terhapus!',
                        text: response.message || 'Data berhasil dihapus',
                        showConfirmButton: false,
                        timer: 1500
                    });
                    
                    setTimeout(function() {
                        loadData();
                    }, 500);
                },
                error: function(xhr) {
                    let message = 'Gagal menghapus data!';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        message = xhr.responseJSON.message;
                    }
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: message
                    });
                }
            });
        }
    });
}

function approveData(id) {
    Swal.fire({
        title: 'ACC Calon Mahasiswa?',
        text: 'NIM akan di-generate otomatis dan data akan dipindahkan ke tabel mahasiswa.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#28a745',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Ya, ACC!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '{{ url("pmb/calon-mahasiswa") }}/' + id + '/approve',
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                    'X-Requested-With': 'XMLHttpRequest'
                },
                success: function(response) {
                    closeDetailModal();
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: response.message || 'Calon mahasiswa berhasil di-ACC!',
                        showConfirmButton: false,
                        timer: 1500
                    });
                    
                    setTimeout(function() {
                        loadData();
                    }, 500);
                },
                error: function(xhr) {
                    let message = 'Gagal meng-ACC!';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        message = xhr.responseJSON.message;
                    }
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: message
                    });
                }
            });
        }
    });
}

function rejectData(id) {
    Swal.fire({
        title: 'Tolak Pendaftaran?',
        text: 'Status akan diubah menjadi ditolak.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Ya, Tolak!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '{{ url("pmb/calon-mahasiswa") }}/' + id + '/reject',
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                    'X-Requested-With': 'XMLHttpRequest'
                },
                success: function(response) {
                    closeDetailModal();
                    Swal.fire({
                        icon: 'success',
                        title: 'Ditolak!',
                        text: response.message || 'Pendaftaran berhasil ditolak!',
                        showConfirmButton: false,
                        timer: 1500
                    });
                    
                    setTimeout(function() {
                        loadData();
                    }, 500);
                },
                error: function(xhr) {
                    let message = 'Gagal menolak!';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        message = xhr.responseJSON.message;
                    }
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: message
                    });
                }
            });
        }
    });
}

function renderPagination(response) {
    if (!response.links || response.links.length <= 3) {
        $('#paginationContainer').html('');
        return;
    }
    
    let html = '<ul class="pagination pagination-lg">';
    
    response.links.forEach(link => {
        const active = link.active ? 'active' : '';
        const disabled = !link.url ? 'disabled' : '';
        let page = 1;
        
        if (link.url) {
            const urlParams = new URL(link.url);
            page = urlParams.searchParams.get('page') || 1;
        }
        
        let label = link.label;
        if (label.includes('Previous')) label = '&laquo; Sebelumnya';
        if (label.includes('Next')) label = 'Selanjutnya &raquo;';
        
        html += `
            <li class="page-item ${active} ${disabled}">
                <a class="page-link" href="javascript:void(0)" onclick="loadData(${page})">
                    ${label}
                </a>
            </li>
        `;
    });
    
    html += '</ul>';
    $('#paginationContainer').html(html);
}
</script>
@endpush