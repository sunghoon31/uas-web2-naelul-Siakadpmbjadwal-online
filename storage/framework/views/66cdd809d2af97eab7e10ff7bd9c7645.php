<div class="modal-header bg-<?php echo e($method == 'POST' ? 'primary' : 'warning'); ?> text-white">
    <h5 class="modal-title">
        <i class="bi bi-<?php echo e($method == 'POST' ? 'plus-circle' : 'pencil-square'); ?>"></i>
        <?php echo e($method == 'POST' ? 'Tambah' : 'Edit'); ?> Dosen
    </h5>
    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
</div>

<form id="formDosen" action="<?php echo e($action); ?>" method="POST" enctype="multipart/form-data">
    <?php echo csrf_field(); ?>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($method == 'PUT'): ?>
        <input type="hidden" name="_method" value="PUT">
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <div class="modal-body">
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">NIDN <span class="text-danger">*</span></label>
                <input type="text" name="nidn" class="form-control" value="<?php echo e(old('nidn', $dosen->nidn)); ?>" required>
            </div>

            <div class="col-md-6 mb-3">
                <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                <input type="text" name="nama" class="form-control" value="<?php echo e(old('nama', $dosen->nama)); ?>" required>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">Email <span class="text-danger">*</span></label>
                <input type="email" name="email" class="form-control" value="<?php echo e(old('email', $dosen->email)); ?>" required>
            </div>

            <div class="col-md-6 mb-3">
                <label class="form-label">No HP <span class="text-danger">*</span></label>
                <input type="text" name="no_hp" class="form-control" value="<?php echo e(old('no_hp', $dosen->no_hp)); ?>" required>
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label">Homebase Prodi (Opsional)</label>
            <select name="prodi_id" class="form-select">
                <option value="">-- Tidak Ada --</option>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $prodis; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $prodi): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($prodi->id); ?>" <?php echo e(old('prodi_id', $dosen->prodi_id) == $prodi->id ? 'selected' : ''); ?>>
                        <?php echo e($prodi->nama_prodi); ?> (<?php echo e($prodi->fakultas->nama_fakultas); ?>)
                    </option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Foto</label>
            
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($dosen->foto): ?>
                <div class="mb-2">
                    <img src="<?php echo e(asset('storage/'.$dosen->foto)); ?>" class="img-thumbnail" width="150">
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
        <button type="submit" class="btn btn-<?php echo e($method == 'POST' ? 'primary' : 'warning'); ?>">
            <i class="bi bi-check-circle"></i> 
            <?php echo e($method == 'POST' ? 'Simpan' : 'Update'); ?>

        </button>
    </div>
</form><?php /**PATH C:\Users\Lenovo\UAS-WEB2\resources\views/dosen/partials/form.blade.php ENDPATH**/ ?>