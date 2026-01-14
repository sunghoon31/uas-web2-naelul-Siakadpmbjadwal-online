

<?php $__env->startSection('content'); ?>
<div class="container">
    <h1>Profile Saya</h1>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('success')): ?>
        <div class="alert alert-success"><?php echo e(session('success')); ?></div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <div class="mb-3">
        <img src="<?php echo e($user->profile_photo ? asset('storage/profile/' . $user->profile_photo) : asset('default-avatar.png')); ?>" 
             alt="Profile Photo" class="img-thumbnail" width="150">
    </div>

    <p><strong>Nama:</strong> <?php echo e($user->name); ?></p>
    <p><strong>Email:</strong> <?php echo e($user->email); ?></p>

    <a href="<?php echo e(route('profile.edit')); ?>" class="btn btn-primary">Edit Profile</a>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Lenovo\UAS-WEB2\resources\views/profile/show.blade.php ENDPATH**/ ?>