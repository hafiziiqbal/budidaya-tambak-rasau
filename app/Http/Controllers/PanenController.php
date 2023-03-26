<?php

namespace App\Http\Controllers;

use stdClass;
use App\Models\Produk;
use App\Models\DetailPanen;
use App\Models\HeaderPanen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\PanenRequest;
use App\Models\DetailPembagianBibit;
use Yajra\DataTables\Facades\DataTables;

class PanenController extends Controller
{
    public function index()
    {
        return view('pages.panen.index')->with([
            'title' => 'PANEN',
        ]);
    }

    public function datatable(Request $request)
    {
        try {
            if ($request->ajax()) {
                $data = HeaderPanen::with(['detail_panen.detail_pembagian_bibit.header_pembagian_bibit.detail_beli.produk'])->orderBy('updated_at', 'desc')->get();
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
        $pembagianBibit = DetailPembagianBibit::with(['header_pembagian_bibit.detail_beli.produk', 'kolam', 'jaring'])
            ->where('quantity', '>', '0')->get();

        // return response()->json($pembagianBibit);


        return view('pages.panen.create')->with([
            'title' => 'PANEN',
            'pembagianBibit' => $pembagianBibit
        ]);
    }

    public function store(PanenRequest $request)
    {
        // jika detail null
        if ($request->detail == null) {
            return response()->json([
                'errors' => [
                    'general' => "Detail panen tidak tersedia"
                ],
            ], 422);
        }
        // return response()->json($request->all());
        $tglPanen = date('Y-m-d', strtotime($request->tgl_panen));
        $details = $request->detail;

        // loop through each detail and calculate total quantity based on id_detail_pembagian_bibit

        $totalQuantities = [];
        $listKey = [];
        foreach ($details as $key => $detail) {

            $idDetail = $detail['id_detail_pembagian_bibit'];
            $quantity = $detail['quantity'];
            $listKey[$idDetail] = $key;

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
                    'errors' => [
                        "detail.$idDetail.quantity-all" => "Total Quantity tadak boleh lebih dari $detailPembagianBibit->quantity"
                    ],
                ], 422);
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

            if ($detail_pembagian_bibit->quantity <= 0) {
                if ($detail_pembagian_bibit->id_jaring != null) {
                    DB::table('master_jaring')->where('id',  $detail_pembagian_bibit->id_jaring)->update([
                        'id_kolam' => null
                    ]);
                }
                $detail_pembagian_bibit->id_jaring_old = $detail_pembagian_bibit->id_jaring;
                $detail_pembagian_bibit->id_jaring = null;
                $detail_pembagian_bibit->save();
            }
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
                        'id_kategori' => 6

                    ];
                }
            }
        }

        foreach ($produk as $item) {
            $produkExists = Produk::where('nama',  $item['nama'])->where('id_kategori', 6)->first();
            if ($produkExists) {
                DB::table('produk')->where('nama',  $item['nama'])->where('id_kategori', 6)->increment('quantity', $item['quantity']);
            } else {
                Produk::create($item);
            }
        }

        return response()->json([
            'success' => 'Berhasil Tambah Data'
        ]);
    }

    public function storeDetail(PanenRequest $request)
    {
        // validate total quantity against detail_pembagian_bibit
        $detailPembagianBibit = DB::table('detail_pembagian_bibit')->where('id', $request->id_detail_pembagian_bibit)->first();
        if ($detailPembagianBibit->quantity == 0) {
            return response()->json([
                'errors' => [
                    'general' => "Pembagian Bibit Sudah Habis Di Panen"
                ],
            ], 422);
        }
        if ($request->quantity > $detailPembagianBibit->quantity) {
            return response()->json([
                'errors' => [
                    'general' => "Total Quantity Melebihi Sisa Jumlah Pembagian"
                ],
            ], 422);
        }

        // Kurangi quantity pada tabel detail_pembagian_bibit sesuai dengan quantity pada request
        $id_detail_pembagian_bibit = $request->id_detail_pembagian_bibit;
        $quantity = $request->quantity;

        $detail_pembagian_bibit = DetailPembagianBibit::where('id', $id_detail_pembagian_bibit)->with(['kolam', 'jaring', 'header_pembagian_bibit.detail_beli.produk'])->first();
        $detail_pembagian_bibit->quantity -= $quantity;
        $detail_pembagian_bibit->save();

        $detailPanen = DetailPanen::create([
            'id_header_panen' => $request->id_header_panen,
            'status' => $request->status,
            'quantity' => $quantity,
            'id_detail_pembagian_bibit' => $request->id_detail_pembagian_bibit,
            'nama_kolam' => $detail_pembagian_bibit->kolam->nama,
            'posisi_kolam' => $detail_pembagian_bibit->kolam->posisi,
            'nama_jaring' => $detail_pembagian_bibit->jaring == null ? null : $detail_pembagian_bibit->jaring->nama,
            'posisi_jaring' => $detail_pembagian_bibit->jaring == null ? null : $detail_pembagian_bibit->jaring->posisi,
        ]);

        // Masukkan data ke dalam tabel produk
        $pembagianBibit = DetailPembagianBibit::with(['header_pembagian_bibit.detail_beli.produk'])->where('id',  $id_detail_pembagian_bibit)->first();
        if ($pembagianBibit->quantity <= 0) {
            if ($detail_pembagian_bibit->id_jaring != null) {
                DB::table('master_jaring')->where('id',  $detail_pembagian_bibit->id_jaring)->update([
                    'id_kolam' => null
                ]);
            }
            $pembagianBibit->id_jaring_old = $pembagianBibit->id_jaring;
            $pembagianBibit->id_jaring = null;
            $pembagianBibit->save();
        }

        $produkExists = Produk::where('nama',  $pembagianBibit->header_pembagian_bibit->detail_beli->produk->nama)->where('id_kategori', 6)->first();
        if ($produkExists) {
            DB::table('produk')->where('nama',  $pembagianBibit->header_pembagian_bibit->detail_beli->produk->nama)->where('id_kategori', 6)->increment('quantity', $quantity);
        } else {
            Produk::create([
                'nama' => $pembagianBibit->header_pembagian_bibit->detail_beli->produk->nama,
                'quantity' => $quantity,
                'id_kategori' => 3
            ]);
        }

        return response()->json([
            'sukses' => 'Berhasil Tambah Data',
            'save_detail' => true,
            'id' => $detailPanen->id
        ]);
    }

    public function edit($id)
    {

        $pembagianBibit = DetailPembagianBibit::with(['header_pembagian_bibit.detail_beli.produk', 'kolam', 'jaring', 'jaring_old'])->get();


        return view('pages.panen.edit')->with([
            'title' => 'EDIT PANEN',
            'pembagianBibit' => $pembagianBibit,
            'id' => $id,
        ]);
    }

    public function editJson($id)
    {
        $data = HeaderPanen::with(['detail_panen.detail_pembagian_bibit.header_pembagian_bibit.detail_beli.produk'])->where('id', $id)->first();
        return response()->json($data);
    }

    public function update(PanenRequest $request, $id)
    {
        $tglPanen = date('Y-m-d', strtotime($request->tgl_panen));
        HeaderPanen::where('id', $id)->update([
            'tgl_panen' => $tglPanen
        ]);

        return response()->json([
            'success' => 'Berhasil Tambah Data'
        ]);
    }

    public function updateDetail(PanenRequest $request, $id)
    {
        $detailPanen  = DetailPanen::find($id);
        $id_detail_pembagian_bibit = $detailPanen->id_detail_pembagian_bibit;
        $new_quantity = $request->quantity;
        $old_quantity = DB::table('detail_panen')
            ->where('id', $id)
            ->value('quantity');
        $same_id_detail_panen = DB::table('detail_panen')
            ->where('id_detail_pembagian_bibit', $id_detail_pembagian_bibit)
            ->where('id', '!=', $id)
            ->get();

        $total_quantity_same_id_detail_pembagian_bibit = 0;
        foreach ($same_id_detail_panen as $data) {
            $total_quantity_same_id_detail_pembagian_bibit += $data->quantity;
        }

        $detail_pembagian_bibit = DB::table('detail_pembagian_bibit')
            ->where('id', $id_detail_pembagian_bibit)
            ->first();

        $old_quantity_detail_pembagian_bibit = $detail_pembagian_bibit->quantity;
        $new_quantity_detail_pembagian_bibit = $old_quantity_detail_pembagian_bibit + ($old_quantity + $total_quantity_same_id_detail_pembagian_bibit) - ($request->quantity + $total_quantity_same_id_detail_pembagian_bibit);

        if ($new_quantity_detail_pembagian_bibit >= 0) {
            DB::table('detail_panen')
                ->where('id', $id)
                ->update([
                    'quantity' => $new_quantity,
                    'status' => $request->status,
                ]);
            DB::table('detail_pembagian_bibit')
                ->where('id', $id_detail_pembagian_bibit)
                ->update([
                    'quantity' => $new_quantity_detail_pembagian_bibit,
                ]);

            $pembagianBibit = DetailPembagianBibit::with(['header_pembagian_bibit.detail_beli.produk'])->where('id', $id_detail_pembagian_bibit)->first();

            if ($pembagianBibit->quantity <= 0 && $pembagianBibit->id_jaring_old == null) {
                if ($pembagianBibit->id_jaring != null) {
                    DB::table('master_jaring')->where('id',  $pembagianBibit->id_jaring)->update([
                        'id_kolam' => null
                    ]);
                }
                $pembagianBibit->id_jaring_old = $pembagianBibit->id_jaring;
                $pembagianBibit->id_jaring = null;
                $pembagianBibit->save();
            }

            if ($pembagianBibit->quantity > 0 && $pembagianBibit->id_jaring == null) {
                if ($pembagianBibit->id_jaring_old != null) {
                    DB::table('master_jaring')->where('id',  $pembagianBibit->id_jaring_old)->update([
                        'id_kolam' => $pembagianBibit->id_kolam
                    ]);
                }
                $pembagianBibit->id_jaring = $pembagianBibit->id_jaring_old;
                $pembagianBibit->id_jaring_old = null;
                $pembagianBibit->save();
            }

            $namaProduk = $pembagianBibit->header_pembagian_bibit->detail_beli->produk->nama;
            $produkExists = Produk::where('nama', $namaProduk)->where('id_kategori', 6)->first();

            if ($request->status == 1) {
                if ($detailPanen->status == 1) {
                    $produkExists->update([
                        'quantity' => ($produkExists->quantity - ($old_quantity + $total_quantity_same_id_detail_pembagian_bibit)) + ($request->quantity + $total_quantity_same_id_detail_pembagian_bibit)
                    ]);
                } else {
                    $produkExists->update([
                        'quantity' => $request->quantity + $total_quantity_same_id_detail_pembagian_bibit
                    ]);
                }
            }
            if ($request->status != 1) {
                $produkExists->update([
                    'quantity' =>  $total_quantity_same_id_detail_pembagian_bibit
                ]);
            }


            return response()->json([
                'success' => 'Berhasil Ubah Data'
            ]);
        } else {
            return response()->json([
                'errors' => [
                    "general" => 'Quantity tidak boleh melebihi jumlah pembagian bibit'
                ],
            ], 422);
        }
    }

    public function destroy($id)
    {
        // Mencari semua record dari tabel detail_pembagian_pakan yang terkait dengan header_pembagian_pakan yang akan dihapus
        $details = DetailPanen::where('id_header_panen', $id)->get();



        // Memperbarui tabel detail_beli dan produk
        foreach ($details as $detail) {
            $detailPembagianBibit = DetailPembagianBibit::with(['header_pembagian_bibit.detail_beli.produk'])
                ->where('id', $detail->id_detail_pembagian_bibit);
            $detailPembagianBibit->increment('quantity', $detail->quantity);

            if ($detailPembagianBibit->first()->quantity > 0 && $detailPembagianBibit->first()->id_jaring == null) {
                if ($detailPembagianBibit->first()->id_jaring_old != null) {
                    DB::table('master_jaring')->where('id',  $detailPembagianBibit->first()->id_jaring_old)->update([
                        'id_kolam' => $detailPembagianBibit->first()->id_kolam
                    ]);
                }
                DetailPembagianBibit::where('id', $detail->id_detail_pembagian_bibit)->update([
                    'id_jaring' => $detailPembagianBibit->first()->id_jaring_old,
                    'id_jaring_old' => null
                ]);
            }


            $produkExists = Produk::where('nama', $detailPembagianBibit->first()->header_pembagian_bibit->detail_beli->produk->nama)->where('id_kategori', 6)->first();
            $produkExists->decrement('quantity', $detail->quantity);
        }

        // Menghapus semua record dari tabel detail_pembagian_pakan yang terkait dengan header_pembagian_pakan yang akan dihapus
        DB::table('detail_panen')
            ->where('id_header_panen', $id)
            ->delete();

        // Menghapus record dari tabel header_pembagian_pakan yang sesuai dengan parameter $id
        DB::table('header_panen')
            ->where('id', $id)
            ->delete();

        return redirect()->route('panen')->with(
            'success',
            'Berhasil Hapus Panen'
        );
    }

    public function destroyDetail($id)
    {
        // Mendapatkan data detail_pembagian_pakan berdasarkan $id
        $detailPanen = DetailPanen::find($id);

        // Menyimpan nilai quantity dari detail_pembagian_pakan yang akan dihapus
        $quantity = $detailPanen->quantity;

        // Memperbarui nilai column quantity pada tabel detail_pembagian_bibit        
        $detailPanen->detail_pembagian_bibit->increment('quantity', $quantity);
        if ($detailPanen->detail_pembagian_bibit->quantity > 0 && $detailPanen->detail_pembagian_bibit->id_jaring == null) {
            if ($detailPanen->detail_pembagian_bibit->id_jaring_old != null) {
                DB::table('master_jaring')->where('id',  $detailPanen->detail_pembagian_bibit->id_jaring_old)->update([
                    'id_kolam' => $detailPanen->detail_pembagian_bibit->id_kolam
                ]);
            }
            $detailPanen->detail_pembagian_bibit->id_jaring = $detailPanen->detail_pembagian_bibit->id_jaring_old;
            $detailPanen->detail_pembagian_bibit->id_jaring_old = null;
            $detailPanen->detail_pembagian_bibit->save();
        }

        // Memperbarui nilai column quantity pada tabel produk
        $pembagianBibit = DetailPembagianBibit::with(['header_pembagian_bibit.detail_beli.produk'])->where('id', $detailPanen->id_detail_pembagian_bibit)->first();
        $namaProduk = $pembagianBibit->header_pembagian_bibit->detail_beli->produk->nama;
        $produkExists = Produk::where('nama', $namaProduk)->where('id_kategori', 6)->first();
        $produkExists->decrement('quantity', $quantity);

        // Menghapus data detail_pembagian_pakan
        $detailPanen->delete();

        return response()->json([
            'success' => 'Data Berhasil di Hapus'
        ], 200);
    }

    public function show($id)
    {

        $pembagianBibit = DetailPembagianBibit::with(['header_pembagian_bibit.detail_beli.produk'])->get();

        return view('pages.panen.show')->with([
            'title' => 'EDIT PANEN',
            'pembagianBibit' => $pembagianBibit,
            'id' => $id,
        ]);
    }

    public function contoh()
    {
        // $data = DetailBeli::select('detail_beli.id_produk, detail_beli.qty, header_beli.tgl_beli, header_beli.tgl_beli, supplier.nama')->with('produk, header_beli.supplier')->orderBy('updated_at', 'desc')->get();
        $data = HeaderPanen::with(['detail_panen.detail_pembagian_bibit.header_pembagian_bibit.detail_beli.produk'])->orderBy('updated_at', 'desc')->get();
        return response()->json(
            $data
        );
    }
}
