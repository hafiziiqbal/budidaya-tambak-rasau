<?php

namespace App\Http\Controllers;

use App\Models\DetailBeli;
use App\Models\HeaderBeli;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class BibitController extends Controller
{
    public function index()
    {
        return view('pages.bibit.index')->with([
            'title' => 'BIBIT',
            'produk_toogle' => 1
        ]);
    }

    public function datatable(Request $request)
    {
        try {
            if ($request->ajax()) {
                $data = DetailBeli::with(['produk'])->whereHas('produk', function ($query) {
                    $query->where('id_kategori', '=', 7);
                })->get();
                return DataTables::of($data)->addIndexColumn()->make(true);
            }
        } catch (\Throwable $th) {
            return redirect('/')->withErrors([
                'error' => 'Terdapat Kesalahan'
            ]);
        }
    }

    public function contoh()
    {
        $bibit = DetailBeli::with(['produk'])->whereHas('produk', function ($query) {
            $query->where('id_kategori', '=', 7);
        })->get();

        return response()->json($bibit);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $detailBeli = DetailBeli::with('produk')->where('id', $id)->first();
        return view('pages.bibit.edit')->with([
            'title' => 'EDIT BIBIT',
            'detailBeli' => $detailBeli,
            'produk_toogle' => 1
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
