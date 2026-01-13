<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Keuangan PMB</title>
    <style>
        @page {
            margin: 20px;
        }
        
        body {
            font-family: Arial, sans-serif;
            font-size: 11pt;
            line-height: 1.4;
            color: #000;
        }
        
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 3px solid #000;
            padding-bottom: 10px;
        }
        
        .header h2 {
            margin: 0;
            font-size: 16pt;
            font-weight: bold;
            text-transform: uppercase;
        }
        
        .header h3 {
            margin: 5px 0;
            font-size: 18pt;
            font-weight: bold;
            color: #1a5490;
        }
        
        .header p {
            margin: 2px 0;
            font-size: 9pt;
        }
        
        .separator {
            border-bottom: 2px solid #000;
            margin: 10px 0;
        }
        
        .title-section {
            text-align: center;
            margin: 20px 0;
            padding: 10px;
            background-color: #f0f0f0;
            border: 2px solid #000;
        }
        
        .title-section h4 {
            margin: 0;
            font-size: 14pt;
            font-weight: bold;
            text-transform: uppercase;
        }
        
        .info-section {
            margin: 20px 0;
        }
        
        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        
        .info-table td {
            padding: 5px;
            vertical-align: top;
        }
        
        .info-table td:first-child {
            width: 30%;
            font-weight: bold;
        }
        
        .info-table td:nth-child(2) {
            width: 5%;
        }
        
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        
        .data-table th {
            background-color: #1a5490;
            color: white;
            padding: 10px 5px;
            text-align: left;
            font-weight: bold;
            border: 1px solid #000;
        }
        
        .data-table td {
            padding: 8px 5px;
            border: 1px solid #000;
        }
        
        .data-table tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        
        .summary-box {
            margin: 20px 0;
            padding: 15px;
            border: 2px solid #1a5490;
            background-color: #f0f8ff;
        }
        
        .summary-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .summary-table td {
            padding: 8px;
            font-size: 12pt;
        }
        
        .summary-table td:first-child {
            font-weight: bold;
            width: 70%;
        }
        
        .summary-table td:last-child {
            text-align: right;
            font-weight: bold;
        }
        
        .summary-table tr:last-child {
            border-top: 2px solid #000;
            font-size: 14pt;
            color: #c00;
        }
        
        .footer {
            margin-top: 40px;
        }
        
        .signature-section {
            margin-top: 50px;
            text-align: right;
        }
        
        .signature-box {
            display: inline-block;
            text-align: center;
            min-width: 200px;
        }
        
        .signature-line {
            margin-top: 60px;
            border-top: 1px solid #000;
            padding-top: 5px;
        }
        
        .barcode-section {
            text-align: center;
            margin: 20px 0;
            padding: 15px;
            border: 1px dashed #000;
        }
        
        .barcode-section img {
            max-width: 200px;
            height: auto;
        }
        
        .text-right {
            text-align: right;
        }
        
        .text-center {
            text-align: center;
        }
        
        .text-success {
            color: #28a745;
            font-weight: bold;
        }
        
        .text-danger {
            color: #dc3545;
            font-weight: bold;
        }
        
        .text-warning {
            color: #ffc107;
            font-weight: bold;
        }
        
        .badge {
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 9pt;
            font-weight: bold;
        }
        
        .badge-success {
            background-color: #28a745;
            color: white;
        }
        
        .badge-danger {
            background-color: #dc3545;
            color: white;
        }
        
        .badge-info {
            background-color: #17a2b8;
            color: white;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <h2>YAYASAN AL MA'SOEM BANDUNG</h2>
        <h3>UNIVERSITAS MA'SOEM</h3>
        <p>Jl. Raya Cipacing No.22 Jatinangor – Sumedang 45363</p>
        <p>Telp. (022) 7798340 | Email: info@masoemuniversity.ac.id</p>
        <p>Website: https://www.masoemuniversity.ac.id</p>
    </div>
    
    <div class="separator"></div>
    
    <!-- Title -->
    <div class="title-section">
        <h4>LAPORAN KEUANGAN PENERIMAAN MAHASISWA BARU</h4>
    </div>
    
    <!-- Informasi Calon Mahasiswa -->
    <div class="info-section">
        <table class="info-table">
            <tr>
                <td>No. Pendaftaran</td>
                <td>:</td>
                <td><strong>{{ $calonMahasiswa->no_pendaftaran }}</strong></td>
            </tr>
            <tr>
                <td>Nama Lengkap</td>
                <td>:</td>
                <td><strong>{{ $calonMahasiswa->nama }}</strong></td>
            </tr>
            <tr>
                <td>Program Studi</td>
                <td>:</td>
                <td>{{ $calonMahasiswa->prodi->nama_prodi }}</td>
            </tr>
            <tr>
                <td>Fakultas</td>
                <td>:</td>
                <td>{{ $calonMahasiswa->prodi->fakultas->nama_fakultas ?? '-' }}</td>
            </tr>
            <tr>
                <td>Jalur Masuk</td>
                <td>:</td>
                <td><span class="badge badge-info">{{ strtoupper($calonMahasiswa->jalur_masuk) }}</span></td>
            </tr>
            <tr>
                <td>Gelombang</td>
                <td>:</td>
                <td>{{ $calonMahasiswa->gelombang ?? '-' }}</td>
            </tr>
            <tr>
                <td>Tanggal Cetak</td>
                <td>:</td>
                <td>{{ date('d F Y, H:i') }} WIB</td>
            </tr>
        </table>
    </div>
    
    <!-- Tabel Biaya -->
    <table class="data-table">
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="40%">Jenis Biaya</th>
                <th width="20%">Nominal</th>
                <th width="15%">Status</th>
                <th width="20%">Tanggal Bayar</th>
            </tr>
        </thead>
        <tbody>
            @php
                $total = 0;
                $terbayar = 0;
            @endphp
            
            @forelse($keuangan as $index => $item)
                @php
                    $total += $item->nominal;
                    if($item->status_bayar != 'belum_bayar') {
                        $terbayar += $item->nominal;
                    }
                    
                    // Konversi jenis biaya ke nama
                    $jenisBiayaNama = '';
                    switch($item->jenis_biaya) {
                        case 'formulir':
                            $jenisBiayaNama = 'Biaya Formulir';
                            break;
                        case 'ujian':
                            $jenisBiayaNama = 'Biaya Seleksi/Ujian';
                            break;
                        case 'daftar_ulang':
                            $jenisBiayaNama = 'Biaya Daftar Ulang';
                            break;
                        default:
                            $jenisBiayaNama = ucfirst($item->jenis_biaya);
                    }
                @endphp
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $jenisBiayaNama }}</td>
                    <td class="text-right">Rp {{ number_format($item->nominal, 0, ',', '.') }}</td>
                    <td class="text-center">
                        @if($item->status_bayar == 'belum_bayar')
                            <span class="badge badge-danger">BELUM BAYAR</span>
                        @elseif($item->status_bayar == 'sudah_bayar')
                            <span class="badge badge-success">SUDAH BAYAR</span>
                        @else
                            <span class="badge badge-info">DIBEBASKAN</span>
                        @endif
                    </td>
                    <td class="text-center">
                        {{ $item->tanggal_bayar ? date('d-m-Y', strtotime($item->tanggal_bayar)) : '-' }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center">Tidak ada data biaya</td>
                </tr>
            @endforelse
        </tbody>
    </table>
    
    <!-- Ringkasan -->
    <div class="summary-box">
        <table class="summary-table">
            <tr>
                <td>Total Biaya PMB</td>
                <td>Rp {{ number_format($total, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td>Total Sudah Dibayar</td>
                <td class="text-success">Rp {{ number_format($terbayar, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td>Sisa Tagihan</td>
                <td class="text-danger">Rp {{ number_format($total - $terbayar, 0, ',', '.') }}</td>
            </tr>
        </table>
    </div>
    
    <!-- Barcode Section dengan Endroid QR Code -->
    <div class="barcode-section">
        <p style="margin: 0 0 10px 0; font-weight: bold;">Kode Verifikasi:</p>
        <img src="{{ $qrCodeDataUri }}" alt="QR Code" style="max-width: 150px; height: auto;">
        <p style="margin: 10px 0 0 0; font-size: 9pt;">
            Scan QR Code untuk verifikasi pembayaran
        </p>
    </div>
    
    <!-- Footer -->
    <div class="footer">
        <p style="font-size: 9pt; font-style: italic;">
            <strong>Catatan:</strong><br>
            - Dokumen ini dicetak secara otomatis oleh sistem<br>
            - Untuk informasi lebih lanjut, hubungi Bagian Keuangan PMB<br>
            - Simpan dokumen ini sebagai bukti pembayaran
        </p>
        
        <div class="signature-section">
            <div class="signature-box">
                <p>Bandung, {{ date('d F Y') }}</p>
                <p><strong>Petugas Keuangan PMB</strong></p>
                <div class="signature-line">
                    ( _____________________ )
                </div>
            </div>
        </div>
    </div>
</body>
</html>