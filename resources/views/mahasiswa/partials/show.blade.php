<div class="modal-header bg-info text-white">
    <h5 class="modal-title">
        <i class="bi bi-eye"></i> Detail Mahasiswa
    </h5>
    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
</div>

<div class="modal-body">
    <div class="row">
        <div class="col-md-4 text-center mb-3">
            @if($mahasiswa->foto)
                <img src="{{ asset('storage/'.$mahasiswa->foto) }}" class="img-fluid img-thumbnail rounded">
            @else
                <div class="border rounded p-5 bg-light">
                    <i class="bi bi-person-circle" style="font-size: 100px; color: #ccc;"></i>
                    <p class="text-muted mt-2">Tidak ada foto</p>
                </div>
            @endif
        </div>
        
        <div class="col-md-8">
            <table class="table table-borderless">
                <tr>
                    <th width="140"><i class="bi bi-credit-card text-primary"></i> NIM</th>
                    <td>: {{ $mahasiswa->nim }}</td>
                </tr>
                <tr>
                    <th><i class="bi bi-person text-success"></i> Nama</th>
                    <td>: {{ $mahasiswa->nama }}</td>
                </tr>
                <tr>
                    <th><i class="bi bi-calendar text-warning"></i> Angkatan</th>
                    <td>: {{ $mahasiswa->angkatan }}</td>
                </tr>
                <tr>
                    <th><i class="bi bi-mortarboard text-info"></i> Prodi</th>
                    <td>: {{ $mahasiswa->prodi->nama_prodi }}</td>
                </tr>
                <tr>
                    <th><i class="bi bi-building text-danger"></i> Fakultas</th>
                    <td>: {{ $mahasiswa->prodi->fakultas->nama_fakultas }}</td>
                </tr>
            </table>
        </div>
    </div>
</div>

<div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
        <i class="bi bi-x-circle"></i> Tutup
    </button>
</div>