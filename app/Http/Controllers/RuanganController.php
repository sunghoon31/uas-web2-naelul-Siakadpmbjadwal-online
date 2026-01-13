<?php

namespace App\Http\Controllers;

use App\Models\Ruangan;
use App\Models\Fakultas;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use App\Exports\RuanganExport;
use App\Imports\RuanganImport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class RuanganController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = Ruangan::with('fakultas');

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('fakultas_nama', function ($row) {
                    return $row->fakultas ? $row->fakultas->nama_fakultas : '-';
                })
                ->addColumn('jenis_badge', function ($row) {
                    $badges = [
                        'Kelas' => 'primary',
                        'Lab' => 'success',
                        'Studio' => 'warning'
                    ];
                    $color = $badges[$row->jenis] ?? 'secondary';
                    return '<span class="badge bg-'.$color.'">'.$row->jenis.'</span>';
                })
                ->addColumn('kapasitas_info', function ($row) {
                    return '<span class="badge bg-info">'.$row->kapasitas.' Orang</span>';
                })
                ->addColumn('aksi', function ($row) {
                    return '
                        <div class="btn-group" role="group">
                            <button class="btn btn-sm btn-warning btn-edit" data-id="'.$row->id.'" title="Edit">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-sm btn-danger btn-delete" data-id="'.$row->id.'" title="Hapus">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    ';
                })
                ->rawColumns(['jenis_badge', 'kapasitas_info', 'aksi'])
                ->make(true);
        }

        $fakultas = Fakultas::all();
        return view('ruangan.index', compact('fakultas'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'fakultas_id' => 'required|exists:fakultas,id',
            'kode_ruangan' => 'required|unique:ruangans,kode_ruangan',
            'nama_ruangan' => 'required|max:255',
            'kapasitas' => 'required|integer|min:1|max:500',
            'jenis' => 'required|in:Kelas,Lab,Studio'
        ], [
            'fakultas_id.required' => 'Fakultas harus dipilih!',
            'kode_ruangan.required' => 'Kode ruangan harus diisi!',
            'kode_ruangan.unique' => 'Kode ruangan sudah digunakan!',
            'nama_ruangan.required' => 'Nama ruangan harus diisi!',
            'kapasitas.required' => 'Kapasitas harus diisi!',
            'kapasitas.min' => 'Kapasitas minimal 1 orang!',
            'kapasitas.max' => 'Kapasitas maksimal 500 orang!',
            'jenis.required' => 'Jenis ruangan harus dipilih!'
        ]);

        Ruangan::create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Ruangan berhasil ditambahkan!'
        ]);
    }

    public function edit(Ruangan $ruangan)
    {
        return response()->json($ruangan);
    }

    public function update(Request $request, Ruangan $ruangan)
    {
        $request->validate([
            'fakultas_id' => 'required|exists:fakultas,id',
            'kode_ruangan' => 'required|unique:ruangans,kode_ruangan,'.$ruangan->id,
            'nama_ruangan' => 'required|max:255',
            'kapasitas' => 'required|integer|min:1|max:500',
            'jenis' => 'required|in:Kelas,Lab,Studio'
        ], [
            'fakultas_id.required' => 'Fakultas harus dipilih!',
            'kode_ruangan.required' => 'Kode ruangan harus diisi!',
            'kode_ruangan.unique' => 'Kode ruangan sudah digunakan!',
            'nama_ruangan.required' => 'Nama ruangan harus diisi!',
            'kapasitas.required' => 'Kapasitas harus diisi!',
            'kapasitas.min' => 'Kapasitas minimal 1 orang!',
            'kapasitas.max' => 'Kapasitas maksimal 500 orang!',
            'jenis.required' => 'Jenis ruangan harus dipilih!'
        ]);

        $ruangan->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Ruangan berhasil diupdate!'
        ]);
    }

    public function destroy(Ruangan $ruangan)
    {
        if ($ruangan->jadwals()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Ruangan tidak bisa dihapus karena masih digunakan dalam ' . $ruangan->jadwals()->count() . ' jadwal!'
            ], 422);
        }

        $ruangan->delete();

        return response()->json([
            'success' => true,
            'message' => 'Ruangan berhasil dihapus!'
        ]);
    }

    public function export()
    {
        return Excel::download(new RuanganExport, 'Data_Ruangan_'.date('d-m-Y_His').'.xlsx');
    }

    public function exportPdf()
    {
        $ruangans = Ruangan::with('fakultas')->orderBy('fakultas_id')->orderBy('kode_ruangan')->get();
        
        $pdf = Pdf::loadView('ruangan.pdf', compact('ruangans'))
            ->setPaper('a4', 'landscape');
        
        return $pdf->download('Data_Ruangan_'.date('d-m-Y_His').'.pdf');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:5120'
        ], [
            'file.required' => 'File harus dipilih!',
            'file.mimes' => 'File harus berformat Excel (xlsx, xls, csv)!',
            'file.max' => 'Ukuran file maksimal 5MB!'
        ]);

        try {
            Excel::import(new RuanganImport, $request->file('file'));

            return response()->json([
                'success' => true,
                'message' => 'Data ruangan berhasil diimport!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Import gagal: ' . $e->getMessage()
            ], 500);
        }
    }

    public function downloadTemplate()
    {
        $filePath = public_path('templates/template_ruangan.xlsx');
        
        if (!file_exists($filePath)) {
            return response()->json([
                'success' => false,
                'message' => 'Template tidak ditemukan!'
            ], 404);
        }

        return response()->download($filePath, 'Template_Import_Ruangan.xlsx');
    }
}