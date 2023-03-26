<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProdukRequest;
use App\Models\DetailBeli;
use App\Models\Produk;
use App\Models\Kategori;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class ProdukController extends Controller
{
    public function index()
    {
        $kategori = Kategori::all();
        return view('pages.produk.index')->with([
            'title' => 'PRODUK',
            'kategori' => $kategori,
            'produk_toogle' => 1
        ]);
    }

    public function datatable(Request $request, $id)
    {
        try {
            if ($request->ajax()) {
                $data = Produk::where('id_kategori', $id)->orderBy('updated_at', 'desc')->get();
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
        $kategori = Kategori::all();
        return view('pages.produk.create')->with([
            'title' => 'TAMBAH PRODUK',
            'kategori' => $kategori,
            'produk_toogle' => 1
        ]);
    }

    public function store(ProdukRequest $request)
    {
        try {
            Produk::create([
                'id_kategori' => $request->id_kategori,
                'nama' => $request->nama,
                'quantity' => $request->quantity,
            ]);

            return redirect()->route('produk')->with(
                'success',
                'Berhasil Tambah Produk'
            );
        } catch (\Throwable $th) {
            return redirect('/')->withErrors([
                'error' => 'Terdapat Kesalahan'
            ]);
        }
    }

    public function edit($id)
    {
        $produk = Produk::find($id);
        return view('pages.produk.edit')->with([
            'title' => 'EDIT PRODUK',
            'produk' => $produk,
            'produk_toogle' => 1
        ]);
    }

    public function update(ProdukRequest $request, $id)
    {
        try {
            $produk = Produk::find($id);
            $produk->update([
                'nama' => $request->nama,
                'quantity' => $request->quantity,
            ]);

            return redirect()->route('produk')->with(
                'success',
                'Berhasil Perbarui Produk'
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
            $produk = Produk::find($id);
            $pembelian = DetailBeli::where('id_produk', $id)->first();
            if ($pembelian) {
                return redirect('produk')->withErrors([
                    'error' => 'Data digunakan oleh tabel detail beli'
                ]);
            }
            $produk->delete();
            return redirect()->route('produk')->with(
                'success',
                'Berhasil Hapus Produk'
            );
        } catch (\Throwable $th) {
            return redirect('/')->withErrors([
                'error' => 'Terdapat Kesalahan'
            ]);
        }
    }
}
