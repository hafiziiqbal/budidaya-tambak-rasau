<?php

namespace App\Http\Controllers;

use App\Models\MasterJaring;
use App\Models\MasterKolam;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class KolamController extends Controller
{
    public function index()
    {
        return view('pages.kolam.index')->with([
            'title' => 'KOLAM',
            'masterdata_toogle' => 1
        ]);
    }

    public function datatable(Request $request)
    {
        try {
            if ($request->ajax()) {
                $data = MasterKolam::orderBy('updated_at', 'desc')->get();
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
        return view('pages.kolam.create')->with([
            'title' => 'TAMBAH KOLAM',
            'masterdata_toogle' => 1
        ]);
    }

    public function store(Request $request)
    {
        try {
            MasterKolam::create([
                'nama' => $request->nama,
                'posisi' => $request->posisi,
            ]);

            return redirect()->route('kolam')->with(
                'success',
                'Berhasil Tambah Kolam'
            );
        } catch (\Throwable $th) {
            return redirect('/')->withErrors([
                'error' => 'Terdapat Kesalahan'
            ]);
        }
    }

    public function edit($id)
    {
        $kolam = MasterKolam::find($id);
        return view('pages.kolam.edit')->with([
            'title' => 'EDIT KOLAM',
            'kolam' => $kolam,
            'masterdata_toogle' => 1
        ]);
    }


    public function update(Request $request, $id)
    {
        try {
            $kolam = MasterKolam::find($id);
            $kolam->update([
                'nama' => $request->nama,
                'posisi' => $request->posisi,
            ]);

            return redirect()->route('kolam')->with(
                'success',
                'Berhasil Perbarui Kolam'
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
            $jaring = MasterJaring::where('id_kolam', $id)->first();
            if ($jaring != '') {
                return redirect('kolam')->withErrors([
                    'alert' => 'Data Ini Digunakan Oleh Tabel Lain'
                ]);
            }
            $kolam = MasterKolam::find($id);
            $kolam->delete();
            return redirect()->route('kolam')->with(
                'success',
                'Berhasil Hapus Kolam'
            );
        } catch (\Throwable $th) {
            return redirect('/')->withErrors([
                'error' => 'Terdapat Kesalahan'
            ]);
        }
    }
}
