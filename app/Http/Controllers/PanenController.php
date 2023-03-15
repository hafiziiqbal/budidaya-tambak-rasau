<?php

namespace App\Http\Controllers;

use App\Models\Produk;
use App\Models\DetailPanen;
use App\Models\HeaderPanen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\DetailPembagianBibit;

class PanenController extends Controller
{
    public function index()
    {
        return view('pages.panen.index')->with([
            'title' => 'PANEN',
        ]);
    }


    public function create()
    {
        $pembagianBibit = DetailPembagianBibit::with(['header_pembagian_bibit.detail_beli.produk'])
            ->where('quantity', '>', '0')->get();


        return view('pages.panen.create')->with([
            'title' => 'PANEN',
            'pembagianBibit' => $pembagianBibit
        ]);
    }

    public function store(Request $request)
    {
        // return response()->json($request->all());
        $tglPanen = date('Y-m-d', strtotime($request->tgl_pembagian));
        $details = $request->detail;

        // loop through each detail and calculate total quantity based on id_detail_pembagian_bibit
        $totalQuantities = [];
        foreach ($details as $detail) {
            $idDetail = $detail['id_detail_pembagian_bibit'];
            $quantity = $detail['quantity'];

            if (isset($totalQuantities[$idDetail])) {
                $totalQuantities[$idDetail] += $quantity;
            } else {
                $totalQuantities[$idDetail] = $quantity;
            }
        }

        // validate total quantity against detail_pembagian_bibit
        foreach ($totalQuantities as $idDetail => $totalQuantity) {
            $detailPembagianBibit = DB::table('detail_pembagian_bibit')->where('id', $idDetail)->first();
            if ($totalQuantity > $detailPembagianBibit->quantity) {
                return response()->json([
                    'error' => "Total Quantity Melebihi Quantity Pembagian"
                ]);
            }
        }

        // Masukkan data ke tabel detail_panen
        $headerPanen = new HeaderPanen;
        $headerPanen->tgl_panen = $tglPanen;
        $headerPanen->save();

        // Kurangi quantity pada tabel detail_pembagian_bibit sesuai dengan quantity pada request
        foreach ($details as $detail) {
            $id_detail_pembagian_bibit = $detail['id_detail_pembagian_bibit'];
            $quantity = $detail['quantity'];

            $detail_pembagian_bibit = DetailPembagianBibit::where('id', $id_detail_pembagian_bibit)->with(['kolam', 'jaring', 'header_pembagian_bibit.detail_beli.produk'])->first();
            $detail_pembagian_bibit->quantity -= $quantity;
            $detail_pembagian_bibit->save();

            $detailPanen = DetailPanen::create([
                'id_header_panen' => $headerPanen->id,
                'status' => $detail['status'],
                'quantity' => $quantity,
                'id_detail_pembagian_bibit' => $detail['id_detail_pembagian_bibit'],
                'nama_kolam' => $detail_pembagian_bibit->kolam->nama,
                'posisi_kolam' => $detail_pembagian_bibit->kolam->posisi,
                'nama_jaring' => $detail_pembagian_bibit->jaring == null ? null : $detail_pembagian_bibit->jaring->nama,
                'posisi_jaring' => $detail_pembagian_bibit->jaring == null ? null : $detail_pembagian_bibit->jaring->posisi,
            ]);
        }
        // exit();
        // Masukkan data ke dalam tabel produk
        $produk = [];
        foreach ($request->detail as $detail) {
            $pembagianBibit = DetailPembagianBibit::with(['header_pembagian_bibit.detail_beli.produk'])->where('id', $detail['id_detail_pembagian_bibit'])->first();

            if ($detail['status'] == 1) {
                if (isset($produk[$detail['id_detail_pembagian_bibit']])) {
                    $produk[$detail['id_detail_pembagian_bibit']]['quantity'] += $detail['quantity'];
                } else {
                    $produk[$detail['id_detail_pembagian_bibit']] = [
                        'nama' => $pembagianBibit->header_pembagian_bibit->detail_beli->produk->nama,
                        'quantity' => $detail['quantity'],
                        'id_kategori' => 3

                    ];
                }
            }
        }

        foreach ($produk as $item) {
            $produkExists = Produk::where('nama',  'LIKE ?', '%' . $item['nama'] . '%')->where('id_kategori', 3)->first();
            if ($produkExists) {
                DB::table('produk')->where('nama', 'LIKE ?', '%' . $item['nama'] . '%')->where('id_kategori', 3)->increment('quantity', $item['quantity']);
            } else {
                Produk::create($item);
            }
        }

        return response()->json([
            'success' => 'Berhasil Tambah Data'
        ]);
    }
}
