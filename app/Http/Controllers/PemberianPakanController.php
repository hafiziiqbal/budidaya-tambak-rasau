<?php

namespace App\Http\Controllers;

use App\Models\MasterTong;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\DetailPembagianBibit;
use App\Models\DetailPembagianPakan;
use App\Models\DetailPemberianPakan;

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
            'Berhasil Tambah Pembelian'
        );
    }

    public function getBagiBibitByTong($id)
    {
        $kolamIds = MasterTong::where('id', $id)->first();
        $detailPembagianBibit = DetailPembagianBibit::whereIn('id_kolam', $kolamIds->id_kolam)->with(['header_pembagian_bibit' => function ($query) {
            $query->with(['detail_beli' => function ($query) {
                $query->with('produk');
            }]);
        }, 'kolam', 'jaring'])->get();


        return response()->json($detailPembagianBibit);
    }
}
