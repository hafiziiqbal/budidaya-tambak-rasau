<?php

namespace App\Http\Controllers;

use App\Models\Produk;
use App\Models\Supplier;
use App\Models\DetailBeli;
use App\Models\HeaderBeli;
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
            $detail = $request->detail_beli;
            $subtotal = [];
            $totalBruto = 0;
            $headBeli =  HeaderBeli::create([
                'tgl_beli' => $tglBeli,
                'id_supplier' => $request->supplier,
                'potongan_harga' => $request->potongan_harga == null ? 0 : $request->potongan_harga,
            ]);

            foreach ($detail as $key => $value) {
                $hargaTotal = $detail[$key]['harga_satuan'] * $detail[$key]['quantity'];
                $percentage = $detail[$key]['diskon_persen'] / 100; // 10% expressed as a decimal
                $diskonRupiah = $detail[$key]['diskon_rupiah'];
                $discount = $hargaTotal * $percentage;

                if ($detail[$key]['diskon_persen'] == null || $detail[$key]['diskon_persen'] == '') {
                    $detail[$key]['diskon_persen'] = 0;
                }
                if ($detail[$key]['diskon_rupiah'] == null ||   $detail[$key]['diskon_rupiah'] == '') {
                    $detail[$key]['diskon_rupiah'] = 0;
                }


                if ($detail[$key]['diskon_persen'] != null || $detail[$key]['diskon_persen'] != '') {
                    array_push($subtotal, $hargaTotal - $discount);
                    $detail[$key]['subtotal'] = $hargaTotal - $discount;
                } elseif ($detail[$key]['diskon_rupiah'] != null ||   $detail[$key]['diskon_rupiah'] != '') {
                    array_push($subtotal, $hargaTotal - $diskonRupiah);
                    $detail[$key]['subtotal'] = $hargaTotal - $diskonRupiah;
                }

                $detail[$key]['id_header_beli'] = $headBeli->id;
                $detail[$key]['quantity_stok'] = $detail[$key]['quantity'];

                Produk::where('id', $detail[$key]['id_produk'])->update([
                    'quantity' => DB::raw("quantity+" . $detail[$key]['quantity']),
                ]);
            }

            $totalBruto = array_sum($subtotal);
            $totalNetto = $totalBruto - $headBeli->potongan_harga;
            $headBeli->update([
                'total_bruto' => $totalBruto,
                'total_netto' => $totalNetto
            ]);
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
                $data = DetailBeli::select(['id', 'id_header_beli', 'id_produk', 'quantity', 'quantity_stok', 'updated_at'])->with(['produk' => function ($query) {
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
            $headerBeli = HeaderBeli::find($pembelian->id_header_beli);
            $produk = Produk::find($pembelian->id_produk);
            $hargaTotal = $request->harga_satuan * $request->quantity;
            $percentage = $request->diskon_persen / 100; // 10% expressed as a decimal
            $diskonRupiah = $request->diskon_rupiah;
            $discount = $hargaTotal * $percentage;
            $subtotal = 0;
            $newTotalBruto = 0;
            $newTotalNetto = 0;
            $newQuantityProduk = 0;

            if ($request->diskon_persen == null || $request->diskon_persen == '') {
                $request->merge(['diskon_persen' => 0]);
            }
            if ($request->diskon_rupiah == null || $request->diskon_rupiah == '') {
                $request->merge(['diskon_rupiah' => 0]);
            }

            if ($request->diskon_persen != null || $request->diskon_persen != '') {
                $subtotal = $hargaTotal - $discount;
            } elseif ($request->diskon_rupiah != null || $request->diskon_rupiah != '') {
                $subtotal = $hargaTotal - $diskonRupiah;
            }


            $newTotalBruto = ($headerBeli->total_bruto - $pembelian->subtotal) + $subtotal;
            $newTotalNetto = $newTotalBruto - $headerBeli->potongan_harga;

            $headerBeli->update([
                'total_bruto' => $newTotalBruto,
                'total_netto' => $newTotalNetto,
            ]);

            $newQuantityProduk = ($produk->quantity - $pembelian->quantity) + $request->quantity;
            $produk->update([
                'quantity' => $newQuantityProduk,
            ]);

            $request->request->add(['quantity_stok' => $request->quantity]);


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
