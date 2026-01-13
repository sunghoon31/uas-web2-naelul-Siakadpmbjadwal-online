@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- Info Calon Mahasiswa -->
        <div class="col-md-4">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-gradient-primary text-white">
                    <h5 class="mb-0 font-weight-bold">
                        <i class="fas fa-user-graduate mr-2"></i>Informasi Pendaftar
                    </h5>
                </div>
                <div class="card-body">
                    <table class="table table-sm table-borderless info-mahasiswa-table">
                        <tr>
                            <th width="40%">No Pendaftaran</th>
                            <td><strong class="text-primary">{{ $calonMahasiswa->no_pendaftaran }}</strong></td>
                        </tr>
                        <tr>
                            <th>Nama</th>
                            <td><strong class="text-dark">{{ $calonMahasiswa->nama }}</strong></td>
                        </tr>
                        <tr>
                            <th>Prodi</th>
                            <td class="text-dark">{{ $calonMahasiswa->prodi->nama_prodi }}</td>
                        </tr>
                        <tr>
                            <th>Jalur Masuk</th>
                            <td>
                                <span class="badge badge-info badge-custom">{{ ucfirst($calonMahasiswa->jalur_masuk) }}</span>
                            </td>
                        </tr>
                        <tr>
                            <th>Status</th>
                            <td>
                                @if($calonMahasiswa->status_seleksi == 'pending')
                                <span class="badge badge-warning badge-custom text-dark">Pending</span>
                                @elseif($calonMahasiswa->status_seleksi == 'diterima')
                                <span class="badge badge-success badge-custom">Diterima</span>
                                @else
                                <span class="badge badge-danger badge-custom">Ditolak</span>
                                @endif
                            </td>
                        </tr>
                    </table>

                    <hr>

                    <!-- Ringkasan Keuangan -->
                    <div class="alert alert-info mb-0 shadow-sm">
                        <h6 class="mb-2 font-weight-bold">
                            <i class="fas fa-calculator mr-2"></i>Ringkasan Keuangan:
                        </h6>
                        <table class="table table-sm table-borderless mb-0">
                            <tr>
                                <td>Total Biaya</td>
                                <td class="text-right"><strong id="totalBiaya" class="text-dark">Rp 0</strong></td>
                            </tr>
                            <tr>
                                <td>Sudah Dibayar</td>
                                <td class="text-right"><strong id="totalBayar" class="text-success">Rp 0</strong></td>
                            </tr>
                            <tr>
                                <td>Sisa Tagihan</td>
                                <td class="text-right"><strong id="sisaTagihan" class="text-danger">Rp 0</strong></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="card mt-3 shadow-sm border-0">
                <div class="card-body">
                    <a href="{{ route('pmb.calon-mahasiswa.index') }}" class="btn btn-secondary btn-block btn-action shadow-sm">
                        <i class="fas fa-arrow-left mr-2"></i> Kembali
                    </a>
                    <button onclick="generateBiaya()" class="btn btn-primary btn-block btn-action shadow-sm" id="btnGenerate">
                        <i class="fas fa-cog mr-2"></i> Generate Biaya
                    </button>
                    <button onclick="showModalTambah()" class="btn btn-success btn-block btn-action shadow-sm">
                        <i class="fas fa-plus mr-2"></i> Tambah Biaya Manual
                    </button>
                    <button onclick="exportKeuanganPDF()" class="btn btn-danger btn-block btn-action shadow-sm" id="btnExport">
                        <i class="fas fa-file-pdf mr-2"></i> Export PDF
                    </button>
                </div>
            </div>
        </div>

        <!-- Daftar Biaya PMB -->
        <div class="col-md-8">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-gradient-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 font-weight-bold">
                        <i class="fas fa-money-bill-wave mr-2"></i>Daftar Biaya PMB
                    </h5>
                    <button onclick="loadKeuangan()" class="btn btn-sm btn-light shadow-sm">
                        <i class="fas fa-sync mr-2"></i> Refresh
                    </button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-striped" id="tableKeuangan">
                            <thead class="thead-dark">
                                <tr>
                                    <th>Jenis Biaya</th>
                                    <th width="15%">Nominal</th>
                                    <th width="15%" class="text-center">Status</th>
                                    <th width="12%" class="text-center">Tanggal Bayar</th>
                                    <th width="28%" class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td colspan="5" class="text-center py-5">
                                        <i class="fas fa-spinner fa-spin fa-2x text-primary mb-3"></i>
                                        <p class="text-muted">Memuat data...</p>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Bayar -->
<div class="modal fade" id="modalBayar" tabindex="-1" role="dialog" aria-labelledby="modalBayarLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-gradient-success text-white">
                <h5 class="modal-title font-weight-bold" id="modalBayarLabel">
                    <i class="fas fa-money-check-alt mr-2"></i>Konfirmasi Pembayaran
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="formBayar" enctype="multipart/form-data">
                <div class="modal-body">
                    <input type="hidden" id="keuanganId">
                    
                    <div class="alert alert-info shadow-sm" id="infoBiaya"></div>

                    <div class="form-group">
                        <label class="font-weight-bold">Tanggal Bayar <span class="text-danger">*</span></label>
                        <input type="date" name="tanggal_bayar" id="tanggal_bayar" class="form-control" required>
                        <small class="form-text text-muted">Pilih tanggal pembayaran</small>
                    </div>

                    <div class="form-group">
                        <label class="font-weight-bold">Metode Bayar <span class="text-danger">*</span></label>
                        <select name="metode_bayar" id="metode_bayar" class="form-control custom-select" required>
                            <option value="">Pilih Metode</option>
                            <option value="Cash">Cash</option>
                            <option value="Transfer Bank">Transfer Bank</option>
                            <option value="Virtual Account">Virtual Account</option>
                            <option value="E-Wallet">E-Wallet</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="font-weight-bold">Bukti Bayar</label>
                        <input type="file" name="bukti_bayar" id="bukti_bayar" class="form-control-file" accept="image/*,application/pdf">
                        <small class="text-muted">Format: JPG, PNG, PDF (Max 2MB)</small>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-times mr-2"></i>Tutup
                    </button>
                    <button type="submit" class="btn btn-success" id="btnKonfirmasi">
                        <i class="fas fa-check mr-2"></i> Konfirmasi Bayar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Tambah Biaya -->
<div class="modal fade" id="modalTambah" tabindex="-1" role="dialog" aria-labelledby="modalTambahLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-gradient-primary text-white">
                <h5 class="modal-title font-weight-bold" id="modalTambahLabel">
                    <i class="fas fa-plus-circle mr-2"></i>Tambah Biaya Manual
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="formTambah">
                <div class="modal-body">
                    <div class="form-group">
                        <label class="font-weight-bold">Jenis Biaya <span class="text-danger">*</span></label>
                        <select name="jenis_biaya" id="jenis_biaya" class="form-control custom-select" required>
                            <option value="">Pilih Jenis</option>
                            <option value="formulir">Biaya Formulir</option>
                            <option value="ujian">Biaya Seleksi/Ujian</option>
                            <option value="daftar_ulang">Biaya Daftar Ulang</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="font-weight-bold">Nominal <span class="text-danger">*</span></label>
                        <input type="number" name="nominal" id="nominal" class="form-control" min="0" required placeholder="Masukkan nominal">
                        <small class="text-muted">Masukkan nominal dalam Rupiah</small>
                    </div>

                    <div class="form-group">
                        <label class="font-weight-bold">Keterangan</label>
                        <textarea name="keterangan" id="keterangan" rows="3" class="form-control" placeholder="Keterangan tambahan (opsional)"></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-times mr-2"></i>Tutup
                    </button>
                    <button type="submit" class="btn btn-primary" id="btnSimpanTambah">
                        <i class="fas fa-save mr-2"></i> Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Lihat Bukti -->
<div class="modal fade" id="modalBukti" tabindex="-1" role="dialog" aria-labelledby="modalBuktiLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-gradient-info text-white">
                <h5 class="modal-title font-weight-bold" id="modalBuktiLabel">
                    <i class="fas fa-file-image mr-2"></i>Bukti Pembayaran
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body text-center" id="buktiBayarContent">
                <div class="text-center py-5">
                    <i class="fas fa-spinner fa-spin fa-3x text-primary mb-3"></i>
                    <p class="text-muted">Memuat bukti...</p>
                </div>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class="fas fa-times mr-2"></i>Tutup
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
/* Gradient Background */
.bg-gradient-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.bg-gradient-success {
    background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
}

.bg-gradient-info {
    background: linear-gradient(135deg, #36d1dc 0%, #5b86e5 100%);
}

/* Card Styling */
.card {
    border-radius: 12px;
    overflow: hidden;
}

.card-header {
    border: none;
    padding: 1.25rem;
}

/* Table Info Mahasiswa */
.info-mahasiswa-table th {
    color: #495057;
    font-weight: 600;
    padding: 8px 5px;
}

.info-mahasiswa-table td {
    color: #212529;
    font-weight: 500;
    padding: 8px 5px;
}

/* Badge Custom */
.badge-custom {
    padding: 0.5em 0.9em;
    font-size: 0.9rem;
    font-weight: 600;
    border-radius: 6px;
    display: inline-block;
}

.badge-warning.badge-custom {
    background-color: #ffc107;
    color: #000 !important;
}

.badge-success.badge-custom {
    background-color: #28a745;
    color: #fff !important;
}

.badge-danger.badge-custom {
    background-color: #dc3545;
    color: #fff !important;
}

.badge-info.badge-custom {
    background-color: #17a2b8;
    color: #fff !important;
}

/* Table Styling */
.table {
    font-size: 0.95rem;
}

.table thead th {
    vertical-align: middle;
    font-weight: 600;
    letter-spacing: 0.5px;
    background: #2c3e50;
    color: white;
    border: none;
    padding: 15px 10px;
}

.table tbody td {
    vertical-align: middle;
    padding: 12px 10px;
    border-color: #e9ecef;
}

.table-hover tbody tr:hover {
    background-color: #f8f9fa;
}

/* Badge Styling */
.badge {
    padding: 0.6em 1em;
    font-size: 0.85rem;
    font-weight: 600;
    border-radius: 6px;
}

/* Button Styling */
.btn {
    border-radius: 8px;
    font-weight: 600;
    transition: all 0.3s ease;
    border: none;
}

.btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

.btn-action {
    padding: 0.75rem 1rem;
    font-size: 0.95rem;
}

.btn-sm {
    padding: 0.5rem 1rem;
    font-size: 0.875rem;
}

/* Action Button Group */
.btn-group-table {
    display: flex;
    gap: 4px;
    flex-wrap: wrap;
    justify-content: center;
}

.btn-group-table .btn {
    min-width: 75px;
    padding: 0.4rem 0.6rem;
    font-size: 0.875rem;
}

/* Form Styling */
.form-control, .custom-select {
    border-radius: 8px;
    border: 2px solid #e9ecef;
    padding: 0.6rem 1rem;
    transition: all 0.3s ease;
}

.form-control:focus, .custom-select:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
}

/* Modal Styling */
.modal-content {
    border-radius: 12px;
}

.modal-header {
    border-top-left-radius: 12px;
    border-top-right-radius: 12px;
    border: none;
    padding: 1.5rem;
    position: relative;
}

.modal-header .close {
    position: absolute;
    right: 1rem;
    top: 50%;
    transform: translateY(-50%);
    padding: 0;
    margin: 0;
    background: transparent;
    border: none;
    font-size: 1.5rem;
    font-weight: 700;
    line-height: 1;
    color: #fff;
    text-shadow: 0 1px 0 rgba(0,0,0,0.5);
    opacity: 0.8;
    cursor: pointer;
}

.modal-header .close:hover {
    opacity: 1;
}

.modal-header .modal-title {
    padding-right: 2rem;
}

.modal-footer {
    border: none;
    padding: 1.5rem;
}

/* Alert Styling */
.alert {
    border-radius: 8px;
    border: none;
}

/* Shadow Effects */
.shadow-sm {
    box-shadow: 0 2px 4px rgba(0,0,0,0.1) !important;
}

.shadow-lg {
    box-shadow: 0 8px 16px rgba(0,0,0,0.15) !important;
}

/* Loading State */
.btn-loading {
    pointer-events: none;
    opacity: 0.7;
}

/* Modal backdrop fix */
.modal-backdrop {
    background-color: rgba(0, 0, 0, 0.5);
}

.modal.show .modal-dialog {
    transform: none;
}

/* Responsive */
@media (max-width: 768px) {
    .btn-group-table .btn {
        min-width: auto;
        padding: 0.35rem 0.5rem;
        font-size: 0.8rem;
    }
    
    .table {
        font-size: 0.85rem;
    }
    
    .btn-action {
        padding: 0.6rem 0.8rem;
        font-size: 0.875rem;
    }
}
</style>
@endpush

@push('scripts')
<script>
const calonMahasiswaId = {{ $calonMahasiswa->id }};
let keuanganData = [];

$(document).ready(function() {
    console.log('Initializing keuangan page...');
    loadKeuangan();
    
    // Set default tanggal hari ini dan max date untuk hari ini + 1 hari (untuk menghindari error timezone)
    const today = new Date();
    const tomorrow = new Date(today);
    tomorrow.setDate(tomorrow.getDate() + 1);
    
    const todayStr = today.toISOString().split('T')[0];
    const tomorrowStr = tomorrow.toISOString().split('T')[0];
    
    $('#tanggal_bayar').val(todayStr).attr('max', tomorrowStr);

    // Form Bayar
    $('#formBayar').submit(function(e) {
        e.preventDefault();
        konfirmasiBayar();
    });

    // Form Tambah
    $('#formTambah').submit(function(e) {
        e.preventDefault();
        tambahBiaya();
    });

    // Validasi file upload
    $('#bukti_bayar').change(function() {
        validateFileUpload(this);
    });
    
    // Fix modal close button
    $('.modal').on('hidden.bs.modal', function () {
        $(this).find('form')[0]?.reset();
        $('body').removeClass('modal-open');
        $('.modal-backdrop').remove();
    });
    
    // Pastikan close button bekerja
    $('[data-dismiss="modal"]').on('click', function() {
        $(this).closest('.modal').modal('hide');
    });
});

// Validasi file upload
function validateFileUpload(input) {
    if (input.files && input.files[0]) {
        const file = input.files[0];
        const fileSize = file.size / 1024 / 1024; // in MB
        const allowedTypes = ['image/jpeg', 'image/png', 'image/jpg', 'application/pdf'];
        
        if (!allowedTypes.includes(file.type)) {
            Swal.fire({
                icon: 'error',
                title: 'Format File Tidak Valid',
                text: 'Hanya file JPG, PNG, atau PDF yang diperbolehkan!'
            });
            $(input).val('');
            return false;
        }
        
        if (fileSize > 2) {
            Swal.fire({
                icon: 'error',
                title: 'File Terlalu Besar',
                text: 'Ukuran file maksimal 2MB!'
            });
            $(input).val('');
            return false;
        }
    }
    return true;
}

// Helper function untuk nama jenis biaya
function getJenisBiayaNama(jenis) {
    const namaMap = {
        'formulir': 'Biaya Formulir',
        'ujian': 'Biaya Seleksi/Ujian',
        'daftar_ulang': 'Biaya Daftar Ulang'
    };
    return namaMap[jenis] || jenis;
}

// Load data keuangan
function loadKeuangan() {
    console.log('Loading keuangan data...');
    
    $.ajax({
        url: `/pmb/keuangan/${calonMahasiswaId}`,
        headers: {'X-Requested-With': 'XMLHttpRequest'},
        beforeSend: function() {
            $('#tableKeuangan tbody').html(`
                <tr>
                    <td colspan="5" class="text-center py-5">
                        <i class="fas fa-spinner fa-spin fa-2x text-primary mb-3"></i>
                        <p class="text-muted">Memuat data...</p>
                    </td>
                </tr>
            `);
        },
        success: function(response) {
            console.log('Data loaded successfully:', response);
            keuanganData = response.keuangan || [];
            renderKeuangan(keuanganData);
            updateRingkasan(keuanganData);
            
            // Disable button generate jika sudah ada data
            if(keuanganData.length > 0) {
                $('#btnGenerate').prop('disabled', true)
                    .removeClass('btn-primary')
                    .addClass('btn-secondary')
                    .html('<i class="fas fa-check mr-2"></i> Sudah Di-Generate');
                $('#btnExport').prop('disabled', false);
            } else {
                $('#btnGenerate').prop('disabled', false)
                    .removeClass('btn-secondary')
                    .addClass('btn-primary')
                    .html('<i class="fas fa-cog mr-2"></i> Generate Biaya');
                $('#btnExport').prop('disabled', true);
            }
        },
        error: function(xhr) {
            console.error('Error loading data:', xhr);
            $('#tableKeuangan tbody').html(`
                <tr>
                    <td colspan="5" class="text-center text-danger py-5">
                        <i class="fas fa-exclamation-triangle fa-3x mb-3"></i>
                        <p>Gagal memuat data keuangan</p>
                        <button onclick="loadKeuangan()" class="btn btn-sm btn-primary mt-2">
                            <i class="fas fa-redo mr-2"></i>Coba Lagi
                        </button>
                    </td>
                </tr>
            `);
            
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: 'Gagal memuat data keuangan. Silakan coba lagi.',
                confirmButtonText: 'OK'
            });
        }
    });
}

// Render table
function renderKeuangan(data) {
    let html = '';
    
    if(data.length > 0) {
        data.forEach(item => {
            const statusBadge = item.status_bayar == 'belum_bayar' ? 'danger' : 
                               item.status_bayar == 'sudah_bayar' ? 'success' : 'info';
            const statusText = item.status_bayar == 'belum_bayar' ? 'Belum Bayar' : 
                              item.status_bayar == 'sudah_bayar' ? 'Sudah Bayar' : 'Dibebaskan';
            
            // Pastikan jenis biaya nama ada
            const jenisBiayaNama = item.jenis_biaya_nama || getJenisBiayaNama(item.jenis_biaya);
            
            html += `
                <tr>
                    <td><strong>${jenisBiayaNama}</strong></td>
                    <td><strong class="text-primary">Rp ${formatNumber(item.nominal)}</strong></td>
                    <td class="text-center">
                        <span class="badge badge-${statusBadge}">${statusText}</span>
                    </td>
                    <td class="text-center">${item.tanggal_bayar || '-'}</td>
                    <td>
                        <div class="btn-group-table">
                            ${item.status_bayar == 'belum_bayar' ? `
                                <button onclick="showModalBayar(${item.id}, '${jenisBiayaNama}', ${item.nominal})" 
                                        class="btn btn-sm btn-success" title="Bayar">
                                    <i class="fas fa-money-bill"></i> Bayar
                                </button>
                                <button onclick="bebaskan(${item.id})" 
                                        class="btn btn-sm btn-info" title="Bebaskan">
                                    <i class="fas fa-gift"></i> Bebaskan
                                </button>
                            ` : `
                                ${item.bukti_bayar ? `
                                    <button onclick="lihatBukti('${item.bukti_bayar}')" 
                                            class="btn btn-sm btn-info" title="Lihat Bukti">
                                        <i class="fas fa-file-image"></i> Bukti
                                    </button>
                                ` : ''}
                                <button onclick="batalkan(${item.id})" 
                                        class="btn btn-sm btn-warning" title="Batalkan">
                                    <i class="fas fa-undo"></i> Batalkan
                                </button>
                            `}
                            <button onclick="hapusBiaya(${item.id})" 
                                    class="btn btn-sm btn-danger" title="Hapus">
                                <i class="fas fa-trash"></i> Hapus
                            </button>
                        </div>
                    </td>
                </tr>
            `;
        });
    } else {
        html = `
            <tr>
                <td colspan="5" class="text-center py-5">
                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                    <p class="text-muted mb-0">Belum ada data biaya. Silakan generate biaya terlebih dahulu.</p>
                </td>
            </tr>
        `;
    }
    
    $('#tableKeuangan tbody').html(html);
}

// Update ringkasan
function updateRingkasan(data) {
    let total = 0, bayar = 0;
    
    data.forEach(item => {
        total += parseFloat(item.nominal);
        if(item.status_bayar != 'belum_bayar') {
            bayar += parseFloat(item.nominal);
        }
    });
    
    $('#totalBiaya').text('Rp ' + formatNumber(total));
    $('#totalBayar').text('Rp ' + formatNumber(bayar));
    $('#sisaTagihan').text('Rp ' + formatNumber(total - bayar));
}

// Generate biaya
function generateBiaya() {
    Swal.fire({
        title: 'Generate Biaya?',
        text: 'Biaya akan di-generate otomatis sesuai jalur masuk.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#667eea',
        cancelButtonColor: '#6c757d',
        confirmButtonText: '<i class="fas fa-check mr-2"></i>Ya, Generate!',
        cancelButtonText: '<i class="fas fa-times mr-2"></i>Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            const btn = $('#btnGenerate');
            const originalHtml = btn.html();
            
            btn.prop('disabled', true)
               .addClass('btn-loading')
               .html('<i class="fas fa-spinner fa-spin mr-2"></i>Processing...');
            
            $.ajax({
                url: `/pmb/keuangan/${calonMahasiswaId}/generate`,
                method: 'POST',
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                success: function(response) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: response.message,
                        showConfirmButton: false,
                        timer: 1500
                    });
                    
                    setTimeout(function() {
                        loadKeuangan();
                    }, 500);
                },
                error: function(xhr) {
                    btn.prop('disabled', false)
                       .removeClass('btn-loading')
                       .html(originalHtml);
                    
                    let message = 'Gagal generate biaya!';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        message = xhr.responseJSON.message;
                    }
                    
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: message,
                        confirmButtonText: 'OK'
                    });
                }
            });
        }
    });
}

// Show modal bayar
function showModalBayar(id, jenis, nominal) {
    $('#keuanganId').val(id);
    $('#infoBiaya').html(`
        <strong><i class="fas fa-info-circle mr-2"></i>${jenis}</strong><br>
        Nominal: <strong class="text-primary">Rp ${formatNumber(nominal)}</strong>
    `);
    $('#formBayar')[0].reset();
    
    // Set tanggal hari ini dan max date untuk hari ini + 1 hari
    const today = new Date();
    const tomorrow = new Date(today);
    tomorrow.setDate(tomorrow.getDate() + 1);
    
    const todayStr = today.toISOString().split('T')[0];
    const tomorrowStr = tomorrow.toISOString().split('T')[0];
    
    $('#tanggal_bayar').val(todayStr).attr('max', tomorrowStr);
    $('#modalBayar').modal('show');
}

// Konfirmasi bayar
function konfirmasiBayar() {
    const id = $('#keuanganId').val();
    const tanggal = $('#tanggal_bayar').val();
    const metode = $('#metode_bayar').val();
    
    // Validasi metode
    if (!metode) {
        Swal.fire({
            icon: 'error',
            title: 'Metode Bayar Belum Dipilih',
            text: 'Silakan pilih metode pembayaran!'
        });
        return;
    }
    
    // Validasi tanggal - lebih permisif, hanya cek jika lebih dari besok
    const selectedDate = new Date(tanggal);
    const tomorrow = new Date();
    tomorrow.setDate(tomorrow.getDate() + 1);
    tomorrow.setHours(23, 59, 59, 999);
    
    if (selectedDate > tomorrow) {
        Swal.fire({
            icon: 'error',
            title: 'Tanggal Tidak Valid',
            text: 'Tanggal pembayaran tidak boleh lebih dari hari ini!'
        });
        return;
    }
    
    const formData = new FormData($('#formBayar')[0]);
    const btn = $('#btnKonfirmasi');
    const originalHtml = btn.html();
    
    btn.prop('disabled', true)
       .addClass('btn-loading')
       .html('<i class="fas fa-spinner fa-spin mr-2"></i>Menyimpan...');
    
    $.ajax({
        url: `/pmb/keuangan/bayar/${id}`,
        method: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        success: function(response) {
            $('#modalBayar').modal('hide');
            
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: response.message,
                showConfirmButton: false,
                timer: 1500
            });
            
            setTimeout(function() {
                loadKeuangan();
                btn.prop('disabled', false)
                   .removeClass('btn-loading')
                   .html(originalHtml);
                $('#formBayar')[0].reset();
            }, 500);
        },
        error: function(xhr) {
            btn.prop('disabled', false)
               .removeClass('btn-loading')
               .html(originalHtml);
            
            let message = 'Gagal konfirmasi pembayaran!';
            if (xhr.responseJSON) {
                if (xhr.responseJSON.errors) {
                    message = Object.values(xhr.responseJSON.errors).flat().join('\n');
                } else if (xhr.responseJSON.message) {
                    message = xhr.responseJSON.message;
                }
            }
            
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: message,
                confirmButtonText: 'OK'
            });
        }
    });
}

// Bebaskan
function bebaskan(id) {
    Swal.fire({
        title: 'Bebaskan Biaya?',
        text: 'Biaya ini akan dibebaskan dari pembayaran.',
        icon: 'info',
        showCancelButton: true,
        confirmButtonColor: '#17a2b8',
        cancelButtonColor: '#6c757d',
        confirmButtonText: '<i class="fas fa-check mr-2"></i>Ya, Bebaskan!',
        cancelButtonText: '<i class="fas fa-times mr-2"></i>Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: `/pmb/keuangan/bebaskan/${id}`,
                method: 'POST',
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                success: function(response) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: response.message,
                        showConfirmButton: false,
                        timer: 1500
                    });
                    
                    setTimeout(function() {
                        loadKeuangan();
                    }, 500);
                },
                error: function(xhr) {
                    let message = 'Gagal membebaskan biaya!';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        message = xhr.responseJSON.message;
                    }
                    
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: message,
                        confirmButtonText: 'OK'
                    });
                }
            });
        }
    });
}

// Batalkan
function batalkan(id) {
    Swal.fire({
        title: 'Batalkan Pembayaran?',
        text: 'Status akan kembali ke belum bayar.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ffc107',
        cancelButtonColor: '#6c757d',
        confirmButtonText: '<i class="fas fa-check mr-2"></i>Ya, Batalkan!',
        cancelButtonText: '<i class="fas fa-times mr-2"></i>Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: `/pmb/keuangan/batalkan/${id}`,
                method: 'POST',
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                success: function(response) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: response.message,
                        showConfirmButton: false,
                        timer: 1500
                    });
                    
                    setTimeout(function() {
                        loadKeuangan();
                    }, 500);
                },
                error: function(xhr) {
                    let message = 'Gagal membatalkan pembayaran!';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        message = xhr.responseJSON.message;
                    }
                    
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: message,
                        confirmButtonText: 'OK'
                    });
                }
            });
        }
    });
}

// Show modal tambah
function showModalTambah() {
    $('#formTambah')[0].reset();
    $('#modalTambah').modal('show');
}

// Tambah biaya manual
function tambahBiaya() {
    const jenis = $('#jenis_biaya').val();
    const nominal = $('#nominal').val();
    
    // Validasi
    if (!jenis) {
        Swal.fire({
            icon: 'error',
            title: 'Jenis Biaya Belum Dipilih',
            text: 'Silakan pilih jenis biaya!'
        });
        return;
    }
    
    if (!nominal || nominal <= 0) {
        Swal.fire({
            icon: 'error',
            title: 'Nominal Tidak Valid',
            text: 'Nominal harus lebih dari 0!'
        });
        return;
    }
    
    const btn = $('#btnSimpanTambah');
    const originalHtml = btn.html();
    
    btn.prop('disabled', true)
       .addClass('btn-loading')
       .html('<i class="fas fa-spinner fa-spin mr-2"></i>Menyimpan...');
    
    $.ajax({
        url: `/pmb/keuangan/${calonMahasiswaId}/tambah`,
        method: 'POST',
        data: $('#formTambah').serialize(),
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        success: function(response) {
            $('#modalTambah').modal('hide');
            
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: response.message,
                showConfirmButton: false,
                timer: 1500
            });
            
            setTimeout(function() {
                loadKeuangan();
                btn.prop('disabled', false)
                   .removeClass('btn-loading')
                   .html(originalHtml);
                $('#formTambah')[0].reset();
            }, 500);
        },
        error: function(xhr) {
            btn.prop('disabled', false)
               .removeClass('btn-loading')
               .html(originalHtml);
            
            let message = 'Gagal menambahkan biaya!';
            if (xhr.responseJSON) {
                if (xhr.responseJSON.errors) {
                    message = Object.values(xhr.responseJSON.errors).flat().join('\n');
                } else if (xhr.responseJSON.message) {
                    message = xhr.responseJSON.message;
                }
            }
            
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: message,
                confirmButtonText: 'OK'
            });
        }
    });
}

// Hapus biaya
function hapusBiaya(id) {
    Swal.fire({
        title: 'Hapus Biaya?',
        text: 'Data akan dihapus permanen!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: '<i class="fas fa-check mr-2"></i>Ya, Hapus!',
        cancelButtonText: '<i class="fas fa-times mr-2"></i>Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: `/pmb/keuangan/${id}`,
                method: 'DELETE',
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                success: function(response) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Terhapus!',
                        text: response.message,
                        showConfirmButton: false,
                        timer: 1500
                    });
                    
                    setTimeout(function() {
                        loadKeuangan();
                    }, 500);
                },
                error: function(xhr) {
                    let message = 'Gagal menghapus biaya!';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        message = xhr.responseJSON.message;
                    }
                    
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: message,
                        confirmButtonText: 'OK'
                    });
                }
            });
        }
    });
}

// Lihat bukti bayar
function lihatBukti(path) {
    if(!path) {
        Swal.fire({
            icon: 'info',
            title: 'Tidak Ada Bukti',
            text: 'Tidak ada bukti pembayaran untuk transaksi ini.',
            confirmButtonText: 'OK'
        });
        return;
    }
    
    $('#modalBukti').modal('show');
    
    const fileExt = path.split('.').pop().toLowerCase();
    const isPdf = fileExt === 'pdf';
    
    if (isPdf) {
        $('#buktiBayarContent').html(`
            <embed src="/storage/${path}" type="application/pdf" width="100%" height="600px" />
            <div class="mt-3">
                <a href="/storage/${path}" target="_blank" class="btn btn-primary">
                    <i class="fas fa-external-link-alt mr-2"></i>Buka di Tab Baru
                </a>
            </div>
        `);
    } else {
        $('#buktiBayarContent').html(`
            <img src="/storage/${path}" class="img-fluid rounded shadow" alt="Bukti Pembayaran" style="max-height: 600px;">
            <div class="mt-3">
                <a href="/storage/${path}" target="_blank" class="btn btn-primary">
                    <i class="fas fa-external-link-alt mr-2"></i>Buka di Tab Baru
                </a>
            </div>
        `);
    }
}

// Export keuangan PDF
function exportKeuanganPDF() {
    if (keuanganData.length === 0) {
        Swal.fire({
            icon: 'warning',
            title: 'Tidak Ada Data',
            text: 'Tidak ada data keuangan untuk di-export!',
            confirmButtonText: 'OK'
        });
        return;
    }
    
    const btn = $('#btnExport');
    const originalHtml = btn.html();
    
    btn.prop('disabled', true)
       .addClass('btn-loading')
       .html('<i class="fas fa-spinner fa-spin mr-2"></i>Generating...');
    
    // Simulasi export - dalam implementasi nyata, panggil endpoint backend
    setTimeout(function() {
        window.open(`/pmb/keuangan/${calonMahasiswaId}/export-pdf`, '_blank');
        
        btn.prop('disabled', false)
           .removeClass('btn-loading')
           .html(originalHtml);
        
        Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: 'File PDF berhasil di-generate!',
            showConfirmButton: false,
            timer: 1500
        });
    }, 1000);
}

// Format number
function formatNumber(num) {
    return parseFloat(num).toLocaleString('id-ID');
}

// Keyboard shortcuts
$(document).keydown(function(e) {
    // Ctrl/Cmd + R = Refresh
    if ((e.ctrlKey || e.metaKey) && e.keyCode === 82) {
        e.preventDefault();
        loadKeuangan();
    }
    
    // ESC = Close modals
    if (e.keyCode === 27) {
        $('.modal').modal('hide');
    }
});
</script>
@endpush