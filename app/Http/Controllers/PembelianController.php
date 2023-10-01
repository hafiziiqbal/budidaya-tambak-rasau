<?php

namespace App\Http\Controllers;

use App\Http\Requests\PembelianRequest;
use App\Models\Produk;
use App\Models\Supplier;
use App\Models\DetailBeli;
use App\Models\DetailPembagianBibit;
use App\Models\DetailPembagianPakan;
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
        $produk = Produk::where('id_kategori', '!=', 6)->get();
        return view('pages.pembelian.create')->with([
            'title' => 'TAMBAH PEMBELIAN',
            'supplier' => $suppliers,
            'produk' => $produk,
            'transaksi_toogle' => 1
        ]);
    }

    public function store(PembelianRequest $request)
    {
        try {
            $tglBeli = date('Y-m-d', strtotime($request->tanggal_beli));
            $headerBeli =  HeaderBeli::create([
                'tgl_beli' => $tglBeli,
                'id_supplier' => $request->supplier,
                'potongan_harga' => $request->potongan_harga == null ? 0 : $request->potongan_harga,
                'total_bruto' => $request->total_bruto,
                'total_netto' => $request->total_netto,
            ]);

            foreach ($request->detail_beli as $key =>  $valueArray) {
                // ubah array ke objek
                $value = (object) $valueArray;

                // memasukkan detail beli ke database
                DetailBeli::create([
                    'id_header_beli' => $headerBeli->id,
                    'id_produk' => $value->id_produk,
                    'harga_satuan' => $value->harga_satuan,
                    'quantity' => $value->quantity,
                    'quantity_stok' => $value->quantity,
                    'diskon_persen' => $value->diskon_persen,
                    'diskon_rupiah' => $value->diskon_rupiah,
                    'subtotal' => $value->subtotal,
                ]);

                // update quantity produk
                Produk::where('id', $value->id_produk)->update([
                    'quantity' => DB::raw("quantity+" . $value->quantity),
                ]);
            }

            return response()->json([
                'success' => 'Berhasil Tambah Data'
            ]);
        } catch (\Throwable $th) {
            return redirect('/')->withErrors([
                'error' => 'Terdapat Kesalahan'
            ]);
        }
    }

    public function storeDetail(PembelianRequest $request)
    {
        // try {
        $detailBeli = DetailBeli::where('id_header_beli', $request->id_header_beli)->get();
        $totalBruto = [];
        $subtotal = 0;
        if ($request->diskon_persen == 0) {
            $subtotal = ($request->harga_satuan * $request->quantity) - $request->diskon_rupiah;
        } elseif ($request->diskon_persen != 0) {
            $subtotal = $request->harga_satuan * $request->quantity * ((100 / 10) - ($request->diskon_persen / 100));
        } else {
            $subtotal = $request->harga_satuan * $request->quantity;
        }

        array_push($totalBruto, $subtotal);

        foreach ($detailBeli as $key => $value) {
            array_push($totalBruto, $value->subtotal);
        }

        $tambahBaru = DetailBeli::create([
            'id_header_beli' => $request->id_header_beli,
            'id_produk' => $request->id_produk,
            'harga_satuan' => $request->harga_satuan,
            'quantity' => $request->quantity,
            'quantity_stok' => $request->quantity,
            'diskon_persen' => $request->diskon_persen,
            'diskon_rupiah' => $request->diskon_rupiah,
            'subtotal' => $subtotal
        ]);

        // update quantity produk
        Produk::where('id', $request->id_produk)->update([
            'quantity' => DB::raw("quantity+" . $request->quantity),
        ]);

        $headerBeli = HeaderBeli::find($request->id_header_beli);
        $headerBeli->update([
            'total_bruto' => array_sum($totalBruto),
            'total_netto' => array_sum($totalBruto) - $headerBeli->potongan_harga,
        ]);

        return response()->json([
            'success' => 'Data Berhasil di Perbarui',
            'save_detail' => true,
            'id' => $tambahBaru->id
        ], 200);

        // } catch (\Throwable $th) {
        //     return response()->json($th);
        // }
    }

    public function edit($id)
    {
        $supplier = Supplier::all();
        $produk = Produk::where('id_kategori', '!=', 6)->get();
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

    public function update(PembelianRequest $request, $id)
    {
        try {
            $tglBeli = date('Y-m-d', strtotime($request->tanggal_beli));
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

    public function updateDetail(PembelianRequest $request, $id)
    {
        $headerPembagianBibit = HeaderPembagianBibit::where('id_detail_beli', $id)->first();
        if ($headerPembagianBibit) {
            return response()->json([
                'errors' => [
                    'general' => "Bibit ini sudah dibagikan"
                ],
            ], 422);
        }

        $detailPembagianPakan = DetailPembagianPakan::where('id_detail_beli', $id)->first();
        if ($detailPembagianPakan) {
            return response()->json([
                'errors' => [
                    'general' => "Pakan ini sudah dibagikan"
                ],
            ], 422);
        }

        $totalBruto = [];
        $detailBeli = DetailBeli::where('id_header_beli', $request->id_header_beli)->where('id', '!=', $id)->get();

        // menghitung subtotal
        $subtotal = 0;
        if ($request->diskon_persen == 0) {
            $subtotal = ($request->harga_satuan * $request->quantity) - $request->diskon_rupiah;
        } elseif ($request->diskon_persen != 0) {
            $subtotal = ($request->harga_satuan * $request->quantity) - (($request->harga_satuan * $request->quantity) * ($request->diskon_persen / 100));
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
                $data = HeaderBeli::with('supplier')->orderBy('created_at', 'desc')->get();
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

        $supplier = Supplier::all();
        $produk = Produk::all();
        return view('pages.pembelian.show')->with([
            'title' => 'PEMBELIAN',
            'id' => $id,
            'supplier' => $supplier,
            'produk' => $produk,
            'transaksi_toogle' => 1
        ]);
    }

    public function destroy($id)
    {
        $headerBeli = HeaderBeli::find($id);
        $detailBeli = DetailBeli::where('id_header_beli', $id)->get();

        foreach ($detailBeli as $key => $value) {
            $headerPembagianBibit = HeaderPembagianBibit::where('id_detail_beli', $value->id)->first();
            if ($headerPembagianBibit) {
                return redirect()->route('pembelian')->withErrors(['error' => 'Terdapat data yang sudah dibagikan']);
            }

            $detailPembagianPakan = DetailPembagianPakan::where('id_detail_beli', $value->id)->first();
            if ($detailPembagianPakan) {
                return redirect()->route('pembelian')->withErrors(['error' => 'Terdapat data yang sudah dibagikan']);
            }
        }


        foreach ($detailBeli as $key => $value) {
            Produk::find($value->id_produk)->update([
                'quantity' => DB::raw("quantity-" . $value->quantity),
            ]);
        }

        $headerBeli->delete();

        return redirect()->route('pembelian')->with(
            'success',
            'Berhasil Hapus Pembelian'
        );
    }

    public function destroyDetail($id)
    {
        $headerPembagianBibit = HeaderPembagianBibit::where('id_detail_beli', $id)->first();
        if ($headerPembagianBibit) {
            return response()->json([
                'errors' => [
                    'general' => "Bibit ini sudah dibagikan"
                ],
            ], 422);
        }
        $detailPembagianPakan = DetailPembagianPakan::where('id_detail_beli', $id)->first();
        if ($detailPembagianPakan) {
            return response()->json([
                'errors' => [
                    'general' => "Pakan  ini sudah dibagikan"
                ],
            ], 422);
        }

        $totalBruto = [];
        $detailBeli = DetailBeli::find($id);
        $detailBeliUpdate = DetailBeli::where('id_header_beli', $detailBeli->id_header_beli)->where('id', '!=', $id)->get();

        foreach ($detailBeliUpdate as $key => $value) {
            array_push($totalBruto, $value->subtotal);
        }

        $headerBeli = HeaderBeli::find($detailBeli->id_header_beli);
        $headerBeli->update([
            'total_bruto' => array_sum($totalBruto),
            'total_netto' => array_sum($totalBruto) - $headerBeli->potongan_harga,
        ]);

        // update quantity produk
        Produk::find($detailBeli->id_produk)->update([
            'quantity' => DB::raw("quantity-" . $detailBeli->quantity_stok),
        ]);

        $detailBeli->delete();

        return response()->json([
            'success' => 'Data Berhasil di Hapus'
        ], 200);
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
