<?php

namespace App\Http\Controllers;

use App\Models\DetailBeli;
use App\Models\HeaderPembagianBibit;
use App\Models\MasterJaring;
use App\Models\MasterKolam;
use App\Models\Produk;
use Illuminate\Http\Request;

class PembagianBibitController extends Controller
{
    public function index()
    {
        return view('pages.pembagian_bibit.index')->with([
            'title' => 'PEMBAGIAN BIBIT',
        ]);
    }

    public function create()
    {
        $bibit = Produk::select(['id', 'nama'])->where('id_kategori')->get();
        $jaring = MasterJaring::select(['id', 'nama'])->get();
        $kolam = MasterKolam::select(['id', 'nama'])->get();

        $pembelian = DetailBeli::select(['id', 'id_header_beli', 'id_produk', 'updated_at', 'quantity'])->with(['produk' => function ($query) {
            $query->select('id', 'nama');
        }, 'header_beli' => function ($query) {
            $query->select('id', 'tgl_beli');
        }])->orderBy('id_header_beli', 'asc')->get();

        // return response()->json($pembelian);

        return view('pages.pembagian_bibit.create')->with([
            'title' => 'PEMBAGIAN BIBIT',
            'jaring' => $jaring,
            'kolam' => $kolam,
            'bibit' => $bibit,
            'pembelian' => $pembelian
        ]);
    }

    public function store(Request $request)
    {
        dd($request->all());
        $tglPembagian = date('Y-m-d', strtotime($request->tgl_pembagian));
        $detail = $request->detail_pembagian_bibit;

        $headPembagian =  HeaderPembagianBibit::create([
            'tgl_pembagian' => $tglPembagian,
            'id_detail_beli' => $request->id_detail_beli,
            'id_detail_panen' => $request->id_detail_beli,
        ]);

        foreach ($detail as $key => $value) {
        }
    }
}
