<?php

namespace App\Http\Controllers;

use App\Models\Jadwal;
use App\Models\MataKuliah;
use App\Models\Dosen;
use App\Models\Ruangan;
use App\Models\Prodi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\JadwalExport;
use App\Imports\JadwalImport;

class JadwalController extends Controller
{
    /**
     * Display jadwal index with filters
     */
    public function index(Request $request)
    {
        $prodis = Prodi::with('fakultas')->get();
        $semesters = range(1, 8);
        
        // Default prodi Sistem Informasi
        $selectedProdi = $request->prodi_id ?? 1;
        $selectedSemester = $request->semester ?? 1;
        
        $prodi = Prodi::with('fakultas')->find($selectedProdi);
        
        if ($request->ajax()) {
            $jadwals = Jadwal::with(['mataKuliah', 'dosen', 'ruangan', 'prodi'])
                ->where('prodi_id', $selectedProdi)
                ->where('semester', $selectedSemester)
                ->where('status', 'Aktif')
                ->orderByRaw("FIELD(hari, 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu')")
                ->orderBy('jam_mulai')
                ->get()
                ->groupBy('hari');
            
            return response()->json([
                'jadwals' => $jadwals,
                'prodi' => $prodi
            ]);
        }
        
        return view('jadwal.index', compact('prodis', 'semesters', 'selectedProdi', 'selectedSemester'));
    }

    /**
     * Get DataTables data (jika masih digunakan untuk table view)
     */
    public function getData(Request $request)
    {
        $jadwals = Jadwal::with(['mataKuliah', 'dosen', 'ruangan', 'prodi'])
            ->where('prodi_id', $request->prodi_id)
            ->where('semester', $request->semester)
            ->select('jadwals.*');

        return DataTables::of($jadwals)
            ->addColumn('mata_kuliah', function($row) {
                return $row->mataKuliah->nama_mk . ' (' . $row->mataKuliah->sks . ' SKS)';
            })
            ->addColumn('dosen', function($row) {
                return $row->dosen->nama;
            })
            ->addColumn('ruangan', function($row) {
                return $row->ruangan->nama_ruangan . ' (' . $row->ruangan->kode_ruangan . ')';
            })
            ->addColumn('waktu', function($row) {
                return date('H:i', strtotime($row->jam_mulai)) . ' - ' . date('H:i', strtotime($row->jam_selesai));
            })
            ->addColumn('action', function($row) {
                $btn = '<button class="btn btn-sm btn-info btn-lihat" data-id="'.$row->id.'" title="Lihat"><i class="bi bi-eye"></i></button> ';
                $btn .= '<button class="btn btn-sm btn-primary btn-edit" data-id="'.$row->id.'" title="Edit"><i class="bi bi-pencil"></i></button> ';
                $btn .= '<button class="btn btn-sm btn-warning btn-pindah" data-id="'.$row->id.'" title="Pindah"><i class="bi bi-arrow-left-right"></i></button> ';
                $btn .= '<button class="btn btn-sm btn-danger btn-delete" data-id="'.$row->id.'" title="Hapus"><i class="bi bi-trash"></i></button>';
                return $btn;
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    /**
     * Store new jadwal - DIPERBAIKI dengan validasi mata kuliah duplikat
     */
    public function store(Request $request)
    {
        try {
            // Validasi input dasar
            $validated = $request->validate([
                'mata_kuliah_id' => 'required|exists:mata_kuliahs,id',
                'dosen_id' => 'required|exists:dosen,id',
                'ruangan_id' => 'required|exists:ruangans,id',
                'prodi_id' => 'required|exists:prodis,id',
                'hari' => 'required|in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu',
                'jam_mulai' => 'required|date_format:H:i',
                'jam_selesai' => 'required|date_format:H:i|after:jam_mulai',
                'semester' => 'required|integer|min:1|max:8',
                'keterangan' => 'nullable|string'
            ]);

            // Validasi ruangan harus dari fakultas yang sama dengan prodi
            $prodi = Prodi::with('fakultas')->findOrFail($validated['prodi_id']);
            $ruangan = Ruangan::findOrFail($validated['ruangan_id']);
            
            if ($ruangan->fakultas_id != $prodi->fakultas_id) {
                return response()->json([
                    'error' => 'Ruangan tidak sesuai dengan fakultas prodi yang dipilih!'
                ], 422);
            }

            // Validasi mata kuliah sesuai dengan prodi
            $mataKuliah = MataKuliah::findOrFail($validated['mata_kuliah_id']);
            if ($mataKuliah->prodi_id != $validated['prodi_id']) {
                return response()->json([
                    'error' => 'Mata kuliah tidak sesuai dengan prodi yang dipilih!'
                ], 422);
            }

            // BARU: Cek mata kuliah duplikat di hari yang sama
            $mataKuliahDuplikat = Jadwal::cekMataKuliahDuplikat(
                $validated['mata_kuliah_id'],
                $validated['prodi_id'],
                $validated['semester'],
                $validated['hari']
            )->with('mataKuliah')->first();

            if ($mataKuliahDuplikat) {
                return response()->json([
                    'error' => "Mata kuliah {$mataKuliah->nama_mk} sudah dijadwalkan pada hari {$validated['hari']}! Tidak boleh ada mata kuliah yang sama 2 kali dalam sehari di prodi yang sama."
                ], 422);
            }

            // Cek bentrok di prodi yang sama pada semester yang sama
            $bentrokProdi = Jadwal::cekBentrokProdi(
                $validated['prodi_id'],
                $validated['semester'],
                $validated['hari'],
                $validated['jam_mulai'],
                $validated['jam_selesai']
            )->with(['mataKuliah', 'ruangan'])->first();

            if ($bentrokProdi) {
                return response()->json([
                    'error' => "Bentrok dengan jadwal {$bentrokProdi->mataKuliah->nama_mk} di ruangan {$bentrokProdi->ruangan->nama_ruangan} pada waktu yang sama!"
                ], 422);
            }

            // Cek bentrok dosen
            $bentrokDosen = Jadwal::cekBentrokDosen(
                $validated['dosen_id'],
                $validated['hari'],
                $validated['jam_mulai'],
                $validated['jam_selesai']
            )->with(['mataKuliah', 'prodi'])->first();

            if ($bentrokDosen) {
                return response()->json([
                    'error' => "Dosen sudah mengajar {$bentrokDosen->mataKuliah->nama_mk} di prodi {$bentrokDosen->prodi->nama_prodi} pada waktu tersebut!"
                ], 422);
            }

            // Cek bentrok ruangan
            $bentrokRuangan = Jadwal::cekBentrokRuangan(
                $validated['ruangan_id'],
                $validated['hari'],
                $validated['jam_mulai'],
                $validated['jam_selesai']
            )->with(['mataKuliah', 'prodi', 'dosen'])->first();

            if ($bentrokRuangan) {
                return response()->json([
                    'error' => "Ruangan sudah digunakan untuk {$bentrokRuangan->mataKuliah->nama_mk} ({$bentrokRuangan->prodi->nama_prodi}) oleh {$bentrokRuangan->dosen->nama} pada waktu tersebut!"
                ], 422);
            }

            // Set status default
            $validated['status'] = 'Aktif';

            // Simpan jadwal
            DB::beginTransaction();
            try {
                $jadwal = Jadwal::create($validated);
                DB::commit();

                return response()->json([
                    'success' => 'Jadwal berhasil ditambahkan!',
                    'data' => $jadwal->load(['mataKuliah', 'dosen', 'ruangan', 'prodi'])
                ]);
            } catch (\Exception $e) {
                DB::rollBack();
                Log::error('Error creating jadwal: ' . $e->getMessage());
                throw $e;
            }

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'error' => 'Validasi gagal',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error in store jadwal: ' . $e->getMessage());
            return response()->json([
                'error' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get jadwal data for edit
     */
    public function edit($id)
    {
        try {
            $jadwal = Jadwal::with(['mataKuliah', 'dosen', 'ruangan', 'prodi.fakultas'])->findOrFail($id);
            
            return response()->json([
                'jadwal' => $jadwal,
                'mata_kuliahs' => MataKuliah::where('prodi_id', $jadwal->prodi_id)->get(),
                'dosens' => Dosen::all(),
                'ruangans' => Ruangan::where('fakultas_id', $jadwal->prodi->fakultas_id)->get(),
                'prodis' => Prodi::with('fakultas')->get()
            ]);
        } catch (\Exception $e) {
            Log::error('Error in edit jadwal: ' . $e->getMessage());
            return response()->json([
                'error' => 'Jadwal tidak ditemukan'
            ], 404);
        }
    }

    /**
     * Update existing jadwal - DIPERBAIKI dengan validasi mata kuliah duplikat
     */
    public function update(Request $request, $id)
    {
        try {
            // Validasi input dasar
            $validated = $request->validate([
                'mata_kuliah_id' => 'required|exists:mata_kuliahs,id',
                'dosen_id' => 'required|exists:dosen,id',
                'ruangan_id' => 'required|exists:ruangans,id',
                'prodi_id' => 'required|exists:prodis,id',
                'hari' => 'required|in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu',
                'jam_mulai' => 'required|date_format:H:i',
                'jam_selesai' => 'required|date_format:H:i|after:jam_mulai',
                'semester' => 'required|integer|min:1|max:8',
                'keterangan' => 'nullable|string'
            ]);

            $jadwal = Jadwal::findOrFail($id);

            // Validasi ruangan harus dari fakultas yang sama dengan prodi
            $prodi = Prodi::with('fakultas')->findOrFail($validated['prodi_id']);
            $ruangan = Ruangan::findOrFail($validated['ruangan_id']);
            
            if ($ruangan->fakultas_id != $prodi->fakultas_id) {
                return response()->json([
                    'error' => 'Ruangan tidak sesuai dengan fakultas prodi yang dipilih!'
                ], 422);
            }

            // Validasi mata kuliah sesuai dengan prodi
            $mataKuliah = MataKuliah::findOrFail($validated['mata_kuliah_id']);
            if ($mataKuliah->prodi_id != $validated['prodi_id']) {
                return response()->json([
                    'error' => 'Mata kuliah tidak sesuai dengan prodi yang dipilih!'
                ], 422);
            }

            // BARU: Cek mata kuliah duplikat di hari yang sama (exclude jadwal yang sedang diupdate)
            $mataKuliahDuplikat = Jadwal::cekMataKuliahDuplikat(
                $validated['mata_kuliah_id'],
                $validated['prodi_id'],
                $validated['semester'],
                $validated['hari'],
                $id
            )->with('mataKuliah')->first();

            if ($mataKuliahDuplikat) {
                return response()->json([
                    'error' => "Mata kuliah {$mataKuliah->nama_mk} sudah dijadwalkan pada hari {$validated['hari']}! Tidak boleh ada mata kuliah yang sama 2 kali dalam sehari di prodi yang sama."
                ], 422);
            }

            // Cek bentrok di prodi yang sama pada semester yang sama (exclude jadwal yang sedang diupdate)
            $bentrokProdi = Jadwal::cekBentrokProdi(
                $validated['prodi_id'],
                $validated['semester'],
                $validated['hari'],
                $validated['jam_mulai'],
                $validated['jam_selesai'],
                $id
            )->with(['mataKuliah', 'ruangan'])->first();

            if ($bentrokProdi) {
                return response()->json([
                    'error' => "Bentrok dengan jadwal {$bentrokProdi->mataKuliah->nama_mk} di ruangan {$bentrokProdi->ruangan->nama_ruangan} pada waktu yang sama!"
                ], 422);
            }

            // Cek bentrok dosen (exclude jadwal yang sedang diupdate)
            $bentrokDosen = Jadwal::cekBentrokDosen(
                $validated['dosen_id'],
                $validated['hari'],
                $validated['jam_mulai'],
                $validated['jam_selesai'],
                $id
            )->with(['mataKuliah', 'prodi'])->first();

            if ($bentrokDosen) {
                return response()->json([
                    'error' => "Dosen sudah mengajar {$bentrokDosen->mataKuliah->nama_mk} di prodi {$bentrokDosen->prodi->nama_prodi} pada waktu tersebut!"
                ], 422);
            }

            // Cek bentrok ruangan (exclude jadwal yang sedang diupdate)
            $bentrokRuangan = Jadwal::cekBentrokRuangan(
                $validated['ruangan_id'],
                $validated['hari'],
                $validated['jam_mulai'],
                $validated['jam_selesai'],
                $id
            )->with(['mataKuliah', 'prodi', 'dosen'])->first();

            if ($bentrokRuangan) {
                return response()->json([
                    'error' => "Ruangan sudah digunakan untuk {$bentrokRuangan->mataKuliah->nama_mk} ({$bentrokRuangan->prodi->nama_prodi}) oleh {$bentrokRuangan->dosen->nama} pada waktu tersebut!"
                ], 422);
            }

            // Update jadwal
            DB::beginTransaction();
            try {
                $jadwal->update($validated);
                DB::commit();

                return response()->json([
                    'success' => 'Jadwal berhasil diupdate!',
                    'data' => $jadwal->load(['mataKuliah', 'dosen', 'ruangan', 'prodi'])
                ]);
            } catch (\Exception $e) {
                DB::rollBack();
                Log::error('Error updating jadwal: ' . $e->getMessage());
                throw $e;
            }

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'error' => 'Validasi gagal',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error in update jadwal: ' . $e->getMessage());
            return response()->json([
                'error' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete jadwal
     */
    public function destroy($id)
    {
        try {
            $jadwal = Jadwal::findOrFail($id);
            
            DB::beginTransaction();
            try {
                $jadwal->delete();
                DB::commit();

                return response()->json(['success' => 'Jadwal berhasil dihapus!']);
            } catch (\Exception $e) {
                DB::rollBack();
                Log::error('Error deleting jadwal: ' . $e->getMessage());
                throw $e;
            }

        } catch (\Exception $e) {
            Log::error('Error in destroy jadwal: ' . $e->getMessage());
            return response()->json([
                'error' => 'Gagal menghapus jadwal'
            ], 500);
        }
    }

    /**
     * Get mata kuliah by prodi
     */
    public function getMataKuliah($prodiId)
    {
        try {
            $mataKuliahs = MataKuliah::where('prodi_id', $prodiId)
                ->orderBy('semester')
                ->orderBy('nama_mk')
                ->get();
                
            return response()->json($mataKuliahs);
        } catch (\Exception $e) {
            Log::error('Error getting mata kuliah: ' . $e->getMessage());
            return response()->json([
                'error' => 'Gagal memuat mata kuliah'
            ], 500);
        }
    }

    /**
     * Get ruangan by fakultas
     */
    public function getRuanganByFakultas($fakultasId)
    {
        try {
            $ruangans = Ruangan::where('fakultas_id', $fakultasId)
                ->orderBy('kode_ruangan')
                ->get();
                
            return response()->json($ruangans);
        } catch (\Exception $e) {
            Log::error('Error getting ruangan: ' . $e->getMessage());
            return response()->json([
                'error' => 'Gagal memuat ruangan'
            ], 500);
        }
    }

    /**
     * Pindah jadwal (move schedule) - DIPERBAIKI dengan validasi mata kuliah duplikat
     */
    public function pindahJadwal(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'hari' => 'required|in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu',
                'jam_mulai' => 'required|date_format:H:i',
                'jam_selesai' => 'required|date_format:H:i|after:jam_mulai',
                'ruangan_id' => 'required|exists:ruangans,id',
            ]);

            $jadwal = Jadwal::with('prodi.fakultas', 'mataKuliah')->findOrFail($id);

            // Validasi ruangan harus dari fakultas yang sama
            $ruangan = Ruangan::findOrFail($validated['ruangan_id']);
            
            if ($ruangan->fakultas_id != $jadwal->prodi->fakultas_id) {
                return response()->json([
                    'error' => 'Ruangan tidak sesuai dengan fakultas prodi!'
                ], 422);
            }

            // BARU: Cek mata kuliah duplikat di hari baru (exclude jadwal yang sedang dipindah)
            $mataKuliahDuplikat = Jadwal::cekMataKuliahDuplikat(
                $jadwal->mata_kuliah_id,
                $jadwal->prodi_id,
                $jadwal->semester,
                $validated['hari'],
                $id
            )->with('mataKuliah')->first();

            if ($mataKuliahDuplikat) {
                return response()->json([
                    'error' => "Mata kuliah {$jadwal->mataKuliah->nama_mk} sudah dijadwalkan pada hari {$validated['hari']}! Tidak boleh ada mata kuliah yang sama 2 kali dalam sehari di prodi yang sama."
                ], 422);
            }

            // Cek bentrok di prodi yang sama
            $bentrokProdi = Jadwal::cekBentrokProdi(
                $jadwal->prodi_id,
                $jadwal->semester,
                $validated['hari'],
                $validated['jam_mulai'],
                $validated['jam_selesai'],
                $id
            )->with(['mataKuliah', 'ruangan'])->first();

            if ($bentrokProdi) {
                return response()->json([
                    'error' => "Bentrok dengan jadwal {$bentrokProdi->mataKuliah->nama_mk} di ruangan {$bentrokProdi->ruangan->nama_ruangan} pada waktu yang sama!"
                ], 422);
            }

            // Cek bentrok dosen
            $bentrokDosen = Jadwal::cekBentrokDosen(
                $jadwal->dosen_id,
                $validated['hari'],
                $validated['jam_mulai'],
                $validated['jam_selesai'],
                $id
            )->with(['mataKuliah', 'prodi'])->first();

            if ($bentrokDosen) {
                return response()->json([
                    'error' => "Dosen sudah mengajar {$bentrokDosen->mataKuliah->nama_mk} di prodi {$bentrokDosen->prodi->nama_prodi} pada waktu tersebut!"
                ], 422);
            }

            // Cek bentrok ruangan
            $bentrokRuangan = Jadwal::cekBentrokRuangan(
                $validated['ruangan_id'],
                $validated['hari'],
                $validated['jam_mulai'],
                $validated['jam_selesai'],
                $id
            )->with(['mataKuliah', 'prodi', 'dosen'])->first();

            if ($bentrokRuangan) {
                return response()->json([
                    'error' => "Ruangan sudah digunakan untuk {$bentrokRuangan->mataKuliah->nama_mk} ({$bentrokRuangan->prodi->nama_prodi}) oleh {$bentrokRuangan->dosen->nama} pada waktu tersebut!"
                ], 422);
            }

            DB::beginTransaction();
            try {
                $jadwal->update($validated);
                DB::commit();

                return response()->json([
                    'success' => 'Jadwal berhasil dipindahkan!',
                    'data' => $jadwal->load(['mataKuliah', 'dosen', 'ruangan', 'prodi'])
                ]);
            } catch (\Exception $e) {
                DB::rollBack();
                Log::error('Error moving jadwal: ' . $e->getMessage());
                throw $e;
            }

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'error' => 'Validasi gagal',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error in pindah jadwal: ' . $e->getMessage());
            return response()->json([
                'error' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Export to PDF
     */
    public function exportPdf(Request $request)
    {
        try {
            $prodi = Prodi::with('fakultas')->findOrFail($request->prodi_id);
            $jadwals = Jadwal::with(['mataKuliah', 'dosen', 'ruangan'])
                ->where('prodi_id', $request->prodi_id)
                ->where('semester', $request->semester)
                ->where('status', 'Aktif')
                ->orderByRaw("FIELD(hari, 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu')")
                ->orderBy('jam_mulai')
                ->get()
                ->groupBy('hari');

            $pdf = Pdf::loadView('jadwal.pdf', compact('jadwals', 'prodi', 'request'))
                ->setPaper('a4', 'landscape');
                
            return $pdf->download('jadwal-'.$prodi->nama_prodi.'-semester-'.$request->semester.'.pdf');
        } catch (\Exception $e) {
            Log::error('Error exporting PDF: ' . $e->getMessage());
            return back()->with('error', 'Gagal export PDF');
        }
    }

    /**
     * Export to Excel
     */
    public function exportExcel(Request $request)
    {
        try {
            $prodi = Prodi::findOrFail($request->prodi_id);
            $filename = 'jadwal-'.$prodi->nama_prodi.'-semester-'.$request->semester.'.xlsx';
            
            return Excel::download(
                new JadwalExport($request->prodi_id, $request->semester), 
                $filename
            );
        } catch (\Exception $e) {
            Log::error('Error exporting Excel: ' . $e->getMessage());
            return back()->with('error', 'Gagal export Excel');
        }
    }

    /**
     * Import from Excel
     */
    public function importExcel(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:2048'
        ]);

        try {
            DB::beginTransaction();
            
            Excel::import(new JadwalImport, $request->file('file'));
            
            DB::commit();
            
            return response()->json([
                'success' => 'Data jadwal berhasil diimport!'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error importing Excel: ' . $e->getMessage());
            
            return response()->json([
                'error' => 'Gagal import data: ' . $e->getMessage()
            ], 422);
        }
    }

    /**
     * Hitung jam selesai otomatis berdasarkan SKS
     */
    public function hitungJamSelesai(Request $request)
    {
        try {
            $validated = $request->validate([
                'jam_mulai' => 'required|date_format:H:i',
                'mata_kuliah_id' => 'required|exists:mata_kuliahs,id'
            ]);

            $mataKuliah = MataKuliah::findOrFail($validated['mata_kuliah_id']);
            $jamMulai = $validated['jam_mulai'];
            
            // 1 SKS = 50 menit
            $durasi = $mataKuliah->sks * 50;
            
            $jamSelesai = date('H:i', strtotime($jamMulai . ' +' . $durasi . ' minutes'));
            
            return response()->json([
                'jam_selesai' => $jamSelesai,
                'sks' => $mataKuliah->sks,
                'durasi_menit' => $durasi
            ]);
        } catch (\Exception $e) {
            Log::error('Error calculating jam selesai: ' . $e->getMessage());
            return response()->json([
                'error' => 'Gagal menghitung jam selesai'
            ], 500);
        }
    }

    /**
     * Check jadwal bentrok - DIPERBAIKI dengan validasi mata kuliah duplikat
     */
    public function checkBentrok(Request $request)
    {
        try {
            $validated = $request->validate([
                'hari' => 'required|in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu',
                'jam_mulai' => 'required|date_format:H:i',
                'jam_selesai' => 'required|date_format:H:i',
                'mata_kuliah_id' => 'nullable|exists:mata_kuliahs,id',
                'dosen_id' => 'nullable|exists:dosen,id',
                'ruangan_id' => 'nullable|exists:ruangans,id',
                'prodi_id' => 'nullable|exists:prodis,id',
                'semester' => 'nullable|integer|min:1|max:8',
                'jadwal_id' => 'nullable|exists:jadwals,id'
            ]);

            $bentrok = [];

            // BARU: Cek mata kuliah duplikat (jika ada mata_kuliah_id, prodi_id, dan semester)
            if (isset($validated['mata_kuliah_id']) && isset($validated['prodi_id']) && isset($validated['semester'])) {
                $mataKuliahDuplikat = Jadwal::cekMataKuliahDuplikat(
                    $validated['mata_kuliah_id'],
                    $validated['prodi_id'],
                    $validated['semester'],
                    $validated['hari'],
                    $validated['jadwal_id'] ?? null
                )
                ->with('mataKuliah')
                ->first();

                if ($mataKuliahDuplikat) {
                    $bentrok['mata_kuliah_duplikat'] = [
                        'status' => true,
                        'message' => "Mata kuliah {$mataKuliahDuplikat->mataKuliah->nama_mk} sudah dijadwalkan pada hari {$validated['hari']}. Tidak boleh ada mata kuliah yang sama 2 kali dalam sehari!",
                        'jadwal' => $mataKuliahDuplikat
                    ];
                }
            }

            // Cek bentrok prodi (jika ada prodi_id dan semester)
            if (isset($validated['prodi_id']) && isset($validated['semester'])) {
                $bentrokProdi = Jadwal::cekBentrokProdi(
                    $validated['prodi_id'],
                    $validated['semester'],
                    $validated['hari'],
                    $validated['jam_mulai'],
                    $validated['jam_selesai'],
                    $validated['jadwal_id'] ?? null
                )
                ->with(['mataKuliah', 'ruangan'])
                ->first();

                if ($bentrokProdi) {
                    $bentrok['prodi'] = [
                        'status' => true,
                        'message' => "Bentrok dengan jadwal {$bentrokProdi->mataKuliah->nama_mk} di ruangan {$bentrokProdi->ruangan->nama_ruangan}",
                        'jadwal' => $bentrokProdi
                    ];
                }
            }

            // Cek bentrok dosen jika ada
            if (isset($validated['dosen_id'])) {
                $bentrokDosen = Jadwal::cekBentrokDosen(
                    $validated['dosen_id'],
                    $validated['hari'],
                    $validated['jam_mulai'],
                    $validated['jam_selesai'],
                    $validated['jadwal_id'] ?? null
                )
                ->with(['mataKuliah', 'ruangan', 'prodi'])
                ->first();

                if ($bentrokDosen) {
                    $bentrok['dosen'] = [
                        'status' => true,
                        'message' => "Dosen sudah mengajar {$bentrokDosen->mataKuliah->nama_mk} di {$bentrokDosen->ruangan->nama_ruangan} ({$bentrokDosen->prodi->nama_prodi})",
                        'jadwal' => $bentrokDosen
                    ];
                }
            }

            // Cek bentrok ruangan jika ada
            if (isset($validated['ruangan_id'])) {
                $bentrokRuangan = Jadwal::cekBentrokRuangan(
                    $validated['ruangan_id'],
                    $validated['hari'],
                    $validated['jam_mulai'],
                    $validated['jam_selesai'],
                    $validated['jadwal_id'] ?? null
                )
                ->with(['mataKuliah', 'dosen', 'prodi'])
                ->first();

                if ($bentrokRuangan) {
                    $bentrok['ruangan'] = [
                        'status' => true,
                        'message' => "Ruangan sedang digunakan untuk {$bentrokRuangan->mataKuliah->nama_mk} oleh {$bentrokRuangan->dosen->nama} ({$bentrokRuangan->prodi->nama_prodi})",
                        'jadwal' => $bentrokRuangan
                    ];
                }
            }

            return response()->json([
                'bentrok' => !empty($bentrok),
                'detail' => $bentrok
            ]);

        } catch (\Exception $e) {
            Log::error('Error checking bentrok: ' . $e->getMessage());
            return response()->json([
                'error' => 'Gagal mengecek bentrok'
            ], 500);
        }
    }

    /**
     * Get jadwal statistik
     */
    public function statistik(Request $request)
    {
        try {
            $prodiId = $request->prodi_id ?? 1;
            $semester = $request->semester ?? 1;

            $stats = [
                'total_jadwal' => Jadwal::where('prodi_id', $prodiId)
                    ->where('semester', $semester)
                    ->where('status', 'Aktif')
                    ->count(),
                    
                'total_sks' => Jadwal::join('mata_kuliahs', 'jadwals.mata_kuliah_id', '=', 'mata_kuliahs.id')
                    ->where('jadwals.prodi_id', $prodiId)
                    ->where('jadwals.semester', $semester)
                    ->where('jadwals.status', 'Aktif')
                    ->sum('mata_kuliahs.sks'),
                    
                'dosen_aktif' => Jadwal::where('prodi_id', $prodiId)
                    ->where('semester', $semester)
                    ->where('status', 'Aktif')
                    ->distinct('dosen_id')
                    ->count('dosen_id'),
                    
                'ruangan_terpakai' => Jadwal::where('prodi_id', $prodiId)
                    ->where('semester', $semester)
                    ->where('status', 'Aktif')
                    ->distinct('ruangan_id')
                    ->count('ruangan_id'),
                    
                'per_hari' => Jadwal::where('prodi_id', $prodiId)
                    ->where('semester', $semester)
                    ->where('status', 'Aktif')
                    ->select('hari', DB::raw('count(*) as total'))
                    ->groupBy('hari')
                    ->orderByRaw("FIELD(hari, 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu')")
                    ->get()
            ];

            return response()->json($stats);

        } catch (\Exception $e) {
            Log::error('Error getting statistik: ' . $e->getMessage());
            return response()->json([
                'error' => 'Gagal memuat statistik'
            ], 500);
        }
    }
}