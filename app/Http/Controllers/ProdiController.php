<?php

namespace App\Http\Controllers;

use App\Models\Prodi;
use App\Models\Fakultas;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use App\Exports\ProdiExport;
use App\Imports\ProdiImport;
use Maatwebsite\Excel\Facades\Excel;

class ProdiController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = Prodi::with('fakultas');

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('fakultas_nama', fn($row) => $row->fakultas->nama_fakultas ?? '-')
                ->addColumn('aksi', function ($row) {
                    return '
                        <button class="btn btn-warning btn-sm btn-edit-prodi" data-id="'.$row->id.'">Edit</button>
                        <button class="btn btn-danger btn-sm btn-delete-prodi" data-id="'.$row->id.'">Hapus</button>
                    ';
                })
                ->rawColumns(['aksi'])
                ->make(true);
        }

        return view('akademik.index');
    }

    public function create()
    {
        $fakultas = Fakultas::all();
        return response()->json($fakultas);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_prodi' => 'required|unique:prodis,nama_prodi',
            'fakultas_id' => 'required|exists:fakultas,id'
        ]);

        Prodi::create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Program Studi berhasil ditambahkan!'
        ]);
    }

    public function edit(Prodi $prodi)
    {
        $prodi->load('fakultas');
        return response()->json($prodi);
    }

    public function update(Request $request, Prodi $prodi)
    {
        $request->validate([
            'nama_prodi' => 'required|unique:prodis,nama_prodi,'.$prodi->id,
            'fakultas_id' => 'required|exists:fakultas,id'
        ]);

        $prodi->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Program Studi berhasil diupdate!'
        ]);
    }

    public function destroy(Prodi $prodi)
    {
        $prodi->delete();

        return response()->json([
            'success' => true,
            'message' => 'Program Studi berhasil dihapus!'
        ]);
    }

    public function export()
    {
        return Excel::download(new ProdiExport, 'prodi_' . date('YmdHis') . '.xlsx');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:5120'
        ]);

        try {
            Excel::import(new ProdiImport, $request->file('file'));

            return response()->json([
                'success' => true,
                'message' => 'Data berhasil diimport!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
}