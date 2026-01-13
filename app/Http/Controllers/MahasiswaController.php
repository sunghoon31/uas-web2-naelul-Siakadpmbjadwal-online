<?php

namespace App\Http\Controllers;

use App\Models\Mahasiswa;
use App\Models\Prodi;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Storage;
use App\Exports\MahasiswaExport;
use App\Imports\MahasiswaImport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;

class MahasiswaController extends Controller
{
    public function index(Request $request)
{
    if ($request->ajax()) {
        $query = Mahasiswa::with('prodi.fakultas');

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('foto', function ($row) {
                if ($row->foto) {
                    return '<img src="'.asset('storage/'.$row->foto).'" width="60">';
                }
                return '-';
            })
            ->addColumn('prodi', fn($row) => $row->prodi->nama_prodi ?? '-')
            ->addColumn('fakultas', fn($row) => $row->prodi->fakultas->nama_fakultas ?? '-')
            ->addColumn('aksi', function ($row) {
                return '
                    <button class="btn btn-info btn-sm btn-show" data-id="'.$row->id.'">Lihat</button>
                    <button class="btn btn-warning btn-sm btn-edit" data-id="'.$row->id.'">Edit</button>
                    <button class="btn btn-danger btn-sm btn-delete" data-id="'.$row->id.'">Hapus</button>
                    <a href="'.route('mahasiswa.pdf', $row->id).'" class="btn btn-secondary btn-sm" target="_blank">PDF</a>
                ';
            })
            ->rawColumns(['foto','aksi'])
            ->make(true);
    }

    // ===========================
    // 🔽 TAMBAHAN (WAJIB UNTUK BLADE)
    // ===========================

    $prodis = Prodi::with('fakultas')->get();

    $semesters = range(1, 8); // atau sesuai kebutuhan

    $selectedProdi = $request->get('prodi');        // boleh null
    $selectedSemester = $request->get('semester'); // boleh null

    return view('mahasiswa.index', compact(
        'prodis',
        'semesters',
        'selectedProdi',
        'selectedSemester'
    ));
}


    public function create()
    {
        $prodis = Prodi::with('fakultas')->get();
        return view('mahasiswa.partials.form', compact('prodis'))
            ->with('mahasiswa', new Mahasiswa())
            ->with('action', route('mahasiswa.store'))
            ->with('method', 'POST');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nim' => 'required|unique:mahasiswa',
            'nama' => 'required',
            'angkatan' => 'required|numeric',
            'prodi_id' => 'required',
            'foto' => 'nullable|image|max:2048'
        ]);

        $data = $request->all();

        if ($request->hasFile('foto')) {
            $data['foto'] = $request->file('foto')->store('mahasiswa', 'public');
        }

        Mahasiswa::create($data);

        return response()->json([
            'success' => true, 
            'message' => 'Data berhasil ditambahkan!'
        ]);
    }

    public function show(Mahasiswa $mahasiswa)
    {
        return view('mahasiswa.partials.show', compact('mahasiswa'));
    }

    public function edit(Mahasiswa $mahasiswa)
    {
        $prodis = Prodi::with('fakultas')->get();
        return view('mahasiswa.partials.form', compact('mahasiswa', 'prodis'))
            ->with('action', route('mahasiswa.update', $mahasiswa->id))
            ->with('method', 'PUT');
    }

    public function update(Request $request, Mahasiswa $mahasiswa)
    {
        $request->validate([
            'nim' => 'required|unique:mahasiswa,nim,'.$mahasiswa->id,
            'nama' => 'required',
            'angkatan' => 'required|numeric',
            'prodi_id' => 'required',
            'foto' => 'nullable|image|max:2048'
        ]);

        $data = $request->all();

        if ($request->hasFile('foto')) {
            if ($mahasiswa->foto) {
                Storage::disk('public')->delete($mahasiswa->foto);
            }
            $data['foto'] = $request->file('foto')->store('mahasiswa','public');
        }

        $mahasiswa->update($data);

        return response()->json([
            'success' => true, 
            'message' => 'Data berhasil diupdate!'
        ]);
    }

    public function destroy(Mahasiswa $mahasiswa)
    {
        if ($mahasiswa->foto) {
            Storage::disk('public')->delete($mahasiswa->foto);
        }

        $mahasiswa->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Data berhasil dihapus!'
        ]);
    }

    // ========== EXPORT EXCEL ==========
    public function export()
    {
        return Excel::download(new MahasiswaExport, 'mahasiswa_' . date('YmdHis') . '.xlsx');
    }

    // ========== IMPORT EXCEL ==========
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:5120'
        ]);

        try {
            Excel::import(new MahasiswaImport, $request->file('file'));

            return response()->json([
                'success' => true,
                'message' => 'Data berhasil diimport!'
            ]);
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failures = $e->failures();
            $errors = [];

            foreach ($failures as $failure) {
                $errors[] = "Baris {$failure->row()}: " . implode(', ', $failure->errors());
            }

            return response()->json([
                'success' => false,
                'message' => 'Import gagal!',
                'errors' => $errors
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    // ========== GENERATE PDF KRS ==========
    public function generatePDF(Mahasiswa $mahasiswa)
    {
        // Generate QR Code
        $writer = new PngWriter();
        $qrCode = new QrCode(route('mahasiswa.show', $mahasiswa->id));
        $result = $writer->write($qrCode);
        
        // Convert ke base64
        $qrCodeBase64 = base64_encode($result->getString());

        // Generate PDF
        $pdf = Pdf::loadView('mahasiswa.pdf.krs', compact('mahasiswa', 'qrCodeBase64'));
        
        return $pdf->stream('KRS_' . $mahasiswa->nim . '.pdf');
    }
}