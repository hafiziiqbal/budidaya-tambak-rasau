<?php

namespace App\Http\Controllers;

use App\Http\Requests\CustomerRequest;
use Illuminate\Http\Request;
use App\Models\MasterCustomer;
use Yajra\DataTables\Facades\DataTables;

class CustomerController extends Controller
{
    public function index()
    {
        return view('pages.customer.index')->with([
            'title' => 'CUSTOMER',
            'masterdata_toogle' => 1
        ]);
    }

    public function datatable(Request $request)
    {
        try {
            if ($request->ajax()) {
                $data = MasterCustomer::orderBy('updated_at', 'desc')->get();
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
        return view('pages.customer.create')->with([
            'title' => 'TAMBAH CUSTOMER',
            'masterdata_toogle' => 1
        ]);
    }


    public function store(CustomerRequest $request)
    {
        try {
            MasterCustomer::create([
                'nama' => $request->nama,
                'alamat' => $request->alamat,
                'telepon' => $request->telepon,
            ]);

            return redirect()->route('customer')->with(
                'success',
                'Berhasil Tambah Customer'
            );
        } catch (\Throwable $th) {
            return redirect('/')->withErrors([
                'error' => 'Terdapat Kesalahan'
            ]);
        }
    }

    public function edit($id)
    {
        $customer = MasterCustomer::find($id);
        return view('pages.customer.edit')->with([
            'title' => 'EDIT CUSTOMER',
            'customer' => $customer,
            'masterdata_toogle' => 1
        ]);
    }

    public function update(CustomerRequest $request, $id)
    {
        try {
            $customer = MasterCustomer::find($id);
            $customer->update([
                'nama' => $request->nama,
                'alamat' => $request->alamat,
                'telepon' => $request->telepon,
            ]);

            return redirect()->route('customer')->with(
                'success',
                'Berhasil Perbarui Customer'
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
            $customer = MasterCustomer::find($id);
            $customer->delete();
            return redirect()->route('customer')->with(
                'success',
                'Berhasil Hapus Customer'
            );
        } catch (\Throwable $th) {
            return redirect('/')->withErrors([
                'error' => 'Terdapat Kesalahan'
            ]);
        }
    }
}
