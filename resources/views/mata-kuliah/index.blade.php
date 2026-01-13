@extends('layouts.app')

@section('content')
<div class="container-fluid px-4">
    <!-- HEADER -->
    <div class="row mb-4">
        <div class="col-md-6">
            <h3 class="mb-0">
                <i class="bi bi-book"></i> Data Mata Kuliah
            </h3>
            <p class="text-muted mb-0">Manajemen Mata Kuliah - FKOM MA'SOEM</p>
        </div>
        <div class="col-md-6 text-end">
            <button class="btn btn-primary" id="btn-add">
                <i class="bi bi-plus-circle"></i> Tambah Mata Kuliah
            </button>
            <button class="btn btn-success" id="btn-import">
                <i class="bi bi-file-earmark-arrow-up"></i> Import
            </button>
            <button class="btn btn-info text-white" id="btn-export">
                <i class="bi bi-file-earmark-excel"></i> Export
            </button>
        </div>
    </div>

    <!-- TABLE -->
    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover" id="tableData">
                    <thead class="table-primary">
                        <tr>
                            <th width="50">No</th>
                            <th>Kode MK</th>
                            <th>Nama Mata Kuliah</th>
                            <th>SKS</th>
                            <th>Semester</th>
                            <th>Program Studi</th>
                            <th>Fakultas</th>
                            <th width="150">Aksi</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- MODAL FORM -->
<div class="modal fade" id="modalForm" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">
                    <i class="bi bi-book"></i> <span id="modal-title">Tambah Mata Kuliah</span>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="formData">
                @csrf
                <input type="hidden" id="id">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Kode MK <span class="text-danger">*</span></label>
                        <input type="text" name="kode_mk" id="kode_mk" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Nama Mata Kuliah <span class="text-danger">*</span></label>
                        <input type="text" name="nama_mk" id="nama_mk" class="form-control" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">SKS <span class="text-danger">*</span></label>
                            <input type="number" name="sks" id="sks" class="form-control" min="1" max="6" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Semester <span class="text-danger">*</span></label>
                            <select name="semester" id="semester" class="form-select" required>
                                @for($i = 1; $i <= 8; $i++)
                                    <option value="{{ $i }}">Semester {{ $i }}</option>
                                @endfor
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Program Studi <span class="text-danger">*</span></label>
                        <select name="prodi_id" id="prodi_id" class="form-select" required>
                            <option value="">-- Pilih Prodi --</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save"></i> Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL IMPORT -->
<div class="modal fade" id="importModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">
                    <i class="bi bi-file-earmark-arrow-up"></i> Import Mata Kuliah
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="formImport" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Pilih File Excel</label>
                        <input type="file" name="file" class="form-control" accept=".xlsx,.xls,.csv" required>
                        <small class="text-muted">Format: kode_mk | nama_mata_kuliah | sks | semester | program_studi</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-upload"></i> Upload & Import
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(function() {
    // DataTable
    const table = $('#tableData').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('mata-kuliah.index') }}",
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'kode_mk', name: 'kode_mk' },
            { data: 'nama_mk', name: 'nama_mk' },
            { data: 'sks', name: 'sks' },
            { data: 'semester', name: 'semester' },
            { data: 'prodi_nama', name: 'prodi.nama_prodi' },
            { data: 'fakultas_nama', name: 'prodi.fakultas.nama_fakultas' },
            { data: 'aksi', name: 'aksi', orderable: false, searchable: false }
        ]
    });

    // Load Prodi
    function loadProdi() {
        $.get("{{ route('mata-kuliah.create') }}", function(prodis) {
            let options = '<option value="">-- Pilih Prodi --</option>';
            prodis.forEach(function(prodi) {
                options += `<option value="${prodi.id}">${prodi.nama_prodi} (${prodi.fakultas.nama_fakultas})</option>`;
            });
            $('#prodi_id').html(options);
        });
    }

    // Tambah
    $('#btn-add').click(function() {
        $('#formData')[0].reset();
        $('#id').val('');
        $('#modal-title').text('Tambah Mata Kuliah');
        loadProdi();
        $('#modalForm').modal('show');
    });

    // Edit
    $(document).on('click', '.btn-edit', function() {
        const id = $(this).data('id');
        
        $.get(`/mata-kuliah/${id}/edit`, function(data) {
            $('#id').val(data.id);
            $('#kode_mk').val(data.kode_mk);
            $('#nama_mk').val(data.nama_mk);
            $('#sks').val(data.sks);
            $('#semester').val(data.semester);
            
            loadProdi();
            setTimeout(() => $('#prodi_id').val(data.prodi_id), 100);
            
            $('#modal-title').text('Edit Mata Kuliah');
            $('#modalForm').modal('show');
        });
    });

    // Submit
    $('#formData').submit(function(e) {
        e.preventDefault();
        
        const id = $('#id').val();
        const url = id ? `/mata-kuliah/${id}` : "{{ route('mata-kuliah.store') }}";
        const method = id ? 'PUT' : 'POST';
        
        $.ajax({
            url: url,
            type: method,
            data: $(this).serialize(),
            success: function(response) {
                $('#modalForm').modal('hide');
                table.ajax.reload();
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: response.message,
                    timer: 2000,
                    showConfirmButton: false
                });
            },
            error: function(xhr) {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: xhr.responseJSON?.message || 'Terjadi kesalahan!'
                });
            }
        });
    });

    // Delete
    $(document).on('click', '.btn-delete', function() {
        const id = $(this).data('id');
        
        Swal.fire({
            title: 'Yakin hapus data ini?',
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
                    url: `/mata-kuliah/${id}`,
                    type: 'DELETE',
                    data: { _token: '{{ csrf_token() }}' },
                    success: function(response) {
                        table.ajax.reload();
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
                            text: xhr.responseJSON?.message || 'Terjadi kesalahan!'
                        });
                    }
                });
            }
        });
    });

    // Export
    $('#btn-export').click(function() {
        window.location.href = "{{ route('mata-kuliah.export') }}";
    });

    // Import
    $('#btn-import').click(function() {
        $('#importModal').modal('show');
    });

    $('#formImport').submit(function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        
        $.ajax({
            url: "{{ route('mata-kuliah.import') }}",
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                $('#importModal').modal('hide');
                table.ajax.reload();
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: response.message,
                    timer: 2000,
                    showConfirmButton: false
                });
            },
            error: function(xhr) {
                Swal.fire({
                    icon: 'error',
                    title: 'Import Gagal!',
                    text: xhr.responseJSON?.message || 'Terjadi kesalahan!'
                });
            }
        });
    });
});
</script>
@endpush