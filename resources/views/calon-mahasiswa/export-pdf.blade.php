<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Data Calon Mahasiswa</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 10px;
            margin: 20px;
        }
        
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 3px solid #333;
            padding-bottom: 10px;
        }
        
        .header h2 {
            margin: 5px 0;
            font-size: 18px;
        }
        
        .header p {
            margin: 3px 0;
            font-size: 11px;
        }
        
        .filter-info {
            background: #f0f0f0;
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 5px;
        }
        
        .filter-info table {
            width: 100%;
        }
        
        .filter-info td {
            padding: 3px 5px;
            font-size: 9px;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        
        table thead {
            background-color: #4472C4;
            color: white;
        }
        
        table th, table td {
            border: 1px solid #ddd;
            padding: 6px;
            text-align: left;
            font-size: 9px;
        }
        
        table th {
            font-weight: bold;
            text-align: center;
        }
        
        table tbody tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        
        .badge {
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 8px;
            font-weight: bold;
            display: inline-block;
        }
        
        .badge-warning {
            background: #ffc107;
            color: #000;
        }
        
        .badge-success {
            background: #28a745;
            color: white;
        }
        
        .badge-danger {
            background: #dc3545;
            color: white;
        }
        
        .badge-info {
            background: #17a2b8;
            color: white;
        }
        
        .badge-secondary {
            background: #6c757d;
            color: white;
        }
        
        .footer {
            margin-top: 20px;
            text-align: right;
            font-size: 9px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>LAPORAN DATA CALON MAHASISWA</h2>
        <p>Sistem Informasi Akademik - Penerimaan Mahasiswa Baru</p>
        <p>Dicetak pada: {{ $tanggal_export }}</p>
    </div>

    <div class="filter-info">
        <strong>Filter yang Diterapkan:</strong>
        <table>
            <tr>
                <td width="20%"><strong>Status Seleksi:</strong></td>
                <td width="30%">{{ $filterInfo['status'] }}</td>
                <td width="20%"><strong>Jalur Masuk:</strong></td>
                <td width="30%">{{ $filterInfo['jalur'] }}</td>
            </tr>
            <tr>
                <td><strong>Program Studi:</strong></td>
                <td>{{ $filterInfo['prodi'] }}</td>
                <td><strong>Pencarian:</strong></td>
                <td>{{ $filterInfo['search'] }}</td>
            </tr>
        </table>
    </div>

    <table>
        <thead>
            <tr>
                <th width="3%">No</th>
                <th width="12%">No Pendaftaran</th>
                <th width="15%">Nama Lengkap</th>
                <th width="5%">L/P</th>
                <th width="20%">Program Studi</th>
                <th width="10%">Jalur</th>
                <th width="8%">Gelombang</th>
                <th width="10%">Status</th>
                <th width="12%">Tgl Daftar</th>
            </tr>
        </thead>
        <tbody>
            @forelse($calonMahasiswa as $index => $item)
            <tr>
                <td style="text-align: center;">{{ $index + 1 }}</td>
                <td>{{ $item->no_pendaftaran }}</td>
                <td><strong>{{ $item->nama }}</strong></td>
                <td style="text-align: center;">{{ $item->jenis_kelamin }}</td>
                <td>{{ $item->prodi ? $item->prodi->nama_prodi : '-' }}</td>
                <td>{{ ucfirst($item->jalur_masuk) }}</td>
                <td>{{ $item->gelombang ?? '-' }}</td>
                <td style="text-align: center;">
                    @if($item->status_seleksi == 'pending')
                        <span class="badge badge-warning">Pending</span>
                    @elseif($item->status_seleksi == 'diterima')
                        <span class="badge badge-success">Diterima</span>
                    @else
                        <span class="badge badge-danger">Ditolak</span>
                    @endif
                </td>
                <td style="text-align: center;">{{ $item->created_at ? $item->created_at->format('d/m/Y') : '-' }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="9" style="text-align: center; padding: 20px;">
                    <em>Tidak ada data</em>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <p><strong>Total Data: {{ $calonMahasiswa->count() }}</strong></p>
    </div>
</body>
</html>