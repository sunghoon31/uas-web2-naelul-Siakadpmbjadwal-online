@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">

    {{-- HEADER --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold">📊 Dashboard PMB</h3>
        <span class="badge bg-success">Realtime</span>
    </div>

    {{-- STAT CARDS --}}
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card shadow border-0 bg-primary text-white">
                <div class="card-body">
                    <h6>Total Pendaftar</h6>
                    <h2 id="stat-total">{{ $stats['total'] }}</h2>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow border-0 bg-warning text-white">
                <div class="card-body">
                    <h6>Pending</h6>
                    <h2 id="stat-pending">{{ $stats['pending'] }}</h2>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow border-0 bg-success text-white">
                <div class="card-body">
                    <h6>Diterima</h6>
                    <h2 id="stat-diterima">{{ $stats['diterima'] }}</h2>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow border-0 bg-danger text-white">
                <div class="card-body">
                    <h6>Ditolak</h6>
                    <h2 id="stat-ditolak">{{ $stats['ditolak'] }}</h2>
                </div>
            </div>
        </div>
    </div>

    {{-- CHART --}}
    <div class="row">
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-header fw-bold">📌 Jalur Masuk</div>
                <div class="card-body">
                    <canvas id="chartJalur"></canvas>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-header fw-bold">🎓 Top Prodi</div>
                <div class="card-body">
                    <canvas id="chartProdi"></canvas>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
let chartJalur, chartProdi;

function renderCharts(data) {

    // JALUR
    const jalurLabels = data.perJalur.map(i => i.jalur_masuk);
    const jalurData   = data.perJalur.map(i => i.total);

    if (chartJalur) chartJalur.destroy();
    chartJalur = new Chart(document.getElementById('chartJalur'), {
        type: 'doughnut',
        data: {
            labels: jalurLabels,
            datasets: [{
                data: jalurData
            }]
        }
    });

    // PRODI
    const prodiLabels = data.perProdi.map(i => i.prodi.nama_prodi);
    const prodiData   = data.perProdi.map(i => i.total);

    if (chartProdi) chartProdi.destroy();
    chartProdi = new Chart(document.getElementById('chartProdi'), {
        type: 'bar',
        data: {
            labels: prodiLabels,
            datasets: [{
                label: 'Jumlah',
                data: prodiData
            }]
        }
    });
}

function refreshDashboard() {
    fetch("{{ route('pmb.dashboard') }}", {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(res => res.json())
    .then(res => {
        document.getElementById('stat-total').innerText = res.stats.total;
        document.getElementById('stat-pending').innerText = res.stats.pending;
        document.getElementById('stat-diterima').innerText = res.stats.diterima;
        document.getElementById('stat-ditolak').innerText = res.stats.ditolak;

        renderCharts(res);
    });
}

// AUTO REFRESH tiap 10 detik
setInterval(refreshDashboard, 10000);

// INITIAL LOAD
renderCharts({
    perJalur: @json($perJalur),
    perProdi: @json($perProdi)
});
</script>
@endpush
