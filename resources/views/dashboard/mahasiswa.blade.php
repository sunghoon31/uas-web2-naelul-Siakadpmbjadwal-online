@extends('layouts.app')

@section('title', 'Dashboard Mahasiswa')

@push('styles')
<style>
    :root {
        --primary: #11998e;
        --primary-light: #38ef7d;
        --secondary: #4facfe;
        --secondary-light: #00f2fe;
        --accent: #f093fb;
        --accent-dark: #f5576c;
        --shadow-sm: 0 2px 8px rgba(0,0,0,0.08);
        --shadow-md: 0 4px 12px rgba(0,0,0,0.1);
        --shadow-lg: 0 8px 20px rgba(0,0,0,0.12);
        --bg: #05070f;
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
        background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
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

    .info-box {
        background: linear-gradient(135deg, var(--accent) 0%, var(--accent-dark) 100%);
        color: white;
        border-radius: 16px;
        padding: 1.5rem;
        margin-bottom: 2rem;
        box-shadow: var(--shadow-md);
    }

    .info-box h4 {
        margin-bottom: 0.5rem;
        font-size: 1.125rem;
        font-weight: 700;
    }

    .info-box p {
        margin-bottom: 0;
        font-size: 1rem;
        opacity: 0.95;
    }

    .stat-card {
        background: rgba(255, 255, 255, 0.95);
        border-radius: 16px;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
        box-shadow: var(--shadow-sm);
        transition: all 0.3s ease;
        border-left: 4px solid var(--primary);
        height: 100%;
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
        background: rgba(17, 153, 142, 0.05);
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

    .stat-icon.primary {
        background: linear-gradient(135deg, rgba(17, 153, 142, 0.4), rgba(17, 153, 142, 0.6)),
                    url('https://images.unsplash.com/photo-1541339907198-e08756dedf3f?w=150&q=80');
        color: white;
        text-shadow: 0 2px 4px rgba(0, 0, 0, 0.7);
    }

    .stat-icon.success {
        background: linear-gradient(135deg, rgba(79, 172, 254, 0.4), rgba(79, 172, 254, 0.6)),
                    url('https://images.unsplash.com/photo-1524178232363-1fb2b075b655?w=150&q=80');
        color: white;
        text-shadow: 0 2px 4px rgba(0, 0, 0, 0.7);
    }

    .stat-icon.warning {
        background: linear-gradient(135deg, rgba(240, 147, 251, 0.4), rgba(240, 147, 251, 0.6)),
                    url('https://images.unsplash.com/photo-1481627834876-b7833e8f5570?w=150&q=80');
        color: white;
        text-shadow: 0 2px 4px rgba(0, 0, 0, 0.7);
    }

    .stat-icon.info {
        background: linear-gradient(135deg, rgba(56, 239, 125, 0.4), rgba(56, 239, 125, 0.6)),
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
        border-bottom: 2px solid rgba(17, 153, 142, 0.3);
    }

    .chart-container {
        position: relative;
        height: 300px;
        width: 100%;
    }

    .jadwal-card {
        background: white;
        border-radius: 12px;
        padding: 1.25rem;
        margin-bottom: 1rem;
        box-shadow: var(--shadow-sm);
        border-left: 4px solid var(--secondary);
        transition: all 0.3s ease;
    }

    .jadwal-card:hover {
        box-shadow: var(--shadow-md);
        transform: translateX(3px);
    }

    .jadwal-waktu {
        background: linear-gradient(135deg, var(--secondary) 0%, var(--secondary-light) 100%);
        color: white;
        padding: 0.5rem 1rem;
        border-radius: 8px;
        font-weight: 700;
        font-size: 0.875rem;
        display: inline-block;
        margin-bottom: 0.75rem;
    }

    .jadwal-card h6 {
        font-size: 1rem;
        font-weight: 700;
        color: #000000;
        margin-bottom: 0.5rem;
    }

    .jadwal-card p {
        font-size: 0.875rem;
        color: #000000;
        margin-bottom: 0.25rem;
    }

    .quick-access-btn {
        background: white;
        border-radius: 16px;
        padding: 1.5rem 1rem;
        font-weight: 700;
        transition: all 0.3s ease;
        text-decoration: none;
        display: block;
        text-align: center;
        border: 2px solid;
        box-shadow: var(--shadow-sm);
        color: #000000;
    }

    .quick-access-btn:hover {
        transform: translateY(-5px);
        box-shadow: var(--shadow-md);
    }

    .quick-access-btn i {
        margin-bottom: 0.5rem;
    }

    .btn-outline-primary {
        border-color: var(--primary);
    }

    .btn-outline-primary:hover {
        background: var(--primary);
        color: white;
    }

    .btn-outline-success {
        border-color: var(--primary-light);
    }

    .btn-outline-success:hover {
        background: var(--primary-light);
        color: white;
    }

    .btn-outline-info {
        border-color: var(--secondary);
    }

    .btn-outline-info:hover {
        background: var(--secondary);
        color: white;
    }

    .btn-outline-warning {
        border-color: var(--accent);
    }

    .btn-outline-warning:hover {
        background: var(--accent);
        color: white;
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

        .info-box {
            padding: 1.25rem;
        }

        .info-box h4 {
            font-size: 1rem;
        }

        .info-box p {
            font-size: 0.9rem;
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
@endpush

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="dashboard-header">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h2 class="mb-2">
                    <i class="fas fa-home me-2"></i>Dashboard Mahasiswa
                </h2>
                <p class="mb-1 opacity-90">Selamat datang di Sistem Informasi Akademik</p>
                <p class="mb-0 opacity-75">
                    <i class="fas fa-user me-1"></i>{{ Auth::user()->name }} • 
                    <i class="fas fa-calendar-alt ms-2 me-1"></i>{{ date('l, d F Y') }}
                </p>
            </div>
            <div class="col-md-4 text-md-end mt-3 mt-md-0">
                <span id="currentTime" class="h4 mb-0"></span>
            </div>
        </div>
    </div>

    <!-- Info Box -->
    <div class="info-box">
        <h4><i class="fas fa-info-circle me-2"></i>Informasi</h4>
        <p>Anda dapat mengakses informasi mahasiswa, dosen, dan jadwal kuliah melalui menu navigasi.</p>
    </div>

    <!-- Statistics Cards -->
    <div class="row">
        <div class="col-xl-3 col-md-6">
            <div class="stat-card">
                <div class="stat-icon primary">
                    <i class="fas fa-user-graduate"></i>
                </div>
                <h3 class="stat-value" data-stat="totalMahasiswa">{{ $stats['total_mahasiswa'] ?? 0 }}</h3>
                <p class="stat-label">Total Mahasiswa</p>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="stat-card">
                <div class="stat-icon success">
                    <i class="fas fa-chalkboard-teacher"></i>
                </div>
                <h3 class="stat-value" data-stat="totalDosen">{{ $stats['total_dosen'] ?? 0 }}</h3>
                <p class="stat-label">Total Dosen</p>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="stat-card">
                <div class="stat-icon warning">
                    <i class="fas fa-book"></i>
                </div>
                <h3 class="stat-value">{{ $stats['total_mata_kuliah'] ?? 0 }}</h3>
                <p class="stat-label">Mata Kuliah</p>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="stat-card">
                <div class="stat-icon info">
                    <i class="fas fa-calendar-check"></i>
                </div>
                <h3 class="stat-value">{{ $stats['total_jadwal'] ?? 0 }}</h3>
                <p class="stat-label">Jadwal Aktif</p>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row">
        <div class="col-xl-6 mb-4">
            <div class="chart-card">
                <h5 class="chart-title">
                    <i class="fas fa-chart-bar text-success me-2"></i>Mahasiswa per Program Studi
                </h5>
                <div class="chart-container">
                    <canvas id="chartMahasiswaProdi"></canvas>
                </div>
            </div>
        </div>

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
    </div>

    <!-- Jadwal Hari Ini -->
    <div class="row">
        <div class="col-12">
            <div class="chart-card">
                <h5 class="chart-title">
                    <i class="fas fa-calendar-day text-primary me-2"></i>Jadwal Kuliah Hari Ini ({{ ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'][date('N') - 1] }})
                </h5>

                @if(isset($jadwalHariIni) && $jadwalHariIni->count() > 0)
                    <div class="row">
                        @foreach($jadwalHariIni as $jadwal)
                        <div class="col-md-6 mb-3">
                            <div class="jadwal-card">
                                <div class="jadwal-waktu">
                                    <i class="fas fa-clock me-1"></i>
                                    {{ date('H:i', strtotime($jadwal->jam_mulai)) }} - {{ date('H:i', strtotime($jadwal->jam_selesai)) }}
                                </div>
                                <h6>
                                    <i class="fas fa-book text-primary me-1"></i>
                                    {{ $jadwal->mataKuliah->nama_mk ?? 'N/A' }}
                                </h6>
                                <p class="mb-1">
                                    <i class="fas fa-user text-success me-1"></i> {{ $jadwal->dosen->nama ?? 'N/A' }}
                                </p>
                                <p class="mb-0">
                                    <i class="fas fa-door-open text-info me-1"></i> 
                                    {{ $jadwal->ruangan->nama_ruangan ?? 'N/A' }} 
                                    @if(isset($jadwal->ruangan->kode_ruangan))
                                        ({{ $jadwal->ruangan->kode_ruangan }})
                                    @endif
                                </p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @else
                    <div class="alert alert-info mb-0">
                        <i class="fas fa-info-circle me-2"></i>Tidak ada jadwal kuliah untuk hari ini.
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Quick Access -->
    <div class="row">
        <div class="col-12">
            <div class="chart-card">
                <h5 class="chart-title">
                    <i class="fas fa-bolt text-warning me-2"></i>Akses Cepat
                </h5>
                <div class="row">
                    <div class="col-lg-3 col-md-6 mb-3">
                        <a href="{{ route('mahasiswa.index') }}" class="quick-access-btn btn-outline-primary">
                            <i class="fas fa-user-graduate fa-2x d-block"></i>
                            <span class="d-block mt-2">Data Mahasiswa</span>
                        </a>
                    </div>
                    <div class="col-lg-3 col-md-6 mb-3">
                        <a href="{{ route('dosen.index') }}" class="quick-access-btn btn-outline-success">
                            <i class="fas fa-chalkboard-teacher fa-2x d-block"></i>
                            <span class="d-block mt-2">Data Dosen</span>
                        </a>
                    </div>
                    <div class="col-lg-3 col-md-6 mb-3">
                        <a href="{{ route('jadwal.index') }}" class="quick-access-btn btn-outline-info">
                            <i class="fas fa-calendar-alt fa-2x d-block"></i>
                            <span class="d-block mt-2">Jadwal Kuliah</span>
                        </a>
                    </div>
                    <div class="col-lg-3 col-md-6 mb-3">
                        <a href="{{ route('mata-kuliah.index') }}" class="quick-access-btn btn-outline-warning">
                            <i class="fas fa-book fa-2x d-block"></i>
                            <span class="d-block mt-2">Mata Kuliah</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

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
        primary: ['#11998e', '#38ef7d', '#4facfe', '#00f2fe', '#f093fb', '#f5576c', '#43e97b', '#38f9d7']
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
    const mahasiswaProdiData = @json($mahasiswaPerProdi ?? []);
    const prodiLabels = mahasiswaProdiData.map(item => item.prodi || 'Unknown');
    const prodiValues = mahasiswaProdiData.map(item => item.total || 0);

    new Chart(document.getElementById('chartMahasiswaProdi'), {
        type: 'bar',
        data: {
            labels: prodiLabels,
            datasets: [{
                label: 'Jumlah Mahasiswa',
                data: prodiValues,
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
                    grid: { color: 'rgba(0, 0, 0, 0.1)' }
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

    // Chart Angkatan
    const angkatanData = @json($mahasiswaPerAngkatan ?? []);
    const angkatanLabels = angkatanData.map(item => item.angkatan || '');
    const angkatanValues = angkatanData.map(item => item.total || 0);

    new Chart(document.getElementById('chartAngkatan'), {
        type: 'line',
        data: {
            labels: angkatanLabels,
            datasets: [{
                label: 'Jumlah Mahasiswa',
                data: angkatanValues,
                borderColor: '#4facfe',
                backgroundColor: 'rgba(79, 172, 254, 0.1)',
                tension: 0.4,
                fill: true,
                pointRadius: 4,
                pointHoverRadius: 6,
                pointBackgroundColor: '#fff',
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
                    grid: { color: 'rgba(0, 0, 0, 0.1)' }
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
    function updateRealtimeStats() {
        $.ajax({
            url: '{{ route("dashboard.realtime") }}',
            method: 'GET',
            success: function(data) {
                // Update stats WITHOUT animation to prevent scroll jump
                if (data.total_mahasiswa) {
                    $('[data-stat="totalMahasiswa"]').text(data.total_mahasiswa);
                }
                if (data.total_dosen) {
                    $('[data-stat="totalDosen"]').text(data.total_dosen);
                }
            },
            error: function(xhr, status, error) {
                console.log('Error updating stats:', error);
            }
        });
    }

    // Start realtime updates every 30 seconds
    setInterval(updateRealtimeStats, 30000);
});
</script>
@endpush