

<?php $__env->startSection('content'); ?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h3>Data Dosen</h3>
    <div>
        <button class="btn btn-primary" id="btn-import">
            <i class="bi bi-file-earmark-arrow-up"></i> Import Excel
        </button>
        <a href="<?php echo e(route('dosen.export')); ?>" class="btn btn-success">
            <i class="bi bi-file-earmark-excel"></i> Export Excel
        </a>
        <button class="btn btn-info" id="btn-create">
            <i class="bi bi-plus-circle"></i> Tambah Dosen
        </button>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-body">
        <table class="table table-bordered table-striped" id="dosenTable">
            <thead class="table-dark">
                <tr>
                    <th width="50">No</th>
                    <th width="80">Foto</th>
                    <th>NIDN</th>
                    <th>Nama</th>
                    <th>Email</th>
                    <th>No HP</th>
                    <th>Homebase</th>
                    <th width="250">Aksi</th>
                </tr>
            </thead>
        </table>
    </div>
</div>

<!-- MODAL CREATE/EDIT/SHOW -->
<div class="modal fade" id="dosenModal" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" id="modalContent">
            <!-- Content loaded via AJAX -->
        </div>
    </div>
</div>

<!-- MODAL IMPORT EXCEL -->
<div class="modal fade" id="importModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Import Data Dosen</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formImport" enctype="multipart/form-data">
                <?php echo csrf_field(); ?>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Pilih File Excel</label>
                        <input type="file" name="file" class="form-control" accept=".xlsx,.xls,.csv" required>
                        <small class="text-muted">Format: NIDN | Nama | Email | No HP | Program Studi</small>
                    </div>
                    <div class="alert alert-info">
                        <strong>Template Excel:</strong><br>
                        Header: <code>nidn | nama | email | no_hp | program_studi</code>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Upload & Import</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
$(function () {
    // ========== DATATABLE ==========
    let table = $('#dosenTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: "<?php echo e(route('dosen.index')); ?>",
        order: [[2, 'asc']],
        columns: [
            { data: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'foto', orderable: false, searchable: false },
            { data: 'nidn' },
            { data: 'nama' },
            { data: 'email' },
            { data: 'no_hp' },
            { data: 'prodi' },
            { data: 'aksi', orderable: false, searchable: false }
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
            }
        }
    });

    // ========== CREATE ==========
    $('#btn-create').click(function () {
        $.get("<?php echo e(route('dosen.create')); ?>", function(data) {
            $('#modalContent').html(data);
            $('#dosenModal').modal('show');
        });
    });

    // ========== SHOW ==========
    $(document).on('click', '.btn-show', function () {
        let id = $(this).data('id');
        $.get('/dosen/' + id, function(data) {
            $('#modalContent').html(data);
            $('#dosenModal').modal('show');
        });
    });

    // ========== EDIT ==========
    $(document).on('click', '.btn-edit', function () {
        let id = $(this).data('id');
        $.get('/dosen/' + id + '/edit', function(data) {
            $('#modalContent').html(data);
            $('#dosenModal').modal('show');
        });
    });

    // ========== SUBMIT FORM ==========
    $(document).on('submit', '#formDosen', function (e) {
        e.preventDefault();
        
        let formData = new FormData(this);
        let url = $(this).attr('action');
        let submitBtn = $(this).find('button[type="submit"]');
        
        submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Menyimpan...');

        $.ajax({
            url: url,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function (response) {
                $('#dosenModal').modal('hide');
                table.ajax.reload();
                
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: response.message,
                    timer: 2000,
                    showConfirmButton: false
                });
            },
            error: function (xhr) {
                let errors = xhr.responseJSON.errors;
                let errorMsg = '';
                
                $.each(errors, function(key, value) {
                    errorMsg += '• ' + value[0] + '\n';
                });
                
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: errorMsg,
                });
                
                submitBtn.prop('disabled', false).html('Simpan');
            }
        });
    });

    // ========== DELETE ==========
    $(document).on('click', '.btn-delete', function () {
        let id = $(this).data('id');
        
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
                    url: '/dosen/' + id,
                    type: 'DELETE',
                    data: { 
                        _token: '<?php echo e(csrf_token()); ?>' 
                    },
                    success: function (response) {
                        table.ajax.reload();
                        
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

    // ========== OPEN IMPORT MODAL ==========
    $('#btn-import').click(function() {
        $('#importModal').modal('show');
    });

    // ========== SUBMIT IMPORT ==========
    $('#formImport').submit(function(e) {
        e.preventDefault();
        
        let formData = new FormData(this);
        let submitBtn = $(this).find('button[type="submit"]');
        
        submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Mengimport...');

        $.ajax({
            url: "<?php echo e(route('dosen.import')); ?>",
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                $('#importModal').modal('hide');
                $('#formImport')[0].reset();
                table.ajax.reload();
                
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: response.message,
                    timer: 2000,
                    showConfirmButton: false
                });
                
                submitBtn.prop('disabled', false).html('Upload & Import');
            },
            error: function(xhr) {
                let errorMsg = xhr.responseJSON.message;
                
                if (xhr.responseJSON.errors) {
                    errorMsg += '\n\n' + xhr.responseJSON.errors.join('\n');
                }
                
                Swal.fire({
                    icon: 'error',
                    title: 'Import Gagal!',
                    text: errorMsg,
                });
                
                submitBtn.prop('disabled', false).html('Upload & Import');
            }
        });
    });
});
</script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Lenovo\UAS-WEB2\resources\views/dosen/index.blade.php ENDPATH**/ ?>