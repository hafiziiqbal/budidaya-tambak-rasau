<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\DetailBeli;
use App\Models\DetailJual;
use App\Models\HeaderBeli;
use App\Models\DetailPanen;
use App\Models\DetailPembagianPakan;
use App\Models\HeaderJual;
use App\Models\HeaderPanen;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        return view('dashboard')->with(['title' => 'DASHBOARD']);
    }

    public function totalDefault()
    {
        $detailBeli = HeaderBeli::select('id')->get()->count();
        $detailJual = HeaderJual::select('id')->get()->count();
        $detailPanen = DetailPanen::select('id', 'id_header_panen', 'id_detail_pembagian_bibit', 'quantity')->with(['detail_jual', 'header_pembagian_bibit.detail_pembagian_bibit' => function ($query) {
            $query->select('id', 'id_header_pembagian_bibit', 'quantity');
        }])->orderBy('updated_at', 'desc')->get();
        $totalPakanDalamTong = DetailPembagianPakan::select('id', 'quantity')->sum('quantity');
        $totalPanen = HeaderPanen::select('id')->get()->count();

        // foreach ($detailPanen as $key => $value) {
        //     if (count($value->detail_jual) > 0) {
        //         $totalPanen += $value->quantityAwalPanen;
        //     } else {
        //         $nilaiSementara = 0;
        //         foreach ($value->header_pembagian_bibit as $key => $bibit) {
        //             $nilaiSementara += $bibit->detail_pembagian_bibit->sum('quantity');
        //         }
        //         $totalPanen += $value->quantity + $nilaiSementara;
        //     }
        // }
        $totalPanenHidup  = DetailPanen::where('status', 1)->sum('quantity_berat');

        // $detailPanenHidup = DetailPanen::select('id', 'id_header_panen', 'status', 'id_detail_pembagian_bibit', 'quantity', 'quantity_berat')->with(['detail_jual', 'header_pembagian_bibit.detail_pembagian_bibit' => function ($query) {
        //     $query->select('id', 'id_header_pembagian_bibit', 'quantity', 'quantity_berat');
        // }])->where('status', 1)->orderBy('updated_at', 'desc')->get();
        // $totalPanenHidup = 0;
        // foreach ($detailPanenHidup as $key => $value) {
        //     if (count($value->detail_jual) > 0) {
        //         $totalPanenHidup += $value->quantityAwalPanen;
        //     } else {
        //         $nilaiSementara = 0;
        //         foreach ($value->header_pembagian_bibit as $key => $bibit) {
        //             $nilaiSementara += $bibit->detail_pembagian_bibit->sum('quantity_berat');
        //         }
        //         $totalPanenHidup += $value->quantity_berat + $nilaiSementara;
        //     }
        // }

        $detailPanenSortir = DetailPanen::select('id', 'id_header_panen', 'status', 'id_detail_pembagian_bibit', 'quantity')->with(['detail_jual', 'header_pembagian_bibit.detail_pembagian_bibit' => function ($query) {
            $query->select('id', 'id_header_pembagian_bibit', 'quantity');
        }])->where('status', 0)->where('quantity', '>', 0)->orderBy('updated_at', 'desc')->get();
        $totalPanenSortir = 0;
        foreach ($detailPanenSortir as $key => $value) {
            if (count($value->detail_jual) > 0) {
                $totalPanenSortir += $value->quantityAwalPanen;
            } else {
                $nilaiSementara = 0;
                foreach ($value->header_pembagian_bibit as $key => $bibit) {
                    $nilaiSementara += $bibit->detail_pembagian_bibit->sum('quantity');
                }
                $totalPanenSortir += $value->quantity + $nilaiSementara;
            }
        }

        $detailPanenMati = DetailPanen::select('id', 'id_header_panen', 'status', 'id_detail_pembagian_bibit', 'quantity')->with(['detail_jual', 'header_pembagian_bibit.detail_pembagian_bibit' => function ($query) {
            $query->select('id', 'id_header_pembagian_bibit', 'quantity');
        }])->where('status', -1)->orderBy('updated_at', 'desc')->get();
        $totalPanenMati = 0;
        foreach ($detailPanenMati as $key => $value) {
            if (count($value->detail_jual) > 0) {
                $totalPanenMati += $value->quantityAwalPanen;
            } else {
                $nilaiSementara = 0;
                foreach ($value->header_pembagian_bibit as $key => $bibit) {
                    $nilaiSementara += $bibit->detail_pembagian_bibit->sum('quantity');
                }
                $totalPanenMati += $value->quantity + $nilaiSementara;
            }
        }

        return response()->json([
            'total_pakan_dalam_tong' => $totalPakanDalamTong,
            'jumlah_pembelian' => $detailBeli,
            'jumlah_penjualan' => $detailJual,
            'jumlah_panen' => $totalPanen,
            'jumlah_panen_hidup' => $totalPanenHidup,
            'jumlah_panen_sortir' => $totalPanenSortir,
            'jumlah_panen_mati' => $totalPanenMati,
        ]);
    }

    public function total(Request $request)
    {
        try {


            if ($request->start_range != null && $request->end_range != null) {
                $startRange = date("Y-m-d", strtotime($request->start_range));
                $endRange = date("Y-m-d", strtotime($request->end_range));

                // list($startMonth, $startYear) = explode('-', $startRange);
                // list($endMonth, $endYear) = explode('-', $endRange);

                $totalDetailBeli = DetailBeli::join('header_beli', 'header_beli.id', '=', 'detail_beli.id_header_beli')
                    ->whereBetween('header_beli.tgl_beli', ["$startRange 00:00:00", "$endRange 23:59:59"])
                    ->count();
                $totalDetailJual = DetailJual::select('id', 'created_at')->whereBetween('created_at', ["$startRange 00:00:00", "$endRange 23:59:59"])->count();

                $detailPanen = DetailPanen::select('id', 'id_header_panen', 'id_detail_pembagian_bibit', 'quantity')->with(['header_panen', 'detail_jual', 'header_pembagian_bibit.detail_pembagian_bibit' => function ($query) {
                    $query->select('id', 'id_header_pembagian_bibit', 'quantity');
                }])->whereHas('header_panen', function ($query) use ($startRange, $endRange) {
                    $query->whereBetween('tgl_panen', ["$startRange 00:00:00", "$endRange 23:59:59"]);
                })->orderBy('updated_at', 'desc')->get();

                $totalPanen = 0;
                foreach ($detailPanen as $key => $value) {
                    if (count($value->detail_jual) > 0) {
                        $totalPanen += $value->quantityAwalPanen;
                    } else {
                        $nilaiSementara = 0;
                        foreach ($value->header_pembagian_bibit as $key => $bibit) {
                            $nilaiSementara += $bibit->detail_pembagian_bibit->sum('quantity');
                        }
                        $totalPanen += $value->quantity + $nilaiSementara;
                    }
                }


                $totalPanenHidup  = DetailPanen::where('status', 1)->whereBetween('updated_at', ["$startRange 00:00:00", "$endRange 23:59:59"])->sum('quantity_berat');

                // $detailPanenHidup = DetailPanen::select('id', 'id_header_panen', 'status', 'id_detail_pembagian_bibit', 'quantity', 'quantity_berat')->with(['detail_jual', 'header_pembagian_bibit.detail_pembagian_bibit' => function ($query) {
                //     $query->select('id', 'id_header_pembagian_bibit', 'quantity', 'quantity_berat');
                // }])->where('status', 1)->whereHas('header_panen', function ($query) use ($startRange, $endRange) {
                //     $query->whereBetween('tgl_panen', ["$startRange 00:00:00", "$endRange 23:59:59"]);
                // })->orderBy('updated_at', 'desc')->get();
                // $totalPanenHidup = 0;
                // foreach ($detailPanenHidup as $key => $value) {
                //     if (count($value->detail_jual) > 0) {
                //         $totalPanenHidup += $value->quantityAwalPanen;
                //     } else {
                //         $nilaiSementara = 0;
                //         foreach ($value->header_pembagian_bibit as $key => $bibit) {
                //             $nilaiSementara += $bibit->detail_pembagian_bibit->sum('quantity_berat');
                //         }
                //         $totalPanenHidup += $value->quantity_berat + $nilaiSementara;
                //     }
                // }

                $detailPanenSortir = DetailPanen::select('id', 'id_header_panen', 'status', 'id_detail_pembagian_bibit', 'quantity', 'quantity_berat')->with(['detail_jual', 'header_pembagian_bibit.detail_pembagian_bibit' => function ($query) {
                    $query->select('id', 'id_header_pembagian_bibit', 'quantity', 'quantity_berat');
                }])->where('status', 0)->whereHas('header_panen', function ($query) use ($startRange, $endRange) {
                    $query->whereBetween('tgl_panen', ["$startRange 00:00:00", "$endRange 23:59:59"]);
                })->orderBy('updated_at', 'desc')->get();
                $totalPanenSortir = 0;
                foreach ($detailPanenSortir as $key => $value) {
                    if (count($value->detail_jual) > 0) {
                        $totalPanenSortir += $value->quantityAwalPanen;
                    } else {
                        $nilaiSementara = 0;
                        foreach ($value->header_pembagian_bibit as $key => $bibit) {
                            $nilaiSementara += $bibit->detail_pembagian_bibit->sum('quantity');
                        }
                        $totalPanenSortir += $value->quantity + $nilaiSementara;
                    }
                }

                $detailPanenMati = DetailPanen::select('id', 'id_header_panen', 'status', 'id_detail_pembagian_bibit', 'quantity')->with(['detail_jual', 'header_pembagian_bibit.detail_pembagian_bibit' => function ($query) {
                    $query->select('id', 'id_header_pembagian_bibit', 'quantity');
                }])->where('status', -1)->whereHas('header_panen', function ($query) use ($startRange, $endRange) {
                    $query->whereBetween('tgl_panen', ["$startRange 00:00:00", "$endRange 23:59:59"]);
                })->orderBy('updated_at', 'desc')->get();
                $totalPanenMati = 0;
                foreach ($detailPanenMati as $key => $value) {
                    if (count($value->detail_jual) > 0) {
                        $totalPanenMati += $value->quantityAwalPanen;
                    } else {
                        $nilaiSementara = 0;
                        foreach ($value->header_pembagian_bibit as $key => $bibit) {
                            $nilaiSementara += $bibit->detail_pembagian_bibit->sum('quantity');
                        }
                        $totalPanenMati += $value->quantity + $nilaiSementara;
                    }
                }

                return response()->json([
                    'jumlah_pembelian' => $totalDetailBeli,
                    'jumlah_penjualan' => $totalDetailJual,
                    'jumlah_panen' => $totalPanen,
                    'jumlah_panen_hidup' => $totalPanenHidup,
                    'jumlah_panen_sortir' => $totalPanenSortir,
                    'jumlah_panen_mati' => $totalPanenMati
                ]);
            } else {
                return $this->totalDefault();
            }
        } catch (\Throwable $th) {
            return $th;
        }
    }
}
