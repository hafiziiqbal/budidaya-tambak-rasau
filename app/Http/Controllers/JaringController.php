<?php

namespace App\Http\Controllers;

use App\Models\MasterJaring;
use App\Models\MasterKolam;
use App\Models\MasterTong;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class JaringController extends Controller
{
    public function index()
    {
        return view('pages.jaring.index')->with([
            'title' => 'JARING',
            'masterdata_toogle' => 1
        ]);
    }

    public function datatable(Request $request)
    {
        try {
            if ($request->ajax()) {
                $data = MasterJaring::with('kolam')->orderBy('updated_at', 'desc')->get();
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
        return view('pages.jaring.create')->with([
            'title' => 'TAMBAH JARING',
            'kolam' => $kolam,
            'masterdata_toogle' => 1
        ]);
    }

    public function store(Request $request)
    {
        // try {
        MasterJaring::create([
            'nama' => $request->nama,
            'id_kolam' => null,
            'posisi' => $request->posisi,
        ]);

        return redirect()->route('jaring')->with(
            'success',
            'Berhasil Tambah Jaring'
        );
        // } catch (\Throwable $th) {
        //     return redirect('/')->withErrors([
        //         'error' => 'Terdapat Kesalahan'
        //     ]);
        // }
    }

    public function edit($id)
    {
        $jaring = MasterJaring::find($id);
        $kolam = MasterKolam::all();
        return view('pages.jaring.edit')->with([
            'title' => 'EDIT KOLAM',
            'jaring' => $jaring,
            'kolam' => $kolam,
            'masterdata_toogle' => 1
        ]);
    }


    public function update(Request $request, $id)
    {
        try {
            $jaring = MasterJaring::find($id);
            $jaring->update([
                'nama' => $request->nama,
                'id_kolam' => $request->id_kolam,
                'quantity' => $request->quantity,
            ]);

            return redirect()->route('jaring')->with(
                'success',
                'Berhasil Perbarui Jaring'
            );
        } catch (\Throwable $th) {
            return redirect('/')->withErrors([
                'error' => 'Terdapat Kesalahan'
            ]);
        }
    }

    public function destroy($id)
    {
        // try {
        $tong = MasterKolam::where('id_jaring', $id)->first();
        if ($tong != '') {
            return redirect('jaring')->withErrors([
                'alert' => 'Data Ini Digunakan Oleh Tabel Lain'
            ]);
        }

        $jaring = MasterJaring::find($id);
        $jaring->delete();
        return redirect()->route('jaring')->with(
            'success',
            'Berhasil Hapus Jaring'
        );
        // } catch (\Throwable $th) {
        //     return redirect('/')->withErrors([
        //         'error' => 'Terdapat Kesalahan'
        //     ]);
        // }
    }
}
