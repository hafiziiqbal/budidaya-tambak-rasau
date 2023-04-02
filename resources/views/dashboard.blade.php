<?php
use Carbon\Carbon;
?>
@extends('layouts.admin')
@section('content')
    <h1 class="mt-4">Dashboard</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item active">Dashboard</li>
    </ol>
    <div class="card  mb-3">
        <div class="card-header">
            <div class="row">
                <div class="col-12 col-md-6">
                    <label>Pilih Rentang Waktu</label>
                </div>
                <div class="col-12 col-md-6">
                    <form id="formTotal" action="{{ route('dashboard.total') }}" method="POST">
                        @csrf
                        <div class="d-flex align-items-center">
                            <input type="text" name="start_range" id="inputStartDateJumalahRange"
                                class="total-range form-control" aria-describedby="basic-addon1" data-date-format="mm-yyyy"
                                data-provide="datepicker" placeholder="All Time">
                            <label class="mx-2">s/d</label>
                            <input type="text" name="end_range" id="inputEndDateJumalahRange"
                                class="total-range form-control" aria-describedby="basic-addon1" data-date-format="mm-yyyy"
                                data-provide="datepicker" placeholder="All Time">
                        </div>
                    </form>
                </div>

            </div>
            <div class="d-flex">

            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-xl-2 col-md-6">
                    <div class="card bg-primary text-white mb-4">
                        <div class="card-header">
                            Total Pembelian
                        </div>
                        <div class="card-body">
                            <h2 class="counter fw-bold" id="totalPembelian">0</h2>
                        </div>

                    </div>
                </div>
                <div class="col-xl-2 col-md-6">
                    <div class="card bg-warning text-white mb-4">
                        <div class="card-header">
                            Total Penjualan
                        </div>
                        <div class="card-body">
                            <h2 class="counter fw-bold" id="totalPenjualan">0</h2>
                        </div>

                    </div>
                </div>
                <div class="col-xl-2 col-md-6">
                    <div class="card bg-success text-white mb-4">
                        <div class="card-header">
                            Total Panen
                        </div>
                        <div class="card-body">
                            <h2 class="fw-bold counter" id="totalPanen">0</h2>
                        </div>

                    </div>
                </div>
                <div class="col-xl-2 col-md-6">
                    <div class="card bg-info text-white mb-4">
                        <div class="card-header">
                            Total Ikan Hidup
                        </div>
                        <div class="card-body">
                            <h2 class="fw-bold counter" id="totalPanenHidup">0</h2>
                        </div>

                    </div>
                </div>
                <div class="col-xl-2 col-md-6">
                    <div class="card bg-danger text-white mb-4">
                        <div class="card-header">
                            Total Ikan Sortir
                        </div>
                        <div class="card-body">
                            <h2 class="fw-bold counter" id="totalPanenSortir">0</h2>
                        </div>

                    </div>
                </div>
                <div class="col-xl-2 col-md-6">
                    <div class="card bg-danger text-white mb-4">
                        <div class="card-header">
                            Total Ikan Mati
                        </div>
                        <div class="card-body">
                            <h2 class="fw-bold counter" id="totalPanenMati">0</h2>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row d-none">
        <div class="col-xl-6">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-chart-area me-1"></i>
                    Area Chart Example
                </div>
                <div class="card-body"><canvas id="myAreaChart" width="100%" height="40"></canvas></div>
            </div>
        </div>
        <div class="col-xl-6">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-chart-bar me-1"></i>
                    Bar Chart Example
                </div>
                <div class="card-body"><canvas id="myBarChart" width="100%" height="40"></canvas></div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script>
        $('#inputEndDateJumalahRange').datepicker({
            todayBtn: "linked",
            todayHighlight: true,
            clearBtn: true,
            autoclose: true,
            viewMode: "months",
            minViewMode: "months"
        });
        $('#inputStartDateJumalahRange').datepicker({
            clearBtn: true,

            autoclose: true,
            viewMode: "months",
            minViewMode: "months"

        });

        $.ajax({
            url: '/dashboard/total',
            type: 'GET',
            beforeSend: function(xhr, type) {
                if (!type.crossDomain) {
                    xhr.setRequestHeader('X-CSRF-Token', $('meta[name="csrf-token"]').attr(
                        'content'));
                }
            },
            success: function(response) {
                $('#totalPembelian').html(response.jumlah_pembelian)
                $('#totalPenjualan').html(response.jumlah_penjualan)
                $('#totalPanen').html(response.jumlah_panen)
                $('#totalPanenHidup').html(response.jumlah_panen_hidup)
                $('#totalPanenSortir').html(response.jumlah_panen_sortir)
                $('#totalPanenMati').html(response.jumlah_panen_mati)
                $(".counter").countUp({
                    'time': 500,
                    'delay': 10
                });
            },
            error: function(xhr, status, error) {
                console.log('Terjadi kesalahan: ' + error);
            }
        });

        $('#formTotal').on('change', 'input.total-range', function() {
            let start_range = $('#formTotal input[name="start_range"]').val();
            let end_range = $('#formTotal input[name="end_range"]').val();

            let action = $('#formTotal').attr("action"); //get submit action from form
            let method = $('#formTotal').attr("method"); // get submit method            

            $.ajax({
                url: action,
                type: method,
                beforeSend: function(xhr, type) {
                    if (!type.crossDomain) {
                        xhr.setRequestHeader('X-CSRF-Token', $('meta[name="csrf-token"]').attr(
                            'content'));
                    }
                },
                data: {
                    start_range: start_range,
                    end_range: end_range
                },
                success: function(response) {
                    $('#totalPembelian').html(response.jumlah_pembelian)
                    $('#totalPenjualan').html(response.jumlah_penjualan)
                    $('#totalPanen').html(response.jumlah_panen)
                    $('#totalPanenHidup').html(response.jumlah_panen_hidup)
                    $('#totalPanenSortir').html(response.jumlah_panen_sortir)
                    $('#totalPanenMati').html(response.jumlah_panen_mati)
                },
                error: function(xhr, status, error) {
                    console.log('Terjadi kesalahan: ' + error);
                }
            });
        });
    </script>
@endpush
