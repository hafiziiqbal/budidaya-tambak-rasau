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
                $data = HeaderPembagianBibit::with([
                    'detail_beli' => function ($query) {
                        $query->select('id', 'id_produk', 'id_header_beli')->with(['produk' => function ($query) {
                            $query->select('id', 'nama');
                        }, 'header_beli']);
                    }
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
        $jaring = MasterJaring::where('id_kolam', null)->get();

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

    public function show($id)
    {
        $data = HeaderPembagianBibit::with([
            'detail_beli' => function ($query) {
                $query->select('id', 'id_produk', 'id_header_beli')->with(['produk' => function ($query) {
                    $query->select('id', 'nama');
                }, 'header_beli']);
            }, 'detail_pembagian_bibit' => function ($query) {
                $query->with(['jaring', 'kolam']);
            }
        ])->where('id', $id)->first();


        return view('pages.pembagian_bibit.show')->with([
            'title' => 'PEMBAGIAN BIBIT',
            'data' => $data,
            'transaksi_toogle' => 1
        ]);
    }

    public function update(Request $request, $id)
    {
        $headerPembagianBibit = HeaderPembagianBibit::find($id);
        $detailPembagianBibit = DetailPembagianBibit::where('id_header_pembagian_bibit', $id)->get();
        $detailBeli = DetailBeli::find($headerPembagianBibit->id_detail_beli);
        $produk = Produk::find($detailBeli->id_produk);
        $tglPembagian = date('Y-m-d', strtotime($request->tgl_pembagian));

        if ($headerPembagianBibit->id_detail_beli != $request->id_detail_beli) {
            foreach ($detailPembagianBibit as $key => $detail) {
                MasterJaring::where('id', $detail->id_jaring)->update([
                    'id_kolam' => null
                ]);
                $produk->update([
                    'quantity' => DB::raw("quantity+" . $detail->quantity),
                ]);
                $detailBeli->update([
                    'quantity_stok' => DB::raw("quantity_stok+" . $detail->quantity),
                ]);
                $detail->delete();
            }
        }

        $headerPembagianBibit->update([
            'tgl_pembagian' => $tglPembagian,
            'id_detail_beli' => $request->id_detail_beli,
            'id_detail_panen' => $request->id_panen,
        ]);

        return redirect()->route('pembagian.bibit.edit', $id)->with(
            'success',
            'Berhasil Mengubah Data Header Bibit'
        );
    }

    public function updateDetail(Request $request, $id)
    {
        // return response()->json($request->all());
        $detailPembagianBibit = DetailPembagianBibit::find($id);
        $headerPembagianBibit = HeaderPembagianBibit::find($detailPembagianBibit->id_header_pembagian_bibit);
        $detailBeli = DetailBeli::find($headerPembagianBibit->id_detail_beli);

        // validasi kolam
        $kolam = MasterKolam::with(['jaring', 'detail_pembagian_bibit'])->where('id', $request->id_kolam)->first();
        $jmlPembagianBibitSatuKolam = count($kolam->detail_pembagian_bibit);
        $jmlJaringSatuKolam = count($kolam->jaring);
        if ($request->id_jaring == 'null' && $detailPembagianBibit->id_jaring != null) {
            $sisaBagianSatuKolam = $jmlPembagianBibitSatuKolam - $jmlJaringSatuKolam;
            if ($sisaBagianSatuKolam > $jmlJaringSatuKolam || $sisaBagianSatuKolam > ($jmlJaringSatuKolam - 1)) {
                return response()->json([
                    'alert_kolam' => 'kolam tidak ada ruang lagi, silahkan tambah jaring'
                ]);
            }
        }
        // end validasi kolam

        // validasi jaring
        if ($request->id_jaring != 'null') {
            $jaring = MasterJaring::find($request->id_jaring);
            if ($jaring->id_kolam != null && $jaring->id != $detailPembagianBibit->id_jaring) {
                return response()->json([
                    'alert_jaring' => $jaring->nama . ' digunakan oleh data lain'
                ]);
            }
        }

        if ($detailPembagianBibit->id_jaring != null) {
            $jaring = MasterJaring::find($detailPembagianBibit->id_jaring);
            if ($request->id_jaring != $detailPembagianBibit->id_jaring) {
                $jaring->update([
                    'id_kolam' => null
                ]);
            }
        }

        if ($request->id_jaring != 'null') {
            $jaring = MasterJaring::find($request->id_jaring);
            $jaring->update([
                'id_kolam' => $request->id_kolam
            ]);
        }

        $detailPembagianBibit->update([
            'id_jaring' => $request->id_jaring == 'null' ? null : $request->id_jaring,
            'id_kolam' => $request->id_kolam
        ]);
        // end validasi jaring        

        // validasi quantity
        $totalQuantityBibit = [];
        foreach ($headerPembagianBibit->with('detail_pembagian_bibit')->first()->detail_pembagian_bibit as $key => $value) {
            if ($value->id != $id) {
                array_push($totalQuantityBibit, $value->quantity);
            }
        }
        array_push($totalQuantityBibit, $request->quantity);
        if (array_sum($totalQuantityBibit) > $detailBeli->quantity) {
            return response()->json([
                'alert_quantity' => 'total quantity melebihi jumlah stok ' . $detailBeli->quantity
            ]);
        } else {
            $detailPembagianBibit->update([
                'quantity' => $request->quantity
            ]);
        }


        return response()->json([
            'sukses' => 'Berhasil Ubah Data'
        ]);
    }

    public function storeDetail(Request $request)
    {
        $headerPembagianBibit = HeaderPembagianBibit::find($request->id_header_pembagian_bibit);
        $detailBeli = DetailBeli::find($headerPembagianBibit->id_detail_beli);

        // validasi quantity
        $totalQuantityBibit = [];
        foreach ($headerPembagianBibit->with('detail_pembagian_bibit')->first()->detail_pembagian_bibit as $key => $value) {
            if ($value->id != $request->id_header_pembagian_bibit) {
                array_push($totalQuantityBibit, $value->quantity);
            }
        }
        array_push($totalQuantityBibit, $request->quantity);
        if (array_sum($totalQuantityBibit) > $detailBeli->quantity) {
            return response()->json([
                'alert_quantity' => 'total quantity melebihi jumlah stok ' . $detailBeli->quantity
            ]);
        }


        if ($request->id_kolam == null) {
            return response()->json([
                'alert_kolam' => 'kolam tidak boleh kosong'
            ]);
        }
        // validasi kolam        
        $kolam = MasterKolam::with(['jaring', 'detail_pembagian_bibit'])->where('id', $request->id_kolam)->first();
        $jmlPembagianBibitSatuKolam = count($kolam->detail_pembagian_bibit);
        $jmlJaringSatuKolam = count($kolam->jaring);

        if ($request->id_jaring == 'null') {
            $sisaBagianSatuKolam = $jmlPembagianBibitSatuKolam - $jmlJaringSatuKolam;
            if ($sisaBagianSatuKolam > $jmlJaringSatuKolam || $sisaBagianSatuKolam > ($jmlJaringSatuKolam - 1 && $jmlPembagianBibitSatuKolam != 0)) {
                return response()->json([
                    'alert_kolam' => 'kolam tidak ada ruang lagi, silahkan tambah jaring'
                ]);
            }
        }
        // end validasi kolam

        // validasi jaring
        if ($request->id_jaring != 'null') {
            $jaring = MasterJaring::find($request->id_jaring);
            if ($jaring->id_kolam != null) {
                return response()->json([
                    'alert_jaring' => $jaring->nama . ' digunakan oleh data lain'
                ]);
            }
        }

        $detailPembagianBibit = DetailPembagianBibit::create([
            'id_header_pembagian_bibit' => $request->id_header_pembagian_bibit,
            'quantity' => $request->quantity,
            'id_jaring' => $request->id_jaring == 'null' ? null : $request->id_jaring,
            'id_kolam' => $request->id_kolam
        ]);


        return response()->json([
            'sukses' => 'Berhasil Tambah Data',
            'id' =>$detailPembagianBibit->id
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

    public function edit($id)
    {
        $jaring = MasterJaring::all();
        $kolam = MasterKolam::select(['id', 'nama'])->get();
        $data = HeaderPembagianBibit::with([
            'detail_beli' => function ($query) {
                $query->select('id', 'id_produk', 'id_header_beli')->with(['produk' => function ($query) {
                    $query->select('id', 'nama');
                }, 'header_beli']);
            }, 'detail_pembagian_bibit' => function ($query) {
                $query->with(['jaring', 'kolam']);
            }
        ])->where('id', $id)->first();
        $detailBeli = DetailBeli::all();

        return view('pages.pembagian_bibit.edit')->with([
            'title' => 'EDIT PEMBAGIAN BIBIT',
            'data' => $data,
            'jaring' => $jaring,
            'kolam' => $kolam,
            'detailBeli' => $detailBeli
        ]);
    }

    public function destroy($id)
    {
        // try {
        $detailPembagianBibit = DetailPembagianBibit::find($id);
        $jaring = MasterJaring::find($detailPembagianBibit->id_jaring);
        $detailPembagianBibitRelation = $detailPembagianBibit::with([
            'header_pembagian_bibit' => function ($query) {
                $query->select('id', 'id_detail_beli',)->with(['detail_beli' => function ($query) {
                    $query->select('id', 'id_produk');
                }]);
            }
        ])->first();

        $produk = Produk::find($detailPembagianBibitRelation->header_pembagian_bibit->detail_beli->id_produk);
        $newQuantityProduk = $produk->quantity + $detailPembagianBibit->quantity;
        $produk->update([
            'quantity' => $newQuantityProduk,
        ]);

        $detailBeli = DetailBeli::find($detailPembagianBibitRelation->header_pembagian_bibit->id_detail_beli);
        $detailBeli->update([
            'quantity_stok' => $newQuantityProduk,
        ]);
        $jaring->update([
            'id_kolam' => null,
        ]);

        $detailPembagianBibit->delete();
        return redirect()->route('pembagian.bibit')->with(
            'success',
            'Berhasil Hapus Pembagian Bibit'
        );
        // } catch (\Throwable $th) {
        //     return redirect('/')->withErrors([
        //         'error' => 'Terdapat Kesalahan'
        //     ]);
        // }
    }

    public function contoh()
    {
        // $data = DetailBeli::select('detail_beli.id_produk, detail_beli.qty, header_beli.tgl_beli, header_beli.tgl_beli, supplier.nama')->with('produk, header_beli.supplier')->orderBy('updated_at', 'desc')->get();
        $data = HeaderPembagianBibit::with([
            'detail_beli' => function ($query) {
                $query->select('id', 'id_produk', 'id_header_beli')->with(['produk' => function ($query) {
                    $query->select('id', 'nama');
                }, 'header_beli']);
            }
        ])->orderBy('updated_at', 'desc')->get();
        return response()->json(
            $data
        );
    }
}
