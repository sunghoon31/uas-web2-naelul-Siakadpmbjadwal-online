<div class="modal-header bg-<?php echo e($method == 'POST' ? 'success' : 'warning'); ?> text-white">
    <h5 class="modal-title">
        <i class="bi bi-<?php echo e($method == 'POST' ? 'plus-circle' : 'pencil-square'); ?>"></i>
        <?php echo e($method == 'POST' ? 'Tambah' : 'Edit'); ?> Mahasiswa
    </h5>
    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
</div>

<form id="formMahasiswa" action="<?php echo e($action); ?>" method="POST" enctype="multipart/form-data">
    <?php echo csrf_field(); ?>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($method == 'PUT'): ?>
        <input type="hidden" name="_method" value="PUT">
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <div class="modal-body">
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">NIM <span class="text-danger">*</span></label>
                <input type="text" name="nim" class="form-control" value="<?php echo e(old('nim', $mahasiswa->nim)); ?>" required>
            </div>

            <div class="col-md-6 mb-3">
                <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                <input type="text" name="nama" class="form-control" value="<?php echo e(old('nama', $mahasiswa->nama)); ?>" required>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">Angkatan <span class="text-danger">*</span></label>
                <input type="number" name="angkatan" class="form-control" value="<?php echo e(old('angkatan', $mahasiswa->angkatan ?? 2020)); ?>" required>
            </div>

            <div class="col-md-6 mb-3">
                <label class="form-label">Program Studi <span class="text-danger">*</span></label>
                <select name="prodi_id" class="form-select" required>
                    <option value="">-- Pilih Prodi --</option>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $prodis; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $prodi): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($prodi->id); ?>" <?php echo e(old('prodi_id', $mahasiswa->prodi_id) == $prodi->id ? 'selected' : ''); ?>>
                            <?php echo e($prodi->nama_prodi); ?> (<?php echo e($prodi->fakultas->nama_fakultas); ?>)
                        </option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </select>
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label">Foto</label>
            
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($mahasiswa->foto): ?>
                <div class="mb-2">
                    <img src="<?php echo e(asset('storage/'.$mahasiswa->foto)); ?>" class="img-thumbnail" width="150">
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            
            <input type="file" name="foto" class="form-control" accept="image/*">
            <small class="text-muted">Format: JPG, PNG, JPEG. Max: 2MB</small>
        </div>
    </div>

    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
            <i class="bi bi-x-circle"></i> Batal
        </button>
        <button type="submit" class="btn btn-<?php echo e($method == 'POST' ? 'success' : 'warning'); ?>">
            <i class="bi bi-check-circle"></i> 
            <?php echo e($method == 'POST' ? 'Simpan' : 'Update'); ?>

        </button>
    </div>
</form><?php /**PATH C:\Users\Lenovo\UAS-WEB2\resources\views/mahasiswa/partials/form.blade.php ENDPATH**/ ?>