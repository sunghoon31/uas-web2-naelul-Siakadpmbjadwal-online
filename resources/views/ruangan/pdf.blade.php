<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Data Ruangan</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 3px solid #333;
            padding-bottom: 10px;
        }
        .header h1 {
            margin: 0;
            font-size: 20px;
            color: #333;
        }
        .header p {
            margin: 5px 0;
            color: #666;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th {
            background-color: #667eea;
            color: white;
            padding: 10px;
            text-align: left;
            font-weight: bold;
        }
        td {
            padding: 8px;
            border-bottom: 1px solid #ddd;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        tr:hover {
            background-color: #f5f5f5;
        }
        .badge {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 10px;
            font-weight: bold;
            display: inline-block;
        }
        .badge-kelas {
            background-color: #0d6efd;
            color: white;
        }
        .badge-lab {
            background-color: #198754;
            color: white;
        }
        .badge-studio {
            background-color: #ffc107;
            color: #000;
        }
        .footer {
            margin-top: 30px;
            text-align: right;
            font-size: 10px;
            color: #666;
        }
        .summary {
            margin-top: 20px;
            padding: 15px;
            background-color: #f8f9fa;
            border-left: 4px solid #667eea;
        }
        .summary-item {
            display: inline-block;
            margin-right: 30px;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>LAPORAN DATA RUANGAN</h1>
        <p>Sistem Informasi Akademik</p>
        <p>Tanggal Cetak: {{ date('d F Y H:i:s') }}</p>
    </div>

    @php
        $totalKapasitas = 0;
        $jenisCount = [
            'Kelas' => 0,
            'Lab' => 0,
            'Studio' => 0
        ];
        
        foreach($ruangans as $r) {
            $totalKapasitas += $r->kapasitas;
            if(isset($jenisCount[$r->jenis])) {
                $jenisCount[$r->jenis]++;
            }
        }
    @endphp

    <div class="summary">
        <div class="summary-item">Total Ruangan: {{ $ruangans->count() }}</div>
        <div class="summary-item">Kelas: {{ $jenisCount['Kelas'] }}</div>
        <div class="summary-item">Lab: {{ $jenisCount['Lab'] }}</div>
        <div class="summary-item">Studio: {{ $jenisCount['Studio'] }}</div>
        <div class="summary-item">Total Kapasitas: {{ $totalKapasitas }} orang</div>
    </div>

    <table>
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="12%">Kode</th>
                <th width="25%">Nama Ruangan</th>
                <th width="30%">Fakultas</th>
                <th width="13%">Jenis</th>
                <th width="15%">Kapasitas</th>
            </tr>
        </thead>
        <tbody>
            @foreach($ruangans as $index => $ruangan)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $ruangan->kode_ruangan }}</td>
                <td>{{ $ruangan->nama_ruangan }}</td>
                <td>{{ $ruangan->fakultas ? $ruangan->fakultas->nama_fakultas : '-' }}</td>
                <td>
                    <span class="badge badge-{{ strtolower($ruangan->jenis) }}">
                        {{ $ruangan->jenis }}
                    </span>
                </td>
                <td>{{ $ruangan->kapasitas }} orang</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>Dicetak oleh: {{ auth()->user()->name ?? 'Admin' }}</p>
        <p>Halaman 1 dari 1</p>
    </div>
</body>
</html>