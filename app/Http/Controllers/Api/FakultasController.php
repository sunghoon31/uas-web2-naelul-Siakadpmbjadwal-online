<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Fakultas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class FakultasController extends Controller
{
    /**
     * GET /api/fakultas
     * List semua fakultas
     */
    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 10);
        $search = $request->get('search');

        $query = Fakultas::withCount('prodis');

        // Filter pencarian
        if ($search) {
            $query->where('nama_fakultas', 'like', "%{$search}%");
        }

        $fakultas = $query->paginate($perPage);

        // Transform data
        $data = $fakultas->map(function($item) {
            return [
                'id' => $item->id,
                'nama_fakultas' => $item->nama_fakultas,
                'jumlah_prodi' => $item->prodis_count,
                'created_at' => $item->created_at?->format('Y-m-d H:i:s'),
                'updated_at' => $item->updated_at?->format('Y-m-d H:i:s')
            ];
        });

        return response()->json([
            'success' => true,
            'message' => 'Data fakultas berhasil diambil',
            'data' => $data,
            'meta' => [
                'current_page' => $fakultas->currentPage(),
                'per_page' => $fakultas->perPage(),
                'total' => $fakultas->total(),
                'last_page' => $fakultas->lastPage()
            ]
        ], 200);
    }

    /**
     * POST /api/fakultas
     * Tambah fakultas baru
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama_fakultas' => 'required|string|max:255|unique:fakultas'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        $fakultas = Fakultas::create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Fakultas berhasil ditambahkan',
            'data' => [
                'id' => $fakultas->id,
                'nama_fakultas' => $fakultas->nama_fakultas,
                'created_at' => $fakultas->created_at->format('Y-m-d H:i:s')
            ]
        ], 201);
    }

    /**
     * GET /api/fakultas/{id}
     * Detail fakultas beserta prodisnya
     */
    public function show($id)
    {
        $fakultas = Fakultas::with('prodis')->find($id);

        if (!$fakultas) {
            return response()->json([
                'success' => false,
                'message' => 'Fakultas tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Detail fakultas',
            'data' => [
                'id' => $fakultas->id,
                'nama_fakultas' => $fakultas->nama_fakultas,
                'jumlah_prodi' => $fakultas->prodis->count(),
                'prodis' => $fakultas->prodis->map(function($prodi) {
                    return [
                        'id' => $prodi->id,
                        'nama_prodi' => $prodi->nama_prodi
                    ];
                }),
                'created_at' => $fakultas->created_at?->format('Y-m-d H:i:s'),
                'updated_at' => $fakultas->updated_at?->format('Y-m-d H:i:s')
            ]
        ], 200);
    }

    /**
     * PUT/PATCH /api/fakultas/{id}
     * Update fakultas
     */
    public function update(Request $request, $id)
    {
        $fakultas = Fakultas::find($id);

        if (!$fakultas) {
            return response()->json([
                'success' => false,
                'message' => 'Fakultas tidak ditemukan'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'nama_fakultas' => 'required|string|max:255|unique:fakultas,nama_fakultas,' . $id
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        $fakultas->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Fakultas berhasil diupdate',
            'data' => [
                'id' => $fakultas->id,
                'nama_fakultas' => $fakultas->nama_fakultas,
                'updated_at' => $fakultas->updated_at->format('Y-m-d H:i:s')
            ]
        ], 200);
    }

    /**
     * DELETE /api/fakultas/{id}
     * Hapus fakultas
     */
    public function destroy($id)
    {
        $fakultas = Fakultas::find($id);

        if (!$fakultas) {
            return response()->json([
                'success' => false,
                'message' => 'Fakultas tidak ditemukan'
            ], 404);
        }

        // Cek apakah masih ada prodi
        if ($fakultas->prodis()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Fakultas tidak bisa dihapus karena masih memiliki Program Studi'
            ], 422);
        }

        $fakultas->delete();

        return response()->json([
            'success' => true,
            'message' => 'Fakultas berhasil dihapus'
        ], 200);
    }
}