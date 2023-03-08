<?php

namespace App\Http\Controllers;

use App\Models\Produk;
use App\Models\DetailBeli;
use App\Models\MasterKolam;
use App\Models\MasterJaring;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\DetailPembagianBibit;
use App\Models\HeaderPembagianBibit;
use Yajra\DataTables\Facades\DataTables;

class PembagianBibitController extends Controller
{
    public function index()
    {
        return view('pages.pembagian_bibit.index')->with([
            'title' => 'PEMBAGIAN BIBIT',
        ]);
    }

    public function datatable(Request $request)
    {
        try {
            if ($request->ajax()) {
                $data = DetailPembagianBibit::with([
                    'header_pembagian_bibit' => function ($query) {
                        $query->select('id', 'id_detail_beli', 'tgl_pembagian')->with(['detail_beli' => function ($query) {
                            $query->select('id', 'id_produk')->with(['produk' => function ($query) {
                                $query->select('id', 'nama');
                            }]);
                        }]);
                    }, 'jaring', 'kolam'
                ])->orderBy('updated_at', 'desc')->get();
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
        // $bibit = Produk::select(['id', 'nama', 'quantity'])->where('id_kategori', 2)->where('quantity', '>', 0.00)->get();
        $jaring = MasterJaring::select(['id', 'nama'])->get();
        $kolam = MasterKolam::select(['id', 'nama'])->get();

        $pembelian = DetailBeli::select(['id', 'id_header_beli', 'id_produk', 'updated_at', 'quantity'])->with(['produk' => function ($query) {
            $query->select('id', 'nama', 'quantity');
        }, 'header_beli' => function ($query) {
            $query->select('id', 'tgl_beli');
        }])->orderBy('id_header_beli', 'asc')->get();

        // return response()->json($pembelian);


        return view('pages.pembagian_bibit.create')->with([
            'title' => 'PEMBAGIAN BIBIT',
            'jaring' => $jaring,
            'kolam' => $kolam,
            // 'bibit' => $bibit,
            'pembelian' => $pembelian
        ]);
    }

    public function store(Request $request)
    {
        try {
            $tglPembagian = date('Y-m-d', strtotime($request->tgl_pembagian));
            $detail = $request->detail_pembagian_bibit;
            $detailBeli = DetailBeli::select('id', 'id_produk', 'quantity_stok')->where('id', $request->id_detail_beli)->first();

            $headPembagian =  HeaderPembagianBibit::create([
                'tgl_pembagian' => $tglPembagian,
                'id_detail_beli' => $request->id_detail_beli,
                'id_detail_panen' => $request->id_panen,
            ]);

            foreach ($detail as $key => $value) {
                $detail[$key]['id_header_pembagian_bibit'] = $headPembagian->id;
                Produk::where('id', $detailBeli->id_produk)->update([
                    'quantity' => DB::raw("quantity-" . $detail[$key]['quantity']),
                ]);
                $detailBeli->update([
                    'quantity_stok' => DB::raw("quantity_stok-" . $detail[$key]['quantity']),
                ]);
                MasterJaring::where('id', $detail[$key]['id_jaring'])->update([
                    'id_kolam' => $detail[$key]['id_kolam'],
                ]);
            }

            DetailPembagianBibit::insert($detail);

            return redirect()->route('pembagian.bibit')->with(
                'success',
                'Berhasil Membagikan Bibit'
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
            $detailPembagianBibit = DetailPembagianBibit::find($id);
            $detailPembagianBibit->delete();
            return redirect()->route('pembagian.bibit')->with(
                'success',
                'Berhasil Hapus Pembagian Bibit'
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
        $data = DetailPembagianBibit::with([
            'header_pembagian_bibit' => function ($query) {
                $query->select('id', 'id_detail_beli', 'tgl_pembagian')->with(['detail_beli' => function ($query) {
                    $query->select('id', 'id_produk')->with(['produk' => function ($query) {
                        $query->select('id', 'nama');
                    }]);
                }]);
            }, 'jaring', 'kolam'
        ])->orderBy('updated_at', 'desc')->get();
        return response()->json(
            $data
        );
    }
}
