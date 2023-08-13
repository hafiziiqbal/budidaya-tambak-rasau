<?php

namespace App\Http\Controllers;

use App\Http\Requests\PemberianPakanRequest;
use App\Models\DetailPanen;
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
            'pekerjaan_toogle' => 1
        ]);
    }

    public function create()
    {
        $pembagianPakan = DetailPembagianPakan::with(['header_pembagian_pakan', 'tong', 'detail_beli.produk'])->where('quantity', '>', '0')->get();
        return view('pages.pemberian_pakan.create')->with([
            'pembagianPakan' => $pembagianPakan,
            'title' => 'PEMBERIAN PAKAN',
            'pekerjaan_toogle' => 1
        ]);
    }

    public function store(PemberianPakanRequest $request)
    {
        $pakan = new DetailPemberianPakan;
        $detailPembagianPakan = DetailPembagianPakan::find($request->id_pembagian_pakan);

        if ($request->quantity > $detailPembagianPakan->quantity) {
            return redirect('/pemberian-pakan/create')->withErrors([
                'error' => 'Quantity melebihi quantity pembagian pakan'
            ])->withInput();
        }
        $pakan->id_detail_pembagian_pakan = $request->id_pembagian_pakan;
        $pakan->quantity = $request->quantity;
        $pakan->id_detail_pembagian_bibit = $request->id_pembagian_bibit;
        $pakan->save();

        $detailPembagianPakan->quantity -= $request->quantity;
        $detailPembagianPakan->save();

        if ($detailPembagianPakan->quantity <= 0) {
            $detailPembagianPakan->id_tong_old = $detailPembagianPakan->id_tong;
            $detailPembagianPakan->id_tong = null;
            $detailPembagianPakan->save();
        }


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
                    $query->with('tong', 'detail_beli.produk', 'tong_old');
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
        $pembagianPakan = DetailPembagianPakan::with(['header_pembagian_pakan', 'tong', 'detail_beli.produk'])->get();
        // $pembagianBibit = DetailPembagianBibit::with(['header_pembagian_bibit.detail_beli.produk'])->where('quantity', '>', 0)->get();
        $pembagianBibit = DetailPembagianBibit::with(['header_pembagian_bibit.detail_beli.produk'])->where('quantity', '>', 0)->get();
        return view('pages.pemberian_pakan.edit')->with([
            'pembagianPakan' => $pembagianPakan,
            'pembagianBibit' => $pembagianBibit,
            'data' => $data,
            'title' => 'PEMBERIAN PAKAN',
            'pekerjaan_toogle' => 1
        ]);
    }

    public function update(Request $request, $id)
    {
        // Mendapatkan data detail pemberian pakan yang akan diupdate
        $detailPemberianPakan = DetailPemberianPakan::findOrFail($id);
        $detailPemberianPakanTanpaDataUpdate = DetailPemberianPakan::where('id_detail_pembagian_pakan', $detailPemberianPakan->id_detail_pembagian_pakan)->where('id', '!=', $id)->get()->sum('quantity');
        $detailPemberianPakanAll = DetailPemberianPakan::where('id_detail_pembagian_pakan', $detailPemberianPakan->id_detail_pembagian_pakan)->get()->sum('quantity');
        $detailPembagianPakan = DetailPembagianPakan::findOrFail($detailPemberianPakan->id_detail_pembagian_pakan);

        if (($request->quantity + $detailPemberianPakanTanpaDataUpdate) > ($detailPembagianPakan->quantity + $detailPemberianPakanAll)) {
            return back()->withErrors([
                'error' => 'Quantity melebihi quantity pembagian pakan'
            ])->withInput();
        }


        // Melakukan update data pada detail pemberian pakan
        $detailPemberianPakan->id_detail_pembagian_pakan = $request->id_pembagian_pakan;
        $detailPemberianPakan->id_detail_pembagian_bibit = $request->id_pembagian_bibit;

        // Menyesuaikan nilai quantity pada detail pemberian pakan        
        $detailPembagianPakan->quantity = ($detailPembagianPakan->quantity + ($detailPemberianPakanAll - $detailPemberianPakanTanpaDataUpdate)) - $request->quantity;
        $detailPembagianPakan->save();
        $detailPemberianPakan->quantity = $request->quantity;

        if ($detailPembagianPakan->quantity > 0 && $detailPembagianPakan->id_tong == null) {
            $detailPembagianPakan->id_tong = $detailPembagianPakan->id_tong_old;
            $detailPembagianPakan->id_tong_old = null;
            $detailPembagianPakan->save();
        }

        if ($detailPembagianPakan->quantity <= 0 && $detailPembagianPakan->id_tong != null) {
            $detailPembagianPakan->id_tong_old = $detailPembagianPakan->id_tong;
            $detailPembagianPakan->id_tong = null;
            $detailPembagianPakan->save();
        }


        $detailPemberianPakan->save();

        return redirect()->route('pemberian.pakan')->with(
            'success',
            'Berhasil Perbarui Data'
        );
    }

    public function destroy($id)
    {
        $detailPemberianPakan = DetailPemberianPakan::findOrFail($id);
        $detailPembagianPakan = DetailPembagianPakan::findOrFail($detailPemberianPakan->id_detail_pembagian_pakan);
        $detailPanen = DetailPanen::where('id_detail_pembagian_bibit', $detailPemberianPakan->id_detail_pembagian_bibit)->first();

        if ($detailPanen) {
            return redirect()->route('pemberian.pakan')->withErrors(
                [
                    'error' => "Data ini digunakan oleh tabel panen"
                ]
            );
        }

        $detailPembagianPakan->quantity += $detailPemberianPakan->quantity;
        $detailPembagianPakan->save();

        if ($detailPembagianPakan->quantity > 0 && $detailPembagianPakan->id_tong == null) {
            $detailPembagianPakan->id_tong = $detailPembagianPakan->id_tong_old;
            $detailPembagianPakan->id_tong_old = null;
            $detailPembagianPakan->save();
        }

        $detailPemberianPakan->delete();

        return redirect()->route('pemberian.pakan')->with(
            'success',
            'Berhasil Hapus Data'
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
