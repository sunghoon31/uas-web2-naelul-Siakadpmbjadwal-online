<?php

namespace App\Http\Controllers;

use App\Models\Fakultas;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use App\Exports\FakultasExport;
use App\Imports\FakultasImport;
use Maatwebsite\Excel\Facades\Excel;

class FakultasController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = Fakultas::withCount('prodis');

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('jumlah_prodi', fn($row) => $row->prodis_count . ' Prodi')
                ->addColumn('aksi', function ($row) {
                    return '
                        <button class="btn btn-warning btn-sm btn-edit-fakultas" data-id="'.$row->id.'">Edit</button>
                        <button class="btn btn-danger btn-sm btn-delete-fakultas" data-id="'.$row->id.'">Hapus</button>
                    ';
                })
                ->rawColumns(['aksi'])
                ->make(true);
        }

        return view('akademik.index');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_fakultas' => 'required|unique:fakultas,nama_fakultas'
        ]);

        Fakultas::create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Fakultas berhasil ditambahkan!'
        ]);
    }

    public function edit(Fakultas $fakultas)
    {
        return response()->json($fakultas);
    }

    public function update(Request $request, Fakultas $fakultas)
    {
        $request->validate([
            'nama_fakultas' => 'required|unique:fakultas,nama_fakultas,'.$fakultas->id
        ]);

        $fakultas->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Fakultas berhasil diupdate!'
        ]);
    }

    public function destroy(Fakultas $fakultas)
    {
        if ($fakultas->prodis()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Fakultas tidak bisa dihapus karena masih memiliki Program Studi!'
            ], 422);
        }

        $fakultas->delete();

        return response()->json([
            'success' => true,
            'message' => 'Fakultas berhasil dihapus!'
        ]);
    }

    public function export()
    {
        return Excel::download(new FakultasExport, 'fakultas_' . date('YmdHis') . '.xlsx');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:5120'
        ]);

        try {
            Excel::import(new FakultasImport, $request->file('file'));

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