<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SupplierController extends Controller
{
    public function index()
    {
        return view('supplier.index')->with([
            'title' => 'SUPPLIER',
            'masterdata_toogle' => 1
        ]);
    }
}
