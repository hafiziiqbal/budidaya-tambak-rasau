<?php

namespace App\Http\Controllers;

use App\Models\DetailBeli;
use App\Models\HeaderBeli;
use App\Models\Produk;
use App\Models\Supplier;
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

    public function create()
    {
        $suppliers = Supplier::all();
        $produk = Produk::all();
        return view('pages.pembelian.create')->with([
            'title' => 'TAMBAH PEMBELIAN',
            'supplier' => $suppliers,
            'produk' => $produk,
            'transaksi_toogle' => 1
        ]);
    }

    public function store(Request $request)
    {
        try {
            $tglBeli = date('Y-m-d', strtotime($request->tanggal_beli));
            $detail = $request->detail_beli;
            //dd($request->detail_beli);
            $headBeli =  HeaderBeli::create([
                'tgl_beli' => $tglBeli,
                'id_supplier' => $request->supplier,
                'total_bruto' => $request->bruto,
                'potongan_harga' => $request->potongan_harga,
                'total_netto' => $request->netto,
            ]);

            foreach ($detail as $key => $value) {
                $detail[$key]['id_header_beli'] = $headBeli->id;
            }

            DetailBeli::insert($detail);

            return redirect()->route('pembelian')->with(
                'success',
                'Berhasil Tambah Pembelian'
            );
        } catch (\Throwable $th) {
            return redirect('/')->withErrors([
                'error' => 'Terdapat Kesalahan'
            ]);
        }
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

    public function edit($id)
    {
        $produk = Produk::all();
        $pembelian = DetailBeli::with([
            'produk' => function ($query) {
                $query->select('id', 'id_kategori', 'nama')->with(['kategori' => function ($query) {
                    $query->select('id', 'nama');
                }]);
            }, 'header_beli.supplier'
        ])->where('id', $id)->first();
        return view('pages.pembelian.edit')->with([
            'title' => 'PEMBELIAN',
            'pembelian' => $pembelian,
            'produk' => $produk,
            'transaksi_toogle' => 1
        ]);
    }

    public function update(Request $request, $id)
    {
        try {
            $pembelian = DetailBeli::find($id);
            $pembelian->update($request->all());

            return redirect()->route('pembelian')->with(
                'success',
                'Berhasil Perbarui Pembelian'
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
            $detailBeli = DetailBeli::find($id);
            $detailBeli->delete();
            return redirect()->route('pembelian')->with(
                'success',
                'Berhasil Hapus Pembelian'
            );
        } catch (\Throwable $th) {
            return redirect('/')->withErrors([
                'error' => 'Terdapat Kesalahan'
            ]);
        }
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
