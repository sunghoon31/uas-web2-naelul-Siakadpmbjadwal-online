<?php

namespace App\Http\Controllers;

use App\Models\CalonMahasiswa;
use App\Models\DokumenCalonMahasiswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class DokumenCalonMahasiswaController extends Controller
{
    /**
     * Menampilkan halaman dokumen calon mahasiswa
     */
    public function index($calonMahasiswaId)
    {
        $calonMahasiswa = CalonMahasiswa::with(['dokumen', 'prodi.fakultas'])->findOrFail($calonMahasiswaId);
        
        // Daftar jenis dokumen yang diperlukan
        $jenisDokumen = [
            'ijazah' => 'Ijazah/STTB',
            'transkrip_nilai' => 'Transkrip Nilai',
            'kartu_keluarga' => 'Kartu Keluarga',
            'akta_kelahiran' => 'Akta Kelahiran',
            'foto_diri' => 'Pas Foto 3x4',
            'surat_keterangan_sehat' => 'Surat Keterangan Sehat',
            'surat_kelakuan_baik' => 'Surat Kelakuan Baik',
        ];

        if ($calonMahasiswa->jalur_masuk === 'prestasi') {
            $jenisDokumen['sertifikat_prestasi'] = 'Sertifikat Prestasi';
        }

        if ($calonMahasiswa->jalur_masuk === 'beasiswa') {
            $jenisDokumen['surat_rekomendasi'] = 'Surat Rekomendasi';
        }

        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'data' => $calonMahasiswa,
                'jenisDokumen' => $jenisDokumen
            ]);
        }

        return view('calon-mahasiswa.dokumen', compact('calonMahasiswa', 'jenisDokumen'));
    }

    /**
     * Upload dokumen
     */
    public function upload(Request $request, $calonMahasiswaId)
    {
        $validator = Validator::make($request->all(), [
            'jenis_dokumen' => 'required|in:ijazah,transkrip_nilai,kartu_keluarga,akta_kelahiran,foto_diri,surat_keterangan_sehat,surat_kelakuan_baik,sertifikat_prestasi,surat_rekomendasi,lainnya',
            'file' => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120', // max 5MB
        ], [
            'jenis_dokumen.required' => 'Jenis dokumen harus dipilih',
            'file.required' => 'File dokumen harus diupload',
            'file.mimes' => 'Format file harus JPG, JPEG, PNG, atau PDF',
            'file.max' => 'Ukuran file maksimal 5MB'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        $calonMahasiswa = CalonMahasiswa::findOrFail($calonMahasiswaId);

        // Cek apakah dokumen jenis ini sudah ada
        $existingDokumen = DokumenCalonMahasiswa::where('calon_mahasiswa_id', $calonMahasiswaId)
            ->where('jenis_dokumen', $request->jenis_dokumen)
            ->first();

        if ($existingDokumen) {
            return response()->json([
                'success' => false,
                'message' => 'Dokumen jenis ini sudah ada. Silakan hapus terlebih dahulu jika ingin mengganti.'
            ], 422);
        }

        try {
            $file = $request->file('file');
            $originalName = $file->getClientOriginalName();
            $extension = $file->getClientOriginalExtension();
            
            // Generate nama file unik
            $fileName = Str::slug($calonMahasiswa->no_pendaftaran) . '_' . 
                       $request->jenis_dokumen . '_' . 
                       time() . '.' . $extension;
            
            // Simpan file
            $path = $file->storeAs('dokumen_pmb/' . $calonMahasiswa->no_pendaftaran, $fileName, 'public');

            // Simpan ke database
            $dokumen = DokumenCalonMahasiswa::create([
                'calon_mahasiswa_id' => $calonMahasiswaId,
                'jenis_dokumen' => $request->jenis_dokumen,
                'nama_file' => $fileName,
                'path_file' => $path,
                'original_name' => $originalName,
                'ukuran_file' => $file->getSize(),
                'mime_type' => $file->getMimeType(),
                'status_verifikasi' => 'menunggu',
            ]);

            // Update status verifikasi berkas calon mahasiswa
            if ($calonMahasiswa->status_verifikasi_berkas === 'belum_upload') {
                $calonMahasiswa->update([
                    'status_verifikasi_berkas' => 'menunggu_verifikasi'
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Dokumen berhasil diupload',
                'data' => $dokumen->load('calonMahasiswa')
            ]);

        } catch (\Exception $e) {
            Log::error('Error upload dokumen: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengupload dokumen: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Hapus dokumen
     */
    public function destroy($calonMahasiswaId, $dokumenId)
    {
        try {
            $dokumen = DokumenCalonMahasiswa::where('calon_mahasiswa_id', $calonMahasiswaId)
                ->findOrFail($dokumenId);

            $dokumen->delete(); // File akan terhapus otomatis via model boot

            // Update status verifikasi berkas jika tidak ada dokumen lagi
            $calonMahasiswa = CalonMahasiswa::find($calonMahasiswaId);
            if ($calonMahasiswa->dokumen()->count() === 0) {
                $calonMahasiswa->update([
                    'status_verifikasi_berkas' => 'belum_upload'
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Dokumen berhasil dihapus'
            ]);

        } catch (\Exception $e) {
            Log::error('Error hapus dokumen: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus dokumen: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Download dokumen
     */
    public function download($calonMahasiswaId, $dokumenId)
    {
        try {
            $dokumen = DokumenCalonMahasiswa::where('calon_mahasiswa_id', $calonMahasiswaId)
                ->findOrFail($dokumenId);

            if (!$dokumen->fileExists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'File tidak ditemukan'
                ], 404);
            }

            return Storage::disk('public')->download($dokumen->path_file, $dokumen->original_name);
            
        } catch (\Exception $e) {
            Log::error('Error download dokumen: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal mendownload dokumen'
            ], 500);
        }
    }

    /**
     * Verifikasi dokumen (Approve)
     */
    public function verify(Request $request, $calonMahasiswaId, $dokumenId)
    {
        try {
            $dokumen = DokumenCalonMahasiswa::where('calon_mahasiswa_id', $calonMahasiswaId)
                ->findOrFail($dokumenId);

            $validator = Validator::make($request->all(), [
                'catatan' => 'nullable|string|max:1000',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Update dokumen
            $dokumen->update([
                'status_verifikasi' => 'disetujui',
                'catatan' => $request->catatan,
                'verified_at' => now(),
                'verified_by' => auth()->id(),
            ]);

            // Cek apakah semua dokumen sudah diverifikasi
            $calonMahasiswa = CalonMahasiswa::with('dokumen')->find($calonMahasiswaId);
            
            $totalDokumen = $calonMahasiswa->dokumen->count();
            $verifiedDokumen = $calonMahasiswa->dokumen->where('status_verifikasi', 'disetujui')->count();
            
            Log::info("Verifikasi Dokumen - Total: $totalDokumen, Verified: $verifiedDokumen");
            
            // Jika semua dokumen sudah diverifikasi
            if ($totalDokumen > 0 && $totalDokumen === $verifiedDokumen) {
                $calonMahasiswa->update([
                    'status_verifikasi_berkas' => 'diverifikasi'
                ]);
                Log::info("Status berkas diupdate menjadi: diverifikasi");
            } else {
                // Update ke menunggu_verifikasi jika masih ada yang belum
                $calonMahasiswa->update([
                    'status_verifikasi_berkas' => 'menunggu_verifikasi'
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Dokumen berhasil diverifikasi',
                'data' => $dokumen->fresh()
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error verifikasi dokumen: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal memverifikasi dokumen: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Tolak dokumen
     */
    public function reject(Request $request, $calonMahasiswaId, $dokumenId)
    {
        try {
            $dokumen = DokumenCalonMahasiswa::where('calon_mahasiswa_id', $calonMahasiswaId)
                ->findOrFail($dokumenId);

            $validator = Validator::make($request->all(), [
                'catatan' => 'required|string|max:1000',
            ], [
                'catatan.required' => 'Alasan penolakan harus diisi'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            $dokumen->update([
                'status_verifikasi' => 'ditolak',
                'catatan' => $request->catatan,
                'verified_at' => now(),
                'verified_by' => auth()->id(),
            ]);

            // Update status verifikasi berkas calon mahasiswa
            $calonMahasiswa = CalonMahasiswa::find($calonMahasiswaId);
            $calonMahasiswa->update([
                'status_verifikasi_berkas' => 'ditolak',
                'catatan_verifikasi' => $request->catatan
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Dokumen ditolak',
                'data' => $dokumen->fresh()
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error tolak dokumen: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal menolak dokumen: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate dan download kartu ujian
     */
    public function generateKartuUjian($calonMahasiswaId)
    {
        try {
            $calonMahasiswa = CalonMahasiswa::with('prodi.fakultas')->findOrFail($calonMahasiswaId);

            // Validasi: pastikan berkas sudah diverifikasi
            if ($calonMahasiswa->status_verifikasi_berkas !== 'diverifikasi') {
                return response()->json([
                    'success' => false,
                    'message' => 'Berkas belum diverifikasi. Kartu ujian hanya dapat dibuat setelah semua berkas diverifikasi.'
                ], 422);
            }

            // Generate kartu ujian (bisa menggunakan PDF library seperti DomPDF atau mPDF)
            // Untuk sementara, kita simpan flag bahwa kartu ujian sudah dibuat
            
            $kartuUjianPath = 'kartu_ujian/' . $calonMahasiswa->no_pendaftaran . '.pdf';
            
            $calonMahasiswa->update([
                'kartu_ujian' => $kartuUjianPath
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Kartu ujian berhasil dibuat',
                'data' => $calonMahasiswa->fresh()
            ]);

        } catch (\Exception $e) {
            Log::error('Error generate kartu ujian: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat kartu ujian: ' . $e->getMessage()
            ], 500);
        }
    }
}