<?php

namespace App\Http\Controllers;

use App\Http\Requests\PembagianBibitRequest;
use App\Models\Produk;
use App\Models\DetailBeli;
use App\Models\DetailPanen;
use App\Models\MasterKolam;
use App\Models\MasterJaring;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\DetailPembagianBibit;
use App\Models\DetailPemberianPakan;
use App\Models\HeaderPembagianBibit;
use Yajra\DataTables\Facades\DataTables;

class PembagianBibitController extends Controller
{
    public function index()
    {
        return view('pages.pembagian_bibit.index')->with([
            'title' => 'PEMBAGIAN BIBIT',
            'pekerjaan_toogle' => 1
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

    public function datatableDetail(Request $request)
    {
        try {
            if ($request->ajax()) {
                $data = DetailPembagianBibit::with(['header_pembagian_bibit.detail_beli.produk', 'jaring', 'jaring_old', 'kolam', 'detail_panen.detail_jual', 'detail_pemberian_pakan'])->orderBy('updated_at', 'desc')->get();
                foreach ($data as $key => $value) {
                    // Menghitung total quantity dari ketiga tabel
                    $totalQuantity = $value->detail_panen->sum(function ($panen) {
                        return $panen->quantity + $panen->detail_jual->sum('quantity');
                    }) + $value->quantity;
                    $value['quantity_awal'] = $totalQuantity;
                }
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
        $jaring = MasterJaring::where('id_kolam', null)->get();
        $kolam = MasterKolam::all();
        $detailSortir = DetailPanen::where('status', 0)->where('quantity', '>', '0')->with(['header_panen', 'detail_pembagian_bibit.header_pembagian_bibit.detail_beli.produk'])->get();

        $pembelian = DetailBeli::select(['id', 'id_header_beli', 'id_produk', 'updated_at', 'quantity', 'quantity_stok'])->with(['produk' => function ($query) {
            $query->select('id', 'nama', 'quantity');
        }, 'header_beli' => function ($query) {
            $query->select('id', 'tgl_beli');
        }])->whereHas('produk', function ($query) {
            $query->where('id_kategori', '=', 7);
        })->where('quantity_stok', '>', 0)->get();

        return view('pages.pembagian_bibit.create')->with([
            'title' => 'PEMBAGIAN BIBIT',
            'jaring' => $jaring,
            'kolam' => $kolam,
            'sortir' => $detailSortir,
            'pembelian' => $pembelian,
            'pekerjaan_toogle' => 1

        ]);
    }

    public function store(PembagianBibitRequest $request)
    {
        // jika detail null
        if ($request->detail == null) {
            return response()->json([
                'errors' => [
                    'general' => "Detail pembagian tidak tersedia"
                ],
            ], 422);
        }

        $tglPembagian = date('Y-m-d', strtotime($request->tgl_pembagian));

        // cek jaring -------------------------------------------------------
        foreach ($request->detail as $key => $valueArray) {
            $value = (object) $valueArray;
            $kolam = MasterKolam::find($value->id_kolam);
            $jumlahKolamJaring = DB::table('detail_pembagian_bibit')
                ->selectRaw('COUNT(CASE WHEN id_kolam IS NOT NULL THEN 1 END) AS kolam_count')
                ->selectRaw('COUNT(CASE WHEN id_jaring IS NOT NULL THEN 1 END) AS jaring_count')
                ->selectRaw('COUNT(CASE WHEN id_jaring_old IS NOT NULL THEN 1 END) AS jaring_old_count')
                ->where('id_kolam', $value->id_kolam)
                ->first();

            $batasKolam = $jumlahKolamJaring->kolam_count - $jumlahKolamJaring->jaring_old_count;
            $batasJaring = $jumlahKolamJaring->kolam_count - $jumlahKolamJaring->jaring_count;

            if ($batasJaring >= 1 && $value->id_jaring == null && $batasKolam > 1) {
                return response()->json([
                    'errors' => [
                        'general' => "$kolam->nama Sudah Penuh, Silahkan Tambah Jaring Untuk Menggunakan"
                    ],
                ], 422);
            }

            if ($value->id_jaring != null) {
                $jaring = MasterJaring::find($value->id_jaring);
                if ($jaring->id_kolam != null) {
                    return response()->json([
                        'errors' => [
                            'general' => "Jaring Sudah Digunakan Oleh Data Lain"
                        ],
                    ], 422);
                }
            }
        }
        // end cek jaring -------------------------------------------------------

        // cek quantity ----------------------------------------------------------
        // hitung total quantity dari request
        $totalQuantity = collect($request->all()['detail'])->sum('quantity');

        // ambil quantity_stok dari tabel pembelian berdasarkan id produk
        $quantityStok = DetailBeli::find($request->id_detail_beli)->quantity_stok;

        // jika total quantity melebihi quantity_stok
        if ($totalQuantity > $quantityStok) {
            return response()->json([
                'errors' => [
                    'general' =>   'Total quantity melebihi quantity stok yang ada'
                ],
            ], 422);
        }
        // cek quantity ----------------------------------------------------------

        // save
        $headBagi =  HeaderPembagianBibit::create([
            'tgl_pembagian' => $tglPembagian,
            'id_detail_beli' => $request->id_detail_beli,
        ]);

        foreach ($request->detail as $key => $valueArray) {
            $value = (object) $valueArray;
            DetailPembagianBibit::create([
                'id_header_pembagian_bibit' => $headBagi->id,
                'quantity' => $value->quantity,
                'id_jaring' => $value->id_jaring,
                'id_kolam' => $value->id_kolam
            ]);

            // Kurangi nilai quantity_stok pada tabel pembelian
            $detailBeli = DetailBeli::find($headBagi->id_detail_beli);
            $detailBeli->update([
                'quantity_stok' => DB::raw("quantity_stok-" . $value->quantity),
            ]);

            // Kurangi nilai quantity pada tabel produk
            Produk::find($detailBeli->id_produk)->update([
                'quantity' => DB::raw("quantity-" . $value->quantity),
            ]);

            if ($value->id_jaring != null) {
                $jaring = MasterJaring::find($value->id_jaring);
                if ($jaring->id_kolam == null) {
                    $jaring->update([
                        'id_kolam' => $value->id_kolam
                    ]);
                }
            }
        }

        return response()->json([
            'success' => 'Berhasil Tambah Data'
        ]);
    }

    public function storeSortir(PembagianBibitRequest $request)
    {
        // jika detail null
        if ($request->detail == null) {
            return response()->json([
                'errors' => [
                    'general' => "Detail pembagian tidak tersedia"
                ],
            ], 422);
        }

        // return response()->json($request->id_detail_beli);
        $tglPembagian = date('Y-m-d', strtotime($request->tgl_pembagian));

        // cek jaring -------------------------------------------------------
        foreach ($request->detail as $key => $valueArray) {
            $value = (object) $valueArray;
            $kolam = MasterKolam::find($value->id_kolam);
            $jumlahKolamJaring = DB::table('detail_pembagian_bibit')
                ->selectRaw('COUNT(CASE WHEN id_kolam IS NOT NULL THEN 1 END) AS kolam_count')
                ->selectRaw('COUNT(CASE WHEN id_jaring IS NOT NULL THEN 1 END) AS jaring_count')
                ->selectRaw('COUNT(CASE WHEN id_jaring_old IS NOT NULL THEN 1 END) AS jaring_old_count')
                ->where('id_kolam', $value->id_kolam)
                ->first();

            $batasKolam = $jumlahKolamJaring->kolam_count - $jumlahKolamJaring->jaring_old_count;
            $batasJaring = $jumlahKolamJaring->kolam_count - $jumlahKolamJaring->jaring_count;

            if ($batasJaring >= 1 && $value->id_jaring == null && $batasKolam > 1) {
                return response()->json([
                    'errors' => [
                        'general' => "$kolam->nama Sudah Penuh, Silahkan Tambah Jaring Untuk Menggunakan"
                    ],
                ], 422);
            }

            if ($value->id_jaring != null) {
                $jaring = MasterJaring::find($value->id_jaring);
                if ($jaring->id_kolam != null) {
                    return response()->json([
                        'errors' => [
                            'general' => "Jaring Sudah Digunakan Oleh Data Lain"
                        ],
                    ], 422);
                }
            }
        }
        // end cek jaring -------------------------------------------------------

        // cek quantity ----------------------------------------------------------
        // hitung total quantity dari request
        $totalQuantity = collect($request->all()['detail'])->sum('quantity');

        // ambil quantity_stok dari tabel pembelian berdasarkan id produk
        $quantityStok = DetailPanen::find($request->id_detail_panen)->quantity;

        // jika total quantity melebihi quantity_stok
        if ($totalQuantity > $quantityStok) {
            return response()->json([
                'errors' => [
                    'general' => 'Total quantity melebihi quantity sortir yang ada'
                ],
            ], 422);
        }
        // cek quantity ----------------------------------------------------------
        $detailPanen = DetailPanen::where('id', $request->id_detail_panen)->with(['detail_pembagian_bibit.header_pembagian_bibit.detail_beli'])->first();
        // save
        $headBagi =  HeaderPembagianBibit::create([
            'tgl_pembagian' => $tglPembagian,
            'id_detail_beli' => $detailPanen->detail_pembagian_bibit->header_pembagian_bibit->id_detail_beli,
            'id_detail_panen' => $request->id_detail_panen,
        ]);

        foreach ($request->detail as $key => $valueArray) {
            $value = (object) $valueArray;
            DetailPembagianBibit::create([
                'id_header_pembagian_bibit' => $headBagi->id,
                'quantity' => $value->quantity,
                'id_jaring' => $value->id_jaring,
                'id_kolam' => $value->id_kolam
            ]);

            // Kurangi nilai quantity_stok pada tabel pembelian           
            $detailPanen->update([
                'quantity' => DB::raw("quantity-" . $value->quantity),
            ]);

            if ($value->id_jaring != null) {
                $jaring = MasterJaring::find($value->id_jaring);
                if ($jaring->id_kolam == null) {
                    $jaring->update([
                        'id_kolam' => $value->id_kolam
                    ]);
                }
            }
        }

        return response()->json([
            'success' => 'Berhasil Tambah Data'
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
            'pekerjaan_toogle' => 1
        ]);
    }

    public function update(Request $request, $id)
    {
        try {
            $tglBeli = date('Y-m-d', strtotime($request->tgl_pembagian));
            $headerPembagianBibit = HeaderPembagianBibit::find($id);

            $headerPembagianBibit->update([
                'tgl_pembagian' => $tglBeli,
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

    public function updateDetail(PembagianBibitRequest $request, $id)
    {
        $panen = DetailPanen::where('id_detail_pembagian_bibit', $id)->first();
        if ($panen) {
            return response()->json([
                'errors' => [
                    'general' => 'Data ini sudah dipanen'
                ],
            ], 422);
        }

        // cek jaring dan kolam
        $kolam = MasterKolam::find($request->id_kolam);

        $jumlahKolamJaring = DB::table('detail_pembagian_bibit')
            ->selectRaw('COUNT(CASE WHEN id_kolam IS NOT NULL THEN 1 END) AS kolam_count')
            ->selectRaw('COUNT(CASE WHEN id_jaring IS NOT NULL THEN 1 END) AS jaring_count')
            ->selectRaw('COUNT(CASE WHEN id_jaring_old IS NOT NULL THEN 1 END) AS jaring_old_count')
            ->where('id_kolam', $request->id_kolam)
            ->first();

        $cekJaringDetailBibit = DB::table('detail_pembagian_bibit')->where('id', $id)->first()->id_jaring;
        if ($cekJaringDetailBibit != null) {
            $batasJaring = $jumlahKolamJaring->kolam_count - $jumlahKolamJaring->jaring_count;
            $batasKolam = $jumlahKolamJaring->kolam_count - $jumlahKolamJaring->jaring_old_count;
            if ($batasJaring >= 1 && $request->id_jaring == null && $batasKolam > 1) {
                if ($request->detail == null) {
                    return response()->json([
                        'errors' => [
                            'general' => "$kolam->nama Sudah Penuh, Silahkan Tambah Jaring Untuk Menggunakan"
                        ],
                    ], 422);
                }
            }
        }

        if ($request->id_jaring != null) {
            $jaring = MasterJaring::find($request->id_jaring);
            if ($jaring->id_kolam != null && $jaring->id != $request->id_jaring) {
                return response()->json([
                    'errors' => [
                        'general' => "Jaring Sudah Digunakan Oleh Data Lain"
                    ],
                ], 422);
            }
        }
        // cek quantity
        $detailPembagianTanpaDataUpdate = DetailPembagianBibit::where('id_header_pembagian_bibit', $request->id_header_pembagian_bibit)->where('id', '!=', $id)->get();
        $totalQuantityDetailPembagian =  collect($detailPembagianTanpaDataUpdate)->sum('quantity') + $request->quantity;
        $headerPembagian = HeaderPembagianBibit::find($request->id_header_pembagian_bibit);
        if ($headerPembagian->id_detail_panen == null) {
            $detailBeli = DetailBeli::find($headerPembagian->id_detail_beli);
            $quantityStokOld = $detailBeli->quantity_stok;
            // jika total quantity melebihi quantity_stok
            if ($totalQuantityDetailPembagian > $detailBeli->quantity) {
                return response()->json([
                    'errors' => [
                        'general' => 'Total quantity melebihi quantity stok yang ada'
                    ],
                ], 422);
            }

            $detailBeli->update([
                'quantity_stok' => $detailBeli->quantity - $totalQuantityDetailPembagian,
            ]);

            $produk = Produk::find($detailBeli->id_produk);
            $quantityProduk = ($produk->quantity - $quantityStokOld) + $detailBeli->quantity_stok;
            $produk->update([
                'quantity' => $quantityProduk,
            ]);
        } else {
            $detailPanen = DetailPanen::find($headerPembagian->id_detail_panen);
            $quantityDetailPanenNow = $detailPanen->quantity;
            $quantityDetailPanenThen = DetailPembagianBibit::where('id_header_pembagian_bibit', $request->id_header_pembagian_bibit)->get()->sum('quantity') + $quantityDetailPanenNow;

            // jika total quantity melebihi quantity_stok
            if ($totalQuantityDetailPembagian > $quantityDetailPanenThen) {
                return response()->json([
                    'errors' => [
                        'general' => 'Total quantity melebihi quantity stok yang ada'
                    ],
                ], 422);
            }

            $detailPanen->update([
                'quantity' => $quantityDetailPanenThen - $totalQuantityDetailPembagian,
            ]);
        }



        DetailPembagianBibit::find($id)->update([
            'quantity' => $request->quantity,
            'id_jaring' => $request->id_jaring,
            'id_kolam' => $request->id_kolam
        ]);

        if ($request->id_jaring != null) {
            $jaring = MasterJaring::find($request->id_jaring);

            $jaring->update([
                'id_kolam' => $request->id_kolam
            ]);
        }
        return response()->json([
            'success' => 'Berhasil Ubah Data'
        ]);
    }

    public function storeDetail(PembagianBibitRequest $request)
    {
        // cek jaring -------------------------------------------------------
        $kolam = MasterKolam::find($request->id_kolam);
        $jumlahKolamJaring = DB::table('detail_pembagian_bibit')
            ->selectRaw('COUNT(CASE WHEN id_kolam IS NOT NULL THEN 1 END) AS kolam_count')
            ->selectRaw('COUNT(CASE WHEN id_jaring IS NOT NULL THEN 1 END) AS jaring_count')
            ->selectRaw('COUNT(CASE WHEN id_jaring_old IS NOT NULL THEN 1 END) AS jaring_old_count')
            ->where('id_kolam', $request->id_kolam)
            ->first();

        $batasKolam = $jumlahKolamJaring->kolam_count - $jumlahKolamJaring->jaring_old_count;
        $batasJaring = $jumlahKolamJaring->kolam_count - $jumlahKolamJaring->jaring_count;
        if ($batasJaring >= 1 && $request->id_jaring == null && $batasKolam > 1) {
            return response()->json([
                'errors' => [
                    'general' => "$kolam->nama Sudah Penuh, Silahkan Tambah Jaring Untuk Menggunakan"
                ],
            ], 422);
        }

        if ($request->id_jaring != null) {
            $jaring = MasterJaring::find($request->id_jaring);
            if ($jaring->id_kolam != null) {
                return response()->json([
                    'errors' => [
                        'general' => "Jaring Sudah Digunakan Oleh Data Lain"
                    ],
                ], 422);
            }
        }
        // end cek jaring -------------------------------------------------------

        // cek quantity
        $detailPembagianTanpaDataUpdate = DetailPembagianBibit::where('id_header_pembagian_bibit', $request->id_header_pembagian_bibit)->get();
        $totalQuantityDetailPembagian =  collect($detailPembagianTanpaDataUpdate)->sum('quantity') + $request->quantity;
        $headerPembagian = HeaderPembagianBibit::find($request->id_header_pembagian_bibit);

        if ($headerPembagian->id_detail_panen == null) {
            $detailBeli = DetailBeli::find($headerPembagian->id_detail_beli);
            $quantityStokOld = $detailBeli->quantity_stok;
            // jika total quantity melebihi quantity_stok
            if ($totalQuantityDetailPembagian > $detailBeli->quantity) {
                return response()->json([
                    'errors' => [
                        'general' => 'Total quantity melebihi quantity stok yang ada'
                    ],
                ], 422);
            }

            // save
            $detailPembagianBibit = DetailPembagianBibit::create([
                'id_header_pembagian_bibit' => $request->id_header_pembagian_bibit,
                'quantity' => $request->quantity,
                'id_jaring' => $request->id_jaring,
                'id_kolam' => $request->id_kolam
            ]);


            $detailBeli->update([
                'quantity_stok' => $detailBeli->quantity - $totalQuantityDetailPembagian,
            ]);

            $produk = Produk::find($detailBeli->id_produk);
            $quantityProduk = ($produk->quantity - $quantityStokOld) + $detailBeli->quantity_stok;
            $produk->update([
                'quantity' => $quantityProduk,
            ]);
        } else {
            // ambil quantity_stok dari tabel pembelian berdasarkan id produk
            $detailPanen = DetailPanen::find($headerPembagian->id_detail_panen);
            $quantityDetailPanenNow = $detailPanen->quantity;
            $quantityDetailPanenThen = DetailPembagianBibit::where('id_header_pembagian_bibit', $request->id_header_pembagian_bibit)->get()->sum('quantity') + $quantityDetailPanenNow;

            // jika total quantity melebihi quantity_stok
            if ($totalQuantityDetailPembagian > $quantityDetailPanenThen) {
                return response()->json([
                    'errors' => [
                        'general' => 'Total quantity melebihi quantity stok yang ada'
                    ],
                ], 422);
            }

            // save
            $detailPembagianBibit = DetailPembagianBibit::create([
                'id_header_pembagian_bibit' => $request->id_header_pembagian_bibit,
                'quantity' => $request->quantity,
                'id_jaring' => $request->id_jaring,
                'id_kolam' => $request->id_kolam
            ]);

            $detailPanen->update([
                'quantity' => $quantityDetailPanenThen - $totalQuantityDetailPembagian,
            ]);
        }

        if ($request->id_jaring != null) {
            $jaring = MasterJaring::find($request->id_jaring);
            $jaring->update([
                'id_kolam' => $request->id_kolam
            ]);
        }

        return response()->json([
            'sukses' => 'Berhasil Tambah Data',
            'save_detail' => true,
            'id' => $detailPembagianBibit->id
        ]);
    }

    public function edit($id)
    {
        $jaring = MasterJaring::all();
        $kolam = MasterKolam::select(['id', 'nama'])->get();
        $detailSortir = DetailPanen::where('status', 0)->with(['header_panen', 'detail_pembagian_bibit.header_pembagian_bibit.detail_beli.produk'])->get();
        $pembelian = DetailBeli::select(['id', 'id_header_beli', 'id_produk', 'updated_at', 'quantity'])->with([
            'produk', 'header_beli'
        ])->orderBy('id_header_beli', 'asc')->get();


        return view('pages.pembagian_bibit.edit')->with([
            'title' => 'EDIT PEMBAGIAN BIBIT',
            'jaring' => $jaring,
            'kolam' => $kolam,
            'id' => $id,
            'sortir' => $detailSortir,
            'pembelian' => $pembelian,
            'pekerjaan_toogle' => 1
        ]);
    }

    public function editJson($id)
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

        return response()->json($data);
    }

    public function destroy($id)
    {
        $details = DB::table('detail_pembagian_bibit')
            ->where('id_header_pembagian_bibit', $id)
            ->get();

        foreach ($details as $key => $value) {
            $pemberianPakan = DetailPemberianPakan::where('id_detail_pembagian_bibit', $value->id)->first();
            if ($pemberianPakan) {
                return redirect()->route('pembagian.bibit')->withErrors(
                    [
                        'error' => "Data ini digunakan oleh tabel pemberian"
                    ]
                );
            }

            $panen = DetailPanen::where('id_detail_pembagian_bibit', $value->id)->first();
            if ($panen) {
                return redirect()->route('pembagian.bibit')->withErrors(
                    [
                        'error' => "Bibit ini sudah dipanen"
                    ]
                );
            }
        }


        $headerPembagianBibit = HeaderPembagianBibit::where('id', $id)->with('detail_beli')->first();

        // Memperbarui tabel detail_beli dan produk
        foreach ($details as $detail) {
            $detailPembagianBibit = DetailPembagianBibit::find($detail->id);
            if ($headerPembagianBibit->id_detail_panen == null) {
                DB::table('detail_beli')
                    ->where('id', $headerPembagianBibit->id_detail_beli)
                    ->increment('quantity_stok', $detail->quantity);

                DB::table('produk')
                    ->where('id', $headerPembagianBibit->detail_beli->id_produk)
                    ->increment('quantity', $detail->quantity);
            } else {
                DB::table('detail_panen')
                    ->where('id', $headerPembagianBibit->id_detail_panen)
                    ->increment('quantity', $detail->quantity);
            }
        }

        // Mengubah column id_kolam pada tabel jaring menjadi null
        DB::table('master_jaring')
            ->whereIn('id', $details->pluck('id_jaring'))
            ->update(['id_kolam' => null]);

        // // Menghapus semua record dari tabel detail_pembagian_bibit yang terkait dengan header_pembagian_bibit yang akan dihapus
        DB::table('detail_pembagian_bibit')
            ->where('id_header_pembagian_bibit', $id)
            ->delete();

        // Menghapus record dari tabel header_pembagian_bibit yang sesuai dengan parameter $id
        DB::table('header_pembagian_bibit')
            ->where('id', $id)
            ->delete();

        return redirect()->route('pembagian.bibit')->with(
            'success',
            'Berhasil Hapus Pembagian Bibit'
        );
    }

    public function destroyDetail($id)
    {
        $panen = DetailPanen::where('id_detail_pembagian_bibit', $id)->first();
        if ($panen) {
            return response()->json([
                'errors' => [
                    'general' => 'Data ini sudah dipanen'
                ],
            ], 422);
        }

        $pemberianPakan = DetailPemberianPakan::where('id_detail_pembagian_bibit', $id)->first();
        if ($pemberianPakan) {
            return response()->json([
                'errors' => [
                    'general' => 'Data ini digunakan oleh tabel pemberian'
                ],
            ], 422);
        }
        // cek quantity
        $detailPembagianBibit = DetailPembagianBibit::find($id);
        $detailPembagianTanpaDataUpdate = DetailPembagianBibit::where('id_header_pembagian_bibit', $detailPembagianBibit->id_header_pembagian_bibit)->where('id', '!=', $id)->get();
        $totalQuantityDetailPembagian =  collect($detailPembagianTanpaDataUpdate)->sum('quantity');
        $headerPembagian = HeaderPembagianBibit::find($detailPembagianBibit->id_header_pembagian_bibit);

        if ($headerPembagian->id_detail_panen == null) {
            $detailBeli = DetailBeli::find($headerPembagian->id_detail_beli);
            $quantityStokOld = $detailBeli->quantity_stok;

            $detailBeli->update([
                'quantity_stok' => $detailBeli->quantity - $totalQuantityDetailPembagian,
            ]);

            $produk = Produk::find($detailBeli->id_produk);
            $quantityProduk = ($produk->quantity - $quantityStokOld) + $detailBeli->quantity_stok;
            $produk->update([
                'quantity' => $quantityProduk,
            ]);
        } else {
            $detailPanen = DetailPanen::find($headerPembagian->id_detail_panen);
            $quantityDetailPanenNow = $detailPanen->quantity;

            $detailPanen->update([
                'quantity' => $quantityDetailPanenNow + $detailPembagianBibit->quantity,
            ]);
        }

        if ($detailPembagianBibit->id_jaring != null) {
            $jaring = MasterJaring::find($detailPembagianBibit->id_jaring);
            $jaring->update([
                'id_kolam' => null
            ]);
        }

        $detailPembagianBibit->delete();


        return response()->json([
            'success' => 'Data Berhasil di Hapus'
        ], 200);
    }

    public function contoh()
    {
        $data = DetailPembagianBibit::with(['header_pembagian_bibit.detail_beli.produk', 'jaring', 'jaring_old', 'kolam', 'detail_panen.detail_jual'])->orderBy('updated_at', 'desc')->get();
        foreach ($data as $key => $value) {
            // Menghitung total quantity dari ketiga tabel
            $totalQuantity = $value->detail_panen->sum(function ($panen) {
                return $panen->quantity + $panen->detail_jual->sum('quantity');
            }) + $value->quantity;
            $value['quantity_awal'] = $totalQuantity;
        }

        return response()->json(
            $data
        );
    }
}
