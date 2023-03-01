<?php

namespace App\Http\Controllers;

use App\Models\Kategori;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class KategoriControler extends Controller
{
    public function index()
    {
        return view('pages.kategori.index')->with([
            'title' => 'KATEGORI',
            'masterdata_toogle' => 1
        ]);
    }

    public function datatable(Request $request)
    {
        try {
            if ($request->ajax()) {
                $data = Kategori::orderBy('updated_at', 'desc')->get();
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
        return view('pages.kategori.create')->with([
            'title' => 'TAMBAH KATEGORI',
            'masterdata_toogle' => 1
        ]);
    }

    public function store(Request $request)
    {
        try {
            Kategori::create([
                'nama' => $request->nama,
                'deskripsi' => $request->deskripsi,
            ]);

            return redirect()->route('kategori')->with(
                'success',
                'Berhasil Tambah Kategori'
            );
        } catch (\Throwable $th) {
            return redirect('/')->withErrors([
                'error' => 'Terdapat Kesalahan'
            ]);
        }
    }

    public function edit($id)
    {
        $kategori = Kategori::find($id);
        return view('pages.kategori.edit')->with([
            'title' => 'EDIT KATEGORI',
            'kategori' => $kategori,
            'masterdata_toogle' => 1
        ]);
    }

    public function update(Request $request, $id)
    {
        try {
            $kategori = Kategori::find($id);
            $kategori->update([
                'nama' => $request->nama,
                'deskripsi' => $request->deskripsi,
            ]);

            return redirect()->route('kategori')->with(
                'success',
                'Berhasil Perbarui Kategori'
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
            $kategori = Kategori::find($id);
            $kategori->delete();
            return redirect()->route('kategori')->with(
                'success',
                'Berhasil Hapus Kategori'
            );
        } catch (\Throwable $th) {
            return redirect('/')->withErrors([
                'error' => 'Terdapat Kesalahan'
            ]);
        }
    }
}
