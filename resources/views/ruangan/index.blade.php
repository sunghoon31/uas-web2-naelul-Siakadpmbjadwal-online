@extends('layouts.app')

@section('title', 'Manajemen Ruangan')

@section('head')
<meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@push('styles')
<link href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
<style>
    .card {
        border: none;
        border-radius: 15px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.1);
    }
    .card-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 15px 15px 0 0 !important;
        padding: 1.5rem;
    }
    .stats-card {
        border-radius: 10px;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
        background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
        transition: transform 0.3s ease;
    }
    .stats-card:hover {
        transform: translateY(-5px);
    }
    .btn-gradient {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border: none;
        color: white;
    }
    .btn-gradient:hover {
        background: linear-gradient(135deg, #764ba2 0%, #667eea 100%);
        color: white;
    }
    .modal-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }
    .table-responsive {
        border-radius: 10px;
        overflow: hidden;
    }
    .dataTables_wrapper .dataTables_filter input {
        border-radius: 20px;
        padding: 0.5rem 1rem;
        border: 2px solid #e0e0e0;
    }
    .dataTables_wrapper .dataTables_length select {
        border-radius: 20px;
        padding: 0.3rem 0.5rem;
        border: 2px solid #e0e0e0;
    }
    .page-item.active .page-link {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-color: #667eea;
    }
    .icon-box {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        margin-bottom: 1rem;
    }
</style>
@endpush

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-md-12">
            <h1 class="display-6 fw-bold">
                <i class="fas fa-door-open text-primary"></i> Manajemen Ruangan
            </h1>
            <p class="text-muted">Kelola data ruangan perkuliahan dengan mudah</p>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="stats-card">
                <div class="icon-box bg-primary bg-gradient text-white mx-auto">
                    <i class="fas fa-door-open"></i>
                </div>
                <h3 class="text-center mb-0" id="totalRuangan">0</h3>
                <p class="text-center text-muted mb-0">Total Ruangan</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card">
                <div class="icon-box bg-success bg-gradient text-white mx-auto">
                    <i class="fas fa-chalkboard"></i>
                </div>
                <h3 class="text-center mb-0" id="totalKelas">0</h3>
                <p class="text-center text-muted mb-0">Ruang Kelas</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card">
                <div class="icon-box bg-info bg-gradient text-white mx-auto">
                    <i class="fas fa-flask"></i>
                </div>
                <h3 class="text-center mb-0" id="totalLab">0</h3>
                <p class="text-center text-muted mb-0">Laboratorium</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card">
                <div class="icon-box bg-warning bg-gradient text-white mx-auto">
                    <i class="fas fa-paint-brush"></i>
                </div>
                <h3 class="text-center mb-0" id="totalStudio">0</h3>
                <p class="text-center text-muted mb-0">Studio</p>
            </div>
        </div>
    </div>

    <!-- Main Card -->
    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="fas fa-list"></i> Daftar Ruangan
                </h5>
                <div class="btn-group" role="group">
                    <button type="button" class="btn btn-light" id="btnTambah">
                        <i class="fas fa-plus"></i> Tambah
                    </button>
                    <button type="button" class="btn btn-light" id="btnImport">
                        <i class="fas fa-file-upload"></i> Import
                    </button>
                    <button type="button" class="btn btn-light" id="btnExportExcel">
                        <i class="fas fa-file-excel"></i> Excel
                    </button>
                    <button type="button" class="btn btn-light" id="btnExportPdf">
                        <i class="fas fa-file-pdf"></i> PDF
                    </button>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover" id="tableRuangan">
                    <thead class="table-light">
                        <tr>
                            <th width="5%">No</th>
                            <th>Kode</th>
                            <th>Nama Ruangan</th>
                            <th>Fakultas</th>
                            <th>Jenis</th>
                            <th>Kapasitas</th>
                            <th width="12%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Form -->
<div class="modal fade" id="modalForm" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-door-open"></i> <span id="modalTitle">Tambah Ruangan</span>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="formRuangan">
                <div class="modal-body">
                    <input type="hidden" id="ruanganId" name="id">
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">
                                    <i class="fas fa-university text-primary"></i> Fakultas <span class="text-danger">*</span>
                                </label>
                                <select class="form-select" id="fakultas_id" name="fakultas_id" required>
                                    <option value="">Pilih Fakultas</option>
                                    @foreach($fakultas as $f)
                                        <option value="{{ $f->id }}">{{ $f->nama_fakultas }}</option>
                                    @endforeach
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">
                                    <i class="fas fa-tag text-primary"></i> Kode Ruangan <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control" id="kode_ruangan" name="kode_ruangan" placeholder="Contoh: F1-R101" required>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">
                            <i class="fas fa-door-open text-primary"></i> Nama Ruangan <span class="text-danger">*</span>
                        </label>
                        <input type="text" class="form-control" id="nama_ruangan" name="nama_ruangan" placeholder="Contoh: Ruang Kelas A1" required>
                        <div class="invalid-feedback"></div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">
                                    <i class="fas fa-users text-primary"></i> Kapasitas <span class="text-danger">*</span>
                                </label>
                                <input type="number" class="form-control" id="kapasitas" name="kapasitas" min="1" max="500" placeholder="Jumlah kapasitas" required>
                                <small class="text-muted">Maksimal 500 orang</small>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">
                                    <i class="fas fa-layer-group text-primary"></i> Jenis Ruangan <span class="text-danger">*</span>
                                </label>
                                <select class="form-select" id="jenis" name="jenis" required>
                                    <option value="">Pilih Jenis</option>
                                    <option value="Kelas">Kelas</option>
                                    <option value="Lab">Laboratorium</option>
                                    <option value="Studio">Studio</option>
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times"></i> Batal
                    </button>
                    <button type="submit" class="btn btn-gradient">
                        <i class="fas fa-save"></i> Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Import -->
<div class="modal fade" id="modalImport" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-file-upload"></i> Import Data Ruangan
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="formImport" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        <strong>Petunjuk:</strong>
                        <ul class="mb-0 mt-2">
                            <li>Download template terlebih dahulu</li>
                            <li>Isi data sesuai format template</li>
                            <li>Upload file Excel (.xlsx, .xls, .csv)</li>
                            <li>Maksimal ukuran file 5MB</li>
                        </ul>
                    </div>

                    <div class="mb-3">
                        <a href="{{ route('ruangan.template') }}" class="btn btn-sm btn-success w-100">
                            <i class="fas fa-download"></i> Download Template
                        </a>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">
                            <i class="fas fa-file-excel text-primary"></i> Pilih File Excel
                        </label>
                        <input type="file" class="form-control" name="file" accept=".xlsx,.xls,.csv" required>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times"></i> Batal
                    </button>
                    <button type="submit" class="btn btn-gradient">
                        <i class="fas fa-upload"></i> Import
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
$(document).ready(function() {
    // Setup CSRF Token untuk semua AJAX requests
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    // Initialize DataTable
    const table = $('#tableRuangan').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('ruangan.index') }}",
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'kode_ruangan', name: 'kode_ruangan' },
            { data: 'nama_ruangan', name: 'nama_ruangan' },
            { data: 'fakultas_nama', name: 'fakultas.nama_fakultas' },
            { data: 'jenis_badge', name: 'jenis' },
            { data: 'kapasitas_info', name: 'kapasitas' },
            { data: 'aksi', name: 'aksi', orderable: false, searchable: false }
        ],
        language: {
            processing: '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Loading...</span>',
            search: "_INPUT_",
            searchPlaceholder: "Cari ruangan...",
            lengthMenu: "Tampilkan _MENU_ data",
            info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
            infoEmpty: "Menampilkan 0 sampai 0 dari 0 data",
            infoFiltered: "(difilter dari _MAX_ total data)",
            paginate: {
                first: "Pertama",
                last: "Terakhir",
                next: "Selanjutnya",
                previous: "Sebelumnya"
            }
        },
        drawCallback: function() {
            updateStatistics();
        }
    });

    // Update Statistics
    function updateStatistics() {
        $.ajax({
            url: "{{ route('ruangan.index') }}",
            data: { stats: true },
            success: function(response) {
                if (response.data) {
                    let totalKelas = 0, totalLab = 0, totalStudio = 0;
                    response.data.forEach(item => {
                        if (item.jenis === 'Kelas') totalKelas++;
                        else if (item.jenis === 'Lab') totalLab++;
                        else if (item.jenis === 'Studio') totalStudio++;
                    });
                    $('#totalRuangan').text(response.data.length);
                    $('#totalKelas').text(totalKelas);
                    $('#totalLab').text(totalLab);
                    $('#totalStudio').text(totalStudio);
                }
            }
        });
    }

    // Tambah Data
    $('#btnTambah').click(function() {
        $('#modalTitle').text('Tambah Ruangan');
        $('#formRuangan')[0].reset();
        $('#ruanganId').val('');
        $('.is-invalid').removeClass('is-invalid');
        $('#modalForm').modal('show');
    });

    // Submit Form
    $('#formRuangan').submit(function(e) {
        e.preventDefault();
        
        const id = $('#ruanganId').val();
        const url = id ? `/ruangan/${id}` : "{{ route('ruangan.store') }}";
        const method = id ? 'PUT' : 'POST';

        $.ajax({
            url: url,
            type: method,
            data: $(this).serialize(),
            beforeSend: function() {
                $('.is-invalid').removeClass('is-invalid');
                Swal.fire({
                    title: 'Menyimpan...',
                    allowOutsideClick: false,
                    didOpen: () => Swal.showLoading()
                });
            },
            success: function(response) {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: response.message,
                    showConfirmButton: false,
                    timer: 1500
                });
                $('#modalForm').modal('hide');
                table.ajax.reload();
            },
            error: function(xhr) {
                Swal.close();
                if (xhr.status === 422) {
                    const errors = xhr.responseJSON.errors;
                    $.each(errors, function(key, value) {
                        $(`#${key}`).addClass('is-invalid')
                            .siblings('.invalid-feedback').text(value[0]);
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: xhr.responseJSON.message || 'Terjadi kesalahan!'
                    });
                }
            }
        });
    });

    // Edit Data
    $(document).on('click', '.btn-edit', function() {
        const id = $(this).data('id');
        
        $.ajax({
            url: `/ruangan/${id}/edit`,
            type: 'GET',
            beforeSend: function() {
                Swal.fire({
                    title: 'Loading...',
                    allowOutsideClick: false,
                    didOpen: () => Swal.showLoading()
                });
            },
            success: function(data) {
                Swal.close();
                $('#modalTitle').text('Edit Ruangan');
                $('#ruanganId').val(data.id);
                $('#fakultas_id').val(data.fakultas_id);
                $('#kode_ruangan').val(data.kode_ruangan);
                $('#nama_ruangan').val(data.nama_ruangan);
                $('#kapasitas').val(data.kapasitas);
                $('#jenis').val(data.jenis);
                $('#modalForm').modal('show');
            },
            error: function() {
                Swal.fire('Error!', 'Gagal mengambil data', 'error');
            }
        });
    });

    // Delete Data
    $(document).on('click', '.btn-delete', function() {
        const id = $(this).data('id');
        
        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: "Data yang dihapus tidak dapat dikembalikan!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `/ruangan/${id}`,
                    type: 'DELETE',
                    data: { _token: '{{ csrf_token() }}' },
                    beforeSend: function() {
                        Swal.fire({
                            title: 'Menghapus...',
                            allowOutsideClick: false,
                            didOpen: () => Swal.showLoading()
                        });
                    },
                    success: function(response) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Terhapus!',
                            text: response.message,
                            showConfirmButton: false,
                            timer: 1500
                        });
                        table.ajax.reload();
                    },
                    error: function(xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal!',
                            text: xhr.responseJSON.message || 'Terjadi kesalahan!'
                        });
                    }
                });
            }
        });
    });

    // Import Modal
    $('#btnImport').click(function() {
        $('#formImport')[0].reset();
        $('#modalImport').modal('show');
    });

    // Submit Import
    $('#formImport').submit(function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        formData.append('_token', $('meta[name="csrf-token"]').attr('content'));
        
        $.ajax({
            url: "{{ route('ruangan.import') }}",
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            beforeSend: function() {
                Swal.fire({
                    title: 'Mengimport...',
                    allowOutsideClick: false,
                    didOpen: () => Swal.showLoading()
                });
            },
            success: function(response) {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: response.message,
                    showConfirmButton: false,
                    timer: 1500
                });
                $('#modalImport').modal('hide');
                table.ajax.reload();
            },
            error: function(xhr) {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: xhr.responseJSON.message || 'Terjadi kesalahan!'
                });
            }
        });
    });

    // Export Excel
    $('#btnExportExcel').click(function() {
        Swal.fire({
            title: 'Export ke Excel?',
            text: "File akan didownload ke komputer Anda",
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Ya, export!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = "{{ route('ruangan.export') }}";
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: 'File sedang didownload...',
                    showConfirmButton: false,
                    timer: 1500
                });
            }
        });
    });

    // Export PDF
    $('#btnExportPdf').click(function() {
        Swal.fire({
            title: 'Export ke PDF?',
            text: "File akan didownload ke komputer Anda",
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Ya, export!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = "{{ route('ruangan.export.pdf') }}";
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: 'File sedang didownload...',
                    showConfirmButton: false,
                    timer: 1500
                });
            }
        });
    });

    // Initial statistics load
    updateStatistics();
});
</script>
@endpush