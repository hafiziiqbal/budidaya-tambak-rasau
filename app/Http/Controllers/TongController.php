<?php

namespace App\Http\Controllers;

use App\Http\Requests\TongRequest;
use App\Models\DetailPembagianPakan;
use App\Models\MasterTong;
use App\Models\MasterKolam;
use App\Models\MasterJaring;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class TongController extends Controller
{
    public function index()
    {
        return view('pages.tong.index')->with([
            'title' => 'TONG',
            'masterdata_toogle' => 1
        ]);
    }

    public function datatable(Request $request)
    {
        try {
            if ($request->ajax()) {

                $tongs = DB::table('master_tong')
                    ->join('master_kolam', function ($join) {
                        $join->on('master_tong.id_kolam', 'LIKE', DB::raw("CONCAT('%', master_kolam.id, '%')"));
                    })
                    ->select('master_tong.id', 'master_tong.nama as tong_nama', 'master_kolam.nama as kolam_nama')
                    ->get();

                $data = [];

                foreach ($tongs as $tong) {
                    $found = false;
                    foreach ($data as &$formatted) {
                        if ($formatted['id'] === $tong->id) {
                            $formatted['kolam'][] = $tong->kolam_nama;
                            $found = true;
                            break;
                        }
                    }
                    if (!$found) {
                        $data[] = [
                            'id' => $tong->id,
                            'tong_nama' => $tong->tong_nama,
                            'kolam' => [$tong->kolam_nama],
                        ];
                    }
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
        $id_kolam = DB::table('master_tong')
            ->select('id_kolam')
            ->get()
            ->pluck('id_kolam')
            ->flatten()
            ->unique()
            ->implode(',');

        $tong = str_replace(array('[', ']', '"'), "", $id_kolam);

        $kolam = MasterKolam::whereNotIn('id', preg_split("/\,/", $tong))->get();
        return view('pages.tong.create')->with([
            'title' => 'TAMBAH TONG',
            'kolam' => $kolam,
            'masterdata_toogle' => 1
        ]);
    }


    public function store(TongRequest $request)
    {

        try {
            MasterTong::create([
                'nama' => $request->nama,
                'id_kolam' => array_values($request->id_kolam),
            ]);

            return redirect()->route('tong')->with(
                'success',
                'Berhasil Tambah Tong'
            );
        } catch (\Throwable $th) {
            return redirect('/')->withErrors([
                'error' => 'Terdapat Kesalahan'
            ]);
        }
    }

    public function edit($id)
    {
        $tong = MasterTong::find($id);

        $id_kolam = DB::table('master_tong')
            ->select('id_kolam')
            ->where('id', '!=', $id)
            ->get()
            ->pluck('id_kolam')
            ->flatten()
            ->unique()
            ->implode(',');

        $dataTong = str_replace(array('[', ']', '"'), "", $id_kolam);

        $kolam = MasterKolam::whereNotIn('id', preg_split("/\,/", $dataTong))->get();

        $checkboxes = [];

        // Looping untuk mengisi nilai pada variabel checkbox
        foreach ($kolam as $value) {
            $checkboxes[$value->id] = in_array($value->id, $tong->id_kolam);
        }
        return view('pages.tong.edit')->with([
            'title' => 'EDIT KOLAM',
            'kolam' => $kolam,
            'tong' => $tong,
            'checkboxes' => $checkboxes,
            'masterdata_toogle' => 1
        ]);
    }

    public function update(TongRequest $request, $id)
    {
        try {
            $tong = MasterTong::find($id);
            $tong->update([
                'nama' => $request->nama,
                'id_kolam' => array_values($request->id_kolam),
            ]);

            return redirect()->route('tong')->with(
                'success',
                'Berhasil Perbarui Tong'
            );
        } catch (\Throwable $th) {
            return redirect('/')->withErrors([
                'error' => 'Terdapat Kesalahan'
            ]);
        }
    }


    public function destroy($id)
    {
        try {
            $tong = MasterTong::find($id);

            $tong->delete();
            return redirect()->route('tong')->with(
                'success',
                'Berhasil Hapus Tong'
            );
        } catch (\Throwable $th) {
            return redirect('/')->withErrors([
                'error' => 'Terdapat Kesalahan'
            ]);
        }
    }

    public function contoh()
    {
        // $tongs = DB::table('master_tong')
        //     ->join('master_kolam', function ($join) {
        //         $join->on('master_tong.id_kolam', 'LIKE', DB::raw("CONCAT('%', master_kolam.id, '%')"));
        //     })
        //     ->select('master_tong.id', 'master_tong.nama as tong_nama', 'master_kolam.nama as kolam_nama')
        //     ->get();

        // $data = [];

        // foreach ($tongs as $tong) {
        //     $found = false;
        //     foreach ($data as &$formatted) {
        //         if ($formatted['id'] === $tong->id) {
        //             $formatted['kolam'][] = $tong->kolam_nama;
        //             $found = true;
        //             break;
        //         }
        //     }
        //     if (!$found) {
        //         $data[] = [
        //             'id' => $tong->id,
        //             'tong_nama' => $tong->tong_nama,
        //             'kolam' => [$tong->kolam_nama],
        //         ];
        //     }
        // }
        $data = MasterTong::all();
        return response()->json($data);
    }
}
