<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Kartu Rencana Studi - {{ $mahasiswa->nama }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
        }
        .header {
            text-align: center;
            border-bottom: 3px solid #000;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .header h2 {
            margin: 5px 0;
            font-size: 18px;
        }
        .header p {
            margin: 3px 0;
            font-size: 12px;
        }
        .info-mahasiswa {
            margin-bottom: 20px;
        }
        .info-mahasiswa table {
            width: 100%;
            font-size: 12px;
        }
        .info-mahasiswa td {
            padding: 5px 0;
        }
        .info-mahasiswa td:first-child {
            width: 150px;
            font-weight: bold;
        }
        .table-krs {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            font-size: 11px;
        }
        .table-krs th, .table-krs td {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
        }
        .table-krs th {
            background-color: #f0f0f0;
            font-weight: bold;
            text-align: center;
        }
        .text-center {
            text-align: center;
        }
        .qr-code {
            position: absolute;
            top: 20px;
            right: 20px;
        }
        .footer {
            margin-top: 50px;
            font-size: 11px;
        }
        .ttd {
            float: right;
            text-align: center;
            width: 200px;
        }
        .ttd-line {
            margin-top: 70px;
            border-top: 1px solid #000;
        }
    </style>
</head>
<body>
    <!-- QR CODE -->
    <div class="qr-code">
        <img src="data:image/png;base64,{{ $qrCodeBase64 }}" alt="QR Code" width="80">
    </div>

    <!-- HEADER -->
    <div class="header">
        <h2>UNIVERSITAS CONTOH INDONESIA</h2>
        <p>{{ $mahasiswa->prodi->fakultas->nama_fakultas ?? 'Fakultas' }}</p>
        <p style="margin-bottom: 10px;">Jl. Pendidikan No. 123, Bandung - Telp: (022) 1234567</p>
        <h3 style="margin: 10px 0;">KARTU RENCANA STUDI (KRS)</h3>
        <p>Semester Ganjil 2024/2025</p>
    </div>

    <!-- INFO MAHASISWA -->
    <div class="info-mahasiswa">
        <table>
            <tr>
                <td>NIM</td>
                <td>: {{ $mahasiswa->nim }}</td>
            </tr>
            <tr>
                <td>Nama Mahasiswa</td>
                <td>: {{ $mahasiswa->nama }}</td>
            </tr>
            <tr>
                <td>Program Studi</td>
                <td>: {{ $mahasiswa->prodi->nama_prodi ?? '-' }}</td>
            </tr>
            <tr>
                <td>Angkatan</td>
                <td>: {{ $mahasiswa->angkatan }}</td>
            </tr>
            <tr>
                <td>Dosen Pembimbing Akademik</td>
                <td>: Dr. John Doe, M.Kom</td>
            </tr>
        </table>
    </div>

    <!-- TABEL KRS -->
    <table class="table-krs">
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="15%">Kode MK</th>
                <th width="40%">Mata Kuliah</th>
                <th width="10%">SKS</th>
                <th width="15%">Kelas</th>
                <th width="15%">Ruang</th>
            </tr>
        </thead>
        <tbody>
            <!-- CONTOH DATA STATIS -->
            <tr>
                <td class="text-center">1</td>
                <td>TIF101</td>
                <td>Pemrograman Web Lanjut</td>
                <td class="text-center">3</td>
                <td class="text-center">A</td>
                <td class="text-center">Lab 1</td>
            </tr>
            <tr>
                <td class="text-center">2</td>
                <td>TIF102</td>
                <td>Basis Data Terdistribusi</td>
                <td class="text-center">3</td>
                <td class="text-center">B</td>
                <td class="text-center">Lab 2</td>
            </tr>
            <tr>
                <td class="text-center">3</td>
                <td>TIF103</td>
                <td>Jaringan Komputer</td>
                <td class="text-center">3</td>
                <td class="text-center">A</td>
                <td class="text-center">R. 301</td>
            </tr>
            <tr>
                <td class="text-center">4</td>
                <td>TIF104</td>
                <td>Kecerdasan Buatan</td>
                <td class="text-center">3</td>
                <td class="text-center">C</td>
                <td class="text-center">Lab 3</td>
            </tr>
            <tr>
                <td class="text-center">5</td>
                <td>TIF105</td>
                <td>Sistem Operasi</td>
                <td class="text-center">3</td>
                <td class="text-center">A</td>
                <td class="text-center">R. 302</td>
            </tr>
            <tr>
                <td colspan="3" style="text-align: right; font-weight: bold;">Total SKS</td>
                <td class="text-center" style="font-weight: bold;">15</td>
                <td colspan="2"></td>
            </tr>
        </tbody>
    </table>

    <!-- FOOTER & TTD -->
    <div class="footer">
        <div class="ttd">
            <p>Bandung, {{ date('d F Y') }}</p>
            <p>Dosen Pembimbing Akademik,</p>
            <div class="ttd-line"></div>
            <p><strong>Dr. John Doe, M.Kom</strong><br>NIP. 198001012005011001</p>
        </div>
        <div style="clear: both;"></div>
        
        <p style="margin-top: 30px; font-size: 10px; color: #666;">
            <strong>Catatan:</strong> Kartu ini sah jika ada QR Code dan tanda tangan Dosen PA.
        </p>
    </div>
</body>
</html>