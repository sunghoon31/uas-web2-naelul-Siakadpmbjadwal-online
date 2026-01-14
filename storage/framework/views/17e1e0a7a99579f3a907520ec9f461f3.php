

<?php $__env->startSection('title', 'Dashboard Admin'); ?>

<?php $__env->startPush('styles'); ?>
<link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
<style>
    :root {
        --primary: #7c7cff;
        --primary-dark: #6060d8;
        --success: #11998e;
        --success-light: #38ef7d;
        --warning: #f093fb;
        --warning-dark: #f5576c;
        --info: #4facfe;
        --info-light: #00f2fe;
        --shadow-sm: 0 2px 8px rgba(0,0,0,0.08);
        --shadow-md: 0 4px 12px rgba(0,0,0,0.1);
        --shadow-lg: 0 8px 20px rgba(0,0,0,0.12);
        --bg: #05070f;
        --bg-card: rgba(255, 255, 255, 0.03);
    }

    * {
        scroll-behavior: auto !important;
    }

    body {
        background: var(--bg);
        overflow-x: hidden;
        color: #000000;
        font-size: 16px;
    }

    .dashboard-header {
        background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
        color: white;
        padding: 1.75rem;
        border-radius: 16px;
        margin-bottom: 2rem;
        box-shadow: var(--shadow-lg);
    }

    .dashboard-header h2 {
        font-size: 1.75rem;
        font-weight: 700;
        margin-bottom: 0.5rem;
    }

    .dashboard-header p {
        font-size: 1rem;
        margin-bottom: 0.25rem;
    }

    .stat-card {
        background: rgba(255, 255, 255, 0.95);
        border: 1px solid rgba(124, 124, 255, 0.2);
        border-radius: 16px;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
        box-shadow: var(--shadow-sm);
        transition: all 0.3s ease;
        border-left: 4px solid var(--primary);
        height: 100%;
        backdrop-filter: blur(10px);
        position: relative;
        overflow: hidden;
    }

    .stat-card::before {
        content: '';
        position: absolute;
        top: 0;
        right: 0;
        width: 120px;
        height: 120px;
        background: rgba(124, 124, 255, 0.05);
        border-radius: 50%;
        transform: translate(30%, -30%);
        transition: transform 0.3s;
    }

    .stat-card:hover::before {
        transform: translate(20%, -20%) scale(1.2);
    }

    .stat-card:hover {
        box-shadow: var(--shadow-md);
        transform: translateY(-4px);
        border-left-width: 6px;
        background: rgba(255, 255, 255, 1);
    }

    .stat-card.success {
        border-left-color: var(--success);
    }

    .stat-card.success::before {
        background: rgba(17, 153, 142, 0.05);
    }

    .stat-card.warning {
        border-left-color: var(--warning);
    }

    .stat-card.warning::before {
        background: rgba(240, 147, 251, 0.05);
    }

    .stat-card.info {
        border-left-color: var(--info);
    }

    .stat-card.info::before {
        background: rgba(79, 172, 254, 0.05);
    }

    .stat-icon {
        width: 60px;
        height: 60px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        margin-bottom: 1rem;
        position: relative;
        z-index: 1;
        background-size: cover;
        background-position: center;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
    }

    /* Icon backgrounds dengan gambar */
    .stat-icon.primary {
        background: linear-gradient(135deg, rgba(124, 124, 255, 0.4), rgba(124, 124, 255, 0.6)),
                    url('https://images.unsplash.com/photo-1541339907198-e08756dedf3f?w=150&q=80');
        color: white;
        text-shadow: 0 2px 4px rgba(0, 0, 0, 0.7);
    }

    .stat-icon.success {
        background: linear-gradient(135deg, rgba(17, 153, 142, 0.4), rgba(17, 153, 142, 0.6)),
                    url('https://images.unsplash.com/photo-1524178232363-1fb2b075b655?w=150&q=80');
        color: white;
        text-shadow: 0 2px 4px rgba(0, 0, 0, 0.7);
    }

    .stat-icon.warning {
        background: linear-gradient(135deg, rgba(240, 147, 251, 0.4), rgba(240, 147, 251, 0.6)),
                    url('https://images.unsplash.com/photo-1434030216411-0b793f4b4173?w=150&q=80');
        color: white;
        text-shadow: 0 2px 4px rgba(0, 0, 0, 0.7);
    }

    .stat-icon.info {
        background: linear-gradient(135deg, rgba(79, 172, 254, 0.4), rgba(79, 172, 254, 0.6)),
                    url('https://images.unsplash.com/photo-1497366216548-37526070297c?w=150&q=80');
        color: white;
        text-shadow: 0 2px 4px rgba(0, 0, 0, 0.7);
    }

    .stat-icon.university {
        background: linear-gradient(135deg, rgba(124, 124, 255, 0.4), rgba(124, 124, 255, 0.6)),
                    url('https://images.unsplash.com/photo-1562774053-701939374585?w=150&q=80');
        color: white;
        text-shadow: 0 2px 4px rgba(0, 0, 0, 0.7);
    }

    .stat-icon.book {
        background: linear-gradient(135deg, rgba(17, 153, 142, 0.4), rgba(17, 153, 142, 0.6)),
                    url('https://images.unsplash.com/photo-1481627834876-b7833e8f5570?w=150&q=80');
        color: white;
        text-shadow: 0 2px 4px rgba(0, 0, 0, 0.7);
    }

    .stat-icon.room {
        background: linear-gradient(135deg, rgba(240, 147, 251, 0.4), rgba(240, 147, 251, 0.6)),
                    url('https://images.unsplash.com/photo-1497366811353-6870744d04b2?w=150&q=80');
        color: white;
        text-shadow: 0 2px 4px rgba(0, 0, 0, 0.7);
    }

    .stat-icon.schedule {
        background: linear-gradient(135deg, rgba(79, 172, 254, 0.4), rgba(79, 172, 254, 0.6)),
                    url('https://images.unsplash.com/photo-1506784983877-45594efa4cbe?w=150&q=80');
        color: white;
        text-shadow: 0 2px 4px rgba(0, 0, 0, 0.7);
    }

    .stat-value {
        font-size: 2rem;
        font-weight: 700;
        color: #000000;
        margin: 0.5rem 0;
        line-height: 1;
        position: relative;
        z-index: 1;
    }

    .stat-label {
        color: #000000;
        font-size: 0.875rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin: 0;
        position: relative;
        z-index: 1;
    }

    .chart-card {
        background: rgba(255, 255, 255, 0.95);
        border: 1px solid rgba(124, 124, 255, 0.15);
        border-radius: 16px;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
        box-shadow: var(--shadow-sm);
        backdrop-filter: blur(10px);
    }

    .chart-title {
        font-size: 1.125rem;
        font-weight: 700;
        color: #000000;
        margin-bottom: 1rem;
        padding-bottom: 0.75rem;
        border-bottom: 2px solid rgba(124, 124, 255, 0.3);
    }

    .chart-container {
        position: relative;
        height: 300px;
        width: 100%;
    }

    .table-card {
        background: rgba(255, 255, 255, 0.95);
        border: 1px solid rgba(124, 124, 255, 0.15);
        border-radius: 16px;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
        box-shadow: var(--shadow-sm);
        backdrop-filter: blur(10px);
    }

    .table-card th {
        background: rgba(124, 124, 255, 0.08);
        color: #000000;
        font-weight: 700;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 0.5px;
        border: none;
        padding: 0.875rem 0.75rem;
    }

    .table-card td {
        border-color: rgba(124, 124, 255, 0.1);
        padding: 0.875rem 0.75rem;
        vertical-align: middle;
        color: #000000;
        font-size: 0.9rem;
    }

    .table-hover tbody tr:hover {
        background-color: rgba(124, 124, 255, 0.06);
    }

    .badge-custom {
        padding: 0.5rem 1rem;
        border-radius: 20px;
        font-weight: 600;
        font-size: 0.75rem;
        color: #000000;
    }

    .btn-notification {
        background: white;
        color: var(--primary);
        border: 2px solid white;
        padding: 0.75rem 1.5rem;
        border-radius: 12px;
        font-weight: 700;
        font-size: 1rem;
        box-shadow: var(--shadow-md);
        transition: all 0.3s ease;
        position: relative;
    }

    .btn-notification:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow-lg);
        background: #f7fafc;
        color: var(--primary-dark);
    }

    .notification-badge {
        position: absolute;
        top: -6px;
        right: -6px;
        background: linear-gradient(135deg, var(--warning-dark) 0%, var(--warning) 100%);
        color: white;
        border-radius: 50%;
        width: 24px;
        height: 24px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.75rem;
        font-weight: 800;
        border: 2px solid white;
        box-shadow: 0 2px 8px rgba(245, 87, 108, 0.4);
    }

    .pulse {
        animation: pulse 2s infinite;
    }

    @keyframes pulse {
        0%, 100% { 
            opacity: 1;
        }
        50% { 
            opacity: 0.6;
        }
    }

    /* Responsive untuk tablet */
    @media (max-width: 991px) {
        .dashboard-header h2 {
            font-size: 1.5rem;
        }
        
        .dashboard-header p {
            font-size: 0.9rem;
        }
        
        .stat-value {
            font-size: 1.75rem;
        }
        
        .stat-icon {
            width: 55px;
            height: 55px;
            font-size: 22px;
        }
        
        .chart-container {
            height: 250px;
        }
    }

    /* Responsive untuk mobile */
    @media (max-width: 768px) {
        .dashboard-header {
            padding: 1.25rem;
            margin-bottom: 1.5rem;
        }
        
        .dashboard-header h2 {
            font-size: 1.25rem;
        }
        
        .dashboard-header p {
            font-size: 0.85rem;
        }
        
        .stat-card {
            padding: 1.25rem;
            margin-bottom: 1.25rem;
        }
        
        .stat-icon {
            width: 50px;
            height: 50px;
            font-size: 20px;
        }
        
        .stat-value {
            font-size: 1.5rem;
        }
        
        .stat-label {
            font-size: 0.8rem;
        }
        
        .chart-container {
            height: 220px;
        }
        
        .btn-notification {
            padding: 0.6rem 1.25rem;
            font-size: 0.9rem;
        }
        
        .notification-badge {
            width: 20px;
            height: 20px;
            font-size: 0.65rem;
        }
        
        .table-responsive {
            font-size: 0.85rem;
        }
        
        .table-card th,
        .table-card td {
            padding: 0.65rem 0.5rem;
            font-size: 0.8rem;
            color: #000000;
        }
    }

    /* Zoom 80% - Scale up */
    @media screen and (min-resolution: 120dpi) {
        body {
            font-size: 18px;
        }
        
        .stat-value {
            font-size: 2.25rem;
        }
        
        .stat-icon {
            width: 65px;
            height: 65px;
            font-size: 26px;
        }
    }

    /* Zoom 125% - Scale down */
    @media screen and (max-resolution: 96dpi) {
        body {
            font-size: 14px;
        }
        
        .stat-value {
            font-size: 1.75rem;
        }
        
        .stat-icon {
            width: 55px;
            height: 55px;
            font-size: 22px;
        }
    }

    /* Container max-width untuk layar besar */
    @media screen and (min-width: 1920px) {
        .container-fluid {
            max-width: 1800px;
            margin: 0 auto;
        }
    }

    /* Fix untuk layar kecil */
    @media screen and (max-width: 1200px) {
        .stat-value {
            font-size: 1.75rem;
        }
        
        .chart-container {
            height: 250px;
        }
    }

    .table-responsive {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }

    .table {
        min-width: 100%;
        width: max-content;
    }

    /* Ensure consistent spacing */
    .row {
        margin-left: -0.75rem;
        margin-right: -0.75rem;
    }

    .row > * {
        padding-left: 0.75rem;
        padding-right: 0.75rem;
    }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="dashboard-header">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h2 class="mb-2">
                    <i class="fas fa-tachometer-alt me-2"></i>Dashboard Admin
                </h2>
                <p class="mb-1 opacity-90">Selamat datang, <strong><?php echo e(Auth::user()->name); ?></strong>!</p>
                <p class="mb-0 opacity-75">
                    <i class="fas fa-calendar-alt me-1"></i><?php echo e(date('l, d F Y')); ?> • 
                    <i class="fas fa-clock ms-2 me-1"></i><span id="currentTime"></span>
                </p>
            </div>
            <div class="col-md-6 text-md-end mt-3 mt-md-0">
                <button class="btn btn-notification" id="notificationBtn">
                    <i class="fas fa-bell me-2"></i>Notifikasi
                    <span class="notification-badge pulse" id="notifCount">0</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Statistics Cards Row 1 -->
    <div class="row">
        <div class="col-xl-3 col-md-6">
            <div class="stat-card">
                <div class="stat-icon primary">
                    <i class="fas fa-user-graduate"></i>
                </div>
                <h3 class="stat-value" data-stat="totalMahasiswa"><?php echo e($stats['total_mahasiswa']); ?></h3>
                <p class="stat-label">Total Mahasiswa</p>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="stat-card success">
                <div class="stat-icon success">
                    <i class="fas fa-chalkboard-teacher"></i>
                </div>
                <h3 class="stat-value" data-stat="totalDosen"><?php echo e($stats['total_dosen']); ?></h3>
                <p class="stat-label">Total Dosen</p>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="stat-card warning">
                <div class="stat-icon warning">
                    <i class="fas fa-user-clock"></i>
                </div>
                <h3 class="stat-value" data-stat="calonPending"><?php echo e($stats['calon_mahasiswa_pending']); ?></h3>
                <p class="stat-label">Calon Mhs Pending</p>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="stat-card info">
                <div class="stat-icon info">
                    <i class="fas fa-building"></i>
                </div>
                <h3 class="stat-value"><?php echo e($stats['total_prodi']); ?></h3>
                <p class="stat-label">Program Studi</p>
            </div>
        </div>
    </div>

    <!-- Statistics Cards Row 2 -->
    <div class="row">
        <div class="col-xl-3 col-md-6">
            <div class="stat-card">
                <div class="stat-icon university">
                    <i class="fas fa-university"></i>
                </div>
                <h3 class="stat-value"><?php echo e($stats['total_fakultas']); ?></h3>
                <p class="stat-label">Fakultas</p>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="stat-card success">
                <div class="stat-icon book">
                    <i class="fas fa-book"></i>
                </div>
                <h3 class="stat-value"><?php echo e($stats['total_mata_kuliah']); ?></h3>
                <p class="stat-label">Mata Kuliah</p>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="stat-card warning">
                <div class="stat-icon room">
                    <i class="fas fa-door-open"></i>
                </div>
                <h3 class="stat-value"><?php echo e($stats['total_ruangan']); ?></h3>
                <p class="stat-label">Ruangan</p>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="stat-card info">
                <div class="stat-icon schedule">
                    <i class="fas fa-calendar-check"></i>
                </div>
                <h3 class="stat-value"><?php echo e($stats['total_jadwal']); ?></h3>
                <p class="stat-label">Jadwal Aktif</p>
            </div>
        </div>
    </div>

    <!-- Charts Row 1 -->
    <div class="row">
        <div class="col-xl-6 mb-4">
            <div class="chart-card">
                <h5 class="chart-title">
                    <i class="fas fa-chart-bar text-primary me-2"></i>Mahasiswa per Program Studi
                </h5>
                <div class="chart-container">
                    <canvas id="chartMahasiswaProdi"></canvas>
                </div>
            </div>
        </div>

        <div class="col-xl-6 mb-4">
            <div class="chart-card">
                <h5 class="chart-title">
                    <i class="fas fa-chart-pie text-success me-2"></i>Mahasiswa per Gender
                </h5>
                <div class="chart-container">
                    <canvas id="chartGender"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row 2 -->
    <div class="row">
        <div class="col-xl-6 mb-4">
            <div class="chart-card">
                <h5 class="chart-title">
                    <i class="fas fa-chart-line text-info me-2"></i>Mahasiswa per Angkatan
                </h5>
                <div class="chart-container">
                    <canvas id="chartAngkatan"></canvas>
                </div>
            </div>
        </div>

        <div class="col-xl-6 mb-4">
            <div class="chart-card">
                <h5 class="chart-title">
                    <i class="fas fa-chart-bar text-warning me-2"></i>Status Calon Mahasiswa
                </h5>
                <div class="chart-container">
                    <canvas id="chartCalonMahasiswa"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Latest Students Table -->
    <div class="row">
        <div class="col-12">
            <div class="table-card">
                <h5 class="chart-title">
                    <i class="fas fa-users text-primary me-2"></i>Mahasiswa Terbaru
                </h5>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>NIM</th>
                                <th>Nama</th>
                                <th>Prodi</th>
                                <th>Fakultas</th>
                                <th>Angkatan</th>
                                <th>Waktu Daftar</th>
                            </tr>
                        </thead>
                        <tbody id="tableMahasiswaTerbaru">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $mahasiswaTerbaru; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $mhs): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td><strong><?php echo e($mhs->nim); ?></strong></td>
                                <td><?php echo e($mhs->nama); ?></td>
                                <td><?php echo e($mhs->prodi->nama_prodi ?? '-'); ?></td>
                                <td><?php echo e($mhs->prodi->fakultas->nama_fakultas ?? '-'); ?></td>
                                <td><span class="badge badge-custom bg-primary"><?php echo e($mhs->angkatan); ?></span></td>
                                <td><?php echo e($mhs->created_at->diffForHumans()); ?></td>
                            </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
$(document).ready(function() {
    // Update current time
    function updateTime() {
        const now = new Date();
        const timeString = now.toLocaleTimeString('id-ID');
        $('#currentTime').text(timeString);
    }
    updateTime();
    setInterval(updateTime, 1000);

    // Chart configuration
    Chart.defaults.font.family = "'Inter', 'Segoe UI', sans-serif";
    Chart.defaults.font.size = 12;
    Chart.defaults.color = '#000000';

    const chartColors = {
        primary: ['#7c7cff', '#a855f7', '#f093fb', '#f5576c', '#4facfe', '#00f2fe', '#43e97b', '#38f9d7'],
        gradient: {
            primary: 'rgba(124, 124, 255, 0.8)',
            success: 'rgba(17, 153, 142, 0.8)',
            warning: 'rgba(240, 147, 251, 0.8)',
            info: 'rgba(79, 172, 254, 0.8)',
            danger: 'rgba(245, 87, 108, 0.8)'
        }
    };

    const commonOptions = {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                labels: {
                    padding: 12,
                    font: {
                        size: 12,
                        weight: '600'
                    },
                    color: '#000000'
                }
            }
        }
    };

    // Chart Mahasiswa per Prodi
    new Chart(document.getElementById('chartMahasiswaProdi'), {
        type: 'bar',
        data: {
            labels: <?php echo json_encode($mahasiswaPerProdi->pluck('prodi')); ?>,
            datasets: [{
                label: 'Jumlah Mahasiswa',
                data: <?php echo json_encode($mahasiswaPerProdi->pluck('total')); ?>,
                backgroundColor: chartColors.primary,
                borderRadius: 8,
                borderWidth: 0
            }]
        },
        options: {
            ...commonOptions,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { 
                        precision: 0,
                        font: { size: 11 },
                        color: '#000000'
                    },
                    grid: { color: 'rgba(124, 124, 255, 0.15)' }
                },
                x: {
                    ticks: { 
                        font: { size: 11 },
                        color: '#000000'
                    },
                    grid: { display: false }
                }
            }
        }
    });

    // Chart Gender
    const genderData = <?php echo json_encode($mahasiswaPerGender); ?>;
    new Chart(document.getElementById('chartGender'), {
        type: 'doughnut',
        data: {
            labels: genderData.map(item => item.gender === 'L' ? 'Laki-laki' : 'Perempuan'),
            datasets: [{
                data: genderData.map(item => item.total),
                backgroundColor: ['#7c7cff', '#f093fb'],
                borderWidth: 4,
                borderColor: '#05070f'
            }]
        },
        options: {
            ...commonOptions,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 15,
                        font: { size: 12, weight: '600' },
                        color: '#000000'
                    }
                }
            }
        }
    });

    // Chart Angkatan
    new Chart(document.getElementById('chartAngkatan'), {
        type: 'line',
        data: {
            labels: <?php echo json_encode($mahasiswaPerAngkatan->pluck('angkatan')); ?>,
            datasets: [{
                label: 'Jumlah Mahasiswa',
                data: <?php echo json_encode($mahasiswaPerAngkatan->pluck('total')); ?>,
                borderColor: '#4facfe',
                backgroundColor: 'rgba(79, 172, 254, 0.1)',
                tension: 0.4,
                fill: true,
                pointRadius: 4,
                pointHoverRadius: 6,
                pointBackgroundColor: '#05070f',
                pointBorderColor: '#4facfe',
                pointBorderWidth: 2,
                borderWidth: 2
            }]
        },
        options: {
            ...commonOptions,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { 
                        precision: 0,
                        font: { size: 11 },
                        color: '#000000'
                    },
                    grid: { color: 'rgba(124, 124, 255, 0.15)' }
                },
                x: {
                    ticks: { 
                        font: { size: 11 },
                        color: '#000000'
                    },
                    grid: { display: false }
                }
            }
        }
    });

    // Chart Calon Mahasiswa
    const calonData = <?php echo json_encode($calonMahasiswaStatus); ?>;
    const statusLabels = {
        'pending': 'Pending',
        'diterima': 'Diterima',
        'ditolak': 'Ditolak'
    };
    new Chart(document.getElementById('chartCalonMahasiswa'), {
        type: 'bar',
        data: {
            labels: calonData.map(item => statusLabels[item.status_seleksi] || item.status_seleksi),
            datasets: [{
                label: 'Jumlah',
                data: calonData.map(item => item.total),
                backgroundColor: [
                    chartColors.gradient.warning,
                    chartColors.gradient.success,
                    chartColors.gradient.danger
                ],
                borderRadius: 8,
                borderWidth: 0
            }]
        },
        options: {
            ...commonOptions,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { 
                        precision: 0,
                        font: { size: 11 },
                        color: '#000000'
                    },
                    grid: { color: 'rgba(124, 124, 255, 0.15)' }
                },
                x: {
                    ticks: { 
                        font: { size: 11 },
                        color: '#000000'
                    },
                    grid: { display: false }
                }
            }
        }
    });

    // Realtime update WITHOUT scroll jumping
    let lastMahasiswaCount = <?php echo e($stats['total_mahasiswa']); ?>;
    
    function updateRealtimeStats() {
        $.ajax({
            url: '/dashboard/realtime',
            method: 'GET',
            success: function(data) {
                // Update stats WITHOUT animation to prevent scroll jump
                $('[data-stat="totalMahasiswa"]').text(data.total_mahasiswa);
                $('[data-stat="totalDosen"]').text(data.total_dosen);
                $('[data-stat="calonPending"]').text(data.calon_mahasiswa_pending);

                // Check for new mahasiswa
                if (data.total_mahasiswa > lastMahasiswaCount) {
                    showNewMahasiswaNotification(data.latest_mahasiswa);
                    lastMahasiswaCount = data.total_mahasiswa;
                    
                    let currentCount = parseInt($('#notifCount').text()) || 0;
                    $('#notifCount').text(currentCount + 1);
                }
            }
        });
    }

    // Show notification
    function showNewMahasiswaNotification(mahasiswa) {
        if (!mahasiswa) return;

        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'success',
            title: 'Mahasiswa Baru!',
            html: `<strong>${mahasiswa.nama}</strong><br><small>${mahasiswa.nim} telah terdaftar</small>`,
            showConfirmButton: false,
            timer: 5000,
            timerProgressBar: true
        });

        // Prepend new row without scroll jump
        const newRow = `
            <tr>
                <td><strong>${mahasiswa.nim}</strong></td>
                <td>${mahasiswa.nama}</td>
                <td>${mahasiswa.prodi?.nama_prodi || '-'}</td>
                <td>${mahasiswa.prodi?.fakultas?.nama_fakultas || '-'}</td>
                <td><span class="badge badge-custom bg-primary">${mahasiswa.angkatan}</span></td>
                <td>Baru saja</td>
            </tr>
        `;
        $('#tableMahasiswaTerbaru').prepend(newRow);
        
        if ($('#tableMahasiswaTerbaru tr').length > 5) {
            $('#tableMahasiswaTerbaru tr:last').remove();
        }
    }

    // Notification button
    $('#notificationBtn').on('click', function() {
        const count = parseInt($('#notifCount').text()) || 0;
        
        if (count > 0) {
            Swal.fire({
                title: 'Notifikasi',
                html: `Anda memiliki <strong>${count}</strong> notifikasi baru`,
                icon: 'info',
                confirmButtonText: 'OK'
            });
            $('#notifCount').text('0');
        } else {
            Swal.fire({
                title: 'Notifikasi',
                text: 'Tidak ada notifikasi baru',
                icon: 'info',
                confirmButtonText: 'OK'
            });
        }
    });

    // Start realtime updates every 30 seconds
    setInterval(updateRealtimeStats, 30000);
});
</script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Lenovo\UAS-WEB2\resources\views/dashboard/admin.blade.php ENDPATH**/ ?>