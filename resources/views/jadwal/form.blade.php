<!-- File: resources/views/jadwal/form.blade.php -->

<!-- Modal Tambah/Edit Jadwal -->
<div class="modal fade" id="modalJadwal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="modalJadwalTitle">Tambah Jadwal</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="formJadwal">
                <div class="modal-body">
                    <input type="hidden" id="jadwal_id" name="jadwal_id">
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Program Studi *</label>
                            <select class="form-select" name="prodi_id" id="prodi_id" required>
                                @foreach($prodis as $prodi)
                                    <option value="{{ $prodi->id }}" data-fakultas-id="{{ $prodi->fakultas_id }}">
                                        {{ $prodi->nama_prodi }}
                                    </option>
                                @endforeach
                            </select>
                            <small class="text-muted">Ruangan akan difilter berdasarkan fakultas prodi</small>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Mata Kuliah *</label>
                            <select class="form-select" name="mata_kuliah_id" id="mata_kuliah_id" required>
                                <option value="">Pilih Mata Kuliah</option>
                            </select>
                            <small class="text-muted">Jam selesai otomatis dihitung dari SKS</small>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Dosen *</label>
                            <select class="form-select" name="dosen_id" id="dosen_id" required>
                                <option value="">Pilih Dosen</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Ruangan *</label>
                            <select class="form-select" name="ruangan_id" id="ruangan_id" required>
                                <option value="">Pilih Ruangan</option>
                            </select>
                            <small class="text-muted">Hanya ruangan fakultas ini</small>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold">Hari *</label>
                            <select class="form-select" name="hari" id="hari" required>
                                <option value="">Pilih Hari</option>
                                <option value="Senin">Senin</option>
                                <option value="Selasa">Selasa</option>
                                <option value="Rabu">Rabu</option>
                                <option value="Kamis">Kamis</option>
                                <option value="Jumat">Jumat</option>
                                <option value="Sabtu">Sabtu</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold">Jam Mulai *</label>
                            <input type="time" class="form-control" name="jam_mulai" id="jam_mulai" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold">Jam Selesai (Otomatis)</label>
                            <div class="form-control bg-light" style="padding-top: 0.7rem;">
                                <strong id="jam_selesai_display">-</strong>
                            </div>
                            <small class="text-muted">1 SKS = 50 menit</small>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Semester *</label>
                            <select class="form-select" name="semester" id="semester" required>
                                @foreach(range(1, 8) as $sem)
                                    <option value="{{ $sem }}">Semester {{ $sem }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Keterangan</label>
                            <input type="text" class="form-control" name="keterangan" id="keterangan" placeholder="Tambahan informasi">
                        </div>
                    </div>

                    <!-- Alert untuk menampilkan pesan validasi -->
                    <div id="validationAlert" class="alert alert-warning d-none" role="alert">
                        <i class="bi bi-exclamation-triangle"></i>
                        <span id="validationMessage"></span>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle"></i> Batal
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save"></i> Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Lihat Detail -->
<div class="modal fade" id="modalLihat" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title">
                    <i class="bi bi-eye"></i> Detail Jadwal
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label class="text-muted small">Program Studi</label>
                                <p class="fw-bold" id="lihat_prodi">-</p>
                            </div>
                            <div class="col-md-4">
                                <label class="text-muted small">Semester</label>
                                <p class="fw-bold" id="lihat_semester">-</p>
                            </div>
                            <div class="col-md-4">
                                <label class="text-muted small">SKS</label>
                                <p class="fw-bold" id="lihat_sks">-</p>
                            </div>
                        </div>

                        <hr>

                        <div class="row mb-3">
                            <div class="col-md-12 mb-3">
                                <label class="text-muted small">Mata Kuliah</label>
                                <p class="fw-bold fs-5" id="lihat_matakuliah">-</p>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label class="text-muted small">Dosen Pengampu</label>
                                <p class="fw-bold" id="lihat_dosen">-</p>
                            </div>
                        </div>

                        <hr>

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label class="text-muted small">Hari</label>
                                <p class="fw-bold" id="lihat_hari">-</p>
                            </div>
                            <div class="col-md-4">
                                <label class="text-muted small">Waktu</label>
                                <p class="fw-bold" id="lihat_waktu">-</p>
                            </div>
                            <div class="col-md-4">
                                <label class="text-muted small">Ruangan</label>
                                <p class="fw-bold" id="lihat_ruangan">-</p>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <label class="text-muted small">Keterangan</label>
                                <p id="lihat_keterangan">-</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle"></i> Tutup
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Pindah Jadwal -->
<div class="modal fade" id="modalPindah" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title">
                    <i class="bi bi-arrow-left-right"></i> Pindah Jadwal
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formPindah">
                <div class="modal-body">
                    <input type="hidden" id="pindah_jadwal_id">
                    <input type="hidden" id="pindah_sks">
                    
                    <!-- Info Jadwal Saat Ini -->
                    <div class="alert alert-info">
                        <h6 class="alert-heading">
                            <i class="bi bi-info-circle"></i> Informasi Jadwal Saat Ini
                        </h6>
                        <div class="row">
                            <div class="col-md-12 mb-2">
                                <strong>Mata Kuliah:</strong> <span id="pindah_info_mk">-</span>
                            </div>
                            <div class="col-md-12 mb-2">
                                <strong>Dosen:</strong> <span id="pindah_info_dosen">-</span>
                            </div>
                            <div class="col-md-12">
                                <strong>Jadwal Lama:</strong> <span id="pindah_info_jadwal_lama">-</span>
                            </div>
                        </div>
                    </div>

                    <h6 class="mt-4 mb-3 text-primary">
                        <i class="bi bi-calendar-event"></i> Jadwal Baru
                    </h6>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Hari Baru *</label>
                            <select class="form-select" name="hari" id="pindah_hari" required>
                                <option value="">Pilih Hari</option>
                                <option value="Senin">Senin</option>
                                <option value="Selasa">Selasa</option>
                                <option value="Rabu">Rabu</option>
                                <option value="Kamis">Kamis</option>
                                <option value="Jumat">Jumat</option>
                                <option value="Sabtu">Sabtu</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Ruangan Baru *</label>
                            <select class="form-select" name="ruangan_id" id="pindah_ruangan_id" required>
                                <option value="">Pilih Ruangan</option>
                            </select>
                            <small class="text-muted">Hanya ruangan dari fakultas yang sama</small>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Jam Mulai Baru *</label>
                            <input type="time" class="form-control" name="jam_mulai" id="pindah_jam_mulai" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Jam Selesai (Otomatis)</label>
                            <div class="form-control bg-light" style="padding-top: 0.7rem;">
                                <strong id="pindah_jam_selesai_display">-</strong>
                            </div>
                            <small class="text-muted">Dihitung otomatis dari SKS mata kuliah</small>
                        </div>
                    </div>

                    <div class="alert alert-warning mt-3">
                        <i class="bi bi-exclamation-triangle"></i> 
                        <strong>Perhatian:</strong> Sistem akan mengecek bentrok jadwal dengan dosen, ruangan, dan prodi yang dipilih.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle"></i> Batal
                    </button>
                    <button type="submit" class="btn btn-warning">
                        <i class="bi bi-arrow-left-right"></i> Pindahkan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .modal-header.bg-primary,
    .modal-header.bg-info {
        border-bottom: 3px solid rgba(255,255,255,0.3);
    }
    
    .modal-header.bg-warning {
        border-bottom: 3px solid rgba(0,0,0,0.1);
    }
    
    .card.shadow-sm {
        border: none;
        box-shadow: 0 0.125rem 0.25rem rgba(0,0,0,0.075);
    }
    
    .form-control.bg-light {
        background-color: #f8f9fa !important;
        font-size: 1.1rem;
        color: #198754;
        border: 2px dashed #dee2e6;
    }
    
    .alert-info {
        background-color: #e7f3ff;
        border-left: 4px solid #0dcaf0;
    }
    
    .alert-warning {
        background-color: #fff3cd;
        border-left: 4px solid #ffc107;
    }

    /* Highlight required fields */
    .form-label.fw-bold:after {
        content: '';
    }

    /* Better form validation feedback */
    .form-control:invalid {
        border-color: #dc3545;
    }

    .form-control:valid {
        border-color: #198754;
    }

    .form-select:invalid {
        border-color: #dc3545;
    }

    .form-select:valid {
        border-color: #198754;
    }
</style>