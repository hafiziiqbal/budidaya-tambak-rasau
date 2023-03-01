<?php

namespace App\Http\Controllers;

use App\Models\DetailBeli;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class PembelianController extends Controller
{
    public function index()
    {
        return view('pages.pembelian.index')->with([
            'title' => 'PEMBELIAN',
            'transaksi_toogle' => 1
        ]);
    }

    public function datatable(Request $request)
    {
        try {
            if ($request->ajax()) {
                $data = DetailBeli::select(['id', 'id_header_beli', 'id_produk', 'quantity', 'updated_at'])->with(['produk' => function ($query) {
                    $query->select('id', 'nama');
                }, 'header_beli' => function ($query) {
                    $query->select('id', 'tgl_beli', 'id_supplier')->with(['supplier' => function ($query) {
                        $query->select('id', 'nama');
                    }]);
                }])->orderBy('updated_at', 'desc')->get();
                return DataTables::of($data)->addIndexColumn()->make(true);
            }
        } catch (\Throwable $th) {
            return redirect('/')->withErrors([
                'error' => 'Terdapat Kesalahan'
            ]);
        }
    }

    public function show($id)
    {
        $pembelian = DetailBeli::with([
            'produk' => function ($query) {
                $query->select('id', 'id_kategori', 'nama')->with(['kategori' => function ($query) {
                    $query->select('id', 'nama');
                }]);
            }, 'header_beli.supplier'
        ])->where('id', $id)->first();
        return view('pages.pembelian.show')->with([
            'title' => 'PEMBELIAN',
            'pembelian' => $pembelian,
            'transaksi_toogle' => 1
        ]);
    }

    public function contoh()
    {
        // $data = DetailBeli::select('detail_beli.id_produk, detail_beli.qty, header_beli.tgl_beli, header_beli.tgl_beli, supplier.nama')->with('produk, header_beli.supplier')->orderBy('updated_at', 'desc')->get();
        $data = DetailBeli::with([
            'produk' => function ($query) {
                $query->select('id', 'id_kategori', 'nama')->with(['kategori' => function ($query) {
                    $query->select('id', 'nama');
                }]);
            }, 'header_beli.supplier'
        ])->where('id', 1)->first();
        return response()->json(
            $data
        );
    }
}
