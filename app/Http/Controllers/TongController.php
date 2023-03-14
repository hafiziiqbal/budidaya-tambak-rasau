<?php

namespace App\Http\Controllers;

use App\Models\MasterJaring;
use App\Models\MasterTong;
use App\Models\MasterKolam;
use Illuminate\Http\Request;
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
                $data = MasterTong::all()->orderBy('updated_at', 'desc')->get();
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
        $kolam = MasterKolam::all();
        return view('pages.tong.create')->with([
            'title' => 'TAMBAH TONG',
            'kolam' => $kolam,
            'masterdata_toogle' => 1
        ]);
    }


    public function store(Request $request)
    {
        try {
            MasterTong::create([
                'nama' => $request->nama,
                'id_kolam' => $request->id_kolam,
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
        $jaring = MasterJaring::all();
        $kolam = MasterKolam::all();
        $tong = MasterTong::find($id);
        return view('pages.tong.edit')->with([
            'title' => 'EDIT KOLAM',
            'jaring' => $jaring,
            'kolam' => $kolam,
            'tong' => $tong,
            'masterdata_toogle' => 1
        ]);
    }

    public function update(Request $request, $id)
    {
        try {
            $tong = MasterTong::find($id);
            $tong->update([
                'nama' => $request->nama,
                'id_kolam' => $request->id_kolam,
                'id_jaring' => $request->id_jaring,
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
}
