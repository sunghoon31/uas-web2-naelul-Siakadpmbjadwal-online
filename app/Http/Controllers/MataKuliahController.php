<?php

namespace App\Http\Controllers;

use App\Models\MataKuliah;
use App\Models\Prodi;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use App\Exports\MataKuliahExport;
use App\Imports\MataKuliahImport;
use Maatwebsite\Excel\Facades\Excel;

class MataKuliahController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = MataKuliah::with('prodi.fakultas');

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('prodi_nama', fn($row) => $row->prodi->nama_prodi ?? '-')
                ->addColumn('fakultas_nama', fn($row) => $row->prodi->fakultas->nama_fakultas ?? '-')
                ->addColumn('aksi', function ($row) {
                    return '
                        <button class="btn btn-warning btn-sm btn-edit" data-id="'.$row->id.'">Edit</button>
                        <button class="btn btn-danger btn-sm btn-delete" data-id="'.$row->id.'">Hapus</button>
                    ';
                })
                ->rawColumns(['aksi'])
                ->make(true);
        }

        return view('mata-kuliah.index');
    }

    public function create()
    {
        $prodis = Prodi::with('fakultas')->get();
        return response()->json($prodis);
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode_mk' => 'required|unique:mata_kuliahs,kode_mk',
            'nama_mk' => 'required',
            'sks' => 'required|integer|min:1|max:6',
            'semester' => 'required|integer|min:1|max:8',
            'prodi_id' => 'required|exists:prodis,id'
        ]);

        MataKuliah::create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Mata kuliah berhasil ditambahkan!'
        ]);
    }

    public function edit(MataKuliah $mataKuliah)
    {
        $mataKuliah->load('prodi');
        return response()->json($mataKuliah);
    }

    public function update(Request $request, MataKuliah $mataKuliah)
    {
        $request->validate([
            'kode_mk' => 'required|unique:mata_kuliahs,kode_mk,'.$mataKuliah->id,
            'nama_mk' => 'required',
            'sks' => 'required|integer|min:1|max:6',
            'semester' => 'required|integer|min:1|max:8',
            'prodi_id' => 'required|exists:prodis,id'
        ]);

        $mataKuliah->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Mata kuliah berhasil diupdate!'
        ]);
    }

    public function destroy(MataKuliah $mataKuliah)
    {
        if ($mataKuliah->jadwals()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Mata kuliah tidak bisa dihapus karena sudah dijadwalkan!'
            ], 422);
        }

        $mataKuliah->delete();

        return response()->json([
            'success' => true,
            'message' => 'Mata kuliah berhasil dihapus!'
        ]);
    }

    public function export()
    {
        return Excel::download(new MataKuliahExport, 'mata_kuliah_'.date('YmdHis').'.xlsx');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:5120'
        ]);

        try {
            Excel::import(new MataKuliahImport, $request->file('file'));

            return response()->json([
                'success' => true,
                'message' => 'Data berhasil diimport!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Import gagal: ' . $e->getMessage()
            ], 500);
        }
    }
}