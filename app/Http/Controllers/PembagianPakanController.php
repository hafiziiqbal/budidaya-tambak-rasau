<?php

namespace App\Http\Controllers;

use App\Models\DetailBeli;
use App\Models\MasterTong;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\HeaderPembagianPakan;
use Yajra\DataTables\Facades\DataTables;

class PembagianPakanController extends Controller
{
    public function index()
    {
        return view('pages.pembagian_pakan.index')->with([
            'title' => 'PEMBAGIAN PAKAN',
        ]);
    }

    public function datatable(Request $request)
    {
        try {
            if ($request->ajax()) {
                $data = HeaderPembagianPakan::with([
                    'detail_pembagian_pakan' => function ($query) {
                        $query->with(['detail_beli' => function ($query) {
                            $query->with('produk');
                        }, 'tong']);
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
        $produkPakan = DetailBeli::with('produk')
            ->whereHas('produk', function ($query) {
                $query->where('id_kategori', '=', 1);
            })->where('quantity_stok', '>', '0')->get();

        $tong =  DB::table('master_tong')
            ->leftJoin('detail_pembagian_pakan', 'master_tong.id', '=', 'detail_pembagian_pakan.id_tong')
            ->select('master_tong.*')
            ->whereNull('detail_pembagian_pakan.id_tong')
            ->get();


        return view('pages.pembagian_pakan.create')->with([
            'title' => 'PEMBAGIAN PAKAN',
            'produkPakan' => $produkPakan,
            'tong' => $tong
        ]);
    }


    public function store(Request $request)
    {
        // return response()->json($request->id_detail_beli);
        $tglPembagian = date('Y-m-d', strtotime($request->tgl_pembagian));
        // return response()->json($request->all());

        // cek quantity -------------------------------------------------------
        $detail = $request->detail;
        // Buat array kosong untuk menyimpan hasil pengolahan
        $detailTemp = [];

        // Looping array detail
        foreach ($detail as $item) {
            // Jika id_detail belum ada di array result, tambahkan sebagai item baru
            if (!isset($detailTemp[$item['id_detail_beli']])) {
                $detailTemp[$item['id_detail_beli']] = $item['quantity'];
            } else {
                // Jika id_detail sudah ada di array result, tambahkan jumlah quantity nya
                $detailTemp[$item['id_detail_beli']] += $item['quantity'];
            }
        }

        // Looping array result untuk membandingkan dengan quantity_stok dari tabel detail_beli
        foreach ($detailTemp as $id_detail_beli => $quantity) {
            $detail_beli = DetailBeli::where('id', $id_detail_beli)->with('produk')->first();
            $quantity_stok = $detail_beli ? $detail_beli->quantity_stok : 0;
            $namaProduk = $detail_beli ? $detail_beli->produk->nama : 'null';

            // Membandingkan quantity dengan quantity_stok
            if ($quantity > $quantity_stok) {
                return response()->json([
                    'error' => "Quantity $namaProduk melebihi jumlah stok"
                ]);
            }
        }

        $tongs = [];
        foreach ($request->detail as $detail) {
            $id_tong = $detail['id_tong'];
            if (in_array($id_tong, $tongs)) {
                return response()->json(['error' => 'Tong Tidak Bisa Digunakan Untuk Dua Pembagian']);
            }
            $tongs[] = $id_tong;
        }

        // SAVE
        // Memasukkan data dari $request ke dalam tabel detail_pembagian_pakan
        $headerPembagian = HeaderPembagianPakan::create([
            'tgl_pembagian_pakan' => $tglPembagian
        ]);

        foreach ($request->detail as $detail) {
            DB::table('detail_pembagian_pakan')->insert([
                'id_header_pembagian_pakan' => $headerPembagian->id,
                'id_detail_beli' => $detail['id_detail_beli'],
                'id_tong' => $detail['id_tong'],
                'quantity' => $detail['quantity'],
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }

        // Melakukan update pada nilai record quantity_stok di tabel detail_beli
        foreach ($request->detail as $detail) {
            DB::table('detail_beli')
                ->where('id', $detail['id_detail_beli'])
                ->decrement('quantity_stok', $detail['quantity']);
        }

        // Melakukan update pada nilai record quantity di tabel produk
        foreach ($request->detail as $detail) {
            $id_produk = DB::table('detail_beli')
                ->where('id', $detail['id_detail_beli'])
                ->value('id_produk');

            DB::table('produk')
                ->where('id', $id_produk)
                ->decrement('quantity', $detail['quantity']);
        }

        return response()->json([
            'success' => 'Berhasil Tambah Data'
        ]);
    }

    public function edit($id)
    {
        $produkPakan = DetailBeli::with('produk')
            ->whereHas('produk', function ($query) {
                $query->where('id_kategori', '=', 1);
            })->get();

        $tong =  DB::table('master_tong')
            ->select('master_tong.*')
            ->get();


        return view('pages.pembagian_pakan.edit')->with([
            'title' => 'EDIT PEMBAGIAN PAKAN',
            'produkPakan' => $produkPakan,
            'tong' => $tong
        ]);
    }

    public function editJson($id)
    {
        $data = HeaderPembagianPakan::with([
            'detail_pembagian_pakan' => function ($query) {
                $query->with(['detail_beli' => function ($query) {
                    $query->with('produk');
                }, 'tong']);
            }
        ])->where('id', $id)->first();

        return response()->json($data);
    }

    public function contoh()
    {
        $data = HeaderPembagianPakan::with([
            'detail_pembagian_pakan' => function ($query) {
                $query->with(['detail_beli' => function ($query) {
                    $query->with('produk');
                }, 'tong']);
            }
        ])->orderBy('updated_at', 'desc')->get();
        return response()->json(
            $data
        );
    }
}
