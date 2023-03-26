<?php

namespace App\Http\Controllers;

use App\Http\Requests\PenjualanRequest;
use Carbon\Carbon;
use App\Models\Produk;
use App\Models\DetailJual;
use App\Models\HeaderJual;
use App\Models\DetailPanen;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\MasterCustomer;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class PenjualanController extends Controller
{
    public function index()
    {
        return view('pages.penjualan.index')->with([
            'title' => 'PENJUALAN',
            'transaksi_toogle' => 1
        ]);
    }

    public function datatable(Request $request)
    {
        try {
            if ($request->ajax()) {
                $data = HeaderJual::with('customer')->orderBy('updated_at', 'desc')->get();
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
        $panen = DetailPanen::where('status', 1)->where('quantity', '>', 0)->with(['detail_pembagian_bibit.header_pembagian_bibit.detail_beli.produk', 'header_panen'])->get();
        $customer = MasterCustomer::all();

        return view('pages.penjualan.create')->with([
            'title' => 'TAMBAH PENJUALAN',
            'customers' => $customer,
            'panen' => $panen,
            'transaksi_toogle' => 1
        ]);
    }

    public function store(PenjualanRequest $request)
    {

        $details = $request->detail;
        $totalQuantities = [];
        if ($request->detail == null) {
            return response()->json([
                'errors' => [
                    'general' => "Detail panen tidak tersedia"
                ],
            ], 422);
        }

        foreach ($details as $key => $detail) {

            $idDetail = $detail['id_detail_panen'];
            $quantity = $detail['quantity'];

            if (isset($totalQuantities[$idDetail])) {
                $totalQuantities[$idDetail] += $quantity;
            } else {
                $totalQuantities[$idDetail] = $quantity;
            }
        }

        // validate total quantity against detail_pembagian_bibit
        foreach ($totalQuantities as $idDetail => $totalQuantity) {
            $detailPanen = DB::table('detail_panen')->where('id', $idDetail)->first();
            if ($totalQuantity > $detailPanen->quantity) {
                return response()->json([
                    'errors' => [
                        "detail.$idDetail.quantity-all" => "Total Quantity tadak boleh lebih dari $detailPanen->quantity"
                    ],
                ], 422);
            }
        }

        $randomString = Str::random(8);
        $invoice = Carbon::now()->timestamp . '-' . $request->customer . '-' . $randomString;
        $headerJual = HeaderJual::create([
            'invoice' => $invoice,
            'user_id' => Auth::user()->id,
            'id_customer' => $request->customer,
            'total_bruto' => $request->total_bruto,
            'potongan_harga' => $request->potongan_harga == '' ? 0 : $request->potongan_harga,
            'total_netto' => $request->total_netto,
            'pay' => $request->pay,
            'change' => $request->change,
        ]);


        foreach ($request->detail as $key => $valueArray) {
            $value = (object) $valueArray;
            $detailPanen = DetailPanen::where('id', $value->id_detail_panen)->with(['detail_pembagian_bibit.header_pembagian_bibit.detail_beli.produk'])->first();
            $produk = Produk::find($detailPanen->detail_pembagian_bibit->header_pembagian_bibit->detail_beli->id_produk);
            $produkIkan = Produk::where('nama',  $produk->nama)->where('id_kategori', 6)->first();
            DetailJual::create([
                'id_header_jual' => $headerJual->id,
                'id_produk' => $produkIkan->id,
                'id_detail_panen' => $value->id_detail_panen,
                'id_detail_beli' => $detailPanen->detail_pembagian_bibit->header_pembagian_bibit->id_detail_beli,
                'harga_satuan' => $value->harga_satuan,
                'diskon' => $value->diskon,
                'quantity' => $value->quantity,
                'sub_total' => $value->subtotal,
            ]);
            // update quantity produk
            $produkIkan->update([
                'quantity' => DB::raw("quantity-" . $value->quantity),
            ]);

            // update quantity produk
            $detailPanen->update([
                'quantity' => DB::raw("quantity-" . $value->quantity),
            ]);
        }

        return response()->json([
            'success' => 'Berhasil Tambah Data'
        ]);
    }

    public function storeDetail(PenjualanRequest $request)
    {
        $panen = DetailPanen::find($request->id_detail_panen);
        if ($panen->quantity == 0) {
            return response()->json([
                'errors' => [
                    'general' => "Stok produk hasil panen sudah habis"
                ],
            ], 422);
        }
        if ($request->quantity > $panen->quantity) {
            return response()->json([
                'errors' => [
                    'general' => 'Total quantity melebihi quantity stok panen'
                ],
            ], 422);
        }

        $detailJual = DetailJual::all();
        $totalBruto = [];
        // menghitung subtotal
        if ($request->diskon_persen != 0 || $request->diskon_persen != null) {
            $subtotal = ($request->harga_satuan * $request->quantity) * ($request->diskon_persen / 100);
        } else {
            $subtotal = $request->harga_satuan * $request->quantity;
        }

        array_push($totalBruto, $subtotal);

        foreach ($detailJual as $key => $value) {
            array_push($totalBruto, $value->sub_total);
        }

        $detailPanen = DetailPanen::where('id', $request->id_detail_panen)->with(['detail_pembagian_bibit.header_pembagian_bibit.detail_beli.produk'])->first();
        $produk = Produk::find($detailPanen->detail_pembagian_bibit->header_pembagian_bibit->detail_beli->id_produk);
        $produkIkan = Produk::where('nama',  $produk->nama)->where('id_kategori', 6)->first();
        $tambahBaru = DetailJual::create([
            'id_header_jual' => $request->id_header_jual,
            'id_produk' => $produkIkan->id,
            'id_detail_panen' => $request->id_detail_panen,
            'id_detail_beli' => $detailPanen->detail_pembagian_bibit->header_pembagian_bibit->id_detail_beli,
            'harga_satuan' => $request->harga_satuan,
            'diskon' => $request->diskon,
            'quantity' => $request->quantity,
            'sub_total' => $subtotal,
        ]);
        // update quantity produk
        $produkIkan->update([
            'quantity' => DB::raw("quantity-" . $request->quantity),
        ]);

        // update quantity produk
        $detailPanen->update([
            'quantity' => DB::raw("quantity-" . $request->quantity),
        ]);

        // update quantity produk
        Produk::where('id', $request->id_produk)->update([
            'quantity' => DB::raw("quantity-" . $request->quantity),
        ]);

        $headerJual = HeaderJual::find($request->id_header_jual);
        $headerJual->update([
            'total_bruto' => array_sum($totalBruto),
            'total_netto' => array_sum($totalBruto) - $headerJual->potongan_harga,
            'change' => $headerJual->pay - (array_sum($totalBruto) - $headerJual->potongan_harga),
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
        $panen = DetailPanen::where('status', 1)->with(['detail_pembagian_bibit.header_pembagian_bibit.detail_beli.produk', 'header_panen'])->get();
        $customer = MasterCustomer::all();
        return view('pages.penjualan.edit')->with([
            'title' => 'PENJUALAN',
            'id' => $id,
            'customer' => $customer,
            'panen' => $panen,
            'transaksi_toogle' => 1
        ]);
    }

    public function update(PenjualanRequest $request, $id)
    {
        try {
            $totalBruto = [];
            $headerJual = HeaderJual::where('id', $id)->with('detail_jual')->first();

            foreach ($headerJual->detail_jual as $key => $value) {
                array_push($totalBruto, $value->sub_total);
            }

            $headerJual->update([
                'id_customer' => $request->customer,
                'potongan_harga' => $request->potongan_harga,
                'total_bruto' => array_sum($totalBruto),
                'total_netto' => array_sum($totalBruto) - $request->potongan_harga,
                'pay' => $request->pay,
                'change' => $request->pay - (array_sum($totalBruto) - $request->potongan_harga),
            ]);

            return response()->json([
                'success' => 'Data Berhasil di Perbarui'
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'errors' => [
                    'general' => "Kesalahan Aplikasi"
                ],
            ], 500);
        }
    }

    public function updateDetail(PenjualanRequest $request, $id)
    {
        $detailJualUpdate = DetailJual::find($id);
        $panen = DetailPanen::find($detailJualUpdate->id_detail_panen);

        $jumlahQuantityPanenAwal = $panen->quantity + $detailJualUpdate->quantity;

        if ($request->quantity > $jumlahQuantityPanenAwal) {
            return response()->json([
                'errors' => [
                    'general' => 'Total quantity melebihi quantity stok panen'
                ],
            ], 422);
        }

        $totalBruto = [];
        $detailJual = DetailJual::all()->except($id);
        // menghitung subtotal
        if ($request->diskon_persen != 0 || $request->diskon_persen != null) {
            $subtotal = ($request->harga_satuan * $request->quantity) * ($request->diskon_persen / 100);
        } else {
            $subtotal = $request->harga_satuan * $request->quantity;
        }

        // memasukkan subtotal ke total bruto
        array_push($totalBruto, $subtotal);

        foreach ($detailJual as $key => $value) {
            array_push($totalBruto, $value->sub_total);
        }

        // update beli ke database

        $quantityProdukOld = $detailJualUpdate->quantity;
        $detailJualUpdate->update([
            'harga_satuan' => $request->harga_satuan,
            'quantity' => $request->quantity,
            'diskon' => $request->diskon,
            'sub_total' => $subtotal
        ]);

        // update quantity produk
        $produk = Produk::find($detailJualUpdate->id_produk);
        $jumlahProduk = ($produk->quantity + $quantityProdukOld) - $request->quantity;
        $produk->update([
            'quantity' => $jumlahProduk,
        ]);


        $jumlahPanen = ($panen->quantity + $quantityProdukOld) - $request->quantity;
        $panen->update([
            'quantity' => $jumlahPanen,
        ]);

        $headerJual = HeaderJual::find($detailJualUpdate->id_header_jual);
        $headerJual->update([
            'total_bruto' => array_sum($totalBruto),
            'total_netto' => array_sum($totalBruto) - $headerJual->potongan_harga,
            'change' => $headerJual->pay - (array_sum($totalBruto) - $headerJual->potongan_harga),
        ]);

        return response()->json([
            'success' => 'Data Berhasil di Perbarui'
        ], 200);
    }

    public function editJson($id)
    {
        $headerJual = HeaderJual::with('detail_jual')->where('id', $id)->first();
        return response()->json($headerJual);
    }

    public function destroy($id)
    {
        $headerJual = HeaderJual::find($id);
        $detailJual = DetailJual::where('id_header_jual', $id);

        foreach ($detailJual->get() as $key => $value) {
            Produk::find($value->id_produk)->update([
                'quantity' => DB::raw("quantity+" . $value->quantity),
            ]);
            DetailPanen::find($value->id_detail_panen)->update([
                'quantity' => DB::raw("quantity+" . $value->quantity),
            ]);
            $detailJual->delete();
        }

        $headerJual->delete();

        return redirect()->route('jual')->with(
            'success',
            'Berhasil Hapus Penjualan'
        );
    }

    public function destroyDetail($id)
    {

        $totalBruto = [];
        $detailJual = DetailJual::find($id);
        $detailJualUpdate = DetailJual::all()->except($id);

        foreach ($detailJualUpdate as $key => $value) {
            array_push($totalBruto, $value->sub_total);
        }

        $headerJual = HeaderJual::find($detailJual->id_header_jual);
        $headerJual->update([
            'total_bruto' => array_sum($totalBruto),
            'total_netto' => array_sum($totalBruto) - $headerJual->potongan_harga,
            'change' => $headerJual->pay - (array_sum($totalBruto) - $headerJual->potongan_harga),
        ]);

        // update quantity produk
        Produk::find($detailJual->id_produk)->update([
            'quantity' => DB::raw("quantity+" . $detailJual->quantity),
        ]);
        DetailPanen::find($detailJual->id_detail_panen)->update([
            'quantity' => DB::raw("quantity+" . $detailJual->quantity),
        ]);

        $detailJual->delete();

        return response()->json([
            'success' => 'Data Berhasil di Hapus'
        ], 200);
    }

    public function show($id)
    {
        $produk = Produk::where('id_kategori', 3)->get();
        $customer = MasterCustomer::all();
        return view('pages.penjualan.show')->with([
            'title' => 'PENJUALAN',
            'id' => $id,
            'customer' => $customer,
            'produk' => $produk,
            'transaksi_toogle' => 1
        ]);
    }
}
