{{-- File: resources/views/jadwal/index.blade.php --}}

@extends('layouts.app')

@section('title', 'Jadwal Kuliah')

@section('content')
<div class="container-fluid py-4">
    <div class="card shadow-sm">
        <div class="card-header bg-white py-3">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h4 class="mb-0">
                        <i class="bi bi-calendar-week"></i> Jadwal Kuliah
                    </h4>
                </div>
                <div class="col-md-4 text-end">
                    <button class="btn btn-primary btn-sm" id="btnTambah">
                        <i class="bi bi-plus-circle"></i> Tambah Jadwal
                    </button>
                    <div class="btn-group">
                        <button type="button" class="btn btn-success btn-sm dropdown-toggle" data-bs-toggle="dropdown">
                            <i class="bi bi-download"></i> Export
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#" id="exportPdf"><i class="bi bi-file-pdf"></i> PDF</a></li>
                            <li><a class="dropdown-item" href="#" id="exportExcel"><i class="bi bi-file-excel"></i> Excel</a></li>
                        </ul>
                    </div>
                    <button class="btn btn-info btn-sm" id="btnImport">
                        <i class="bi bi-upload"></i> Import
                    </button>
                </div>
            </div>
        </div>
        <div class="card-body">
            <!-- Filter -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <label class="form-label fw-bold">
                        <i class="bi bi-building"></i> Program Studi
                    </label>
                    <select class="form-select" id="filterProdi">
                        @foreach($prodis as $prodi)
                            <option value="{{ $prodi->id }}" 
                                    data-fakultas-id="{{ $prodi->fakultas_id }}"
                                    {{ $prodi->id == $selectedProdi ? 'selected' : '' }}>
                                {{ $prodi->nama_prodi }} - {{ $prodi->fakultas->nama_fakultas }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold">
                        <i class="bi bi-bookmark"></i> Semester
                    </label>
                    <select class="form-select" id="filterSemester">
                        @foreach($semesters as $sem)
                            <option value="{{ $sem }}" {{ $sem == $selectedSemester ? 'selected' : '' }}>
                                Semester {{ $sem }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Info Alert -->
            <div class="alert alert-info alert-dismissible fade show" role="alert">
                <i class="bi bi-info-circle"></i>
                <strong>Informasi:</strong> 
                Sistem akan otomatis mengecek bentrok jadwal untuk <strong>Prodi, Dosen, dan Ruangan</strong>. 
                Tidak akan ada jadwal yang bentrok di waktu yang sama.
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>

            <!-- Jadwal Display -->
            <div id="jadwalContainer">
                <div class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-2 text-muted">Memuat jadwal...</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Include Form Modals -->
@include('jadwal.form')

<!-- Modal Import -->
<div class="modal fade" id="modalImport" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title">
                    <i class="bi bi-upload"></i> Import Jadwal dari Excel
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="formImport" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">File Excel *</label>
                        <input type="file" class="form-control" name="file" accept=".xlsx,.xls,.csv" required>
                        <div class="form-text">Format: .xlsx, .xls, .csv (Maksimal 2MB)</div>
                    </div>
                    <div class="alert alert-warning">
                        <i class="bi bi-exclamation-triangle"></i>
                        <strong>Perhatian:</strong> Pastikan format file sesuai dengan template yang disediakan.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle"></i> Batal
                    </button>
                    <button type="submit" class="btn btn-info">
                        <i class="bi bi-upload"></i> Import
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('styles')
<style>
    .jadwal-table {
        width: 100%;
        margin-bottom: 2rem;
        font-size: 0.95rem;
        border-collapse: separate;
        border-spacing: 0;
    }
    
    .jadwal-header {
        background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
        color: white;
        font-weight: bold;
        text-align: center;
    }
    
    .jadwal-header th {
        font-size: 0.95rem;
        letter-spacing: 0.3px;
        padding: 14px 12px;
        border: 2px solid #6366f1 !important;
        border-bottom: 3px solid #6366f1 !important;
    }
    
    .jadwal-row {
        transition: all 0.3s ease;
        background-color: #ffffff;
    }
    
    .jadwal-row:hover {
        background-color: #f0f4ff;
        transform: scale(1.002);
        box-shadow: 0 4px 12px rgba(79, 70, 229, 0.15);
    }
    
    .jadwal-row-empty {
        background-color: #fafafa;
    }
    
    .jadwal-row-empty:hover {
        background-color: #f5f5f5;
    }
    
    .jadwal-cell {
        padding: 18px 14px;
        vertical-align: middle;
        border: 2px solid #e5e7eb !important;
        border-top: none !important;
    }
    
    .jadwal-cell-empty {
        padding: 22px 14px;
        vertical-align: middle;
        border: 2px solid #e5e7eb !important;
        border-top: none !important;
        text-align: center;
        color: #9ca3af;
        font-style: italic;
    }
    
    /* Border untuk kolom pertama di setiap baris */
    .jadwal-row td:first-child,
    .jadwal-row-empty td:first-child {
        border-left: 3px solid #6366f1 !important;
    }
    
    /* Border untuk kolom terakhir di setiap baris */
    .jadwal-row td:last-child,
    .jadwal-row-empty td:last-child {
        border-right: 3px solid #6366f1 !important;
    }
    
    /* Border bawah untuk baris terakhir */
    .jadwal-table tbody tr:last-child td {
        border-bottom: 3px solid #6366f1 !important;
    }
    
    .hari-section {
        margin-bottom: 2.5rem;
        animation: fadeIn 0.5s ease-in;
        box-shadow: 0 4px 16px rgba(0,0,0,0.1);
        border-radius: 8px;
        overflow: hidden;
    }
    
    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    .hari-title {
        background: linear-gradient(90deg, #f59e0b 0%, #f97316 100%);
        padding: 16px 20px;
        font-weight: 700;
        margin-bottom: 0;
        font-size: 1.15rem;
        box-shadow: 0 3px 8px rgba(245, 158, 11, 0.4);
        color: white;
        text-shadow: 1px 1px 2px rgba(0,0,0,0.1);
        border-bottom: 3px solid #ea580c;
    }
    
    .badge-sks {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        color: white;
        padding: 6px 14px;
        border-radius: 16px;
        font-size: 0.85em;
        font-weight: 600;
        display: inline-block;
        box-shadow: 0 2px 6px rgba(16, 185, 129, 0.4);
    }

    .btn-action-group {
        display: flex;
        gap: 6px;
        justify-content: center;
        flex-wrap: wrap;
    }

    .btn-sm {
        transition: all 0.3s ease;
        padding: 0.5rem 0.85rem;
        font-size: 0.875rem;
        font-weight: 500;
        border: none;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    .btn-sm i {
        font-size: 1rem;
    }

    .btn-sm:hover {
        transform: translateY(-3px);
        box-shadow: 0 6px 12px rgba(0,0,0,0.25);
    }

    .btn-sm:active {
        transform: translateY(-1px);
    }

    .btn-info {
        background: linear-gradient(135deg, #0ea5e9 0%, #06b6d4 100%);
    }

    .btn-info:hover {
        background: linear-gradient(135deg, #0284c7 0%, #0891b2 100%);
    }

    .btn-primary {
        background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
    }

    .btn-primary:hover {
        background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
    }

    .btn-warning {
        background: linear-gradient(135deg, #f59e0b 0%, #f97316 100%);
    }

    .btn-warning:hover {
        background: linear-gradient(135deg, #ea580c 0%, #dc2626 100%);
    }

    .btn-danger {
        background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
    }

    .btn-danger:hover {
        background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);
    }

    .card {
        border: none;
        border-radius: 12px;
        box-shadow: 0 4px 24px rgba(0,0,0,0.1);
    }

    .card-header {
        border-radius: 12px 12px 0 0 !important;
        border-bottom: 3px solid #e5e7eb;
    }

    .alert-info {
        border-left: 5px solid #3b82f6;
        background-color: #eff6ff;
        border-color: #bfdbfe;
        box-shadow: 0 2px 8px rgba(59, 130, 246, 0.1);
    }

    /* Loading animation */
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

    .spinner-border {
        width: 3rem;
        height: 3rem;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .jadwal-header th {
            font-size: 0.8rem;
            padding: 10px 6px;
        }
        
        .jadwal-cell {
            padding: 12px 8px;
            font-size: 0.85rem;
        }

        .btn-sm {
            padding: 0.4rem 0.6rem;
            font-size: 0.8rem;
        }

        .btn-sm i {
            font-size: 0.9rem;
        }
        
        .hari-title {
            font-size: 1rem;
            padding: 12px 15px;
        }

        .btn-action-group {
            gap: 4px;
        }
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
$(document).ready(function() {
    let modalJadwal = new bootstrap.Modal(document.getElementById('modalJadwal'));
    let modalLihat = new bootstrap.Modal(document.getElementById('modalLihat'));
    let modalPindah = new bootstrap.Modal(document.getElementById('modalPindah'));
    let modalImport = new bootstrap.Modal(document.getElementById('modalImport'));
    
    // Load initial data
    loadJadwal();
    loadDosen();
    loadAllRuangan(); // Load semua ruangan untuk filter nanti

    // Filter change
    $('#filterProdi, #filterSemester').change(function() {
        loadJadwal();
    });

    // Load Jadwal
    function loadJadwal() {
        const prodiId = $('#filterProdi').val();
        const semester = $('#filterSemester').val();
        
        // Show loading
        $('#jadwalContainer').html(`
            <div class="text-center py-5">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>
        `);
        
        $.ajax({
            url: '{{ route("jadwal.index") }}',
            method: 'GET',
            data: { 
                prodi_id: prodiId, 
                semester: semester 
            },
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            success: function(response) {
                displayJadwal(response.jadwals, response.prodi);
            },
            error: function(xhr) {
                console.error('Error loading jadwal:', xhr);
                $('#jadwalContainer').html(`
                    <div class="alert alert-danger">
                        <i class="bi bi-exclamation-triangle"></i> Gagal memuat data jadwal. Silakan refresh halaman.
                    </div>
                `);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Gagal memuat data jadwal'
                });
            }
        });
    }

    // Display Jadwal dengan 5 slot per hari
    function displayJadwal(jadwals, prodi) {
        let html = '';
        const hariOrder = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        const maxSlots = 5; // Jumlah slot yang ditampilkan per hari
        
        hariOrder.forEach(hari => {
            html += `
                <div class="hari-section">
                    <h5 class="hari-title">${hari}</h5>
                    <table class="jadwal-table table table-bordered">
                        <thead class="jadwal-header">
                            <tr>
                                <th width="15%">WAKTU</th>
                                <th width="48%">${prodi.nama_prodi}</th>
                                <th width="12%">RUANGAN</th>
                                <th width="25%">AKSI</th>
                            </tr>
                        </thead>
                        <tbody>`;
            
            // Ambil jadwal untuk hari ini atau buat array kosong
            const jadwalHari = jadwals[hari] || [];
            
            // Tampilkan maksimal 5 slot
            for (let i = 0; i < maxSlots; i++) {
                if (i < jadwalHari.length) {
                    // Ada jadwal, tampilkan data jadwal
                    const j = jadwalHari[i];
                    const waktu = `${j.jam_mulai.substring(0,5)} - ${j.jam_selesai.substring(0,5)}`;
                    html += `
                        <tr class="jadwal-row">
                            <td class="jadwal-cell">${waktu}</td>
                            <td class="jadwal-cell">
                                <strong>${j.mata_kuliah.nama_mk}</strong>
                                <span class="badge-sks ms-2">${j.mata_kuliah.sks} SKS</span><br>
                                <em class="text-muted">${j.dosen.nama}</em>
                            </td>
                            <td class="jadwal-cell">${j.ruangan.nama_ruangan}</td>
                            <td class="jadwal-cell">
                                <div class="btn-action-group">
                                    <button class="btn btn-sm btn-info btn-lihat" data-id="${j.id}" title="Lihat Detail">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                    <button class="btn btn-sm btn-primary btn-edit" data-id="${j.id}" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <button class="btn btn-sm btn-warning btn-pindah" data-id="${j.id}" title="Pindah">
                                        <i class="bi bi-arrow-left-right"></i>
                                    </button>
                                    <button class="btn btn-sm btn-danger btn-delete" data-id="${j.id}" title="Hapus">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>`;
                } else {
                    // Tidak ada jadwal, tampilkan slot kosong
                    html += `
                        <tr class="jadwal-row-empty">
                            <td class="jadwal-cell-empty">-</td>
                            <td class="jadwal-cell-empty" colspan="2">Belum ada jadwal</td>
                            <td class="jadwal-cell-empty">-</td>
                        </tr>`;
                }
            }
            
            html += `
                        </tbody>
                    </table>
                </div>`;
        });
        
        $('#jadwalContainer').html(html);
    }

    // Load Dosen
    function loadDosen() {
        $.ajax({
            url: '{{ route("dosen.index") }}',
            method: 'GET',
            success: function(response) {
                let options = '<option value="">Pilih Dosen</option>';
                response.data.forEach(function(dosen) {
                    options += `<option value="${dosen.id}">${dosen.nama}</option>`;
                });
                $('#dosen_id').html(options);
            },
            error: function(xhr) {
                console.error('Error loading dosen:', xhr);
            }
        });
    }

    // Load All Ruangan (akan difilter berdasarkan fakultas)
    let allRuangans = [];
    function loadAllRuangan() {
        $.ajax({
            url: '{{ route("ruangan.index") }}',
            method: 'GET',
            success: function(response) {
                allRuangans = response.data;
            },
            error: function(xhr) {
                console.error('Error loading ruangan:', xhr);
            }
        });
    }

    // Filter Ruangan berdasarkan Fakultas dari Prodi
    function filterRuanganByProdi(prodiId, targetSelect) {
        const selectedProdi = $(`#filterProdi option[value="${prodiId}"]`);
        const fakultasId = selectedProdi.data('fakultas-id');
        
        // Filter ruangan berdasarkan fakultas_id
        const filteredRuangans = allRuangans.filter(r => r.fakultas_id == fakultasId);
        
        let options = '<option value="">Pilih Ruangan</option>';
        filteredRuangans.forEach(function(ruangan) {
            options += `<option value="${ruangan.id}">${ruangan.nama_ruangan} (${ruangan.kode_ruangan}) - Kapasitas: ${ruangan.kapasitas}</option>`;
        });
        
        $(targetSelect).html(options);
    }

    // Load Mata Kuliah by Prodi
    $('#prodi_id').change(function() {
        const prodiId = $(this).val();
        const targetMK = '#mata_kuliah_id';
        const targetRuangan = '#ruangan_id';
        
        if (prodiId) {
            // Load Mata Kuliah
            $.ajax({
                url: `/jadwal/mata-kuliah/${prodiId}`,
                method: 'GET',
                success: function(response) {
                    let options = '<option value="">Pilih Mata Kuliah</option>';
                    response.forEach(function(mk) {
                        options += `<option value="${mk.id}" data-sks="${mk.sks}">${mk.nama_mk} (${mk.kode_mk}) - ${mk.sks} SKS</option>`;
                    });
                    $(targetMK).html(options);
                },
                error: function(xhr) {
                    console.error('Error loading mata kuliah:', xhr);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Gagal memuat mata kuliah'
                    });
                }
            });
            
            // Filter Ruangan berdasarkan Fakultas
            filterRuanganByProdi(prodiId, targetRuangan);
        } else {
            $(targetMK).html('<option value="">Pilih Mata Kuliah</option>');
            $(targetRuangan).html('<option value="">Pilih Ruangan</option>');
        }
    });

    // Hitung Jam Selesai otomatis berdasarkan SKS
    function hitungJamSelesai(jamMulai, sks) {
        if (!jamMulai || !sks) return '';
        
        const [hours, minutes] = jamMulai.split(':');
        const startTime = new Date();
        startTime.setHours(parseInt(hours));
        startTime.setMinutes(parseInt(minutes));
        
        // 1 SKS = 50 menit
        const durasi = sks * 50;
        startTime.setMinutes(startTime.getMinutes() + durasi);
        
        const endHours = String(startTime.getHours()).padStart(2, '0');
        const endMinutes = String(startTime.getMinutes()).padStart(2, '0');
        
        return `${endHours}:${endMinutes}`;
    }

    // Event handler untuk perubahan mata kuliah dan jam mulai (Tambah)
    $('#mata_kuliah_id').change(function() {
        updateJamSelesai('#jam_mulai', '#mata_kuliah_id', '#jam_selesai_display');
    });
    
    $('#jam_mulai').change(function() {
        updateJamSelesai('#jam_mulai', '#mata_kuliah_id', '#jam_selesai_display');
    });

    // Event handler untuk perubahan jam mulai (Pindah)
    $('#pindah_jam_mulai').change(function() {
        const sks = $('#pindah_sks').val();
        const jamMulai = $(this).val();
        if (sks && jamMulai) {
            const jamSelesai = hitungJamSelesai(jamMulai, sks);
            $('#pindah_jam_selesai_display').text(jamSelesai);
        }
    });

    function updateJamSelesai(jamMulaiSelector, mkSelector, displaySelector) {
        const jamMulai = $(jamMulaiSelector).val();
        const selectedMK = $(mkSelector + ' option:selected');
        const sks = selectedMK.data('sks');
        
        if (jamMulai && sks) {
            const jamSelesai = hitungJamSelesai(jamMulai, sks);
            $(displaySelector).text(jamSelesai);
        } else {
            $(displaySelector).text('-');
        }
    }

    // Tambah Jadwal
    $('#btnTambah').click(function() {
        $('#formJadwal')[0].reset();
        $('#jadwal_id').val('');
        $('#modalJadwalTitle').text('Tambah Jadwal');
        $('#prodi_id').val($('#filterProdi').val()).trigger('change');
        $('#semester').val($('#filterSemester').val());
        $('#jam_selesai_display').text('-');
        modalJadwal.show();
    });

    // Submit Form Jadwal (Tambah/Edit)
    $('#formJadwal').submit(function(e) {
        e.preventDefault();
        
        // Validasi form
        const jamMulai = $('#jam_mulai').val();
        const selectedMK = $('#mata_kuliah_id option:selected');
        const sks = selectedMK.data('sks');
        
        if (!jamMulai || !sks) {
            Swal.fire({
                icon: 'warning',
                title: 'Perhatian',
                text: 'Mohon lengkapi semua field yang diperlukan'
            });
            return;
        }
        
        const jamSelesai = hitungJamSelesai(jamMulai, sks);
        
        // Prepare form data
        const formDataArray = $(this).serializeArray();
        formDataArray.push({name: 'jam_selesai', value: jamSelesai});
        
        const id = $('#jadwal_id').val();
        const url = id ? `/jadwal/${id}` : '{{ route("jadwal.store") }}';
        const method = id ? 'PUT' : 'POST';
        
        // Show loading
        Swal.fire({
            title: 'Menyimpan...',
            text: 'Mohon tunggu sebentar',
            allowOutsideClick: false,
            allowEscapeKey: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
        
        $.ajax({
            url: url,
            method: method,
            data: $.param(formDataArray),
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                modalJadwal.hide();
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: response.success,
                    showConfirmButton: false,
                    timer: 1500
                });
                loadJadwal();
            },
            error: function(xhr) {
                console.error('Error submit jadwal:', xhr);
                let errorMessage = 'Terjadi kesalahan saat menyimpan jadwal';
                
                if (xhr.responseJSON) {
                    if (xhr.responseJSON.error) {
                        errorMessage = xhr.responseJSON.error;
                    } else if (xhr.responseJSON.errors) {
                        // Handle validation errors
                        const errors = xhr.responseJSON.errors;
                        errorMessage = Object.values(errors).flat().join('<br>');
                    }
                }
                
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    html: errorMessage
                });
            }
        });
    });

    // Lihat Detail Jadwal
    $(document).on('click', '.btn-lihat', function() {
        const id = $(this).data('id');
        
        $.ajax({
            url: `/jadwal/${id}/edit`,
            method: 'GET',
            success: function(response) {
                const j = response.jadwal;
                const waktu = `${j.jam_mulai.substring(0,5)} - ${j.jam_selesai.substring(0,5)}`;
                
                $('#lihat_prodi').text(j.prodi.nama_prodi);
                $('#lihat_matakuliah').text(`${j.mata_kuliah.nama_mk} (${j.mata_kuliah.kode_mk})`);
                $('#lihat_sks').text(`${j.mata_kuliah.sks} SKS`);
                $('#lihat_dosen').text(j.dosen.nama);
                $('#lihat_ruangan').text(`${j.ruangan.nama_ruangan} (${j.ruangan.kode_ruangan})`);
                $('#lihat_hari').text(j.hari);
                $('#lihat_waktu').text(waktu);
                $('#lihat_semester').text(`Semester ${j.semester}`);
                $('#lihat_keterangan').text(j.keterangan || '-');
                
                modalLihat.show();
            },
            error: function(xhr) {
                console.error('Error loading jadwal detail:', xhr);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Gagal memuat detail jadwal'
                });
            }
        });
    });

    // Edit Jadwal
    $(document).on('click', '.btn-edit', function() {
        const id = $(this).data('id');
        
        // Show loading
        Swal.fire({
            title: 'Memuat data...',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
        
        $.ajax({
            url: `/jadwal/${id}/edit`,
            method: 'GET',
            success: function(response) {
                Swal.close();
                
                $('#jadwal_id').val(response.jadwal.id);
                $('#prodi_id').val(response.jadwal.prodi_id);
                
                // Trigger change untuk load mata kuliah dan ruangan
                $('#prodi_id').trigger('change');
                
                // Tunggu mata kuliah dan ruangan ter-load
                setTimeout(function() {
                    $('#mata_kuliah_id').val(response.jadwal.mata_kuliah_id);
                    $('#ruangan_id').val(response.jadwal.ruangan_id);
                    $('#dosen_id').val(response.jadwal.dosen_id);
                    $('#hari').val(response.jadwal.hari);
                    $('#jam_mulai').val(response.jadwal.jam_mulai.substring(0,5));
                    $('#semester').val(response.jadwal.semester);
                    $('#keterangan').val(response.jadwal.keterangan);
                    
                    // Update display jam selesai
                    updateJamSelesai('#jam_mulai', '#mata_kuliah_id', '#jam_selesai_display');
                }, 500);
                
                $('#modalJadwalTitle').text('Edit Jadwal');
                modalJadwal.show();
            },
            error: function(xhr) {
                console.error('Error loading jadwal for edit:', xhr);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Gagal memuat data jadwal'
                });
            }
        });
    });

    // Pindah Jadwal
    $(document).on('click', '.btn-pindah', function() {
        const id = $(this).data('id');
        
        // Show loading
        Swal.fire({
            title: 'Memuat data...',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
        
        $.ajax({
            url: `/jadwal/${id}/edit`,
            method: 'GET',
            success: function(response) {
                Swal.close();
                
                const j = response.jadwal;
                
                $('#pindah_jadwal_id').val(j.id);
                $('#pindah_info_mk').text(`${j.mata_kuliah.nama_mk} (${j.mata_kuliah.sks} SKS)`);
                $('#pindah_info_dosen').text(j.dosen.nama);
                $('#pindah_info_jadwal_lama').text(`${j.hari}, ${j.jam_mulai.substring(0,5)} - ${j.jam_selesai.substring(0,5)}, ${j.ruangan.nama_ruangan}`);
                $('#pindah_sks').val(j.mata_kuliah.sks);
                
                // Filter ruangan berdasarkan fakultas prodi
                filterRuanganByProdi(j.prodi_id, '#pindah_ruangan_id');
                
                $('#formPindah')[0].reset();
                $('#pindah_jam_selesai_display').text('-');
                
                modalPindah.show();
            },
            error: function(xhr) {
                console.error('Error loading jadwal for pindah:', xhr);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Gagal memuat data jadwal'
                });
            }
        });
    });

    // Submit Pindah Jadwal
    $('#formPindah').submit(function(e) {
        e.preventDefault();
        
        const jamMulai = $('#pindah_jam_mulai').val();
        const sks = $('#pindah_sks').val();
        
        if (!jamMulai || !sks) {
            Swal.fire({
                icon: 'warning',
                title: 'Perhatian',
                text: 'Mohon lengkapi semua field yang diperlukan'
            });
            return;
        }
        
        const jamSelesai = hitungJamSelesai(jamMulai, sks);
        
        const formDataArray = $(this).serializeArray();
        formDataArray.push({name: 'jam_selesai', value: jamSelesai});
        
        const id = $('#pindah_jadwal_id').val();
        
        // Show loading
        Swal.fire({
            title: 'Memindahkan jadwal...',
            text: 'Mohon tunggu sebentar',
            allowOutsideClick: false,
            allowEscapeKey: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
        
        $.ajax({
            url: `/jadwal/${id}/pindah`,
            method: 'POST',
            data: $.param(formDataArray),
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                modalPindah.hide();
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: response.success,
                    showConfirmButton: false,
                    timer: 1500
                });
                loadJadwal();
            },
            error: function(xhr) {
                console.error('Error pindah jadwal:', xhr);
                let errorMessage = 'Terjadi kesalahan saat memindahkan jadwal';
                
                if (xhr.responseJSON) {
                    if (xhr.responseJSON.error) {
                        errorMessage = xhr.responseJSON.error;
                    } else if (xhr.responseJSON.errors) {
                        const errors = xhr.responseJSON.errors;
                        errorMessage = Object.values(errors).flat().join('<br>');
                    }
                }
                
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    html: errorMessage
                });
            }
        });
    });

    // Delete Jadwal
    $(document).on('click', '.btn-delete', function() {
        const id = $(this).data('id');
        
        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: "Jadwal akan dihapus permanen!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                // Show loading
                Swal.fire({
                    title: 'Menghapus...',
                    text: 'Mohon tunggu sebentar',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
                
                $.ajax({
                    url: `/jadwal/${id}`,
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Terhapus!',
                            text: response.success,
                            showConfirmButton: false,
                            timer: 1500
                        });
                        loadJadwal();
                    },
                    error: function(xhr) {
                        console.error('Error deleting jadwal:', xhr);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: 'Gagal menghapus jadwal'
                        });
                    }
                });
            }
        });
    });

    // Export PDF
    $('#exportPdf').click(function(e) {
        e.preventDefault();
        const prodiId = $('#filterProdi').val();
        const semester = $('#filterSemester').val();
        window.location.href = `/jadwal/export/pdf?prodi_id=${prodiId}&semester=${semester}`;
    });

    // Export Excel
    $('#exportExcel').click(function(e) {
        e.preventDefault();
        const prodiId = $('#filterProdi').val();
        const semester = $('#filterSemester').val();
        window.location.href = `/jadwal/export/excel?prodi_id=${prodiId}&semester=${semester}`;
    });

    // Import
    $('#btnImport').click(function() {
        modalImport.show();
    });

    $('#formImport').submit(function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        
        // Show loading
        Swal.fire({
            title: 'Mengimport data...',
            text: 'Mohon tunggu, proses ini mungkin memakan waktu',
            allowOutsideClick: false,
            allowEscapeKey: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
        
        $.ajax({
            url: '{{ route("jadwal.import") }}',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                modalImport.hide();
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: response.success
                });
                loadJadwal();
            },
            error: function(xhr) {
                console.error('Error importing:', xhr);
                let errorMessage = 'Gagal import data';
                
                if (xhr.responseJSON && xhr.responseJSON.error) {
                    errorMessage = xhr.responseJSON.error;
                }
                
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: errorMessage
                });
            }
        });
    });
});
</script>
@endpush

@endsection