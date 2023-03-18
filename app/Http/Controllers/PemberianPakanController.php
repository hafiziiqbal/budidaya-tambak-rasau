<?php

namespace App\Http\Controllers;

use App\Models\MasterTong;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\DetailPembagianBibit;
use App\Models\DetailPembagianPakan;
use App\Models\DetailPemberianPakan;
use Yajra\DataTables\Facades\DataTables;

class PemberianPakanController extends Controller
{
    public function index()
    {
        return view('pages.pemberian_pakan.index')->with([
            'title' => 'PEMBERIAN PAKAN',
        ]);
    }

    public function create()
    {
        $pembagianPakan = DetailPembagianPakan::with(['header_pembagian_pakan', 'tong', 'detail_beli.produk'])->where('quantity', '>', '0')->get();
        return view('pages.pemberian_pakan.create')->with([
            'pembagianPakan' => $pembagianPakan,
            'title' => 'PEMBERIAN PAKAN',
        ]);
    }

    public function store(Request $request)
    {
        $pakan = new DetailPemberianPakan;
        $pakan->id_detail_pembagian_pakan = $request->id_pembagian_pakan;
        $pakan->quantity = $request->quantity;
        $pakan->id_detail_pembagian_bibit = $request->id_pembagian_bibit;
        $pakan->save();

        $detailPembagianPakan = DetailPembagianPakan::find($request->id_pembagian_pakan);
        $detailPembagianPakan->quantity -= $request->quantity;
        $detailPembagianPakan->save();

        return redirect()->route('pemberian.pakan')->with(
            'success',
            'Berhasil Tambah Data'
        );
    }

    public function getBagiBibitByTong($id)
    {
        $kolamIds = MasterTong::where('id', $id)->first();
        $detailPembagianBibit = DetailPembagianBibit::whereIn('id_kolam', $kolamIds->id_kolam)->with(['header_pembagian_bibit' => function ($query) {
            $query->with(['detail_beli' => function ($query) {
                $query->with('produk');
            }]);
        }, 'kolam', 'jaring'])->where('quantity', '>', 0)->get();


        return response()->json($detailPembagianBibit);
    }

    public function datatable(Request $request)
    {
        try {
            if ($request->ajax()) {
                $data = DetailPemberianPakan::with(['detail_pembagian_pakan' => function ($query) {
                    $query->with('tong', 'detail_beli.produk');
                }, 'detail_pembagian_bibit.header_pembagian_bibit.detail_beli.produk'])->get();
                return DataTables::of($data)->addIndexColumn()->make(true);
            }
        } catch (\Throwable $th) {
            return redirect('/')->withErrors([
                'error' => 'Terdapat Kesalahan'
            ]);
        }
    }

    public function edit($id)
    {
        $data = DetailPemberianPakan::find($id);
        $pembagianPakan = DetailPembagianPakan::with(['header_pembagian_pakan', 'tong', 'detail_beli.produk'])->where('quantity', '>', '0')->get();
        // $pembagianBibit = DetailPembagianBibit::with(['header_pembagian_bibit.detail_beli.produk'])->where('quantity', '>', 0)->get();
        $pembagianBibit = DetailPembagianBibit::with(['header_pembagian_bibit.detail_beli.produk'])->get();
        return view('pages.pemberian_pakan.edit')->with([
            'pembagianPakan' => $pembagianPakan,
            'pembagianBibit' => $pembagianBibit,
            'data' => $data,
            'title' => 'PEMBERIAN PAKAN',
        ]);
    }

    public function update(Request $request, $id)
    {
        // Mendapatkan data detail pemberian pakan yang akan diupdate
        $detailPemberianPakan = DetailPemberianPakan::findOrFail($id);
        $detailPembagianPakan = DetailPembagianPakan::findOrFail($detailPemberianPakan->id_detail_pembagian_pakan);

        // Menyimpan nilai id_pembagian_pakan sebelum diupdate
        $oldIdPembagianPakan = $detailPemberianPakan->id_detail_pembagian_pakan;

        // Melakukan update data pada detail pemberian pakan
        $detailPemberianPakan->id_detail_pembagian_pakan = $request->id_pembagian_pakan;
        $detailPemberianPakan->id_detail_pembagian_bibit = $request->id_pembagian_bibit;

        // Menyesuaikan nilai quantity pada detail pemberian pakan
        if ($oldIdPembagianPakan != $request->id_pembagian_pakan) {

            // Mengembalikan nilai quantity yang lama pada tabel detail_pembagian_pakan
            $detailPembagianPakan->quantity += $detailPemberianPakan->quantity;
            $detailPembagianPakan->save();

            $detailPembagianBibitNew = DetailPembagianPakan::findOrFail($request->id_pembagian_pakan);
            $detailPembagianBibitNew->quantity -= $request->quantity;
            $detailPembagianBibitNew->save();

            $detailPemberianPakan->quantity = $request->quantity;
        } else {

            $detailPembagianPakan->quantity = ($detailPembagianPakan->quantity + $detailPemberianPakan->quantity) - $request->quantity;
            $detailPembagianPakan->save();
            $detailPemberianPakan->quantity = $request->quantity;
        }

        $detailPemberianPakan->save();

        return redirect()->route('pemberian.pakan')->with(
            'success',
            'Berhasil Perbarui Data'
        );
    }


    public function contoh()
    {
        $data = DetailPemberianPakan::with(['detail_pembagian_pakan' => function ($query) {
            $query->with('tong', 'detail_beli.produk');
        }, 'detail_pembagian_bibit.header_pembagian_bibit.detail_beli.produk'])->get();
        return response()->json($data);
    }
}
