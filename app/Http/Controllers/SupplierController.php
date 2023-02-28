<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUpdateSupplierRequest;
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
                $data = Supplier::orderBy('updated_at', 'desc')->get();
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

    public function store(StoreUpdateSupplierRequest $request)
    {
        try {
            Supplier::create([
                'nama' => $request->nama,
                'alamat' => $request->alamat,
                'telepon' => $request->telepon,
            ]);

            return redirect()->route('supplier')->with(
                'success',
                'Berhasil Tambah Supplier'
            );
        } catch (\Throwable $th) {
            return redirect('/')->withErrors([
                'error' => 'Terdapat Kesalahan'
            ]);
        }
    }

    public function edit($id)
    {
        $supplier = Supplier::find($id);
        return view('supplier.edit')->with([
            'title' => 'EDIT SUPPLIER',
            'supplier' => $supplier,
            'masterdata_toogle' => 1
        ]);
    }

    public function update(StoreUpdateSupplierRequest $request, $id)
    {
        try {
            $supplier = Supplier::find($id);
            $supplier->update([
                'nama' => $request->nama,
                'alamat' => $request->alamat,
                'telepon' => $request->telepon,
            ]);

            return redirect()->route('supplier')->with(
                'success',
                'Berhasil Perbarui Supplier'
            );
        } catch (\Throwable $th) {
            return redirect('/')->withErrors([
                'error' => 'Terdapat Kesalahan'
            ]);
        }
    }

    public function destroy($id)
    {
        try {
            $supplier = Supplier::find($id);
            $supplier->delete();
            return redirect()->route('supplier')->with(
                'success',
                'Berhasil Hapus Supplier'
            );
        } catch (\Throwable $th) {
            return redirect('/')->withErrors([
                'error' => 'Terdapat Kesalahan'
            ]);
        }
    }
}
