<?php

namespace App\Http\Controllers;

use App\Models\CalonMahasiswa;
use App\Models\KeuanganPMB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;

class KeuanganPMBController extends Controller
{
    /**
     * Menampilkan halaman keuangan calon mahasiswa
     */
    public function show($id)
    {
        try {
            $calonMahasiswa = CalonMahasiswa::with(['prodi.fakultas', 'keuanganPMB'])
                ->findOrFail($id);

            // Return JSON untuk AJAX
            if (request()->wantsJson() || request()->ajax()) {
                $keuangan = $calonMahasiswa->keuanganPMB->map(function($item) {
                    return [
                        'id' => $item->id,
                        'jenis_biaya' => $item->jenis_biaya,
                        'jenis_biaya_nama' => $this->getJenisBiayaNama($item->jenis_biaya),
                        'nominal' => $item->nominal,
                        'status_bayar' => $item->status_bayar,
                        'tanggal_bayar' => $item->tanggal_bayar ? date('d-m-Y', strtotime($item->tanggal_bayar)) : null,
                        'metode_bayar' => $item->metode_bayar,
                        'bukti_bayar' => $item->bukti_bayar,
                        'keterangan' => $item->keterangan,
                    ];
                });

                return response()->json([
                    'success' => true,
                    'calon_mahasiswa' => $calonMahasiswa,
                    'keuangan' => $keuangan
                ]);
            }

            return view('keuangan-pmb.show', compact('calonMahasiswa'));

        } catch (\Exception $e) {
            Log::error('Error loading keuangan: ' . $e->getMessage());
            
            if (request()->wantsJson() || request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal memuat data keuangan: ' . $e->getMessage()
                ], 500);
            }

            return redirect()
                ->route('pmb.calon-mahasiswa.index')
                ->with('error', 'Gagal memuat data keuangan!');
        }
    }

    /**
     * Generate biaya otomatis untuk calon mahasiswa
     */
    public function generate($id)
    {
        DB::beginTransaction();
        try {
            $calonMahasiswa = CalonMahasiswa::findOrFail($id);

            // Cek apakah sudah pernah di-generate
            if ($calonMahasiswa->keuanganPMB()->count() > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Biaya PMB sudah pernah di-generate sebelumnya!'
                ], 400);
            }

            // Biaya berdasarkan jalur masuk
            $biayaList = $this->getBiayaByJalur($calonMahasiswa->jalur_masuk);

            foreach ($biayaList as $biaya) {
                KeuanganPMB::create([
                    'calon_mahasiswa_id' => $calonMahasiswa->id,
                    'jenis_biaya' => $biaya['jenis'],
                    'nominal' => $biaya['nominal'],
                    'keterangan' => $biaya['keterangan'],
                    'status_bayar' => 'belum_bayar',
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Biaya PMB berhasil di-generate!'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error generating biaya: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal generate biaya: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Proses konfirmasi pembayaran
     */
    public function konfirmasiBayar(Request $request, $id)
    {
        try {
            // Validasi dengan tanggal yang lebih permisif (sampai besok untuk menghindari timezone issue)
            $tomorrow = date('Y-m-d', strtotime('+1 day'));
            
            $validated = $request->validate([
                'tanggal_bayar' => "required|date|before_or_equal:{$tomorrow}",
                'metode_bayar' => 'required|string|max:255',
                'bukti_bayar' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:2048',
            ], [
                'tanggal_bayar.required' => 'Tanggal bayar harus diisi',
                'tanggal_bayar.date' => 'Format tanggal tidak valid',
                'tanggal_bayar.before_or_equal' => 'Tanggal bayar tidak boleh lebih dari hari ini',
                'metode_bayar.required' => 'Metode bayar harus dipilih',
                'bukti_bayar.mimes' => 'Format file harus JPG, PNG, atau PDF',
                'bukti_bayar.max' => 'Ukuran file maksimal 2MB',
            ]);

            $keuanganPMB = KeuanganPMB::findOrFail($id);

            if ($keuanganPMB->status_bayar === 'sudah_bayar') {
                return response()->json([
                    'success' => false,
                    'message' => 'Biaya ini sudah dibayar sebelumnya!'
                ], 400);
            }

            DB::beginTransaction();

            // Upload bukti bayar jika ada
            if ($request->hasFile('bukti_bayar')) {
                $file = $request->file('bukti_bayar');
                $filename = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '', $file->getClientOriginalName());
                $path = $file->storeAs('bukti-bayar-pmb', $filename, 'public');
                $validated['bukti_bayar'] = $path;
            }

            $validated['status_bayar'] = 'sudah_bayar';
            $keuanganPMB->update($validated);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Pembayaran berhasil dikonfirmasi!'
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error konfirmasi bayar: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal konfirmasi pembayaran: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Bebaskan pembayaran
     */
    public function bebaskan($id)
    {
        try {
            $keuanganPMB = KeuanganPMB::findOrFail($id);

            if ($keuanganPMB->status_bayar !== 'belum_bayar') {
                return response()->json([
                    'success' => false,
                    'message' => 'Biaya ini sudah diproses sebelumnya!'
                ], 400);
            }

            $keuanganPMB->update([
                'status_bayar' => 'dibebaskan',
                'tanggal_bayar' => now(),
                'keterangan' => ($keuanganPMB->keterangan ?? '') . ' - Dibebaskan oleh admin pada ' . now()->format('d-m-Y H:i'),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Biaya berhasil dibebaskan!'
            ]);

        } catch (\Exception $e) {
            Log::error('Error bebaskan: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal membebaskan biaya: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Batalkan pembayaran
     */
    public function batalkan($id)
    {
        try {
            $keuanganPMB = KeuanganPMB::findOrFail($id);

            if ($keuanganPMB->status_bayar === 'belum_bayar') {
                return response()->json([
                    'success' => false,
                    'message' => 'Biaya ini belum dibayar!'
                ], 400);
            }

            DB::beginTransaction();

            // Hapus bukti bayar jika ada
            if ($keuanganPMB->bukti_bayar) {
                Storage::disk('public')->delete($keuanganPMB->bukti_bayar);
            }

            $keuanganPMB->update([
                'status_bayar' => 'belum_bayar',
                'tanggal_bayar' => null,
                'metode_bayar' => null,
                'bukti_bayar' => null,
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Pembayaran berhasil dibatalkan!'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error batalkan: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal membatalkan pembayaran: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Tambah biaya manual
     */
    public function tambahBiaya(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'jenis_biaya' => 'required|in:formulir,ujian,daftar_ulang',
                'nominal' => 'required|numeric|min:1',
                'keterangan' => 'nullable|string',
            ], [
                'jenis_biaya.required' => 'Jenis biaya harus dipilih',
                'nominal.required' => 'Nominal harus diisi',
                'nominal.min' => 'Nominal harus lebih dari 0',
            ]);

            $calonMahasiswa = CalonMahasiswa::findOrFail($id);

            $validated['calon_mahasiswa_id'] = $calonMahasiswa->id;
            $validated['status_bayar'] = 'belum_bayar';

            KeuanganPMB::create($validated);

            return response()->json([
                'success' => true,
                'message' => 'Biaya tambahan berhasil ditambahkan!'
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error tambah biaya: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan biaya: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Hapus item biaya
     */
    public function destroy($id)
    {
        try {
            $keuanganPMB = KeuanganPMB::findOrFail($id);

            DB::beginTransaction();

            // Hapus bukti bayar jika ada
            if ($keuanganPMB->bukti_bayar) {
                Storage::disk('public')->delete($keuanganPMB->bukti_bayar);
            }

            $keuanganPMB->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Item biaya berhasil dihapus!'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error hapus biaya: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus biaya: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Export PDF dengan QR Code
     */
    public function exportPDF($id)
    {
        try {
            $calonMahasiswa = CalonMahasiswa::with(['prodi.fakultas', 'keuanganPMB'])
                ->findOrFail($id);

            $keuangan = $calonMahasiswa->keuanganPMB;

            // Generate QR Code untuk verifikasi
            $verifyCode = md5($calonMahasiswa->id . $calonMahasiswa->no_pendaftaran . date('Ymd'));
            $qrData = $calonMahasiswa->no_pendaftaran . '|' . $verifyCode;
            
            // Generate QR Code dengan Simple PHP QR Code
            // Jika tidak ada library, gunakan URL API sebagai fallback
            $qrCodeDataUri = 'https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=' . urlencode($qrData);
            
            // Jika ingin base64 langsung, uncomment ini (require library)
            /*
            try {
                $qrCode = new QrCode($qrData);
                $writer = new PngWriter();
                $result = $writer->write($qrCode);
                $qrCodeImage = base64_encode($result->getString());
                $qrCodeDataUri = 'data:image/png;base64,' . $qrCodeImage;
            } catch (\Exception $qrError) {
                // Fallback ke API jika error
                $qrCodeDataUri = 'https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=' . urlencode($qrData);
            }
            */

            $pdf = \PDF::loadView('keuangan-pmb.export-pdf', compact('calonMahasiswa', 'keuangan', 'qrCodeDataUri'));
            
            $filename = 'Keuangan_PMB_' . $calonMahasiswa->no_pendaftaran . '_' . date('Ymd') . '.pdf';
            
            return $pdf->download($filename);

        } catch (\Exception $e) {
            Log::error('Error export PDF: ' . $e->getMessage());
            
            return redirect()
                ->back()
                ->with('error', 'Gagal export PDF: ' . $e->getMessage());
        }
    }

    /**
     * Helper: Get jenis biaya nama
     */
    private function getJenisBiayaNama($jenis)
    {
        $namaMap = [
            'formulir' => 'Biaya Formulir',
            'ujian' => 'Biaya Seleksi/Ujian',
            'daftar_ulang' => 'Biaya Daftar Ulang'
        ];

        return $namaMap[$jenis] ?? ucfirst($jenis);
    }

    /**
     * Get biaya berdasarkan jalur masuk
     */
    private function getBiayaByJalur(string $jalur): array
    {
        $biayaList = [];

        switch ($jalur) {
            case 'reguler':
                $biayaList = [
                    [
                        'jenis' => 'formulir',
                        'nominal' => 200000,
                        'keterangan' => 'Biaya Formulir - Jalur Reguler'
                    ],
                    [
                        'jenis' => 'ujian',
                        'nominal' => 300000,
                        'keterangan' => 'Biaya Seleksi/Ujian - Jalur Reguler'
                    ],
                    [
                        'jenis' => 'daftar_ulang',
                        'nominal' => 5000000,
                        'keterangan' => 'Biaya Daftar Ulang Awal - Jalur Reguler'
                    ],
                ];
                break;

            case 'prestasi':
                $biayaList = [
                    [
                        'jenis' => 'formulir',
                        'nominal' => 200000,
                        'keterangan' => 'Biaya Formulir - Jalur Prestasi'
                    ],
                    [
                        'jenis' => 'daftar_ulang',
                        'nominal' => 4000000,
                        'keterangan' => 'Biaya Daftar Ulang Awal - Jalur Prestasi (Diskon 20%)'
                    ],
                ];
                break;

            case 'beasiswa':
                $biayaList = [
                    [
                        'jenis' => 'formulir',
                        'nominal' => 200000,
                        'keterangan' => 'Biaya Formulir - Jalur Beasiswa'
                    ],
                ];
                break;

            case 'pindahan':
                $biayaList = [
                    [
                        'jenis' => 'formulir',
                        'nominal' => 200000,
                        'keterangan' => 'Biaya Formulir - Jalur Pindahan'
                    ],
                    [
                        'jenis' => 'ujian',
                        'nominal' => 300000,
                        'keterangan' => 'Biaya Seleksi/Ujian - Jalur Pindahan (Opsional)'
                    ],
                    [
                        'jenis' => 'daftar_ulang',
                        'nominal' => 5000000,
                        'keterangan' => 'Biaya Daftar Ulang Awal - Jalur Pindahan'
                    ],
                ];
                break;
        }

        return $biayaList;
    }
}