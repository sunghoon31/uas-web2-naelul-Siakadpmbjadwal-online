<!-- Modal Create/Edit with Document Upload -->
<div class="modal fade" id="modalCreate" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-gradient-primary text-white">
                <h5 class="modal-title font-weight-bold" id="modalCreateLabel">
                    <i class="fas fa-user-plus mr-2"></i> Form Pendaftaran Calon Mahasiswa
                </h5>
                <button type="button" class="close text-white close-button" onclick="closeModal()" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="formCalonMahasiswa" enctype="multipart/form-data">
                <?php echo csrf_field(); ?>
                <div class="modal-body">
                    <input type="hidden" id="calonMahasiswaId" name="id">
                    <input type="hidden" id="formMethod" name="_method" value="POST">
                    
                    <!-- Nav Tabs -->
                    <ul class="nav nav-tabs nav-fill mb-4" id="formTabs" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="data-pribadi-tab" data-toggle="tab" href="#dataPribadi" role="tab" aria-controls="dataPribadi" aria-selected="true">
                                <i class="fas fa-user mr-2"></i> Data Pribadi
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="data-pendaftaran-tab" data-toggle="tab" href="#dataPendaftaran" role="tab" aria-controls="dataPendaftaran" aria-selected="false">
                                <i class="fas fa-graduation-cap mr-2"></i> Data Pendaftaran
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="dokumen-tab" data-toggle="tab" href="#dokumenSection" role="tab" aria-controls="dokumenSection" aria-selected="false">
                                <i class="fas fa-file-upload mr-2"></i> Upload Dokumen
                            </a>
                        </li>
                    </ul>

                    <!-- Tab Content -->
                    <div class="tab-content" id="formTabsContent">
                        <!-- Tab 1: Data Pribadi -->
                        <div class="tab-pane fade show active" id="dataPribadi" role="tabpanel" aria-labelledby="data-pribadi-tab">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="font-weight-bold">Nama Lengkap <span class="text-danger">*</span></label>
                                        <input type="text" name="nama" id="nama" class="form-control form-control-lg" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="font-weight-bold">Jenis Kelamin <span class="text-danger">*</span></label>
                                        <select name="jenis_kelamin" id="jenis_kelamin" class="form-control form-control-lg custom-select" required>
                                            <option value="">Pilih Jenis Kelamin</option>
                                            <option value="L">Laki-laki</option>
                                            <option value="P">Perempuan</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="font-weight-bold">Alamat <span class="text-danger">*</span></label>
                                <textarea name="alamat" id="alamat" rows="3" class="form-control" required></textarea>
                            </div>
                            <div class="form-group">
                                <label class="font-weight-bold">No HP <span class="text-danger">*</span></label>
                                <input type="text" name="no_hp" id="no_hp" class="form-control form-control-lg" required>
                            </div>
                        </div>

                        <!-- Tab 2: Data Pendaftaran -->
                        <div class="tab-pane fade" id="dataPendaftaran" role="tabpanel" aria-labelledby="data-pendaftaran-tab">
                            <div class="form-group">
                                <label class="font-weight-bold">Program Studi <span class="text-danger">*</span></label>
                                <select name="prodi_id" id="prodi_id" class="form-control form-control-lg custom-select" required>
                                    <option value="">Pilih Program Studi</option>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $prodis->groupBy('fakultas.nama_fakultas'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $fakultas => $prodiList): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <optgroup label="<?php echo e($fakultas); ?>">
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $prodiList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $prodi): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($prodi->id); ?>"><?php echo e($prodi->nama_prodi); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </optgroup>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="font-weight-bold">Jalur Masuk <span class="text-danger">*</span></label>
                                <select name="jalur_masuk" id="jalur_masuk" class="form-control form-control-lg custom-select" required>
                                    <option value="">Pilih Jalur Masuk</option>
                                    <option value="reguler">Reguler</option>
                                    <option value="prestasi">Prestasi</option>
                                    <option value="beasiswa">Beasiswa</option>
                                    <option value="pindahan">Pindahan</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="font-weight-bold">Gelombang</label>
                                <input type="text" name="gelombang" id="gelombang" class="form-control form-control-lg" placeholder="Contoh: Gelombang 1">
                            </div>
                        </div>

                        <!-- Tab 3: Upload Dokumen -->
                        <div class="tab-pane fade" id="dokumenSection" role="tabpanel" aria-labelledby="dokumen-tab">
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle mr-2"></i>
                                <strong>Catatan:</strong> Upload dokumen pendukung untuk mempercepat proses verifikasi. Format: JPG, PNG, PDF. Maksimal 5MB per file.
                            </div>

                            <div id="dokumenUploadContainer">
                                <!-- Dokumen Wajib -->
                                <h6 class="font-weight-bold text-primary mb-3">
                                    <i class="fas fa-check-circle mr-2"></i> Dokumen Wajib
                                </h6>
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="font-weight-bold">Ijazah/STTB</label>
                                        <div class="custom-file">
                                            <input type="file" class="custom-file-input dokumen-file" id="file_ijazah" name="dokumen[ijazah]" accept=".jpg,.jpeg,.png,.pdf">
                                            <label class="custom-file-label" for="file_ijazah">Pilih file...</label>
                                        </div>
                                        <small class="text-muted">Max: 5MB</small>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="font-weight-bold">Transkrip Nilai</label>
                                        <div class="custom-file">
                                            <input type="file" class="custom-file-input dokumen-file" id="file_transkrip" name="dokumen[transkrip_nilai]" accept=".jpg,.jpeg,.png,.pdf">
                                            <label class="custom-file-label" for="file_transkrip">Pilih file...</label>
                                        </div>
                                        <small class="text-muted">Max: 5MB</small>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="font-weight-bold">Kartu Keluarga</label>
                                        <div class="custom-file">
                                            <input type="file" class="custom-file-input dokumen-file" id="file_kk" name="dokumen[kartu_keluarga]" accept=".jpg,.jpeg,.png,.pdf">
                                            <label class="custom-file-label" for="file_kk">Pilih file...</label>
                                        </div>
                                        <small class="text-muted">Max: 5MB</small>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="font-weight-bold">Akta Kelahiran</label>
                                        <div class="custom-file">
                                            <input type="file" class="custom-file-input dokumen-file" id="file_akta" name="dokumen[akta_kelahiran]" accept=".jpg,.jpeg,.png,.pdf">
                                            <label class="custom-file-label" for="file_akta">Pilih file...</label>
                                        </div>
                                        <small class="text-muted">Max: 5MB</small>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="font-weight-bold">Pas Foto 3x4</label>
                                        <div class="custom-file">
                                            <input type="file" class="custom-file-input dokumen-file" id="file_foto" name="dokumen[foto_diri]" accept=".jpg,.jpeg,.png">
                                            <label class="custom-file-label" for="file_foto">Pilih file...</label>
                                        </div>
                                        <small class="text-muted">Format: JPG, PNG. Max: 5MB</small>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="font-weight-bold">Surat Keterangan Sehat</label>
                                        <div class="custom-file">
                                            <input type="file" class="custom-file-input dokumen-file" id="file_sehat" name="dokumen[surat_keterangan_sehat]" accept=".jpg,.jpeg,.png,.pdf">
                                            <label class="custom-file-label" for="file_sehat">Pilih file...</label>
                                        </div>
                                        <small class="text-muted">Max: 5MB</small>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="font-weight-bold">Surat Kelakuan Baik</label>
                                        <div class="custom-file">
                                            <input type="file" class="custom-file-input dokumen-file" id="file_kelakuan" name="dokumen[surat_kelakuan_baik]" accept=".jpg,.jpeg,.png,.pdf">
                                            <label class="custom-file-label" for="file_kelakuan">Pilih file...</label>
                                        </div>
                                        <small class="text-muted">Max: 5MB</small>
                                    </div>
                                </div>

                                <!-- Dokumen Opsional (muncul berdasarkan jalur masuk) -->
                                <div id="dokumenOpsionalContainer" style="display: none;">
                                    <hr class="my-4">
                                    <h6 class="font-weight-bold text-success mb-3">
                                        <i class="fas fa-plus-circle mr-2"></i> Dokumen Tambahan
                                    </h6>
                                    
                                    <div class="row">
                                        <div class="col-md-6 mb-3" id="container_sertifikat" style="display: none;">
                                            <label class="font-weight-bold">Sertifikat Prestasi</label>
                                            <div class="custom-file">
                                                <input type="file" class="custom-file-input dokumen-file" id="file_sertifikat" name="dokumen[sertifikat_prestasi]" accept=".jpg,.jpeg,.png,.pdf">
                                                <label class="custom-file-label" for="file_sertifikat">Pilih file...</label>
                                            </div>
                                            <small class="text-muted">Max: 5MB</small>
                                        </div>

                                        <div class="col-md-6 mb-3" id="container_rekomendasi" style="display: none;">
                                            <label class="font-weight-bold">Surat Rekomendasi</label>
                                            <div class="custom-file">
                                                <input type="file" class="custom-file-input dokumen-file" id="file_rekomendasi" name="dokumen[surat_rekomendasi]" accept=".jpg,.jpeg,.png,.pdf">
                                                <label class="custom-file-label" for="file_rekomendasi">Pilih file...</label>
                                            </div>
                                            <small class="text-muted">Max: 5MB</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer bg-light d-flex justify-content-between">
                    <button type="button" class="btn btn-secondary btn-lg" id="btnSebelumnya" style="display: none;">
                        <i class="fas fa-arrow-left mr-2"></i> Sebelumnya
                    </button>
                    <div class="ml-auto">
                        <button type="button" class="btn btn-light btn-lg mr-2" onclick="closeModal()">
                            <i class="fas fa-times mr-2"></i> Batal
                        </button>
                        <button type="button" class="btn btn-primary btn-lg" id="btnSelanjutnya">
                            Selanjutnya <i class="fas fa-arrow-right ml-2"></i>
                        </button>
                        <button type="button" class="btn btn-success btn-lg" id="btnSimpan" style="display: none;">
                            <i class="fas fa-save mr-2"></i> Simpan Data
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<?php $__env->startPush('styles'); ?>
<style>
/* Gradient Background */
.bg-gradient-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

/* Modal Styling */
.modal-xl {
    max-width: 1140px;
}

.modal-content {
    border-radius: 12px;
    overflow: hidden;
}

.modal-header {
    border-top-left-radius: 12px;
    border-top-right-radius: 12px;
    border: none;
    padding: 1.5rem 2rem;
    position: relative;
}

.modal-header .close-button {
    position: absolute;
    top: 15px;
    right: 20px;
    padding: 0;
    margin: 0;
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.2);
    border: 2px solid rgba(255, 255, 255, 0.3);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
    line-height: 1;
    opacity: 1;
    cursor: pointer;
    transition: all 0.3s ease;
    z-index: 1;
}

.modal-header .close-button:hover {
    background: rgba(255, 255, 255, 0.3);
    border-color: rgba(255, 255, 255, 0.5);
    transform: rotate(90deg);
}

.modal-header .close-button span {
    display: block;
    line-height: 1;
}

.modal-title {
    padding-right: 50px;
}

.modal-footer {
    border: none;
    padding: 1.5rem;
    background: #f8f9fa;
}

/* Tabs Styling */
.nav-tabs {
    border-bottom: 2px solid #e9ecef;
}

.nav-tabs .nav-link {
    border: none;
    color: #6c757d;
    font-weight: 600;
    padding: 1rem 1.5rem;
    transition: all 0.3s ease;
    cursor: pointer;
    position: relative;
}

.nav-tabs .nav-link:hover {
    color: #667eea;
    border-color: transparent;
    background: rgba(102, 126, 234, 0.05);
}

.nav-tabs .nav-link.active {
    color: #667eea;
    border-bottom: 3px solid #667eea;
    background: transparent;
}

.nav-tabs .nav-link.active::after {
    content: '';
    position: absolute;
    bottom: -2px;
    left: 0;
    right: 0;
    height: 3px;
    background: #667eea;
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
    outline: none;
}

.form-control-lg {
    padding: 0.75rem 1.25rem;
    font-size: 1rem;
}

.form-control.is-invalid, .custom-select.is-invalid {
    border-color: #dc3545;
}

.invalid-feedback {
    display: block;
    color: #dc3545;
    font-size: 0.875rem;
    margin-top: 0.25rem;
}

/* Custom File Input */
.custom-file {
    height: calc(2.5rem + 2px);
}

.custom-file-label {
    border-radius: 8px;
    border: 2px dashed #dee2e6;
    padding: 0.6rem 1rem;
    background: #f8f9fa;
    cursor: pointer;
    transition: all 0.3s ease;
    height: calc(2.5rem + 2px);
    line-height: 1.5;
}

.custom-file-label:hover {
    border-color: #667eea;
    background: #e7f1ff;
}

.custom-file-input:focus ~ .custom-file-label {
    border-color: #667eea;
    box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
}

.custom-file-label::after {
    background: #667eea;
    color: white;
    border-radius: 0 6px 6px 0;
    padding: 0.6rem 1rem;
    content: "Browse";
    height: calc(2.5rem - 2px);
    line-height: 1.5;
}

.custom-file-label.selected {
    color: #495057;
    background: #fff;
    border-color: #667eea;
}

/* Button Styling */
.btn {
    border-radius: 8px;
    font-weight: 600;
    transition: all 0.3s ease;
    border: none;
    cursor: pointer;
}

.btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

.btn:active {
    transform: translateY(0);
}

.btn:disabled {
    cursor: not-allowed;
    opacity: 0.6;
}

.btn-lg {
    padding: 0.6rem 1.5rem;
    font-size: 1rem;
}

.btn-light {
    background: #fff;
    color: #6c757d;
    border: 2px solid #dee2e6;
}

.btn-light:hover {
    background: #f8f9fa;
    color: #495057;
    border-color: #adb5bd;
}

.btn-primary {
    background: #667eea;
    color: white;
}

.btn-primary:hover {
    background: #5568d3;
}

.btn-success {
    background: #28a745;
    color: white;
}

.btn-success:hover {
    background: #218838;
}

.btn-secondary {
    background: #6c757d;
    color: white;
}

.btn-secondary:hover {
    background: #5a6268;
}

/* Alert Styling */
.alert {
    border-radius: 8px;
    border: none;
    padding: 1rem 1.25rem;
}

.alert-info {
    background: #e7f1ff;
    color: #004085;
    border-left: 4px solid #667eea;
}

/* Tab Content Animation */
.tab-pane {
    animation: fadeIn 0.3s ease-in-out;
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Loading State */
.btn .fa-spinner {
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

/* Responsive */
@media (max-width: 768px) {
    .modal-xl {
        max-width: 95%;
        margin: 0.5rem;
    }
    
    .modal-header {
        padding: 1rem 1.5rem;
    }
    
    .modal-header .close-button {
        width: 35px;
        height: 35px;
        font-size: 20px;
        top: 12px;
        right: 15px;
    }
    
    .nav-tabs .nav-link {
        padding: 0.75rem 0.5rem;
        font-size: 0.9rem;
    }
    
    .btn-lg {
        padding: 0.5rem 1rem;
        font-size: 0.9rem;
    }
}
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
let currentTab = 0;
const tabs = ['dataPribadi', 'dataPendaftaran', 'dokumenSection'];
const tabButtons = ['data-pribadi-tab', 'data-pendaftaran-tab', 'dokumen-tab'];

$(document).ready(function() {
    // Update custom file input label saat file dipilih
    $('.dokumen-file').on('change', function() {
        const fileName = $(this).val().split('\\').pop();
        $(this).next('.custom-file-label').addClass("selected").html(fileName || 'Pilih file...');
        
        // Validasi ukuran file (max 5MB)
        const fileSize = this.files[0]?.size || 0;
        if (fileSize > 5242880) { // 5MB dalam bytes
            Swal.fire({
                icon: 'error',
                title: 'File Terlalu Besar',
                text: 'Ukuran file maksimal 5MB',
                confirmButtonColor: '#667eea'
            });
            $(this).val('');
            $(this).next('.custom-file-label').removeClass('selected').html('Pilih file...');
        }
    });

    // Show/hide dokumen tambahan berdasarkan jalur masuk
    $('#jalur_masuk').on('change', function() {
        const jalur = $(this).val();
        
        // Reset semua dokumen opsional
        $('#dokumenOpsionalContainer').hide();
        $('#container_sertifikat').hide();
        $('#container_rekomendasi').hide();
        
        if (jalur === 'prestasi') {
            $('#dokumenOpsionalContainer').fadeIn();
            $('#container_sertifikat').fadeIn();
        } else if (jalur === 'beasiswa') {
            $('#dokumenOpsionalContainer').fadeIn();
            $('#container_rekomendasi').fadeIn();
        }
    });

    // Tombol Selanjutnya
    $('#btnSelanjutnya').on('click', function(e) {
        e.preventDefault();
        if (validateCurrentTab()) {
            nextTab();
        }
    });

    // Tombol Sebelumnya
    $('#btnSebelumnya').on('click', function(e) {
        e.preventDefault();
        prevTab();
    });

    // Tombol Simpan
    $('#btnSimpan').on('click', function(e) {
        e.preventDefault();
        if (validateCurrentTab()) {
            saveCalonMahasiswa();
        }
    });

    // Prevent tab click langsung (harus pakai tombol navigasi)
    $('#formTabs .nav-link').on('click', function(e) {
        e.preventDefault();
        return false;
    });

    // Prevent form submit dengan enter
    $('#formCalonMahasiswa').on('submit', function(e) {
        e.preventDefault();
        return false;
    });
});

function validateCurrentTab() {
    const currentTabElement = $('#' + tabs[currentTab]);
    let isValid = true;
    let firstInvalidField = null;
    
    // Validasi field required di tab saat ini
    currentTabElement.find('input[required], select[required], textarea[required]').each(function() {
        if (!$(this).val() || $(this).val().trim() === '') {
            isValid = false;
            $(this).addClass('is-invalid');
            
            if (!firstInvalidField) {
                firstInvalidField = $(this);
            }
            
            // Tambah feedback jika belum ada
            if (!$(this).next('.invalid-feedback').length) {
                $(this).after('<div class="invalid-feedback">Field ini wajib diisi</div>');
            }
        } else {
            $(this).removeClass('is-invalid');
            $(this).next('.invalid-feedback').remove();
        }
    });
    
    if (!isValid) {
        Swal.fire({
            icon: 'warning',
            title: 'Perhatian!',
            text: 'Mohon lengkapi semua field yang wajib diisi',
            confirmButtonColor: '#667eea'
        });
        
        // Focus pada field pertama yang invalid
        if (firstInvalidField) {
            firstInvalidField.focus();
        }
    }
    
    return isValid;
}

function showTab(index) {
    // Hide all tabs
    $('.tab-pane').removeClass('show active');
    $('#formTabs .nav-link').removeClass('active');
    
    // Show current tab
    $('#' + tabs[index]).addClass('show active');
    $('#' + tabButtons[index]).addClass('active');
    
    // Update button visibility
    if (index === 0) {
        $('#btnSebelumnya').hide();
        $('#btnSelanjutnya').show();
        $('#btnSimpan').hide();
    } else if (index === tabs.length - 1) {
        $('#btnSebelumnya').show();
        $('#btnSelanjutnya').hide();
        $('#btnSimpan').show();
    } else {
        $('#btnSebelumnya').show();
        $('#btnSelanjutnya').show();
        $('#btnSimpan').hide();
    }
    
    // Scroll to top of modal
    $('.modal-body').scrollTop(0);
}

function nextTab() {
    if (currentTab < tabs.length - 1) {
        currentTab++;
        showTab(currentTab);
    }
}

function prevTab() {
    if (currentTab > 0) {
        currentTab--;
        showTab(currentTab);
    }
}

function closeModal() {
    // Konfirmasi jika ada data yang sudah diisi
    const formData = $('#formCalonMahasiswa').serializeArray();
    let hasData = false;
    
    $.each(formData, function(i, field) {
        if (field.value && field.name !== '_token' && field.name !== '_method') {
            hasData = true;
            return false;
        }
    });
    
    if (hasData) {
        Swal.fire({
            title: 'Konfirmasi',
            text: 'Data yang sudah diisi akan hilang. Apakah Anda yakin ingin menutup form?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#667eea',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Tutup',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                forceCloseModal();
            }
        });
    } else {
        forceCloseModal();
    }
}

function forceCloseModal() {
    // Reset form
    $('#formCalonMahasiswa')[0].reset();
    $('#calonMahasiswaId').val('');
    $('#formMethod').val('POST');
    
    // Reset custom file labels
    $('.custom-file-label').html('Pilih file...').removeClass('selected');
    
    // Reset tabs
    currentTab = 0;
    showTab(0);
    
    // Remove validation feedback
    $('.is-invalid').removeClass('is-invalid');
    $('.invalid-feedback').remove();
    
    // Hide dokumen opsional
    $('#dokumenOpsionalContainer').hide();
    $('#container_sertifikat').hide();
    $('#container_rekomendasi').hide();
    
    // Close modal
    $('#modalCreate').modal('hide');
    
    // Clean up backdrop
    setTimeout(function() {
        $('.modal-backdrop').remove();
        $('body').removeClass('modal-open').css('padding-right', '');
    }, 300);
}

function showModalCreate() {
    console.log('Opening create modal...');
    
    // Reset form
    $('#formCalonMahasiswa')[0].reset();
    $('#calonMahasiswaId').val('');
    $('#formMethod').val('POST');
    $('.custom-file-label').html('Pilih file...').removeClass('selected');
    $('#modalCreateLabel').html('<i class="fas fa-user-plus mr-2"></i> Form Pendaftaran Calon Mahasiswa');
    
    // Reset tabs
    currentTab = 0;
    showTab(0);
    
    // Remove validation feedback
    $('.is-invalid').removeClass('is-invalid');
    $('.invalid-feedback').remove();
    
    // Hide dokumen opsional
    $('#dokumenOpsionalContainer').hide();
    $('#container_sertifikat').hide();
    $('#container_rekomendasi').hide();
    
    // Show modal
    $('#modalCreate').modal('show');
}

function saveCalonMahasiswa() {
    const id = $('#calonMahasiswaId').val();
    const formData = new FormData($('#formCalonMahasiswa')[0]);
    
    let url, method;
    if (id) {
        url = '<?php echo e(url("pmb/calon-mahasiswa")); ?>/' + id;
        method = 'POST';
        formData.append('_method', 'PUT');
    } else {
        url = '<?php echo e(route("pmb.calon-mahasiswa.store")); ?>';
        method = 'POST';
    }
    
    const btnSimpan = $('#btnSimpan');
    const originalText = btnSimpan.html();
    btnSimpan.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-2"></i> Menyimpan...');

    $.ajax({
        url: url,
        method: method,
        data: formData,
        processData: false,
        contentType: false,
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
            'X-Requested-With': 'XMLHttpRequest'
        },
        success: function(response) {
            btnSimpan.prop('disabled', false).html(originalText);
            forceCloseModal();
            
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: response.message || (id ? 'Data berhasil diperbarui' : 'Data berhasil disimpan'),
                showConfirmButton: false,
                timer: 1500,
                confirmButtonColor: '#667eea'
            });
            
            // Reload data setelah sukses
            setTimeout(function() {
                if (typeof loadData === 'function') {
                    loadData();
                } else {
                    location.reload();
                }
            }, 500);
        },
        error: function(xhr) {
            btnSimpan.prop('disabled', false).html(originalText);
            
            let message = 'Terjadi kesalahan!';
            if (xhr.responseJSON) {
                if (xhr.responseJSON.errors) {
                    message = '';
                    $.each(xhr.responseJSON.errors, function(key, value) {
                        message += value[0] + '\n';
                    });
                } else if (xhr.responseJSON.message) {
                    message = xhr.responseJSON.message;
                }
            }
            
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: message,
                confirmButtonColor: '#667eea'
            });
        }
    });
}

function editData(id) {
    $.ajax({
        url: '<?php echo e(url("pmb/calon-mahasiswa")); ?>/' + id,
        headers: {'X-Requested-With': 'XMLHttpRequest'},
        success: function(response) {
            const data = response.data || response;
            
            // Set form values
            $('#calonMahasiswaId').val(data.id);
            $('#formMethod').val('PUT');
            $('#nama').val(data.nama);
            $('#jenis_kelamin').val(data.jenis_kelamin);
            $('#alamat').val(data.alamat);
            $('#no_hp').val(data.no_hp);
            $('#prodi_id').val(data.prodi_id);
            $('#jalur_masuk').val(data.jalur_masuk).trigger('change');
            $('#gelombang').val(data.gelombang);
            
            // Update modal title
            $('#modalCreateLabel').html('<i class="fas fa-edit mr-2"></i> Edit Data Calon Mahasiswa');
            
            // Reset tabs
            currentTab = 0;
            showTab(0);
            
            // Remove validation feedback
            $('.is-invalid').removeClass('is-invalid');
            $('.invalid-feedback').remove();
            
            // Show modal
            $('#modalCreate').modal('show');
        },
        error: function(xhr) {
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: 'Gagal memuat data!',
                confirmButtonColor: '#667eea'
            });
        }
    });
}

// Handle modal hidden event
$('#modalCreate').on('hidden.bs.modal', function() {
    // Clean up
    $('.modal-backdrop').remove();
    $('body').removeClass('modal-open').css('padding-right', '');
});

// Handle ESC key
$(document).on('keydown', function(e) {
    if (e.key === 'Escape' && $('#modalCreate').hasClass('show')) {
        e.preventDefault();
        closeModal();
    }
});
</script>
<?php $__env->stopPush(); ?><?php /**PATH C:\Users\Lenovo\UAS-WEB2\resources\views/calon-mahasiswa/tambah.blade.php ENDPATH**/ ?>