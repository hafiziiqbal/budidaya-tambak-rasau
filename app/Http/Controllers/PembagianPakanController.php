<?php

namespace App\Http\Controllers;

use App\Http\Requests\PembagianPakanRequest;
use App\Models\DetailBeli;
use App\Models\MasterTong;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\DetailPembagianPakan;
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

    public function show($id)
    {
        $produkPakan = DetailBeli::with('produk')
            ->whereHas('produk', function ($query) {
                $query->where('id_kategori', '=', 5);
            })->get();

        $tong =  DB::table('master_tong')
            ->select('master_tong.*')
            ->get();

        return view('pages.pembagian_pakan.show')->with([
            'title' => 'EDIT PEMBAGIAN PAKAN',
            'produkPakan' => $produkPakan,
            'tong' => $tong,
            'id' => $id,
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
                $query->where('id_kategori', '=', 5);
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

    public function store(PembagianPakanRequest $request)
    {
        if ($request->detail == null) {
            return response()->json([
                'errors' => [
                    'general' => "Detail pembagian tidak tersedia"
                ],
            ], 422);
        }
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
                    'errors' => [
                        'general' => "Quantity $namaProduk melebihi jumlah stok"
                    ],
                ], 422);
            }
        }

        $tongs = [];
        foreach ($request->detail as $detail) {
            $id_tong = $detail['id_tong'];
            if (in_array($id_tong, $tongs)) {
                return response()->json([
                    'errors' => [
                        'general' => 'Tong Tidak Bisa Digunakan Untuk Dua Pembagian'
                    ],
                ], 422);
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

    public function storeDetail(PembagianPakanRequest $request)
    {

        // return response()->json($request->all());
        // Mendapatkan data detail_beli berdasarkan id_detail_beli dari $request
        $detailBeli = DetailBeli::find($request->id_detail_beli);

        // Mengecek apakah quantity_stok pada tabel detail_beli lebih besar dari 0
        if ($detailBeli->quantity_stok <= 0) {
            return response()->json([
                'errors' => [
                    'general' => "Produk Pakan Sudah Tidak Ada Stok"
                ],
            ], 422);
        }

        // cek apakah id_tong sudah dipakai pada tabel detail_pembagian_bibit
        $count = DB::table('detail_pembagian_pakan')
            ->where('id_tong', $request->id_tong)
            ->count();

        if ($count > 0) {
            return response()->json([
                'errors' => [
                    'general' => "Tong Sudah Digunakan"
                ],
            ], 422);
        }

        // Hitung total quantity dari detail_pembagian_pakan yang menggunakan id_tong yang sama dengan yang diinputkan di request
        $total_quantity = DB::table('detail_pembagian_pakan')->where('id_detail_beli', $request->id_detail_beli)->sum('quantity');

        // Hitung total quantity yang akan terpakai (jumlah quantity di request ditambah dengan total quantity dari record yang sudah ada di tabel detail_pembagian_pakan)
        $total_quantity_to_use = $total_quantity + $request->quantity;
        // Cek apakah total_quantity_to_use melebihi column quantity_stok pada tabel detail_beli
        if ($total_quantity_to_use > (float)$detailBeli->quantity_stok) {
            // Jika melebihi, berikan respon quantity melebihi batas
            return response()->json([
                'errors' => [
                    'general' => "Quantity melebihi jumlah stok"
                ],
            ], 422);
        }

        $detailPembagianPakan = DetailPembagianPakan::create([
            'id_header_pembagian_pakan' => $request->id_header_pembagian_pakan,
            'id_detail_beli' => $request->id_detail_beli,
            'id_tong' => $request->id_tong,
            'quantity' => $request->quantity,
        ]);


        DB::table('detail_beli')
            ->where('id', $request->id_detail_beli)
            ->decrement('quantity_stok', $request->quantity);

        $id_produk = DB::table('detail_beli')
            ->where('id', $request->id_detail_beli)
            ->value('id_produk');

        DB::table('produk')
            ->where('id', $id_produk)
            ->decrement('quantity', $request->quantity);

        return response()->json([
            'success' => 'Berhasil Tambah Data',
            'save_detail' => true,
            'id' => $detailPembagianPakan->id
        ]);
    }

    public function edit($id)
    {
        $produkPakan = DetailBeli::with('produk')
            ->whereHas('produk', function ($query) {
                $query->where('id_kategori', '=', 5);
            })->get();

        $tong =  DB::table('master_tong')
            ->select('master_tong.*')
            ->get();

        return view('pages.pembagian_pakan.edit')->with([
            'title' => 'EDIT PEMBAGIAN PAKAN',
            'produkPakan' => $produkPakan,
            'tong' => $tong,
            'id' => $id,
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

    public function update(Request $request, $id)
    {
        $tglPembagian = date('Y-m-d', strtotime($request->tgl_pembagian));
        HeaderPembagianPakan::where('id', $id)->update([
            'tgl_pembagian_pakan' => $tglPembagian
        ]);

        return response()->json([
            'success' => 'Berhasil Tambah Data'
        ]);
    }

    public function updateDetail(PembagianPakanRequest $request, $id)
    {

        // Mendapatkan data detail_pembagian_pakan berdasarkan $id
        $detailPembagianPakan = DetailPembagianPakan::where('id', $id)->with('detail_beli.produk')->first();

        // Memeriksa apakah terdapat record lain pada tabel detail_pembagian_pakan dengan id_tong yang sama
        $duplicate = DetailPembagianPakan::where('id_tong', $request->id_tong)
            ->where('id', '!=', $id)
            ->first();

        if ($duplicate) {
            return response()->json([
                'errors' => [
                    'general' => 'Tong Tidak Bisa Digunakan Untuk Dua Pembagian'
                ],
            ], 422);
        }

        // Menghitung jumlah quantity dari tabel detail_pembagian_pakan dengan acuan id_tong yang sama dengan nilai yang diterima dari $request
        $totalQuantity = DetailPembagianPakan::where('id_detail_beli', $detailPembagianPakan->id_detail_beli)->sum('quantity');

        // Menambahkan jumlah quantity dari detail_pembagian_pakan yang sedang diupdate dengan nilai yang diterima dari $request
        $totalQuantity += $request->quantity - $detailPembagianPakan->quantity;

        $namaProduk = $detailPembagianPakan->detail_beli->produk->nama;
        // Memeriksa apakah jumlah quantity dari tabel detail_pembagian_pakan dengan acuan id_tong yang sama dengan nilai yang diterima dari $request melebihi jumlah column quantity_stok pada tabel detail_beli
        if ($totalQuantity > $detailPembagianPakan->detail_beli->quantity) {
            return response()->json([
                'errors' => [
                    'general' => "Quantity $namaProduk melebihi jumlah stok"
                ],
            ], 422);
        }

        // Memperbarui nilai column quantity pada detail_pembagian_pakan dengan nilai yang diterima dari $request
        $detailPembagianPakan->quantity = $request->quantity;
        $detailPembagianPakan->id_tong = $request->id_tong;

        // Menghitung selisih antara nilai column quantity pada detail_pembagian_pakan sebelum dan sesudah diupdate
        $quantityDiff = $request->quantity - $detailPembagianPakan->getOriginal('quantity');

        // Mendapatkan data detail_beli yang terkait dengan detail_pembagian_pakan
        $detailBeli = $detailPembagianPakan->detail_beli;

        // Memperbarui nilai column quantity_stok pada detail_beli
        $detailBeli->quantity_stok -= $quantityDiff;

        // Mendapatkan data produk yang terkait dengan detail_beli
        $produk = $detailBeli->produk;

        // Memperbarui nilai column quantity pada produk
        $produk->quantity -= $quantityDiff;

        // Menyimpan perubahan pada semua model yang telah diupdate
        $detailPembagianPakan->save();
        $detailBeli->save();
        $produk->save();

        return response()->json([
            'success' => 'Berhasil Ubah Data'
        ]);
    }

    public function destroy($id)
    {
        // Mencari semua record dari tabel detail_pembagian_pakan yang terkait dengan header_pembagian_pakan yang akan dihapus
        $details = DetailPembagianPakan::where('id_header_pembagian_pakan', $id)->with('detail_beli')
            ->get();

        // Memperbarui tabel detail_beli dan produk
        foreach ($details as $detail) {
            DB::table('detail_beli')
                ->where('id', $detail->id_detail_beli)
                ->increment('quantity_stok', $detail->quantity);

            DB::table('produk')
                ->where('id', $detail->detail_beli->id_produk)
                ->increment('quantity', $detail->quantity);
        }

        // Menghapus semua record dari tabel detail_pembagian_pakan yang terkait dengan header_pembagian_pakan yang akan dihapus
        DB::table('detail_pembagian_pakan')
            ->where('id_header_pembagian_pakan', $id)
            ->delete();

        // Menghapus record dari tabel header_pembagian_pakan yang sesuai dengan parameter $id
        DB::table('header_pembagian_pakan')
            ->where('id', $id)
            ->delete();

        return redirect()->route('pembagian.pakan')->with(
            'success',
            'Berhasil Hapus Pembagian Pakan'
        );
    }

    public function destroyDetail($id)
    {
        // Mendapatkan data detail_pembagian_pakan berdasarkan $id
        $detailPembagianPakan = DetailPembagianPakan::find($id);

        // Menyimpan nilai quantity dari detail_pembagian_pakan yang akan dihapus
        $quantity = $detailPembagianPakan->quantity;

        // Memperbarui nilai column quantity_stok pada tabel detail_beli
        $detailPembagianPakan->detail_beli->increment('quantity_stok', $quantity);

        // Memperbarui nilai column quantity pada tabel produk
        $detailPembagianPakan->detail_beli->produk->increment('quantity', $quantity);

        // Menghapus data detail_pembagian_pakan
        $detailPembagianPakan->delete();

        return response()->json([
            'success' => 'Data Berhasil di Hapus'
        ], 200);
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
