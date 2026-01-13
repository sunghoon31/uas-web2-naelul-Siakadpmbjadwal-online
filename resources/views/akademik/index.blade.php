@extends('layouts.app')

@section('content')
<div class="container-fluid px-4">
    <div class="row mb-4">
        <div class="col-12">
            <h3><i class="bi bi-bank"></i> Data Akademik</h3>
        </div>
    </div>

    <!-- NAV TABS -->
    <ul class="nav nav-tabs" id="akademikTab" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="fakultas-tab" data-bs-toggle="tab" data-bs-target="#fakultas" type="button">
                <i class="bi bi-building"></i> Fakultas
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="prodi-tab" data-bs-toggle="tab" data-bs-target="#prodi" type="button">
                <i class="bi bi-mortarboard"></i> Program Studi
            </button>
        </li>
    </ul>

    <!-- TAB CONTENT -->
    <div class="tab-content border border-top-0 p-4 bg-white shadow-sm" id="akademikTabContent">
        
        <!-- ========== TAB FAKULTAS ========== -->
        <div class="tab-pane fade show active" id="fakultas" role="tabpanel">
            <div class="row mb-3">
                <div class="col-md-6">
                    <h5 class="mb-0"><i class="bi bi-list-ul"></i> Daftar Fakultas</h5>
                </div>
                <div class="col-md-6 text-end">
                    <button class="btn btn-sm btn-primary" id="btn-import-fakultas">
                        <i class="bi bi-file-earmark-arrow-up"></i> Import
                    </button>
                    <a href="{{ route('fakultas.export') }}" class="btn btn-sm btn-success">
                        <i class="bi bi-file-earmark-excel"></i> Export
                    </a>
                    <button class="btn btn-sm btn-info text-white" id="btn-add-fakultas">
                        <i class="bi bi-plus-circle"></i> Tambah Fakultas
                    </button>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover w-100" id="fakultasTable">
                            <thead class="table-dark">
                                <tr>
                                    <th width="50">No</th>
                                    <th>Nama Fakultas</th>
                                    <th width="150">Jumlah Prodi</th>
                                    <th width="180">Aksi</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- ========== TAB PRODI ========== -->
        <div class="tab-pane fade" id="prodi" role="tabpanel">
            <div class="row mb-3">
                <div class="col-md-6">
                    <h5 class="mb-0"><i class="bi bi-list-ul"></i> Daftar Program Studi</h5>
                </div>
                <div class="col-md-6 text-end">
                    <button class="btn btn-sm btn-primary" id="btn-import-prodi">
                        <i class="bi bi-file-earmark-arrow-up"></i> Import
                    </button>
                    <a href="{{ route('prodi.export') }}" class="btn btn-sm btn-success">
                        <i class="bi bi-file-earmark-excel"></i> Export
                    </a>
                    <button class="btn btn-sm btn-info text-white" id="btn-add-prodi">
                        <i class="bi bi-plus-circle"></i> Tambah Prodi
                    </button>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover w-100" id="prodiTable">
                            <thead class="table-dark">
                                <tr>
                                    <th width="50">No</th>
                                    <th>Nama Program Studi</th>
                                    <th width="250">Fakultas</th>
                                    <th width="180">Aksi</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<!-- ========== MODAL FAKULTAS ========== -->
<div class="modal fade" id="modalFakultas" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title" id="modalFakultasTitle">
                    <i class="bi bi-building"></i> Tambah Fakultas
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="formFakultas">
                @csrf
                <input type="hidden" id="fakultas_id" name="id">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">
                            Nama Fakultas <span class="text-danger">*</span>
                        </label>
                        <input type="text" name="nama_fakultas" id="nama_fakultas" class="form-control" placeholder="Contoh: Fakultas Teknik" required>
                        <small class="text-muted">Nama fakultas harus unik</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle"></i> Batal
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save"></i> Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ========== MODAL PRODI ========== -->
<div class="modal fade" id="modalProdi" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title" id="modalProdiTitle">
                    <i class="bi bi-mortarboard"></i> Tambah Program Studi
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="formProdi">
                @csrf
                <input type="hidden" id="prodi_id" name="id">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">
                            Fakultas <span class="text-danger">*</span>
                        </label>
                        <select name="fakultas_id" id="fakultas_id_select" class="form-select" required>
                            <option value="">-- Pilih Fakultas --</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">
                            Nama Program Studi <span class="text-danger">*</span>
                        </label>
                        <input type="text" name="nama_prodi" id="nama_prodi" class="form-control" placeholder="Contoh: Teknik Informatika" required>
                        <small class="text-muted">Nama prodi harus unik</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle"></i> Batal
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save"></i> Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ========== MODAL IMPORT FAKULTAS ========== -->
<div class="modal fade" id="importModalFakultas" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">
                    <i class="bi bi-file-earmark-arrow-up"></i> Import Data Fakultas
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="formImportFakultas" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Pilih File Excel</label>
                        <input type="file" name="file" class="form-control" accept=".xlsx,.xls,.csv" required>
                        <small class="text-muted d-block mt-2">
                            <i class="bi bi-info-circle"></i> Format: <code>.xlsx</code>, <code>.xls</code>, atau <code>.csv</code>
                        </small>
                    </div>
                    <div class="alert alert-info mb-0">
                        <strong><i class="bi bi-lightbulb"></i> Template Excel:</strong><br>
                        <div class="mt-2">Header: <code>nama_fakultas</code></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-upload"></i> Upload & Import
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ========== MODAL IMPORT PRODI ========== -->
<div class="modal fade" id="importModalProdi" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">
                    <i class="bi bi-file-earmark-arrow-up"></i> Import Data Program Studi
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="formImportProdi" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Pilih File Excel</label>
                        <input type="file" name="file" class="form-control" accept=".xlsx,.xls,.csv" required>
                        <small class="text-muted d-block mt-2">
                            <i class="bi bi-info-circle"></i> Format: <code>.xlsx</code>, <code>.xls</code>, atau <code>.csv</code>
                        </small>
                    </div>
                    <div class="alert alert-info mb-0">
                        <strong><i class="bi bi-lightbulb"></i> Template Excel:</strong><br>
                        <div class="mt-2">Header: <code>nama_program_studi</code> | <code>fakultas</code></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-upload"></i> Upload & Import
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    /* Pastikan tabel full width */
    .table-responsive {
        overflow-x: auto;
        width: 100%;
    }
    
    #fakultasTable, #prodiTable {
        width: 100% !important;
        margin: 0;
    }
    
    /* DataTables wrapper full width */
    .dataTables_wrapper {
        width: 100%;
    }
    
    /* Konsistensi padding */
    .card-body {
        padding: 1.5rem;
    }
    
    /* Tab content consistent */
    .tab-content {
        min-height: 400px;
    }
</style>
@endpush

@push('scripts')
<script>
$(function () {
    let fakultasTable, prodiTable;

    // ========== FAKULTAS DATATABLE ==========
    fakultasTable = $('#fakultasTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('fakultas.index') }}",
        order: [[1, 'asc']],
        scrollX: false,
        autoWidth: false,
        columns: [
            { data: 'DT_RowIndex', orderable: false, searchable: false, width: '50px' },
            { data: 'nama_fakultas', width: 'auto' },
            { data: 'jumlah_prodi', searchable: false, width: '150px' },
            { data: 'aksi', orderable: false, searchable: false, width: '180px' }
        ],
        language: {
            processing: "Memuat data...",
            search: "Cari:",
            lengthMenu: "Tampilkan _MENU_ data",
            info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
            infoEmpty: "Menampilkan 0 sampai 0 dari 0 data",
            infoFiltered: "(difilter dari _MAX_ total data)",
            paginate: {
                first: "Pertama",
                last: "Terakhir",
                next: "Selanjutnya",
                previous: "Sebelumnya"
            },
            emptyTable: "Tidak ada data fakultas"
        }
    });

    // ========== PRODI DATATABLE ==========
    prodiTable = $('#prodiTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('prodi.index') }}",
        order: [[1, 'asc']],
        scrollX: false,
        autoWidth: false,
        columns: [
            { data: 'DT_RowIndex', orderable: false, searchable: false, width: '50px' },
            { data: 'nama_prodi', width: 'auto' },
            { data: 'fakultas_nama', width: '250px' },
            { data: 'aksi', orderable: false, searchable: false, width: '180px' }
        ],
        language: {
            processing: "Memuat data...",
            search: "Cari:",
            lengthMenu: "Tampilkan _MENU_ data",
            info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
            infoEmpty: "Menampilkan 0 sampai 0 dari 0 data",
            infoFiltered: "(difilter dari _MAX_ total data)",
            paginate: {
                first: "Pertama",
                last: "Terakhir",
                next: "Selanjutnya",
                previous: "Sebelumnya"
            },
            emptyTable: "Tidak ada data program studi"
        }
    });

    // Adjust column width saat tab switch
    $('button[data-bs-toggle="tab"]').on('shown.bs.tab', function (e) {
        $.fn.dataTable.tables({ visible: true, api: true }).columns.adjust();
    });

    // ========== LOAD FAKULTAS UNTUK SELECT OPTION ==========
    function loadFakultasOptions() {
        $.get("{{ route('prodi.create') }}", function(data) {
            let options = '<option value="">-- Pilih Fakultas --</option>';
            data.forEach(function(fakultas) {
                options += `<option value="${fakultas.id}">${fakultas.nama_fakultas}</option>`;
            });
            $('#fakultas_id_select').html(options);
        });
    }

    // ========== ADD FAKULTAS ==========
    $('#btn-add-fakultas').click(function() {
        $('#formFakultas')[0].reset();
        $('#fakultas_id').val('');
        $('#modalFakultasTitle').html('<i class="bi bi-building"></i> Tambah Fakultas');
        $('#modalFakultas').modal('show');
    });

    // ========== SUBMIT FAKULTAS ==========
    $('#formFakultas').submit(function(e) {
        e.preventDefault();
        let id = $('#fakultas_id').val();
        let url = id ? `/fakultas/${id}` : "{{ route('fakultas.store') }}";
        let submitBtn = $(this).find('button[type="submit"]');

        submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Menyimpan...');

        let formData = $(this).serializeArray();
        if (id) formData.push({name: '_method', value: 'PUT'});

        $.ajax({
            url: url,
            type: 'POST',
            data: $.param(formData),
            success: function(response) {
                $('#modalFakultas').modal('hide');
                fakultasTable.ajax.reload();
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: response.message,
                    timer: 2000,
                    showConfirmButton: false
                });
                submitBtn.prop('disabled', false).html('<i class="bi bi-save"></i> Simpan');
            },
            error: function(xhr) {
                let errorMsg = xhr.responseJSON?.message || 'Terjadi kesalahan!';
                if (xhr.responseJSON?.errors) {
                    errorMsg = Object.values(xhr.responseJSON.errors).flat().join('\n');
                }
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: errorMsg
                });
                submitBtn.prop('disabled', false).html('<i class="bi bi-save"></i> Simpan');
            }
        });
    });

    // ========== EDIT FAKULTAS ==========
    $(document).on('click', '.btn-edit-fakultas', function() {
        let id = $(this).data('id');
        $.get(`/fakultas/${id}/edit`, function(data) {
            $('#fakultas_id').val(data.id);
            $('#nama_fakultas').val(data.nama_fakultas);
            $('#modalFakultasTitle').html('<i class="bi bi-pencil"></i> Edit Fakultas');
            $('#modalFakultas').modal('show');
        });
    });

    // ========== DELETE FAKULTAS ==========
    $(document).on('click', '.btn-delete-fakultas', function() {
        let id = $(this).data('id');
        Swal.fire({
            title: 'Yakin hapus fakultas ini?',
            text: "Data yang dihapus tidak bisa dikembalikan!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `/fakultas/${id}`,
                    type: 'DELETE',
                    data: { _token: '{{ csrf_token() }}' },
                    success: function(response) {
                        fakultasTable.ajax.reload();
                        Swal.fire({
                            icon: 'success',
                            title: 'Terhapus!',
                            text: response.message,
                            timer: 2000,
                            showConfirmButton: false
                        });
                    },
                    error: function(xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal!',
                            text: xhr.responseJSON.message
                        });
                    }
                });
            }
        });
    });

    // ========== ADD PRODI ==========
    $('#btn-add-prodi').click(function() {
        $('#formProdi')[0].reset();
        $('#prodi_id').val('');
        $('#modalProdiTitle').html('<i class="bi bi-mortarboard"></i> Tambah Program Studi');
        loadFakultasOptions();
        $('#modalProdi').modal('show');
    });

    // ========== SUBMIT PRODI ==========
    $('#formProdi').submit(function(e) {
        e.preventDefault();
        let id = $('#prodi_id').val();
        let url = id ? `/prodi/${id}` : "{{ route('prodi.store') }}";
        let submitBtn = $(this).find('button[type="submit"]');

        submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Menyimpan...');

        let formData = $(this).serializeArray();
        if (id) formData.push({name: '_method', value: 'PUT'});

        $.ajax({
            url: url,
            type: 'POST',
            data: $.param(formData),
            success: function(response) {
                $('#modalProdi').modal('hide');
                prodiTable.ajax.reload();
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: response.message,
                    timer: 2000,
                    showConfirmButton: false
                });
                submitBtn.prop('disabled', false).html('<i class="bi bi-save"></i> Simpan');
            },
            error: function(xhr) {
                let errorMsg = xhr.responseJSON?.message || 'Terjadi kesalahan!';
                if (xhr.responseJSON?.errors) {
                    errorMsg = Object.values(xhr.responseJSON.errors).flat().join('\n');
                }
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: errorMsg
                });
                submitBtn.prop('disabled', false).html('<i class="bi bi-save"></i> Simpan');
            }
        });
    });

    // ========== EDIT PRODI ==========
    $(document).on('click', '.btn-edit-prodi', function() {
        let id = $(this).data('id');
        loadFakultasOptions();
        $.get(`/prodi/${id}/edit`, function(data) {
            $('#prodi_id').val(data.id);
            $('#nama_prodi').val(data.nama_prodi);
            setTimeout(function() {
                $('#fakultas_id_select').val(data.fakultas_id);
            }, 300);
            $('#modalProdiTitle').html('<i class="bi bi-pencil"></i> Edit Program Studi');
            $('#modalProdi').modal('show');
        });
    });

    // ========== DELETE PRODI ==========
    $(document).on('click', '.btn-delete-prodi', function() {
        let id = $(this).data('id');
        Swal.fire({
            title: 'Yakin hapus prodi ini?',
            text: "Data yang dihapus tidak bisa dikembalikan!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `/prodi/${id}`,
                    type: 'DELETE',
                    data: { _token: '{{ csrf_token() }}' },
                    success: function(response) {
                        prodiTable.ajax.reload();
                        Swal.fire({
                            icon: 'success',
                            title: 'Terhapus!',
                            text: response.message,
                            timer: 2000,
                            showConfirmButton: false
                        });
                    }
                });
            }
        });
    });

    // ========== IMPORT FAKULTAS ==========
    $('#btn-import-fakultas').click(function() {
        $('#formImportFakultas')[0].reset();
        $('#importModalFakultas').modal('show');
    });

    $('#formImportFakultas').submit(function(e) {
        e.preventDefault();
        let formData = new FormData(this);
        let submitBtn = $(this).find('button[type="submit"]');

        submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Mengimport...');

        $.ajax({
            url: "{{ route('fakultas.import') }}",
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                $('#importModalFakultas').modal('hide');
                fakultasTable.ajax.reload();
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: response.message,
                    timer: 2000,
                    showConfirmButton: false
                });
                submitBtn.prop('disabled', false).html('<i class="bi bi-upload"></i> Upload & Import');
            },
            error: function(xhr) {
                Swal.fire({
                    icon: 'error',
                    title: 'Import Gagal!',
                    text: xhr.responseJSON?.message || 'Terjadi kesalahan!'
                });
                submitBtn.prop('disabled', false).html('<i class="bi bi-upload"></i> Upload & Import');
            }
        });
    });

    // ========== IMPORT PRODI ==========
    $('#btn-import-prodi').click(function() {
        $('#formImportProdi')[0].reset();
        $('#importModalProdi').modal('show');
    });

    $('#formImportProdi').submit(function(e) {
        e.preventDefault();
        let formData = new FormData(this);
        let submitBtn = $(this).find('button[type="submit"]');

        submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Mengimport...');

        $.ajax({
            url: "{{ route('prodi.import') }}",
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                $('#importModalProdi').modal('hide');
                prodiTable.ajax.reload();
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: response.message,
                    timer: 2000,
                    showConfirmButton: false
                });
                submitBtn.prop('disabled', false).html('<i class="bi bi-upload"></i> Upload & Import');
            },
            error: function(xhr) {
                Swal.fire({
                    icon: 'error',
                    title: 'Import Gagal!',
                    text: xhr.responseJSON?.message || 'Terjadi kesalahan!'
                });
                submitBtn.prop('disabled', false).html('<i class="bi bi-upload"></i> Upload & Import');
            }
        });
    });
});
</script>
@endpush