<?php

namespace App\Http\Controllers;

use App\Models\CalonMahasiswa;
use App\Models\DokumenCalonMahasiswa;
use App\Models\Mahasiswa;
use App\Models\Prodi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use PDF; // Barryvdh\DomPDF
use Excel; // Maatwebsite\Excel

class CalonMahasiswaController extends Controller
{
    /**
     * Menampilkan daftar calon mahasiswa
     * FIXED: Load relasi dokumen untuk status verifikasi
     */
    public function index(Request $request)
    {
        // PENTING: Load relasi dokumen untuk accessor status_verifikasi_berkas_text
        $query = CalonMahasiswa::with(['prodi.fakultas', 'dokumen']);

        // Filter berdasarkan status seleksi
        if ($request->filled('status')) {
            $query->where('status_seleksi', $request->status);
        }

        // Filter berdasarkan jalur masuk
        if ($request->filled('jalur')) {
            $query->where('jalur_masuk', $request->jalur);
        }

        // Filter berdasarkan prodi
        if ($request->filled('prodi_id')) {
            $query->where('prodi_id', $request->prodi_id);
        }

        // Filter berdasarkan gelombang
        if ($request->filled('gelombang')) {
            $query->where('gelombang', $request->gelombang);
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('no_pendaftaran', 'like', "%{$search}%")
                  ->orWhere('no_hp', 'like', "%{$search}%");
            });
        }

        $calonMahasiswa = $query->latest()->paginate(20);

        // Return JSON untuk AJAX request
        if ($request->wantsJson() || $request->ajax()) {
            // PENTING: Transform data agar accessor ter-include
            $calonMahasiswa->getCollection()->transform(function ($item) {
                return [
                    'id' => $item->id,
                    'no_pendaftaran' => $item->no_pendaftaran,
                    'nama' => $item->nama,
                    'jenis_kelamin' => $item->jenis_kelamin,
                    'alamat' => $item->alamat,
                    'no_hp' => $item->no_hp,
                    'prodi' => $item->prodi,
                    'jalur_masuk' => $item->jalur_masuk,
                    'gelombang' => $item->gelombang,
                    'status_seleksi' => $item->status_seleksi,
                    
                    // CRITICAL: Status verifikasi dari accessor
                    'status_verifikasi_berkas_text' => $item->status_verifikasi_berkas_text,
                    'status_verifikasi_badge' => $item->status_verifikasi_badge,
                    
                    // Info dokumen
                    'jumlah_dokumen' => $item->jumlah_dokumen,
                    'jumlah_dokumen_diverifikasi' => $item->jumlah_dokumen_diverifikasi,
                ];
            });
            
            return response()->json($calonMahasiswa);
        }

        $prodis = Prodi::with('fakultas')->orderBy('nama_prodi')->get();
        return view('calon-mahasiswa.index', compact('calonMahasiswa', 'prodis'));
    }

    /**
     * Menampilkan form pendaftaran calon mahasiswa
     */
    public function create()
    {
        $prodis = Prodi::with('fakultas')->orderBy('nama_prodi')->get();
        
        // Data jalur masuk untuk dropdown
        $jalurMasuk = [
            'reguler' => 'Reguler',
            'prestasi' => 'Prestasi',
            'beasiswa' => 'Beasiswa',
            'pindahan' => 'Pindahan'
        ];

        return view('calon-mahasiswa.create', compact('prodis', 'jalurMasuk'));
    }

    /**
     * Menyimpan data pendaftaran calon mahasiswa baru dengan dokumen
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'jenis_kelamin' => 'required|in:L,P',
            'alamat' => 'required|string',
            'no_hp' => 'required|string|max:20',
            'prodi_id' => 'required|exists:prodis,id',
            'jalur_masuk' => 'required|in:reguler,prestasi,beasiswa,pindahan',
            'gelombang' => 'nullable|string|max:255',
            'dokumen.*' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120', // 5MB
        ]);

        DB::beginTransaction();
        try {
            // Generate nomor pendaftaran otomatis
            $validated['no_pendaftaran'] = $this->generateNoPendaftaran();
            $validated['status_seleksi'] = 'pending';

            // Simpan data calon mahasiswa
            $calonMahasiswa = CalonMahasiswa::create($validated);

            // Proses upload dokumen jika ada
            $uploadedCount = 0;
            if ($request->has('dokumen') && is_array($request->dokumen)) {
                $uploadedCount = $this->handleDokumenUpload($request->dokumen, $calonMahasiswa);
                
                // Update status verifikasi berkas jika ada dokumen yang diupload
                if ($uploadedCount > 0) {
                    $calonMahasiswa->update([
                        'status_verifikasi_berkas' => 'menunggu_verifikasi'
                    ]);
                }
            }

            DB::commit();

            // Return JSON untuk AJAX
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Data berhasil disimpan' . ($uploadedCount > 0 ? " dengan {$uploadedCount} dokumen" : ''),
                    'data' => $calonMahasiswa->load('prodi.fakultas', 'dokumen')
                ]);
            }

            return redirect()
                ->route('pmb.calon-mahasiswa.index')
                ->with('success', 'Pendaftaran berhasil! Nomor Pendaftaran: ' . $calonMahasiswa->no_pendaftaran);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error storing calon mahasiswa: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan: ' . $e->getMessage()
                ], 500);
            }

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan saat menyimpan data');
        }
    }

    /**
     * Handle upload multiple dokumen
     */
    private function handleDokumenUpload($dokumenFiles, $calonMahasiswa)
    {
        $uploadedCount = 0;

        foreach ($dokumenFiles as $jenisDokumen => $file) {
            if (!$file || !$file->isValid()) {
                continue;
            }

            try {
                $originalName = $file->getClientOriginalName();
                $extension = $file->getClientOriginalExtension();
                
                // Generate nama file unik
                $fileName = Str::slug($calonMahasiswa->no_pendaftaran) . '_' . 
                           $jenisDokumen . '_' . 
                           time() . '_' . 
                           Str::random(6) . '.' . $extension;
                
                // Simpan file
                $path = $file->storeAs(
                    'dokumen_pmb/' . $calonMahasiswa->no_pendaftaran, 
                    $fileName, 
                    'public'
                );

                // Simpan ke database
                DokumenCalonMahasiswa::create([
                    'calon_mahasiswa_id' => $calonMahasiswa->id,
                    'jenis_dokumen' => $jenisDokumen,
                    'nama_file' => $fileName,
                    'path_file' => $path,
                    'original_name' => $originalName,
                    'ukuran_file' => $file->getSize(),
                    'mime_type' => $file->getMimeType(),
                    'status_verifikasi' => 'menunggu',
                ]);

                $uploadedCount++;
                
            } catch (\Exception $e) {
                Log::error("Error uploading dokumen {$jenisDokumen}: " . $e->getMessage());
                // Continue dengan dokumen lainnya
                continue;
            }
        }

        return $uploadedCount;
    }

    /**
     * Menampilkan detail calon mahasiswa
     * FIXED: Load relasi dokumen
     */
    public function show(CalonMahasiswa $calonMahasiswa)
    {
        $calonMahasiswa->load('prodi.fakultas', 'dokumen');
        
        // Cek apakah sudah menjadi mahasiswa aktif
        $mahasiswa = null;
        if ($calonMahasiswa->status_seleksi === 'diterima') {
            $mahasiswa = Mahasiswa::where('nama', $calonMahasiswa->nama)
                ->where('prodi_id', $calonMahasiswa->prodi_id)
                ->latest()
                ->first();
        }

        // Return JSON untuk AJAX
        if (request()->wantsJson() || request()->ajax()) {
            return response()->json([
                'success' => true,
                'data' => array_merge(
                    $calonMahasiswa->toArray(),
                    [
                        'status_verifikasi_berkas_text' => $calonMahasiswa->status_verifikasi_berkas_text,
                        'status_verifikasi_badge' => $calonMahasiswa->status_verifikasi_badge,
                        'jumlah_dokumen' => $calonMahasiswa->jumlah_dokumen,
                        'jumlah_dokumen_diverifikasi' => $calonMahasiswa->jumlah_dokumen_diverifikasi,
                    ]
                ),
                'mahasiswa' => $mahasiswa
            ]);
        }

        return view('calon-mahasiswa.show', compact('calonMahasiswa', 'mahasiswa'));
    }

    /**
     * Menampilkan form edit data calon mahasiswa
     */
    public function edit(CalonMahasiswa $calonMahasiswa)
    {
        // Return JSON untuk AJAX
        if (request()->wantsJson() || request()->ajax()) {
            return response()->json([
                'success' => true,
                'data' => $calonMahasiswa->load('prodi.fakultas', 'dokumen')
            ]);
        }

        // Tidak bisa edit jika sudah diterima atau ditolak
        if (in_array($calonMahasiswa->status_seleksi, ['diterima', 'ditolak'])) {
            return redirect()
                ->route('pmb.calon-mahasiswa.index')
                ->with('error', 'Data tidak dapat diubah karena sudah diproses!');
        }

        $prodis = Prodi::with('fakultas')->orderBy('nama_prodi')->get();
        
        $jalurMasuk = [
            'reguler' => 'Reguler',
            'prestasi' => 'Prestasi',
            'beasiswa' => 'Beasiswa',
            'pindahan' => 'Pindahan'
        ];

        return view('calon-mahasiswa.edit', compact('calonMahasiswa', 'prodis', 'jalurMasuk'));
    }

    /**
     * Update data calon mahasiswa dengan dokumen
     */
    public function update(Request $request, CalonMahasiswa $calonMahasiswa)
    {
        // Tidak bisa update jika sudah diterima atau ditolak
        if (in_array($calonMahasiswa->status_seleksi, ['diterima', 'ditolak'])) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data tidak dapat diubah karena sudah diproses!'
                ], 422);
            }
            
            return redirect()
                ->route('pmb.calon-mahasiswa.index')
                ->with('error', 'Data tidak dapat diubah karena sudah diproses!');
        }

        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'jenis_kelamin' => 'required|in:L,P',
            'alamat' => 'required|string',
            'no_hp' => 'required|string|max:20',
            'prodi_id' => 'required|exists:prodis,id',
            'jalur_masuk' => 'required|in:reguler,prestasi,beasiswa,pindahan',
            'gelombang' => 'nullable|string|max:255',
            'dokumen.*' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
        ]);

        DB::beginTransaction();
        try {
            // Update data calon mahasiswa
            $calonMahasiswa->update($validated);

            // Proses upload dokumen tambahan jika ada
            $uploadedCount = 0;
            if ($request->has('dokumen') && is_array($request->dokumen)) {
                $uploadedCount = $this->handleDokumenUpload($request->dokumen, $calonMahasiswa);
                
                // Update status verifikasi berkas jika ada dokumen baru dan status masih belum upload
                if ($uploadedCount > 0 && $calonMahasiswa->status_verifikasi_berkas === 'belum_upload') {
                    $calonMahasiswa->update([
                        'status_verifikasi_berkas' => 'menunggu_verifikasi'
                    ]);
                }
            }

            DB::commit();

            // Return JSON untuk AJAX
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Data berhasil diperbarui' . ($uploadedCount > 0 ? " dengan {$uploadedCount} dokumen baru" : ''),
                    'data' => $calonMahasiswa->load('prodi.fakultas', 'dokumen')
                ]);
            }

            return redirect()
                ->route('pmb.calon-mahasiswa.index')
                ->with('success', 'Data berhasil diperbarui!');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating calon mahasiswa: ' . $e->getMessage());
            
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan: ' . $e->getMessage()
                ], 500);
            }

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan saat memperbarui data');
        }
    }

    /**
     * Menghapus data calon mahasiswa
     */
    public function destroy(CalonMahasiswa $calonMahasiswa)
    {
        // Tidak bisa hapus jika sudah diterima
        if ($calonMahasiswa->status_seleksi === 'diterima') {
            if (request()->wantsJson() || request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data tidak dapat dihapus karena sudah diterima dan menjadi mahasiswa aktif!'
                ], 422);
            }
            
            return redirect()
                ->route('pmb.calon-mahasiswa.index')
                ->with('error', 'Data tidak dapat dihapus karena sudah diterima dan menjadi mahasiswa aktif!');
        }

        $noPendaftaran = $calonMahasiswa->no_pendaftaran;
        $calonMahasiswa->delete();

        // Return JSON untuk AJAX
        if (request()->wantsJson() || request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Data berhasil dihapus'
            ]);
        }

        return redirect()
            ->route('pmb.calon-mahasiswa.index')
            ->with('success', "Data calon mahasiswa {$noPendaftaran} berhasil dihapus!");
    }

    /**
     * ACC/Approve calon mahasiswa menjadi mahasiswa aktif
     * Data inti akan dipindahkan ke tabel mahasiswa
     */
    public function approve(CalonMahasiswa $calonMahasiswa)
    {
        // Validasi: cek apakah masih pending
        if ($calonMahasiswa->status_seleksi !== 'pending') {
            if (request()->wantsJson() || request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Calon mahasiswa ini sudah diproses sebelumnya!'
                ], 422);
            }
            
            return redirect()
                ->back()
                ->with('error', 'Calon mahasiswa ini sudah diproses sebelumnya!');
        }

        DB::beginTransaction();
        try {
            // Generate NIM dan angkatan berdasarkan tahun ACC
            $tahunSekarang = now()->year;
            $nim = $this->generateNIM($tahunSekarang);
            
            // Pindahkan data inti ke tabel mahasiswa
            $mahasiswa = Mahasiswa::create([
                'nim' => $nim,
                'nama' => $calonMahasiswa->nama,
                'angkatan' => $tahunSekarang,
                'prodi_id' => $calonMahasiswa->prodi_id,
                'foto' => null,
            ]);

            // Update status calon mahasiswa menjadi diterima
            $calonMahasiswa->update([
                'status_seleksi' => 'diterima'
            ]);

            DB::commit();

            // Return JSON untuk AJAX
            if (request()->wantsJson() || request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Calon mahasiswa berhasil di-ACC',
                    'data' => $calonMahasiswa->load('prodi.fakultas'),
                    'nim' => $nim
                ]);
            }

            return redirect()
                ->route('pmb.calon-mahasiswa.index')
                ->with('success', "Selamat! Calon mahasiswa berhasil di-ACC dan menjadi mahasiswa aktif dengan NIM: {$nim}");

        } catch (\Exception $e) {
            DB::rollBack();
            
            if (request()->wantsJson() || request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan saat memproses ACC: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()
                ->back()
                ->with('error', 'Terjadi kesalahan saat memproses ACC: ' . $e->getMessage());
        }
    }

    /**
     * Tolak pendaftaran calon mahasiswa
     */
    public function reject(Request $request, CalonMahasiswa $calonMahasiswa)
    {
        // Validasi: cek apakah masih pending
        if ($calonMahasiswa->status_seleksi !== 'pending') {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Calon mahasiswa ini sudah diproses sebelumnya!'
                ], 422);
            }
            
            return redirect()
                ->back()
                ->with('error', 'Calon mahasiswa ini sudah diproses sebelumnya!');
        }

        $calonMahasiswa->update([
            'status_seleksi' => 'ditolak'
        ]);

        // Return JSON untuk AJAX
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Pendaftaran berhasil ditolak',
                'data' => $calonMahasiswa->load('prodi.fakultas')
            ]);
        }

        return redirect()
            ->route('pmb.calon-mahasiswa.index')
            ->with('warning', 'Calon mahasiswa telah ditolak!');
    }

    /**
     * Reset status seleksi kembali ke pending
     * (untuk kasus khusus, misalnya salah input)
     */
    public function resetStatus(CalonMahasiswa $calonMahasiswa)
    {
        // Tidak bisa reset jika sudah diterima dan sudah jadi mahasiswa
        if ($calonMahasiswa->status_seleksi === 'diterima') {
            $mahasiswaExists = Mahasiswa::where('nama', $calonMahasiswa->nama)
                ->where('prodi_id', $calonMahasiswa->prodi_id)
                ->exists();
                
            if ($mahasiswaExists) {
                if (request()->wantsJson() || request()->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Tidak dapat mereset status karena sudah menjadi mahasiswa aktif!'
                    ], 422);
                }
                
                return redirect()
                    ->back()
                    ->with('error', 'Tidak dapat mereset status karena sudah menjadi mahasiswa aktif!');
            }
        }

        $calonMahasiswa->update([
            'status_seleksi' => 'pending'
        ]);

        // Return JSON untuk AJAX
        if (request()->wantsJson() || request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Status seleksi berhasil direset ke pending',
                'data' => $calonMahasiswa->load('prodi.fakultas')
            ]);
        }

        return redirect()
            ->route('pmb.calon-mahasiswa.index')
            ->with('success', 'Status seleksi berhasil direset ke pending!');
    }

    /**
     * Generate nomor pendaftaran otomatis
     * Format: PMB-YYYYMMDD-XXXX
     * Contoh: PMB-20260104-0001
     */
    private function generateNoPendaftaran(): string
    {
        $today = now()->format('Ymd');
        $prefix = "PMB-{$today}";

        // Cari nomor urut terakhir hari ini
        $lastRecord = CalonMahasiswa::where('no_pendaftaran', 'like', "{$prefix}%")
            ->orderBy('no_pendaftaran', 'desc')
            ->first();

        if ($lastRecord) {
            // Ambil 4 digit terakhir dan tambah 1
            $lastNumber = intval(substr($lastRecord->no_pendaftaran, -4));
            $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            // Nomor pertama hari ini
            $newNumber = '0001';
        }

        return "{$prefix}-{$newNumber}";
    }

    /**
     * Generate NIM otomatis
     * Format: 2 digit tahun + 8 digit angka acak
     * Contoh untuk tahun 2026: 26XXXXXXXX
     */
    private function generateNIM(int $tahun): string
    {
        // Ambil 2 digit terakhir tahun
        $duaDigitTahun = substr($tahun, -2);
        
        do {
            // Generate 8 digit angka acak
            $randomNumber = str_pad(mt_rand(0, 99999999), 8, '0', STR_PAD_LEFT);
            $nim = $duaDigitTahun . $randomNumber;
            
            // Cek apakah NIM sudah ada di database
            $exists = Mahasiswa::where('nim', $nim)->exists();
        } while ($exists);

        return $nim;
    }

    
    /**
     * Dashboard/Statistik PMB
     */
    public function dashboard()
    {
        $stats = [
            'total' => CalonMahasiswa::count(),
            'pending' => CalonMahasiswa::where('status_seleksi', 'pending')->count(),
            'diterima' => CalonMahasiswa::where('status_seleksi', 'diterima')->count(),
            'ditolak' => CalonMahasiswa::where('status_seleksi', 'ditolak')->count(),
        ];

        $perJalur = CalonMahasiswa::select('jalur_masuk', DB::raw('count(*) as total'))
            ->groupBy('jalur_masuk')
            ->get();

        $perProdi = CalonMahasiswa::select('prodi_id', DB::raw('count(*) as total'))
            ->with('prodi')
            ->groupBy('prodi_id')
            ->orderBy('total', 'desc')
            ->limit(10)
            ->get();

        if (request()->ajax()) {
            return response()->json(compact('stats', 'perJalur', 'perProdi'));
        }

        return view('calon-mahasiswa.dashboard', compact('stats', 'perJalur', 'perProdi'));
    }

    /**
     * Export PDF dengan filter
     * FIXED: Load relasi dokumen
     */
    public function exportPDF(Request $request)
    {
        try {
            $query = CalonMahasiswa::with(['prodi.fakultas', 'dokumen']);

            // Apply filters
            if ($request->filled('status')) {
                $query->where('status_seleksi', $request->status);
            }
            
            if ($request->filled('jalur')) {
                $query->where('jalur_masuk', $request->jalur);
            }
            
            if ($request->filled('prodi_id')) {
                $query->where('prodi_id', $request->prodi_id);
            }
            
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('nama', 'like', "%{$search}%")
                      ->orWhere('no_pendaftaran', 'like', "%{$search}%")
                      ->orWhere('no_hp', 'like', "%{$search}%");
                });
            }

            $calonMahasiswa = $query->latest()->get();
            
            // Info filter untuk ditampilkan di PDF
            $filterInfo = [
                'status' => $request->status ? ucfirst($request->status) : 'Semua',
                'jalur' => $request->jalur ? ucfirst($request->jalur) : 'Semua',
                'prodi' => $request->prodi_id ? Prodi::find($request->prodi_id)->nama_prodi ?? 'Semua' : 'Semua',
                'search' => $request->search ?? '-'
            ];

            // Load view untuk PDF
            $pdf = PDF::loadView('calon-mahasiswa.export-pdf', [
                'calonMahasiswa' => $calonMahasiswa,
                'filterInfo' => $filterInfo,
                'tanggal_export' => now()->format('d/m/Y H:i:s')
            ]);

            // Set ukuran dan orientasi kertas
            $pdf->setPaper('A4', 'landscape');

            // Download PDF dengan nama file dinamis
            $fileName = 'Calon_Mahasiswa_' . now()->format('Ymd_His') . '.pdf';
            return $pdf->download($fileName);

        } catch (\Exception $e) {
            Log::error('Error exporting PDF: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            
            return redirect()
                ->back()
                ->with('error', 'Gagal export PDF: ' . $e->getMessage());
        }
    }

    /**
     * Export Excel dengan filter
     * FIXED: Load relasi dokumen
     */
    public function exportExcel(Request $request)
    {
        try {
            $query = CalonMahasiswa::with(['prodi.fakultas', 'dokumen']);

            // Apply filters
            if ($request->filled('status')) {
                $query->where('status_seleksi', $request->status);
            }
            
            if ($request->filled('jalur')) {
                $query->where('jalur_masuk', $request->jalur);
            }
            
            if ($request->filled('prodi_id')) {
                $query->where('prodi_id', $request->prodi_id);
            }
            
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('nama', 'like', "%{$search}%")
                      ->orWhere('no_pendaftaran', 'like', "%{$search}%")
                      ->orWhere('no_hp', 'like', "%{$search}%");
                });
            }

            $calonMahasiswa = $query->latest()->get();

            // Nama file dinamis
            $fileName = 'Calon_Mahasiswa_' . now()->format('Ymd_His') . '.xlsx';

            // Export menggunakan Laravel Excel
            return Excel::download(new \App\Exports\CalonMahasiswaExport($calonMahasiswa), $fileName);

        } catch (\Exception $e) {
            Log::error('Error exporting Excel: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            
            return redirect()
                ->back()
                ->with('error', 'Gagal export Excel: ' . $e->getMessage());
        }
    }

    /**
     * Import Excel
     */
    public function importExcel(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls|max:5120'
        ]);

        try {
            Excel::import(new \App\Imports\CalonMahasiswaImport, $request->file('file'));

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Data berhasil diimport'
                ]);
            }

            return redirect()
                ->back()
                ->with('success', 'Data calon mahasiswa berhasil diimport!');

        } catch (\Exception $e) {
            Log::error('Error importing calon mahasiswa: ' . $e->getMessage());
            
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal import: ' . $e->getMessage()
                ], 500);
            }

            return redirect()
                ->back()
                ->with('error', 'Gagal import data: ' . $e->getMessage());
        }
    }
}