<?php

namespace App\Http\Controllers;

use App\Models\MasterTong;
use App\Models\MasterKolam;
use App\Models\MasterJaring;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\KolamRequest;
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

    public function daftarIkan($id)
    {
        $kolam = MasterKolam::with(['detail_pembagian_bibit' => function ($query) {
            $query->select(['id', 'id_header_pembagian_bibit', 'quantity', 'id_kolam'])->with(['header_pembagian_bibit' => function ($query) {
                $query->select(['id', 'id_detail_beli'])->with(['detail_beli' => function ($query) {
                    $query->select(['id', 'id_produk'])->with('produk');
                }]);
            }]);
        }])->whereHas('detail_pembagian_bibit', function ($query) {
            $query->where('quantity', '>', 0);
        })->where('id', $id)->first();

        $daftarIkan = [];

        if ($kolam) {
            foreach ($kolam->detail_pembagian_bibit as $key => $value) {
                if (array_key_exists($value->header_pembagian_bibit->detail_beli->id_produk, $daftarIkan)) {
                    $daftarIkan[$value->header_pembagian_bibit->detail_beli->id_produk]->quantity += $value->quantity;
                } else {
                    $daftarIkan[$value->header_pembagian_bibit->detail_beli->id_produk] = (object)[
                        'nama' => $value->header_pembagian_bibit->detail_beli->produk->nama,
                        'quantity' => (int)$value->quantity,
                    ];
                }
            }
        } else {
            $kolam = MasterKolam::findOrFail($id);
        }

        return view('pages.kolam.ikan')->with([
            'title' => 'IKAN DALAM KOLAM',
            'masterdata_toogle' => 1,
            'kolam' => $kolam,
            'daftarIkan' => $daftarIkan
        ]);
    }

    public function store(KolamRequest $request)
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


    public function update(KolamRequest $request, $id)
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

        $jaring = MasterJaring::where('id_kolam', $id)->first();
        if ($jaring != null) {
            return redirect('kolam')->withErrors([
                'alert' => 'Data Ini Digunakan Oleh Tabel Lain'
            ]);
        }

        $tong = DB::table('master_tong')
            ->whereRaw("FIND_IN_SET('$id', id_kolam) > 0")
            ->get();
        if ($tong != null) {
            return redirect()->route('kolam')->withErrors(['error' => 'Tabel tong sedang menggunakan kolam ini']);
        }

        $kolam = MasterKolam::find($id);
        $kolam->delete();
        return redirect()->route('kolam')->with(
            'success',
            'Berhasil Hapus Kolam'
        );
    }
}
