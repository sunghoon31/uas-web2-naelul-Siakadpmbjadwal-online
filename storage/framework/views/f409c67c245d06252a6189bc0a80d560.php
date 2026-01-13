<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title><?php echo e(config('app.name', 'SIAKAD')); ?> - <?php echo $__env->yieldContent('title', 'Dashboard'); ?></title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- DataTables Bootstrap 5 -->
    <link href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.min.css" rel="stylesheet">

    <!-- SweetAlert2 -->
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <?php echo $__env->yieldPushContent('styles'); ?>

    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --secondary-gradient: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            --success-gradient: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            --sidebar-bg: #1a1f3a;
            --sidebar-hover: rgba(102, 126, 234, 0.1);
            --sidebar-active: linear-gradient(90deg, rgba(102, 126, 234, 0.2) 0%, transparent 100%);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', 'Segoe UI', sans-serif;
            background: #f0f2f5;
            overflow-x: hidden;
        }

        /* Sidebar Styles */
        .sidebar {
            min-height: 100vh;
            background: var(--sidebar-bg);
            position: fixed;
            top: 0;
            left: 0;
            width: 280px;
            z-index: 1000;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 4px 0 20px rgba(0, 0, 0, 0.1);
        }

        .sidebar-header {
            padding: 1.5rem;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .sidebar-logo {
            display: flex;
            align-items: center;
            gap: 1rem;
            color: white;
            text-decoration: none;
        }

        .sidebar-logo-icon {
            width: 50px;
            height: 50px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            backdrop-filter: blur(10px);
        }

        .sidebar-logo-text h4 {
            margin: 0;
            font-size: 1.3rem;
            font-weight: 700;
        }

        .sidebar-logo-text small {
            font-size: 0.75rem;
            opacity: 0.9;
        }

        .user-profile {
            padding: 1rem 1.5rem;
            background: rgba(255, 255, 255, 0.05);
            margin: 1rem;
            border-radius: 12px;
            backdrop-filter: blur(10px);
        }

        .user-profile-content {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .user-avatar {
            width: 45px;
            height: 45px;
            background: var(--primary-gradient);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.2rem;
            font-weight: 600;
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
        }

        .user-info h6 {
            margin: 0;
            color: white;
            font-size: 0.9rem;
            font-weight: 600;
        }

        .user-role {
            display: inline-block;
            padding: 0.15rem 0.6rem;
            background: rgba(102, 126, 234, 0.3);
            border-radius: 20px;
            font-size: 0.7rem;
            color: #fff;
            margin-top: 0.25rem;
        }

        .sidebar-menu {
            padding: 1rem 0;
            overflow-y: auto;
            max-height: calc(100vh - 280px);
        }

        .sidebar-menu::-webkit-scrollbar {
            width: 6px;
        }

        .sidebar-menu::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.05);
        }

        .sidebar-menu::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.2);
            border-radius: 3px;
        }

        .menu-section-title {
            padding: 0.5rem 1.5rem;
            color: rgba(255, 255, 255, 0.5);
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-top: 1rem;
        }

        .nav-link {
            color: rgba(255, 255, 255, 0.8);
            padding: 0.875rem 1.5rem;
            margin: 0.15rem 1rem;
            border-radius: 10px;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 0.875rem;
            position: relative;
            overflow: hidden;
        }

        .nav-link::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            height: 100%;
            width: 3px;
            background: var(--primary-gradient);
            transform: scaleY(0);
            transition: transform 0.3s ease;
        }

        .nav-link:hover {
            background: var(--sidebar-hover);
            color: white;
            transform: translateX(5px);
        }

        .nav-link.active {
            background: var(--sidebar-active);
            color: white;
            font-weight: 600;
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.2);
        }

        .nav-link.active::before {
            transform: scaleY(1);
        }

        .nav-link i {
            font-size: 1.1rem;
            width: 24px;
            text-align: center;
        }

        /* Main Content Area */
        .main-wrapper {
            margin-left: 280px;
            min-height: 100vh;
            transition: margin-left 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .top-navbar {
            background: white;
            padding: 1rem 2rem;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
            position: sticky;
            top: 0;
            z-index: 999;
            backdrop-filter: blur(10px);
        }

        .navbar-content {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .page-title {
            margin: 0;
            font-size: 1.5rem;
            font-weight: 700;
            background: var(--primary-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .navbar-actions {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .btn-toggle-sidebar {
            display: none;
            width: 40px;
            height: 40px;
            border: none;
            background: var(--primary-gradient);
            color: white;
            border-radius: 10px;
            cursor: pointer;
            transition: transform 0.3s ease;
        }

        .btn-toggle-sidebar:hover {
            transform: scale(1.1);
        }

        .main-content {
            padding: 2rem;
        }

        /* Alert Styles */
        .alert {
            border: none;
            border-radius: 12px;
            padding: 1rem 1.25rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            animation: slideInDown 0.3s ease;
        }

        @keyframes slideInDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .alert-success {
            background: linear-gradient(135deg, #d4fc79 0%, #96e6a1 100%);
            color: #155724;
        }

        .alert-danger {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            color: white;
        }

        .alert-warning {
            background: linear-gradient(135deg, #ffecd2 0%, #fcb69f 100%);
            color: #856404;
        }

        .alert-info {
            background: linear-gradient(135deg, #a1c4fd 0%, #c2e9fb 100%);
            color: #004085;
        }

        /* Responsive Design */
        @media (max-width: 991px) {
            .sidebar {
                transform: translateX(-100%);
            }

            .sidebar.show {
                transform: translateX(0);
            }

            .main-wrapper {
                margin-left: 0;
            }

            .btn-toggle-sidebar {
                display: flex;
                align-items: center;
                justify-content: center;
            }

            .page-title {
                font-size: 1.2rem;
            }
        }

        @media (max-width: 768px) {
            .main-content {
                padding: 1rem;
            }

            .top-navbar {
                padding: 0.75rem 1rem;
            }

            .user-profile {
                margin: 0.75rem;
                padding: 0.75rem;
            }

            .user-avatar {
                width: 40px;
                height: 40px;
                font-size: 1rem;
            }

            .user-info h6 {
                font-size: 0.85rem;
            }
        }

        /* Logout Button Special Style */
        .nav-link-logout {
            background: rgba(245, 87, 108, 0.1);
            color: #f5576c !important;
            margin-top: 1rem;
        }

        .nav-link-logout:hover {
            background: rgba(245, 87, 108, 0.2);
            color: #f5576c !important;
        }

        /* Smooth Transitions */
        * {
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        /* Custom Scrollbar for Main Content */
        .main-content::-webkit-scrollbar {
            width: 8px;
        }

        .main-content::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        .main-content::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 4px;
        }

        .main-content::-webkit-scrollbar-thumb:hover {
            background: #555;
        }
    </style>
</head>
<body>
    
    <nav class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <a href="<?php echo e(route('dashboard')); ?>" class="sidebar-logo">
                <div class="sidebar-logo-icon">
                    <i class="fas fa-graduation-cap"></i>
                </div>
                <div class="sidebar-logo-text">
                    <h4>SIAKAD</h4>
                    <small>Sistem Informasi Akademik</small>
                </div>
            </a>
        </div>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(auth()->guard()->check()): ?>
        <div class="user-profile">
            <div class="user-profile-content">
                <div class="user-avatar">
                    <?php echo e(strtoupper(substr(Auth::user()->name, 0, 1))); ?>

                </div>
                <div class="user-info">
                    <h6><?php echo e(Auth::user()->name); ?></h6>
                    <span class="user-role">
                        <i class="fas fa-circle" style="font-size: 0.5rem;"></i>
                        <?php echo e(ucfirst(Auth::user()->role)); ?>

                    </span>
                </div>
            </div>
        </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        <div class="sidebar-menu">
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link <?php echo e(request()->routeIs('dashboard') ? 'active' : ''); ?>" href="<?php echo e(route('dashboard')); ?>">
                        <i class="fas fa-home"></i>
                        <span>Dashboard</span>
                    </a>
                </li>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(Auth::user()->role === 'admin'): ?>
                
                <li class="menu-section-title">Akademik</li>
                
                <li class="nav-item">
                    <a class="nav-link <?php echo e(request()->routeIs('fakultas.*') ? 'active' : ''); ?>" href="<?php echo e(route('fakultas.index')); ?>">
                        <i class="fas fa-building"></i>
                        <span>Fakultas</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo e(request()->routeIs('prodi.*') ? 'active' : ''); ?>" href="<?php echo e(route('prodi.index')); ?>">
                        <i class="fas fa-project-diagram"></i>
                        <span>Program Studi</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo e(request()->routeIs('mata-kuliah.*') ? 'active' : ''); ?>" href="<?php echo e(route('mata-kuliah.index')); ?>">
                        <i class="fas fa-book"></i>
                        <span>Mata Kuliah</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo e(request()->routeIs('ruangan.*') ? 'active' : ''); ?>" href="<?php echo e(route('ruangan.index')); ?>">
                        <i class="fas fa-door-open"></i>
                        <span>Ruangan</span>
                    </a>
                </li>

                <li class="menu-section-title">Data Master</li>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                
                <li class="nav-item">
                    <a class="nav-link <?php echo e(request()->is('mahasiswa*') ? 'active' : ''); ?>" href="<?php echo e(route('mahasiswa.index')); ?>">
                        <i class="fas fa-user-graduate"></i>
                        <span>Mahasiswa</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo e(request()->is('dosen*') ? 'active' : ''); ?>" href="<?php echo e(route('dosen.index')); ?>">
                        <i class="fas fa-chalkboard-teacher"></i>
                        <span>Dosen</span>
                    </a>
                </li>

                <?php if(Auth::user()->role === 'mahasiswa'): ?>
                <li class="nav-item">
                    <a class="nav-link <?php echo e(request()->routeIs('mata-kuliah.*') ? 'active' : ''); ?>" href="<?php echo e(route('mata-kuliah.index')); ?>">
                        <i class="fas fa-book"></i>
                        <span>Mata Kuliah</span>
                    </a>
                </li>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                <li class="menu-section-title">Penjadwalan</li>
                
                <li class="nav-item">
                    <a class="nav-link <?php echo e(request()->routeIs('jadwal.*') ? 'active' : ''); ?>" href="<?php echo e(route('jadwal.index')); ?>">
                        <i class="fas fa-calendar-alt"></i>
                        <span>Jadwal Kuliah</span>
                    </a>
                </li>

                <li class="menu-section-title">PMB</li>
                
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(Auth::user()->role === 'admin'): ?>
                <li class="nav-item">
                    <a class="nav-link <?php echo e(request()->routeIs('pmb.calon-mahasiswa.dashboard') ? 'active' : ''); ?>" href="<?php echo e(route('pmb.calon-mahasiswa.dashboard')); ?>">
                        <i class="fas fa-chart-line"></i>
                        <span>Dashboard PMB</span>
                    </a>
                </li>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                <li class="nav-item">
                    <a class="nav-link <?php echo e(request()->routeIs('pmb.calon-mahasiswa.*') && !request()->routeIs('pmb.calon-mahasiswa.dashboard') ? 'active' : ''); ?>" href="<?php echo e(route('pmb.calon-mahasiswa.index')); ?>">
                        <i class="fas fa-user-plus"></i>
                        <span>Calon Mahasiswa</span>
                    </a>
                </li>

                <li class="menu-section-title">Pengaturan</li>
                
                <li class="nav-item">
                    <a class="nav-link <?php echo e(request()->routeIs('profile.*') ? 'active' : ''); ?>" href="<?php echo e(route('profile.edit')); ?>">
                        <i class="fas fa-user-cog"></i>
                        <span>Profil Saya</span>
                    </a>
                </li>
                <li class="nav-item">
                    <form method="POST" action="<?php echo e(route('logout')); ?>" id="logout-form">
                        <?php echo csrf_field(); ?>
                        <a class="nav-link nav-link-logout" href="#" onclick="event.preventDefault(); confirmLogout();">
                            <i class="fas fa-sign-out-alt"></i>
                            <span>Logout</span>
                        </a>
                    </form>
                </li>
            </ul>
        </div>
    </nav>

    
    <div class="main-wrapper">
        
        <div class="top-navbar">
            <div class="navbar-content">
                <div class="d-flex align-items-center gap-3">
                    <button class="btn-toggle-sidebar" id="toggleSidebar">
                        <i class="fas fa-bars"></i>
                    </button>
                    <h1 class="page-title">
                        <?php echo $__env->yieldContent('page-title', 'Dashboard'); ?>
                    </h1>
                </div>
                <div class="navbar-actions">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(auth()->guard()->check()): ?>
                    <span class="badge" style="background: var(--primary-gradient); padding: 0.5rem 1rem; border-radius: 20px;">
                        <i class="fas fa-user-circle me-1"></i>
                        <?php echo e(Auth::user()->name); ?>

                    </span>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>
        </div>

        
        <main class="main-content">
            
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('success')): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>
                <?php echo e(session('success')); ?>

                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <?php echo e(session('error')); ?>

                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('warning')): ?>
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i>
                <?php echo e(session('warning')); ?>

                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('info')): ?>
            <div class="alert alert-info alert-dismissible fade show" role="alert">
                <i class="fas fa-info-circle me-2"></i>
                <?php echo e(session('info')); ?>

                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            <?php echo $__env->yieldContent('content'); ?>
        </main>
    </div>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    
    <!-- Bootstrap Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- DataTables -->
    <script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js"></script>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        // Setup CSRF Token untuk semua AJAX request
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // Auto hide alerts after 5 seconds
        setTimeout(function() {
            $('.alert').fadeOut('slow');
        }, 5000);

        // Toggle Sidebar untuk Mobile
        const toggleBtn = document.getElementById('toggleSidebar');
        const sidebar = document.getElementById('sidebar');

        if (toggleBtn) {
            toggleBtn.addEventListener('click', function() {
                sidebar.classList.toggle('show');
            });
        }

        // Close sidebar when clicking outside on mobile
        document.addEventListener('click', function(event) {
            if (window.innerWidth <= 991) {
                const isClickInsideSidebar = sidebar.contains(event.target);
                const isClickOnToggle = toggleBtn.contains(event.target);
                
                if (!isClickInsideSidebar && !isClickOnToggle && sidebar.classList.contains('show')) {
                    sidebar.classList.remove('show');
                }
            }
        });

        // Konfirmasi Logout dengan redirect ke /
        function confirmLogout() {
            Swal.fire({
                title: 'Logout?',
                text: "Anda yakin ingin keluar dari sistem?",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#f5576c',
                cancelButtonColor: '#6c757d',
                confirmButtonText: '<i class="fas fa-sign-out-alt me-1"></i> Ya, Logout',
                cancelButtonText: '<i class="fas fa-times me-1"></i> Batal',
                reverseButtons: true,
                backdrop: true,
                customClass: {
                    confirmButton: 'btn btn-danger px-4',
                    cancelButton: 'btn btn-secondary px-4'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    // Show loading
                    Swal.fire({
                        title: 'Logging out...',
                        text: 'Mohon tunggu sebentar',
                        allowOutsideClick: false,
                        showConfirmButton: false,
                        willOpen: () => {
                            Swal.showLoading();
                        }
                    });
                    
                    // Submit form
                    document.getElementById('logout-form').submit();
                }
            });
        }

        // Smooth scroll behavior
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });
    </script>

    <?php echo $__env->yieldPushContent('scripts'); ?>
</body>
</html><?php /**PATH C:\Users\Lenovo\UAS-WEB2\resources\views/layouts/app.blade.php ENDPATH**/ ?>