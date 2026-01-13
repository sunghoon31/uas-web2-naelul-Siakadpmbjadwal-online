

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <!-- Header //ini dokumen.blade.php-->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-lg border-0">
                <div class="card-header bg-gradient-primary text-white py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-1 font-weight-bold">
                                <i class="fas fa-folder-open mr-2"></i> Dokumen & Verifikasi Berkas
                            </h4>
                            <p class="mb-0 small"><?php echo e($calonMahasiswa->nama); ?> - <?php echo e($calonMahasiswa->no_pendaftaran); ?></p>
                        </div>
                        <a href="<?php echo e(route('pmb.calon-mahasiswa.index')); ?>" class="btn btn-light btn-lg shadow-sm">
                            <i class="fas fa-arrow-left mr-2"></i> Kembali
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Informasi Calon Mahasiswa -->
        <div class="col-lg-4 mb-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-info text-white">
                    <h6 class="mb-0 font-weight-bold">
                        <i class="fas fa-user-circle mr-2"></i> Informasi Pendaftar
                    </h6>
                </div>
                <div class="card-body">
                    <table class="table table-sm info-table">
                        <tr>
                            <th width="45%">No Pendaftaran</th>
                            <td><strong class="text-primary"><?php echo e($calonMahasiswa->no_pendaftaran); ?></strong></td>
                        </tr>
                        <tr>
                            <th>Nama</th>
                            <td><strong class="text-dark"><?php echo e($calonMahasiswa->nama); ?></strong></td>
                        </tr>
                        <tr>
                            <th>Prodi</th>
                            <td class="text-dark"><?php echo e($calonMahasiswa->prodi->nama_prodi); ?></td>
                        </tr>
                        <tr>
                            <th>Jalur Masuk</th>
                            <td><span class="badge badge-info badge-custom"><?php echo e($calonMahasiswa->jalur_masuk_nama); ?></span></td>
                        </tr>
                        <tr>
                            <th>Status Seleksi</th>
                            <td>
                                <span class="badge badge-<?php echo e($calonMahasiswa->status_seleksi == 'pending' ? 'warning' : ($calonMahasiswa->status_seleksi == 'diterima' ? 'success' : 'danger')); ?> badge-custom">
                                    <?php echo e($calonMahasiswa->status_seleksi_text); ?>

                                </span>
                            </td>
                        </tr>
                        <tr>
                            <th>Status Verifikasi</th>
                            <td>
                                <span class="badge badge-<?php echo e($calonMahasiswa->status_verifikasi_badge); ?> badge-custom">
                                    <?php echo e($calonMahasiswa->status_verifikasi_berkas_text); ?>

                                </span>
                            </td>
                        </tr>
                    </table>

                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($calonMahasiswa->catatan_verifikasi): ?>
                    <div class="alert alert-warning mt-3">
                        <strong><i class="fas fa-exclamation-triangle mr-2"></i> Catatan:</strong><br>
                        <?php echo e($calonMahasiswa->catatan_verifikasi); ?>

                    </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                    <!-- Progress Bar -->
                    <div class="mt-4">
                        <h6 class="font-weight-bold mb-3 text-dark">Progress Verifikasi</h6>
                        <?php
                            $total = $calonMahasiswa->jumlah_dokumen;
                            $verified = $calonMahasiswa->jumlah_dokumen_diverifikasi;
                            $percentage = $total > 0 ? round(($verified / $total) * 100) : 0;
                        ?>
                        <div class="progress" style="height: 30px;">
                            <div class="progress-bar bg-success progress-bar-striped progress-bar-animated" role="progressbar" 
                                 style="width: <?php echo e($percentage); ?>%" 
                                 aria-valuenow="<?php echo e($percentage); ?>" 
                                 aria-valuemin="0" 
                                 aria-valuemax="100">
                                <strong style="font-size: 14px; color: #000;"><?php echo e($verified); ?>/<?php echo e($total); ?> Dokumen (<?php echo e($percentage); ?>%)</strong>
                            </div>
                        </div>
                    </div>

                    <!-- Kartu Ujian -->
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($calonMahasiswa->status_verifikasi_berkas == 'diverifikasi'): ?>
                    <div class="mt-4">
                        <button type="button" class="btn btn-success btn-block btn-lg" onclick="generateKartuUjian()">
                            <i class="fas fa-id-card mr-2"></i> Generate Kartu Ujian
                        </button>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($calonMahasiswa->kartu_ujian): ?>
                        <a href="<?php echo e($calonMahasiswa->kartu_ujian_url); ?>" target="_blank" class="btn btn-primary btn-block mt-2">
                            <i class="fas fa-download mr-2"></i> Download Kartu Ujian
                        </a>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Upload & List Dokumen -->
        <div class="col-lg-8 mb-4">
            <!-- Upload Form -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-gradient-success text-white">
                    <h6 class="mb-0 font-weight-bold">
                        <i class="fas fa-cloud-upload-alt mr-2"></i> Upload Dokumen
                    </h6>
                </div>
                <div class="card-body">
                    <form id="formUploadDokumen" enctype="multipart/form-data">
                        <?php echo csrf_field(); ?>
                        <div class="row">
                            <div class="col-md-5 mb-3">
                                <label class="font-weight-bold">Jenis Dokumen <span class="text-danger">*</span></label>
                                <select name="jenis_dokumen" id="jenis_dokumen" class="form-control form-control-lg custom-select" required>
                                    <option value="">-- Pilih Jenis Dokumen --</option>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $jenisDokumen; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($key); ?>"><?php echo e($value); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </select>
                            </div>
                            <div class="col-md-5 mb-3">
                                <label class="font-weight-bold">File Dokumen <span class="text-danger">*</span></label>
                                <input type="file" name="file" id="file" class="form-control-file" accept=".jpg,.jpeg,.png,.pdf" required>
                                <small class="text-muted">Format: JPG, PNG, PDF. Max: 5MB</small>
                            </div>
                            <div class="col-md-2 mb-3">
                                <label class="font-weight-bold">&nbsp;</label>
                                <button type="submit" class="btn btn-success btn-block btn-lg">
                                    <i class="fas fa-upload"></i> Upload
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- List Dokumen -->
            <div class="card shadow-sm border-0">
                <div class="card-header bg-dark text-white">
                    <h6 class="mb-0 font-weight-bold">
                        <i class="fas fa-list mr-2"></i> Daftar Dokumen Terupload
                    </h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0" id="tableDokumen">
                            <thead class="thead-light">
                                <tr>
                                    <th width="5%" class="text-center">No</th>
                                    <th width="25%">Jenis Dokumen</th>
                                    <th width="25%">Nama File</th>
                                    <th width="10%" class="text-center">Ukuran</th>
                                    <th width="15%" class="text-center">Status</th>
                                    <th width="20%" class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $calonMahasiswa->dokumen; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $dok): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr id="row-dokumen-<?php echo e($dok->id); ?>">
                                    <td class="text-center font-weight-bold"><?php echo e($index + 1); ?></td>
                                    <td>
                                        <i class="fas fa-file-alt text-primary mr-2"></i>
                                        <strong><?php echo e($dok->jenis_dokumen_nama); ?></strong>
                                    </td>
                                    <td>
                                        <small class="text-muted"><?php echo e(Str::limit($dok->original_name, 30)); ?></small>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge badge-secondary"><?php echo e($dok->ukuran_file_bytes); ?></span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge badge-<?php echo e($dok->status_badge_class); ?>">
                                            <?php echo e($dok->status_text); ?>

                                        </span>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($dok->catatan): ?>
                                        <i class="fas fa-info-circle text-info ml-1" 
                                           data-toggle="tooltip" 
                                           title="<?php echo e($dok->catatan); ?>"></i>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group-sm">
                                            <a href="<?php echo e(route('pmb.dokumen.download', [$calonMahasiswa->id, $dok->id])); ?>" 
                                               class="btn btn-sm btn-info" title="Download">
                                                <i class="fas fa-download"></i>
                                            </a>
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($dok->status_verifikasi == 'menunggu'): ?>
                                            <button type="button" onclick="verifyDokumen(<?php echo e($dok->id); ?>)" 
                                                    class="btn btn-sm btn-success" title="Verifikasi">
                                                <i class="fas fa-check"></i>
                                            </button>
                                            <button type="button" onclick="rejectDokumen(<?php echo e($dok->id); ?>)" 
                                                    class="btn btn-sm btn-warning" title="Tolak">
                                                <i class="fas fa-times"></i>
                                            </button>
                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                            <button type="button" onclick="deleteDokumen(<?php echo e($dok->id); ?>)" 
                                                    class="btn btn-sm btn-danger" title="Hapus">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr id="emptyRow">
                                    <td colspan="6" class="text-center py-5">
                                        <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                        <p class="text-muted mb-0">Belum ada dokumen yang diupload</p>
                                    </td>
                                </tr>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Verifikasi -->
<div class="modal fade" id="modalVerify" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title font-weight-bold">
                    <i class="fas fa-check-circle mr-2"></i> Verifikasi Dokumen
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form id="formVerify">
                <?php echo csrf_field(); ?>
                <input type="hidden" id="verifyDokumenId">
                <div class="modal-body">
                    <div class="form-group">
                        <label class="font-weight-bold">Catatan (Opsional)</label>
                        <textarea name="catatan" id="verifyCatatan" rows="3" class="form-control" 
                                  placeholder="Tambahkan catatan jika diperlukan..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-check mr-2"></i> Verifikasi
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Reject -->
<div class="modal fade" id="modalReject" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title font-weight-bold">
                    <i class="fas fa-times-circle mr-2"></i> Tolak Dokumen
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form id="formReject">
                <?php echo csrf_field(); ?>
                <input type="hidden" id="rejectDokumenId">
                <div class="modal-body">
                    <div class="form-group">
                        <label class="font-weight-bold">Alasan Penolakan <span class="text-danger">*</span></label>
                        <textarea name="catatan" id="rejectCatatan" rows="4" class="form-control" 
                                  placeholder="Jelaskan alasan penolakan dokumen..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-times mr-2"></i> Tolak Dokumen
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php $__env->stopSection(); ?>

<?php $__env->startPush('styles'); ?>
<style>
.bg-gradient-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}
.bg-gradient-success {
    background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
}
.card {
    border-radius: 12px;
}
.btn {
    border-radius: 8px;
    font-weight: 600;
    transition: all 0.3s ease;
}
.btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}
.table td, .table th {
    vertical-align: middle;
}
.form-control-file {
    padding: 0.5rem;
    border: 2px dashed #dee2e6;
    border-radius: 8px;
    background: #f8f9fa;
}

/* Custom styling untuk tabel informasi */
.info-table {
    margin-bottom: 0;
}
.info-table tr {
    border-bottom: 1px solid #e9ecef;
}
.info-table tr:last-child {
    border-bottom: none;
}
.info-table th {
    font-weight: 600;
    color: #495057;
    padding: 12px 8px;
    font-size: 14px;
}
.info-table td {
    padding: 12px 8px;
    font-size: 14px;
    color: #212529;
}

/* Badge custom untuk lebih jelas */
.badge-custom {
    padding: 8px 14px;
    font-size: 14px;
    font-weight: 700;
    letter-spacing: 0.5px;
    text-transform: uppercase;
    box-shadow: 0 2px 4px rgba(0,0,0,0.2);
}

/* Override warna badge untuk kontras lebih baik */
.badge-info {
    background-color: #17a2b8 !important;
    color: #ffffff !important;
}

.badge-warning {
    background-color: #ffc107 !important;
    color: #000000 !important;
}

.badge-success {
    background-color: #28a745 !important;
    color: #ffffff !important;
}

.badge-danger {
    background-color: #dc3545 !important;
    color: #ffffff !important;
}

/* Progress bar text */
.progress-bar strong {
    text-shadow: 1px 1px 2px rgba(255,255,255,0.3);
}
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
$(document).ready(function() {
    $('[data-toggle="tooltip"]').tooltip();

    // Upload dokumen
    $('#formUploadDokumen').on('submit', function(e) {
        e.preventDefault();
        
        let formData = new FormData(this);
        
        $.ajax({
            url: '<?php echo e(route("pmb.dokumen.upload", $calonMahasiswa->id)); ?>',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: response.message,
                    showConfirmButton: false,
                    timer: 1500
                });
                
                $('#formUploadDokumen')[0].reset();
                location.reload();
            },
            error: function(xhr) {
                let message = 'Gagal mengupload dokumen';
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
    });

    // Verifikasi dokumen
    $('#formVerify').on('submit', function(e) {
        e.preventDefault();
        let dokumenId = $('#verifyDokumenId').val();
        
        $.ajax({
            url: '<?php echo e(url("pmb/dokumen")); ?>/<?php echo e($calonMahasiswa->id); ?>/' + dokumenId + '/verify',
            method: 'POST',
            data: $(this).serialize(),
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                $('#modalVerify').modal('hide');
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: response.message,
                    showConfirmButton: false,
                    timer: 1500
                });
                location.reload();
            },
            error: function(xhr) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'Gagal memverifikasi dokumen'
                });
            }
        });
    });

    // Reject dokumen
    $('#formReject').on('submit', function(e) {
        e.preventDefault();
        let dokumenId = $('#rejectDokumenId').val();
        
        $.ajax({
            url: '<?php echo e(url("pmb/dokumen")); ?>/<?php echo e($calonMahasiswa->id); ?>/' + dokumenId + '/reject',
            method: 'POST',
            data: $(this).serialize(),
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                $('#modalReject').modal('hide');
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: response.message,
                    showConfirmButton: false,
                    timer: 1500
                });
                location.reload();
            },
            error: function(xhr) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'Gagal menolak dokumen'
                });
            }
        });
    });
});

function verifyDokumen(id) {
    $('#verifyDokumenId').val(id);
    $('#verifyCatatan').val('');
    $('#modalVerify').modal('show');
}

function rejectDokumen(id) {
    $('#rejectDokumenId').val(id);
    $('#rejectCatatan').val('');
    $('#modalReject').modal('show');
}

function deleteDokumen(id) {
    Swal.fire({
        title: 'Yakin hapus dokumen?',
        text: 'Dokumen yang dihapus tidak dapat dikembalikan!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '<?php echo e(url("pmb/dokumen")); ?>/<?php echo e($calonMahasiswa->id); ?>/' + id,
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Terhapus!',
                        text: response.message,
                        showConfirmButton: false,
                        timer: 1500
                    });
                    location.reload();
                },
                error: function(xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: 'Gagal menghapus dokumen'
                    });
                }
            });
        }
    });
}

function generateKartuUjian() {
    Swal.fire({
        title: 'Generate Kartu Ujian?',
        text: 'Kartu ujian akan dibuat untuk calon mahasiswa ini.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#28a745',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Ya, Generate!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '<?php echo e(route("pmb.dokumen.kartu-ujian", $calonMahasiswa->id)); ?>',
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: response.message,
                        showConfirmButton: false,
                        timer: 1500
                    });
                    location.reload();
                },
                error: function(xhr) {
                    let message = 'Gagal membuat kartu ujian';
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
</script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Lenovo\UAS-WEB2\resources\views/calon-mahasiswa/dokumen.blade.php ENDPATH**/ ?>