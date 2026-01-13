<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Mahasiswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class MahasiswaController extends Controller
{
    /**
     * GET /api/mahasiswa
     * List semua mahasiswa
     */
    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 10);
        $search = $request->get('search');
        $prodiId = $request->get('prodi_id');

        $query = Mahasiswa::with(['prodi.fakultas']);

        // Filter pencarian
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('nim', 'like', "%{$search}%")
                  ->orWhere('nama', 'like', "%{$search}%");
            });
        }

        // Filter prodi
        if ($prodiId) {
            $query->where('prodi_id', $prodiId);
        }

        $mahasiswa = $query->paginate($perPage);

        // Transform data
        $data = $mahasiswa->map(function($item) {
            return [
                'id' => $item->id,
                'nim' => $item->nim,
                'nama' => $item->nama,
                'angkatan' => $item->angkatan,
                'foto' => $item->foto ? asset('storage/' . $item->foto) : null,
                'prodi' => [
                    'id' => $item->prodi->id ?? null,
                    'nama' => $item->prodi->nama_prodi ?? null,
                    'fakultas' => $item->prodi->fakultas->nama_fakultas ?? null
                ]
            ];
        });

        return response()->json([
            'success' => true,
            'message' => 'Data mahasiswa berhasil diambil',
            'data' => $data,
            'meta' => [
                'current_page' => $mahasiswa->currentPage(),
                'per_page' => $mahasiswa->perPage(),
                'total' => $mahasiswa->total(),
                'last_page' => $mahasiswa->lastPage()
            ]
        ], 200);
    }

    /**
     * POST /api/mahasiswa
     * Tambah mahasiswa baru
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nim' => 'required|unique:mahasiswa',
            'nama' => 'required|string|max:255',
            'angkatan' => 'required|numeric|digits:4',
            'prodi_id' => 'required|exists:prodis,id',
            'foto' => 'nullable|image|mimes:jpg,jpeg,png|max:2048'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $request->all();

        // Upload foto jika ada
        if ($request->hasFile('foto')) {
            $data['foto'] = $request->file('foto')->store('mahasiswa', 'public');
        }

        $mahasiswa = Mahasiswa::create($data);
        $mahasiswa->load('prodi.fakultas');

        return response()->json([
            'success' => true,
            'message' => 'Mahasiswa berhasil ditambahkan',
            'data' => [
                'id' => $mahasiswa->id,
                'nim' => $mahasiswa->nim,
                'nama' => $mahasiswa->nama,
                'angkatan' => $mahasiswa->angkatan,
                'foto' => $mahasiswa->foto ? asset('storage/' . $mahasiswa->foto) : null,
                'prodi' => [
                    'id' => $mahasiswa->prodi->id,
                    'nama' => $mahasiswa->prodi->nama_prodi,
                    'fakultas' => $mahasiswa->prodi->fakultas->nama_fakultas
                ]
            ]
        ], 201);
    }

    /**
     * GET /api/mahasiswa/{id}
     * Detail mahasiswa
     */
    public function show($id)
    {
        $mahasiswa = Mahasiswa::with('prodi.fakultas')->find($id);

        if (!$mahasiswa) {
            return response()->json([
                'success' => false,
                'message' => 'Mahasiswa tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Detail mahasiswa',
            'data' => [
                'id' => $mahasiswa->id,
                'nim' => $mahasiswa->nim,
                'nama' => $mahasiswa->nama,
                'angkatan' => $mahasiswa->angkatan,
                'foto' => $mahasiswa->foto ? asset('storage/' . $mahasiswa->foto) : null,
                'prodi' => [
                    'id' => $mahasiswa->prodi->id,
                    'nama' => $mahasiswa->prodi->nama_prodi,
                    'fakultas' => $mahasiswa->prodi->fakultas->nama_fakultas
                ]
            ]
        ], 200);
    }

    /**
     * PUT/PATCH /api/mahasiswa/{id}
     * Update mahasiswa
     */
    public function update(Request $request, $id)
    {
        $mahasiswa = Mahasiswa::find($id);

        if (!$mahasiswa) {
            return response()->json([
                'success' => false,
                'message' => 'Mahasiswa tidak ditemukan'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'nim' => 'required|unique:mahasiswa,nim,' . $id,
            'nama' => 'required|string|max:255',
            'angkatan' => 'required|numeric|digits:4',
            'prodi_id' => 'required|exists:prodis,id',
            'foto' => 'nullable|image|mimes:jpg,jpeg,png|max:2048'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $request->all();

        // Upload foto baru jika ada
        if ($request->hasFile('foto')) {
            // Hapus foto lama
            if ($mahasiswa->foto) {
                Storage::disk('public')->delete($mahasiswa->foto);
            }
            $data['foto'] = $request->file('foto')->store('mahasiswa', 'public');
        }

        $mahasiswa->update($data);
        $mahasiswa->load('prodi.fakultas');

        return response()->json([
            'success' => true,
            'message' => 'Mahasiswa berhasil diupdate',
            'data' => [
                'id' => $mahasiswa->id,
                'nim' => $mahasiswa->nim,
                'nama' => $mahasiswa->nama,
                'angkatan' => $mahasiswa->angkatan,
                'foto' => $mahasiswa->foto ? asset('storage/' . $mahasiswa->foto) : null,
                'prodi' => [
                    'id' => $mahasiswa->prodi->id,
                    'nama' => $mahasiswa->prodi->nama_prodi,
                    'fakultas' => $mahasiswa->prodi->fakultas->nama_fakultas
                ]
            ]
        ], 200);
    }

    /**
     * DELETE /api/mahasiswa/{id}
     * Hapus mahasiswa
     */
    public function destroy($id)
    {
        $mahasiswa = Mahasiswa::find($id);

        if (!$mahasiswa) {
            return response()->json([
                'success' => false,
                'message' => 'Mahasiswa tidak ditemukan'
            ], 404);
        }

        // Hapus foto jika ada
        if ($mahasiswa->foto) {
            Storage::disk('public')->delete($mahasiswa->foto);
        }

        $mahasiswa->delete();

        return response()->json([
            'success' => true,
            'message' => 'Mahasiswa berhasil dihapus'
        ], 200);
    }
}