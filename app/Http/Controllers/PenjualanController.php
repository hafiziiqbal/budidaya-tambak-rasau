<?php

namespace App\Http\Controllers;

use App\Models\MasterCustomer;
use App\Models\Produk;
use Illuminate\Http\Request;

class PenjualanController extends Controller
{
    public function index()
    {
        return view('pages.penjualan.index')->with([
            'title' => 'PENJUALAN',
            'transaksi_toogle' => 1
        ]);
    }

    public function create()
    {
        $produk = Produk::where('id_kategori', 3)->where('quantity', '>', 0)->get();
        $customer = MasterCustomer::all();

        return view('pages.penjualan.create')->with([
            'title' => 'TAMBAH PEMBELIAN',
            'customers' => $customer,
            'produk' => $produk,
            'transaksi_toogle' => 1
        ]);
    }

    public function store(Request $request)
    {
        
    }
}
