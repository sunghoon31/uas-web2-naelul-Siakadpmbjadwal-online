<div class="modal-header bg-info text-white">
    <h5 class="modal-title">
        <i class="bi bi-eye"></i> Detail Dosen
    </h5>
    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
</div>

<div class="modal-body">
    <div class="row">
        <div class="col-md-4 text-center mb-3">
            @if($dosen->foto)
                <img src="{{ asset('storage/'.$dosen->foto) }}" class="img-fluid img-thumbnail rounded">
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
                    <th width="140"><i class="bi bi-credit-card text-primary"></i> NIDN</th>
                    <td>: {{ $dosen->nidn }}</td>
                </tr>
                <tr>
                    <th><i class="bi bi-person text-success"></i> Nama</th>
                    <td>: {{ $dosen->nama }}</td>
                </tr>
                <tr>
                    <th><i class="bi bi-envelope text-danger"></i> Email</th>
                    <td>: {{ $dosen->email }}</td>
                </tr>
                <tr>
                    <th><i class="bi bi-telephone text-warning"></i> No HP</th>
                    <td>: {{ $dosen->no_hp }}</td>
                </tr>
                <tr>
                    <th><i class="bi bi-mortarboard text-info"></i> Homebase</th>
                    <td>
                        : {{ optional($dosen->prodi)->nama_prodi ?? '-' }}
                        @if($dosen->prodi)
                            <br>
                            <small class="text-muted">{{ $dosen->prodi->fakultas->nama_fakultas }}</small>
                        @endif
                    </td>
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