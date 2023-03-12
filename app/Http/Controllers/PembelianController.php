<?php

namespace App\Http\Controllers;

use App\Models\Produk;
use App\Models\Supplier;
use App\Models\DetailBeli;
use App\Models\DetailPembagianBibit;
use App\Models\HeaderBeli;
use App\Models\HeaderPembagianBibit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
            $totalBruto = [];
            $headerBeli =  HeaderBeli::create([
                'tgl_beli' => $tglBeli,
                'id_supplier' => $request->supplier,
                'potongan_harga' => $request->potongan_harga == null ? 0 : $request->potongan_harga,
            ]);

            foreach ($request->detail_beli as $key =>  $valueArray) {
                // ubah array ke objek
                $value = (object) $valueArray;

                // menghitung subtotal            
                $subtotal = 0;
                if ($value->diskon_persen == null) {
                    $subtotal = ($value->harga_satuan * $value->quantity) - $value->diskon_rupiah;
                } elseif ($value->diskon_persen != null) {
                    $subtotal = $value->harga_satuan * $value->quantity * ((100 / 10) - ($value->diskon_persen / 100));
                } else {
                    $subtotal = $value->harga_satuan * $value->quantity;
                }

                // memasukkan subtotal ke total bruto
                array_push($totalBruto, $subtotal);

                // memasukkan detail beli ke database
                DetailBeli::create([
                    'id_header_beli' => $headerBeli->id,
                    'id_produk' => $value->id_produk,
                    'harga_satuan' => $value->harga_satuan,
                    'quantity' => $value->quantity,
                    'quantity_stok' => $value->quantity,
                    'diskon_persen' => $value->diskon_persen,
                    'diskon_rupiah' => $value->diskon_rupiah,
                    'subtotal' => $subtotal
                ]);

                // update quantity produk
                Produk::where('id', $value->id_produk)->update([
                    'quantity' => DB::raw("quantity+" . $value->quantity),
                ]);
            }

            $headerBeli->update([
                'total_bruto' => array_sum($totalBruto),
                'total_netto' => array_sum($totalBruto) - $request->potongan_harga,
            ]);

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

    public function edit($id)
    {
        $supplier = Supplier::all();
        $produk = Produk::all();
        return view('pages.pembelian.edit')->with([
            'title' => 'PEMBELIAN',
            'id' => $id,
            'supplier' => $supplier,
            'produk' => $produk,
            'transaksi_toogle' => 1
        ]);
    }

    public function editJson($id)
    {
        $headerBeli = HeaderBeli::with('detail_beli')->where('id', $id)->first();
        return response()->json($headerBeli);
    }

    public function update(Request $request, $id)
    {
        try {
            $tglBeli = date('Y-m-d', strtotime($request->tgl_beli));
            $totalBruto = [];
            $headerBeli = HeaderBeli::where('id', $id)->with('detail_beli')->first();

            foreach ($headerBeli->detail_beli as $key => $value) {
                array_push($totalBruto, $value->subtotal);
            }

            $headerBeli->update([
                'tgl_beli' => $tglBeli,
                'supplier' => $request->supplier,
                'potongan_harga' => $request->potongan_harga,
                'total_bruto' => array_sum($totalBruto),
                'total_netto' => array_sum($totalBruto) - $request->potongan_harga,
            ]);

            return response()->json([
                'success' => 'Data Berhasil di Perbarui'
            ], 200);
        } catch (\Throwable $th) {
            return response()->json(
                'Kesalahan Aplikasi',
                400
            );
        }
    }

    public function updateDetail(Request $request, $id)
    {
        $totalBruto = [];
        $detailBeli = DetailBeli::all()->except($id);
        // menghitung subtotal
        $subtotal = 0;
        if ($request->diskon_persen == null) {
            $subtotal = ($request->harga_satuan * $request->quantity) - $request->diskon_rupiah;
        } elseif ($request->diskon_persen != null) {
            $subtotal = $request->harga_satuan * $request->quantity * ((100 / 10) - ($request->diskon_persen / 100));
        } else {
            $subtotal = $request->harga_satuan * $request->quantity;
        }

        // memasukkan subtotal ke total bruto
        array_push($totalBruto, $subtotal);

        foreach ($detailBeli as $key => $value) {
            array_push($totalBruto, $value->subtotal);
        }

        // update beli ke database
        $detailBeliUpdate = DetailBeli::find($id);
        $quantityProdukOld = $detailBeliUpdate->quantity;
        $detailBeliUpdate->update([
            'harga_satuan' => $request->harga_satuan,
            'quantity' => $request->quantity,
            'quantity_stok' => $request->quantity,
            'diskon_persen' => $request->diskon_persen,
            'diskon_rupiah' => $request->diskon_rupiah,
            'subtotal' => $subtotal
        ]);

        // update quantity produk
        $produk = Produk::find($detailBeliUpdate->id_produk);
        $jumlahProduk = ($produk->quantity - $quantityProdukOld) + $request->quantity;
        $produk->update([
            'quantity' => $jumlahProduk,
        ]);

        $headerBeli = HeaderBeli::find($detailBeliUpdate->id_header_beli);
        $headerBeli->update([
            'total_bruto' => array_sum($totalBruto),
            'total_netto' => array_sum($totalBruto) - $headerBeli->potongan_harga,
        ]);

        return response()->json([
            'success' => 'Data Berhasil di Perbarui'
        ], 200);
    }

    public function datatable(Request $request)
    {
        try {
            if ($request->ajax()) {
                $data = HeaderBeli::with('supplier')->orderBy('updated_at', 'desc')->get();
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



    // public function update(Request $request, $id)
    // {
    //     try {
    //         $pembelian = DetailBeli::find($id);

    //         $headerBeli = HeaderBeli::find($pembelian->id_header_beli);
    //         $produk = Produk::find($pembelian->id_produk);
    //         $hargaTotal = $request->harga_satuan * $request->quantity;
    //         $percentage = $request->diskon_persen / 100; // 10% expressed as a decimal
    //         $diskonRupiah = $request->diskon_rupiah;
    //         $discount = $hargaTotal * $percentage;
    //         $subtotal = 0;
    //         $newTotalBruto = 0;
    //         $newTotalNetto = 0;
    //         $newQuantityProduk = 0;

    //         if ($request->diskon_persen == null || $request->diskon_persen == '') {
    //             $request->merge(['diskon_persen' => 0]);
    //         }
    //         if ($request->diskon_rupiah == null || $request->diskon_rupiah == '') {
    //             $request->merge(['diskon_rupiah' => 0]);
    //         }

    //         if ($request->diskon_persen != null || $request->diskon_persen != '') {
    //             $subtotal = $hargaTotal - $discount;
    //         } elseif ($request->diskon_rupiah != null || $request->diskon_rupiah != '') {
    //             $subtotal = $hargaTotal - $diskonRupiah;
    //         }


    //         $newTotalBruto = ($headerBeli->total_bruto - $pembelian->subtotal) + $subtotal;
    //         $newTotalNetto = $newTotalBruto - $headerBeli->potongan_harga;

    //         $headerBeli->update([
    //             'total_bruto' => $newTotalBruto,
    //             'total_netto' => $newTotalNetto,
    //         ]);

    //         $newQuantityProduk = ($produk->quantity - $pembelian->quantity) + $request->quantity;
    //         $produk->update([
    //             'quantity' => $newQuantityProduk,
    //         ]);

    //         $request->request->add(['quantity_stok' => $request->quantity]);


    //         $pembelian->update($request->all());

    //         return redirect()->route('pembelian')->with(
    //             'success',
    //             'Berhasil Perbarui Pembelian'
    //         );
    //     } catch (\Throwable $th) {
    //         return redirect('/')->withErrors([
    //             'error' => 'Terdapat Kesalahan'
    //         ]);
    //     }
    // }

    public function destroy($id)
    {
        // try {
        $detailBeli = DetailBeli::find($id);
        $cekPembagianBibit = HeaderPembagianBibit::where('id_detail_beli', $detailBeli->id)->first();
        if (!empty($cekPembagianBibit)) {
            return redirect('pembelian')->withErrors([
                'alert' => 'Data Ini Sudah Digunakan Di Tabel Bibit'
            ]);
        }
        $headerBeli = HeaderBeli::find($detailBeli->id_header_beli);
        $produk = Produk::find($detailBeli->id_produk);
        $subtotal = $detailBeli->subtotal;
        $newTotalBruto = 0;
        $newTotalNetto = 0;
        $newQuantityProduk = 0;

        $newTotalBruto = $headerBeli->total_bruto - $subtotal;
        $newTotalNetto = $newTotalBruto - $headerBeli->potongan_harga;

        $headerBeli->update([
            'total_bruto' => $newTotalBruto,
            'total_netto' => $newTotalNetto,
        ]);


        $newQuantityProduk = $produk->quantity - $detailBeli->quantity;
        $produk->update([
            'quantity' => $newQuantityProduk,
        ]);

        $detailBeli->delete();
        return redirect()->route('pembelian')->with(
            'success',
            'Berhasil Hapus Pembelian'
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
