<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Mahasiswa;
use App\Models\Dosen;
use App\Models\CalonMahasiswa;
use App\Models\Prodi;
use App\Models\Fakultas;
use App\Models\MataKuliah;
use App\Models\Jadwal;
use App\Models\Ruangan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Dashboard utama - redirect berdasarkan role
     */
    public function index()
    {
        $user = Auth::user();
        
        if ($user->role === 'admin') {
            return $this->adminDashboard();
        } else {
            return $this->mahasiswaDashboard();
        }
    }

    /**
     * Dashboard Admin - Full Access
     */
    private function adminDashboard()
    {
        // Statistik Utama
        $stats = [
            'total_mahasiswa' => Mahasiswa::count(),
            'total_dosen' => Dosen::count(),
            'total_prodi' => Prodi::count(),
            'total_fakultas' => Fakultas::count(),
            'total_mata_kuliah' => MataKuliah::count(),
            'total_ruangan' => Ruangan::count(),
            'total_jadwal' => Jadwal::where('status', 'Aktif')->count(),
            'calon_mahasiswa_pending' => CalonMahasiswa::where('status_seleksi', 'pending')->count(),
        ];

        // Mahasiswa per Prodi
        $mahasiswaPerProdi = Mahasiswa::select('prodi_id', DB::raw('count(*) as total'))
            ->with('prodi')
            ->groupBy('prodi_id')
            ->orderBy('total', 'desc')
            ->get()
            ->map(function($item) {
                return [
                    'prodi' => $item->prodi->nama_prodi ?? 'Unknown',
                    'total' => $item->total
                ];
            });

        // Mahasiswa per Gender
        $mahasiswaPerGender = Mahasiswa::select(
                DB::raw("CASE WHEN LOWER(LEFT(nama, 2)) IN ('ny', 'ms', 'si', 'de', 'an', 'pu', 'yu', 'nu', 'ma', 'li', 'fi', 'ri') THEN 'P' ELSE 'L' END as gender"),
                DB::raw('count(*) as total')
            )
            ->groupBy('gender')
            ->get();

        // Mahasiswa per Angkatan
        $mahasiswaPerAngkatan = Mahasiswa::select('angkatan', DB::raw('count(*) as total'))
            ->groupBy('angkatan')
            ->orderBy('angkatan', 'desc')
            ->limit(5)
            ->get();

        // Dosen per Prodi
        $dosenPerProdi = Dosen::select('prodi_id', DB::raw('count(*) as total'))
            ->with('prodi')
            ->whereNotNull('prodi_id')
            ->groupBy('prodi_id')
            ->orderBy('total', 'desc')
            ->limit(5)
            ->get()
            ->map(function($item) {
                return [
                    'prodi' => $item->prodi->nama_prodi ?? 'Unknown',
                    'total' => $item->total
                ];
            });

        // Calon Mahasiswa Status
        $calonMahasiswaStatus = CalonMahasiswa::select('status_seleksi', DB::raw('count(*) as total'))
            ->groupBy('status_seleksi')
            ->get();

        // Mahasiswa Terbaru (5 terakhir)
        $mahasiswaTerbaru = Mahasiswa::with('prodi.fakultas')
            ->latest()
            ->limit(5)
            ->get();

        // Ruangan per Jenis
        $ruanganPerJenis = Ruangan::select('jenis', DB::raw('count(*) as total'))
            ->groupBy('jenis')
            ->get();

        // Mata Kuliah per Semester
        $mataKuliahPerSemester = MataKuliah::select('semester', DB::raw('count(*) as total'))
            ->groupBy('semester')
            ->orderBy('semester')
            ->get();

        if (request()->ajax()) {
            return response()->json([
                'stats' => $stats,
                'mahasiswaPerProdi' => $mahasiswaPerProdi,
                'mahasiswaPerGender' => $mahasiswaPerGender,
                'mahasiswaPerAngkatan' => $mahasiswaPerAngkatan,
                'dosenPerProdi' => $dosenPerProdi,
                'calonMahasiswaStatus' => $calonMahasiswaStatus,
                'mahasiswaTerbaru' => $mahasiswaTerbaru,
                'ruanganPerJenis' => $ruanganPerJenis,
                'mataKuliahPerSemester' => $mataKuliahPerSemester,
            ]);
        }

        return view('dashboard.admin', compact(
            'stats',
            'mahasiswaPerProdi',
            'mahasiswaPerGender',
            'mahasiswaPerAngkatan',
            'dosenPerProdi',
            'calonMahasiswaStatus',
            'mahasiswaTerbaru',
            'ruanganPerJenis',
            'mataKuliahPerSemester'
        ));
    }

    /**
     * Dashboard Mahasiswa - Limited Access
     */
    private function mahasiswaDashboard()
    {
        // Statistik yang bisa dilihat mahasiswa
        $stats = [
            'total_mahasiswa' => Mahasiswa::count(),
            'total_dosen' => Dosen::count(),
            'total_mata_kuliah' => MataKuliah::count(),
            'total_jadwal' => Jadwal::where('status', 'Aktif')->count(),
        ];

        // Mahasiswa per Prodi
        $mahasiswaPerProdi = Mahasiswa::select('prodi_id', DB::raw('count(*) as total'))
            ->with('prodi')
            ->groupBy('prodi_id')
            ->orderBy('total', 'desc')
            ->limit(5)
            ->get()
            ->map(function($item) {
                return [
                    'prodi' => $item->prodi->nama_prodi ?? 'Unknown',
                    'total' => $item->total
                ];
            });

        // Mahasiswa per Angkatan
        $mahasiswaPerAngkatan = Mahasiswa::select('angkatan', DB::raw('count(*) as total'))
            ->groupBy('angkatan')
            ->orderBy('angkatan', 'desc')
            ->limit(5)
            ->get();

        // Jadwal Hari Ini (contoh untuk Sistem Informasi)
        // Fix: Tambahkan Minggu di array
        $hariIni = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'][date('N') - 1];
        $jadwalHariIni = Jadwal::with(['mataKuliah', 'dosen', 'ruangan'])
            ->where('hari', $hariIni)
            ->where('status', 'Aktif')
            ->where('prodi_id', 1) // Default Sistem Informasi
            ->orderBy('jam_mulai')
            ->limit(5)
            ->get();

        if (request()->ajax()) {
            return response()->json([
                'stats' => $stats,
                'mahasiswaPerProdi' => $mahasiswaPerProdi,
                'mahasiswaPerAngkatan' => $mahasiswaPerAngkatan,
                'jadwalHariIni' => $jadwalHariIni,
            ]);
        }

        return view('dashboard.mahasiswa', compact(
            'stats',
            'mahasiswaPerProdi',
            'mahasiswaPerAngkatan',
            'jadwalHariIni'
        ));
    }

    /**
     * Get realtime stats - untuk auto refresh
     */
    public function getRealtimeStats()
    {
        $user = Auth::user();
        
        if ($user->role === 'admin') {
            return response()->json([
                'total_mahasiswa' => Mahasiswa::count(),
                'total_dosen' => Dosen::count(),
                'calon_mahasiswa_pending' => CalonMahasiswa::where('status_seleksi', 'pending')->count(),
                'latest_mahasiswa' => Mahasiswa::with('prodi')->latest()->first(),
            ]);
        } else {
            return response()->json([
                'total_mahasiswa' => Mahasiswa::count(),
                'total_dosen' => Dosen::count(),
            ]);
        }
    }
}