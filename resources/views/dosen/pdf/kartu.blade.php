<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Kartu Identitas Dosen - {{ $dosen->nama }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
        }
        .card-container {
            width: 100%;
            max-width: 600px;
            margin: 0 auto;
            border: 3px solid #000;
            padding: 20px;
            position: relative;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #000;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        .header h2 {
            margin: 5px 0;
            font-size: 20px;
            color: #1a1a1a;
        }
        .header p {
            margin: 3px 0;
            font-size: 11px;
            color: #555;
        }
        .qr-code {
            position: absolute;
            top: 20px;
            right: 20px;
        }
        .photo-section {
            text-align: center;
            margin: 20px 0;
        }
        .photo-placeholder {
            width: 120px;
            height: 150px;
            border: 2px solid #333;
            margin: 0 auto;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #f5f5f5;
        }
        .info-section {
            margin-top: 20px;
        }
        .info-table {
            width: 100%;
            font-size: 13px;
        }
        .info-table td {
            padding: 8px 5px;
            vertical-align: top;
        }
        .info-table td:first-child {
            width: 150px;
            font-weight: bold;
            color: #333;
        }
        .info-table td:nth-child(2) {
            width: 10px;
        }
        .badge {
            display: inline-block;
            padding: 5px 10px;
            background-color: #007bff;
            color: white;
            border-radius: 3px;
            font-size: 11px;
            margin-top: 5px;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 15px;
        }
        .valid-date {
            text-align: right;
            font-size: 11px;
            margin-top: 20px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="card-container">
        <!-- QR CODE -->
        <div class="qr-code">
            <img src="data:image/png;base64,{{ $qrCodeBase64 }}" alt="QR Code" width="70">
        </div>

        <!-- HEADER -->
        <div class="header">
            <h2>UNIVERSITAS CONTOH INDONESIA</h2>
            <p style="font-weight: bold; font-size: 14px;">KARTU IDENTITAS DOSEN</p>
            <p>Jl. Pendidikan No. 123, Bandung - Telp: (022) 1234567</p>
        </div>

        <!-- PHOTO SECTION -->
        <div class="photo-section">
            @if($dosen->foto)
                <img src="{{ public_path('storage/' . $dosen->foto) }}" width="120" style="border: 2px solid #333;">
            @else
                <div class="photo-placeholder">
                    <span style="color: #999;">Foto Dosen</span>
                </div>
            @endif
        </div>

        <!-- INFO SECTION -->
        <div class="info-section">
            <table class="info-table">
                <tr>
                    <td>NIDN</td>
                    <td>:</td>
                    <td><strong style="font-size: 14px;">{{ $dosen->nidn }}</strong></td>
                </tr>
                <tr>
                    <td>Nama Lengkap</td>
                    <td>:</td>
                    <td><strong>{{ $dosen->nama }}</strong></td>
                </tr>
                <tr>
                    <td>Email</td>
                    <td>:</td>
                    <td>{{ $dosen->email }}</td>
                </tr>
                <tr>
                    <td>No. HP/WA</td>
                    <td>:</td>
                    <td>{{ $dosen->no_hp }}</td>
                </tr>
                <tr>
                    <td>Program Studi</td>
                    <td>:</td>
                    <td>
                        {{ $dosen->prodi->nama_prodi ?? '-' }}
                        @if($dosen->prodi)
                            <span class="badge">{{ $dosen->prodi->fakultas->nama_fakultas ?? '' }}</span>
                        @endif
                    </td>
                </tr>
            </table>
        </div>

        <!-- VALID DATE -->
        <div class="valid-date">
            <strong>Berlaku hingga:</strong> 31 Desember 2025
        </div>

        <!-- FOOTER -->
        <div class="footer">
            <p><strong>CATATAN:</strong> Kartu ini adalah bukti identitas resmi dosen.</p>
            <p>Harap membawa kartu ini saat berada di lingkungan kampus.</p>
            <p style="margin-top: 10px;">Diterbitkan: {{ date('d F Y') }}</p>
        </div>
    </div>
</body>
</html>