<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Dosen;
use App\Models\Prodi;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\DataTables;
use App\Exports\DosenExport;
use App\Imports\DosenImport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;

class DosenController extends Controller
{
    public function index(Request $request)
    {
        // 🔥 AJAX REQUEST DARI DATATABLES
        if ($request->ajax()) {
            $query = Dosen::with('prodi.fakultas');

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('foto', function ($row) {
                    if ($row->foto) {
                        return '<img src="'.asset('storage/'.$row->foto).'" width="60">';
                    }
                    return '-';
                })
                ->addColumn('prodi', function($row) {
                    if ($row->prodi) {
                        return $row->prodi->nama_prodi . '<br><small class="text-muted">' . $row->prodi->fakultas->nama_fakultas . '</small>';
                    }
                    return '-';
                })
                ->addColumn('aksi', function ($row) {
                    return '
                        <button class="btn btn-info btn-sm btn-show" data-id="'.$row->id.'">Lihat</button>
                        <button class="btn btn-warning btn-sm btn-edit" data-id="'.$row->id.'">Edit</button>
                        <button class="btn btn-danger btn-sm btn-delete" data-id="'.$row->id.'">Hapus</button>
                        <a href="'.route('dosen.pdf', $row->id).'" class="btn btn-secondary btn-sm" target="_blank">PDF</a>
                    ';
                })
                ->rawColumns(['foto','prodi','aksi'])
                ->make(true);
        }

        return view('dosen.index');
    }

    public function create()
    {
        $prodis = Prodi::with('fakultas')->get();
        return view('dosen.partials.form', compact('prodis'))
            ->with('dosen', new Dosen())
            ->with('action', route('dosen.store'))
            ->with('method', 'POST');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nidn'     => 'required|unique:dosen,nidn',
            'nama'     => 'required',
            'email'    => 'required|email|unique:dosen,email',
            'no_hp'    => 'required',
            'prodi_id' => 'nullable|exists:prodis,id',
            'foto'     => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        if ($request->hasFile('foto')) {
            $data['foto'] = $request->file('foto')->store('dosen', 'public');
        }

        Dosen::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Data dosen berhasil ditambahkan!'
        ]);
    }

    public function show(Dosen $dosen)
    {
        return view('dosen.partials.show', compact('dosen'));
    }

    public function edit(Dosen $dosen)
    {
        $prodis = Prodi::with('fakultas')->get();
        return view('dosen.partials.form', compact('dosen', 'prodis'))
            ->with('action', route('dosen.update', $dosen->id))
            ->with('method', 'PUT');
    }

    public function update(Request $request, Dosen $dosen)
    {
        $data = $request->validate([
            'nidn'     => 'required|unique:dosen,nidn,' . $dosen->id,
            'nama'     => 'required',
            'email'    => 'required|email|unique:dosen,email,' . $dosen->id,
            'no_hp'    => 'required',
            'prodi_id' => 'nullable|exists:prodis,id',
            'foto'     => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        if ($request->hasFile('foto')) {
            if ($dosen->foto) {
                Storage::disk('public')->delete($dosen->foto);
            }
            $data['foto'] = $request->file('foto')->store('dosen', 'public');
        }

        $dosen->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Data dosen berhasil diupdate!'
        ]);
    }

    public function destroy(Dosen $dosen)
    {
        if ($dosen->foto) {
            Storage::disk('public')->delete($dosen->foto);
        }

        $dosen->delete();

        return response()->json([
            'success' => true,
            'message' => 'Data dosen berhasil dihapus!'
        ]);
    }

    // ========== EXPORT EXCEL ==========
    public function export()
    {
        return Excel::download(new DosenExport, 'dosen_' . date('YmdHis') . '.xlsx');
    }

    // ========== IMPORT EXCEL ==========
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:5120'
        ]);

        try {
            Excel::import(new DosenImport, $request->file('file'));

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

    // ========== GENERATE PDF ==========
    public function generatePDF(Dosen $dosen)
    {
        // Generate QR Code
        $writer = new PngWriter();
        $qrCode = new QrCode(route('dosen.show', $dosen->id));
        $result = $writer->write($qrCode);
        
        // Convert ke base64
        $qrCodeBase64 = base64_encode($result->getString());

        // Generate PDF
        $pdf = Pdf::loadView('dosen.pdf.kartu', compact('dosen', 'qrCodeBase64'));
        
        return $pdf->stream('Kartu_Dosen_' . $dosen->nidn . '.pdf');
    }
}