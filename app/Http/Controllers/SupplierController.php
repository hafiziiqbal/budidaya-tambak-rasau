<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class SupplierController extends Controller
{
    public function index()
    {
        return view('supplier.index')->with([
            'title' => 'SUPPLIER',
            'masterdata_toogle' => 1
        ]);
    }

    public function datatable(Request $request)
    {
        try {
            if ($request->ajax()) {
                $data = Supplier::all();
                return DataTables::of($data)->addIndexColumn()->make(true);
            }
        } catch (\Throwable $th) {
            return redirect('/')->withErrors([
                'error' => 'Terdapat Kesalahan'
            ]);
        }
    }

    public function create()
    {
        return view('supplier.create')->with([
            'title' => 'TAMBAH SUPPLIER',
            'masterdata_toogle' => 1
        ]);
    }

    public function store(Request $request)
    {
        Supplier::create([
            'nama' => $request->nama,
            'alamat' => $request->alamat,
            'telepon' => $request->telepon,
        ]);

        return redirect()->route('supplier')->with(
            'success',
            'Berhasil Tambah Supplier'
        );
    }
}
