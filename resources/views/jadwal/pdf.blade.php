<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Jadwal Kuliah - {{ $prodi->nama_prodi }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header h2 {
            margin: 5px 0;
        }
        .info {
            margin-bottom: 15px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .table-header {
            background-color: #764ba2;
            color: white;
            font-weight: bold;
            text-align: center;
        }
        .hari-header {
            background-color: #ffd700;
            font-weight: bold;
            padding: 8px;
        }
        th, td {
            border: 1px solid #000;
            padding: 6px;
        }
        th {
            background-color: #764ba2;
            color: white;
        }
        .waktu {
            width: 20%;
        }
        .matkul {
            width: 45%;
        }
        .ruangan {
            width: 15%;
        }
        .footer {
            margin-top: 30px;
            font-size: 10px;
            text-align: right;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>JADWAL KULIAH</h2>
        <h3>{{ $prodi->nama_prodi }}</h3>
        <h4>Semester {{ $request->semester }} - Tahun Akademik 2024/2025</h4>
    </div>

    @php
        $hariOrder = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
    @endphp

    @foreach($hariOrder as $hari)
        @if(isset($jadwals[$hari]) && count($jadwals[$hari]) > 0)
            <div class="hari-header">{{ $hari }}</div>
            <table>
                <thead>
                    <tr class="table-header">
                        <th class="waktu">WAKTU</th>
                        <th class="matkul">MATA KULIAH & DOSEN</th>
                        <th class="ruangan">RUANGAN</th>
                        <th>SKS</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($jadwals[$hari] as $jadwal)
                    <tr>
                        <td class="waktu">
                            {{ date('H:i', strtotime($jadwal->jam_mulai)) }} - 
                            {{ date('H:i', strtotime($jadwal->jam_selesai)) }}
                        </td>
                        <td class="matkul">
                            <strong>{{ $jadwal->mataKuliah->nama_mk }}</strong><br>
                            <em>{{ $jadwal->dosen->nama }}</em>
                        </td>
                        <td class="ruangan">{{ $jadwal->ruangan->kode_ruangan }}</td>
                        <td style="text-align: center;">{{ $jadwal->mataKuliah->sks }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    @endforeach

    <div class="footer">
        <p>Dicetak pada: {{ date('d F Y H:i') }}</p>
    </div>
</body>
</html>