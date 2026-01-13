<div class="modal-header bg-{{ $method == 'POST' ? 'success' : 'warning' }} text-white">
    <h5 class="modal-title">
        <i class="bi bi-{{ $method == 'POST' ? 'plus-circle' : 'pencil-square' }}"></i>
        {{ $method == 'POST' ? 'Tambah' : 'Edit' }} Mahasiswa
    </h5>
    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
</div>

<form id="formMahasiswa" action="{{ $action }}" method="POST" enctype="multipart/form-data">
    @csrf
    @if($method == 'PUT')
        <input type="hidden" name="_method" value="PUT">
    @endif

    <div class="modal-body">
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">NIM <span class="text-danger">*</span></label>
                <input type="text" name="nim" class="form-control" value="{{ old('nim', $mahasiswa->nim) }}" required>
            </div>

            <div class="col-md-6 mb-3">
                <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                <input type="text" name="nama" class="form-control" value="{{ old('nama', $mahasiswa->nama) }}" required>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">Angkatan <span class="text-danger">*</span></label>
                <input type="number" name="angkatan" class="form-control" value="{{ old('angkatan', $mahasiswa->angkatan ?? 2020) }}" required>
            </div>

            <div class="col-md-6 mb-3">
                <label class="form-label">Program Studi <span class="text-danger">*</span></label>
                <select name="prodi_id" class="form-select" required>
                    <option value="">-- Pilih Prodi --</option>
                    @foreach($prodis as $prodi)
                        <option value="{{ $prodi->id }}" {{ old('prodi_id', $mahasiswa->prodi_id) == $prodi->id ? 'selected' : '' }}>
                            {{ $prodi->nama_prodi }} ({{ $prodi->fakultas->nama_fakultas }})
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label">Foto</label>
            
            @if($mahasiswa->foto)
                <div class="mb-2">
                    <img src="{{ asset('storage/'.$mahasiswa->foto) }}" class="img-thumbnail" width="150">
                </div>
            @endif
            
            <input type="file" name="foto" class="form-control" accept="image/*">
            <small class="text-muted">Format: JPG, PNG, JPEG. Max: 2MB</small>
        </div>
    </div>

    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
            <i class="bi bi-x-circle"></i> Batal
        </button>
        <button type="submit" class="btn btn-{{ $method == 'POST' ? 'success' : 'warning' }}">
            <i class="bi bi-check-circle"></i> 
            {{ $method == 'POST' ? 'Simpan' : 'Update' }}
        </button>
    </div>
</form>