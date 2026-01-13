<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Dosen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class DosenController extends Controller
{
    /**
     * GET /api/dosen
     * List semua dosen
     */
    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 10);
        $search = $request->get('search');
        $prodiId = $request->get('prodi_id');

        $query = Dosen::with(['prodi.fakultas']);

        // Filter pencarian
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('nidn', 'like', "%{$search}%")
                  ->orWhere('nama', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Filter prodi
        if ($prodiId) {
            $query->where('prodi_id', $prodiId);
        }

        $dosen = $query->paginate($perPage);

        // Transform data
        $data = $dosen->map(function($item) {
            return [
                'id' => $item->id,
                'nidn' => $item->nidn,
                'nama' => $item->nama,
                'email' => $item->email,
                'no_hp' => $item->no_hp,
                'foto' => $item->foto ? asset('storage/' . $item->foto) : null,
                'prodi' => $item->prodi ? [
                    'id' => $item->prodi->id,
                    'nama' => $item->prodi->nama_prodi,
                    'fakultas' => $item->prodi->fakultas->nama_fakultas ?? null
                ] : null
            ];
        });

        return response()->json([
            'success' => true,
            'message' => 'Data dosen berhasil diambil',
            'data' => $data,
            'meta' => [
                'current_page' => $dosen->currentPage(),
                'per_page' => $dosen->perPage(),
                'total' => $dosen->total(),
                'last_page' => $dosen->lastPage()
            ]
        ], 200);
    }

    /**
     * POST /api/dosen
     * Tambah dosen baru
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nidn' => 'required|unique:dosen',
            'nama' => 'required|string|max:255',
            'email' => 'required|email|unique:dosen',
            'no_hp' => 'required|string|max:20',
            'prodi_id' => 'nullable|exists:prodis,id',
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
            $data['foto'] = $request->file('foto')->store('dosen', 'public');
        }

        $dosen = Dosen::create($data);
        $dosen->load('prodi.fakultas');

        return response()->json([
            'success' => true,
            'message' => 'Dosen berhasil ditambahkan',
            'data' => [
                'id' => $dosen->id,
                'nidn' => $dosen->nidn,
                'nama' => $dosen->nama,
                'email' => $dosen->email,
                'no_hp' => $dosen->no_hp,
                'foto' => $dosen->foto ? asset('storage/' . $dosen->foto) : null,
                'prodi' => $dosen->prodi ? [
                    'id' => $dosen->prodi->id,
                    'nama' => $dosen->prodi->nama_prodi,
                    'fakultas' => $dosen->prodi->fakultas->nama_fakultas
                ] : null
            ]
        ], 201);
    }

    /**
     * GET /api/dosen/{id}
     * Detail dosen
     */
    public function show($id)
    {
        $dosen = Dosen::with('prodi.fakultas')->find($id);

        if (!$dosen) {
            return response()->json([
                'success' => false,
                'message' => 'Dosen tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Detail dosen',
            'data' => [
                'id' => $dosen->id,
                'nidn' => $dosen->nidn,
                'nama' => $dosen->nama,
                'email' => $dosen->email,
                'no_hp' => $dosen->no_hp,
                'foto' => $dosen->foto ? asset('storage/' . $dosen->foto) : null,
                'prodi' => $dosen->prodi ? [
                    'id' => $dosen->prodi->id,
                    'nama' => $dosen->prodi->nama_prodi,
                    'fakultas' => $dosen->prodi->fakultas->nama_fakultas
                ] : null
            ]
        ], 200);
    }

    /**
     * PUT/PATCH /api/dosen/{id}
     * Update dosen
     */
    public function update(Request $request, $id)
    {
        $dosen = Dosen::find($id);

        if (!$dosen) {
            return response()->json([
                'success' => false,
                'message' => 'Dosen tidak ditemukan'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'nidn' => 'required|unique:dosen,nidn,' . $id,
            'nama' => 'required|string|max:255',
            'email' => 'required|email|unique:dosen,email,' . $id,
            'no_hp' => 'required|string|max:20',
            'prodi_id' => 'nullable|exists:prodis,id',
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
            if ($dosen->foto) {
                Storage::disk('public')->delete($dosen->foto);
            }
            $data['foto'] = $request->file('foto')->store('dosen', 'public');
        }

        $dosen->update($data);
        $dosen->load('prodi.fakultas');

        return response()->json([
            'success' => true,
            'message' => 'Dosen berhasil diupdate',
            'data' => [
                'id' => $dosen->id,
                'nidn' => $dosen->nidn,
                'nama' => $dosen->nama,
                'email' => $dosen->email,
                'no_hp' => $dosen->no_hp,
                'foto' => $dosen->foto ? asset('storage/' . $dosen->foto) : null,
                'prodi' => $dosen->prodi ? [
                    'id' => $dosen->prodi->id,
                    'nama' => $dosen->prodi->nama_prodi,
                    'fakultas' => $dosen->prodi->fakultas->nama_fakultas
                ] : null
            ]
        ], 200);
    }

    /**
     * DELETE /api/dosen/{id}
     * Hapus dosen
     */
    public function destroy($id)
    {
        $dosen = Dosen::find($id);

        if (!$dosen) {
            return response()->json([
                'success' => false,
                'message' => 'Dosen tidak ditemukan'
            ], 404);
        }

        // Hapus foto jika ada
        if ($dosen->foto) {
            Storage::disk('public')->delete($dosen->foto);
        }

        $dosen->delete();

        return response()->json([
            'success' => true,
            'message' => 'Dosen berhasil dihapus'
        ], 200);
    }
}