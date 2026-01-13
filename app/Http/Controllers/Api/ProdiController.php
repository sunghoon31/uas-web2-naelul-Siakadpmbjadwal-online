<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Prodi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProdiController extends Controller
{
    /**
     * GET /api/prodi
     * List semua program studi
     */
    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 10);
        $search = $request->get('search');
        $fakultasId = $request->get('fakultas_id');

        $query = Prodi::with('fakultas');

        // Filter pencarian
        if ($search) {
            $query->where('nama_prodi', 'like', "%{$search}%");
        }

        // Filter fakultas
        if ($fakultasId) {
            $query->where('fakultas_id', $fakultasId);
        }

        $prodi = $query->paginate($perPage);

        // Transform data
        $data = $prodi->map(function($item) {
            return [
                'id' => $item->id,
                'nama_prodi' => $item->nama_prodi,
                'fakultas' => [
                    'id' => $item->fakultas->id,
                    'nama' => $item->fakultas->nama_fakultas
                ],
                'created_at' => $item->created_at?->format('Y-m-d H:i:s'),
                'updated_at' => $item->updated_at?->format('Y-m-d H:i:s')
            ];
        });

        return response()->json([
            'success' => true,
            'message' => 'Data program studi berhasil diambil',
            'data' => $data,
            'meta' => [
                'current_page' => $prodi->currentPage(),
                'per_page' => $prodi->perPage(),
                'total' => $prodi->total(),
                'last_page' => $prodi->lastPage()
            ]
        ], 200);
    }

    /**
     * POST /api/prodi
     * Tambah program studi baru
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama_prodi' => 'required|string|max:255|unique:prodis',
            'fakultas_id' => 'required|exists:fakultas,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        $prodi = Prodi::create($request->all());
        $prodi->load('fakultas');

        return response()->json([
            'success' => true,
            'message' => 'Program studi berhasil ditambahkan',
            'data' => [
                'id' => $prodi->id,
                'nama_prodi' => $prodi->nama_prodi,
                'fakultas' => [
                    'id' => $prodi->fakultas->id,
                    'nama' => $prodi->fakultas->nama_fakultas
                ],
                'created_at' => $prodi->created_at->format('Y-m-d H:i:s')
            ]
        ], 201);
    }

    /**
     * GET /api/prodi/{id}
     * Detail program studi
     */
    public function show($id)
    {
        $prodi = Prodi::with('fakultas')->find($id);

        if (!$prodi) {
            return response()->json([
                'success' => false,
                'message' => 'Program studi tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Detail program studi',
            'data' => [
                'id' => $prodi->id,
                'nama_prodi' => $prodi->nama_prodi,
                'fakultas' => [
                    'id' => $prodi->fakultas->id,
                    'nama' => $prodi->fakultas->nama_fakultas
                ],
                'created_at' => $prodi->created_at?->format('Y-m-d H:i:s'),
                'updated_at' => $prodi->updated_at?->format('Y-m-d H:i:s')
            ]
        ], 200);
    }

    /**
     * PUT/PATCH /api/prodi/{id}
     * Update program studi
     */
    public function update(Request $request, $id)
    {
        $prodi = Prodi::find($id);

        if (!$prodi) {
            return response()->json([
                'success' => false,
                'message' => 'Program studi tidak ditemukan'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'nama_prodi' => 'required|string|max:255|unique:prodis,nama_prodi,' . $id,
            'fakultas_id' => 'required|exists:fakultas,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        $prodi->update($request->all());
        $prodi->load('fakultas');

        return response()->json([
            'success' => true,
            'message' => 'Program studi berhasil diupdate',
            'data' => [
                'id' => $prodi->id,
                'nama_prodi' => $prodi->nama_prodi,
                'fakultas' => [
                    'id' => $prodi->fakultas->id,
                    'nama' => $prodi->fakultas->nama_fakultas
                ],
                'updated_at' => $prodi->updated_at->format('Y-m-d H:i:s')
            ]
        ], 200);
    }

    /**
     * DELETE /api/prodi/{id}
     * Hapus program studi
     */
    public function destroy($id)
    {
        $prodi = Prodi::find($id);

        if (!$prodi) {
            return response()->json([
                'success' => false,
                'message' => 'Program studi tidak ditemukan'
            ], 404);
        }

        $prodi->delete();

        return response()->json([
            'success' => true,
            'message' => 'Program studi berhasil dihapus'
        ], 200);
    }
}