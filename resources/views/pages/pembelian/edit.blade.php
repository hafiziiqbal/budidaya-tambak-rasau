@extends('layouts.admin')
@section('content')
    <h1 class="mt-4">Edit Pembelian</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('pembelian') }}">Pembelian</a></li>
        <li class="breadcrumb-item active">Edit Pembelian</li>
    </ol>

    {{-- header beli --}}
    <form method="POST" id="formHeader" action="{{ route('pembelian.update', $id) }}" name="form_header">
        @csrf
        <div id="headerPembelian" class="mb-4">
            <div class="bg-info p-2 border-dark border-bottom mb-3">
                <label class="fw-bold">Header Pembelian</label>
            </div>

            <label class="text-success fw-bold status-header d-none mb-2"><i class="fa fa-check" aria-hidden="true"></i>
                <span></span></label>
            <label class="text-danger fw-bold status-error-header d-none  mb-2"><i class="fa fa-exclamation-triangle"
                    aria-hidden="true"></i>
                <span></span></label>

            <div class="mb-3">
                <label for="inputNama" class="form-label">Tanggal Beli</label>
                <div class="input-group mb-3">
                    <span class="input-group-text" id="basic-addon1"><i class="fa fa-calendar"></i></span>
                    <input type="text" name="tgl_beli" class="form-control" aria-describedby="basic-addon1"
                        data-date-format="dd-mm-yyyy" data-provide="datepicker" value="">>
                </div>
            </div>

            <div class="mb-3">
                <label for="selectSupplier" class="form-label">Supplier</label>
                <select class="form-select" id="selectSupplier" data-placeholder="Pilih Supplier" name="supplier">
                    <option></option>

                    @foreach ($supplier as $supplier)
                        <option value="{{ $supplier->id }}">
                            {{ $supplier->nama }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="mb-3">
                <label for="inputBruto" class="form-label">Total Bruto</label>
                <input type="text" class="form-control mata-uang" id="inputBruto" value="" disabled>

            </div>
            <div class="mb-3">
                <label for="inputPotonganHarga" class="form-label">Potongan Harga</label>
                <input type="text" class="form-control number mata-uang" id="inputPotonganHarga" required
                    name="potongan_harga" value="">
            </div>
            <div class="mb-3">
                <label for="inputBruto" class="form-label">Total Netto</label>
                <input type="text" class="form-control mata-uang" id="inputNetto" value="" disabled>

            </div>
            <button type="submit" class="btn btn-primary w-100" id="btnSimpanHeader"><i
                    class="fas fa-spinner fa-spin d-none me-2"></i>Simpan
                Perubahan</button>
        </div>
    </form>
    <div id="detail">
        <div class="bg-info p-2 border-dark border-bottom mb-3 mt-5">
            <label class="fw-bold">Detail Pembelian</label>
        </div>
        {{-- <form name="form_detail" method="POST" action="" class="d-none form-detail first mb-5">
            @csrf
            <div class="mb-4">
                <div class="card-header border d-flex justify-content-between align-items-center">
                    <div class="fw-bold">
                        <span class="me-2 title"></span>
                        <label class="text-success fw-bold status-header d-none mb-2"><i class="fa fa-check"
                                aria-hidden="true"></i>
                            <span></span>
                        </label>
                        <label class="text-danger fw-bold status-error-header d-none  mb-2"><i
                                class="fa fa-exclamation-triangle" aria-hidden="true"></i>
                            <span></span>
                        </label>
                    </div>
                    <div class="btn-content d-none">
                        <button class="btn btn-primary simpan-detail-baru me-2"> <i
                                class="fas fa-spinner fa-spin d-none me-2"></i>Simpan</button>
                    </div>
                </div>
                <div class="card-body border">
                    <div class="mb-3">
                        <label class="form-label">Pilih Produk</label>
                        <select class="form-select produk" data-placeholder="Pilih Produk">
                            <option></option>
                            @foreach ($produk as $value)
                                <option value="{{ $value->id }}">
                                    {{ $value->nama }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Harga Satuan</label>
                        <input type="text" class="form-control mata-uang harga-satuan">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Quantity</label>
                        <input type="text" class="form-control quantity">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Quantity Stok</label>
                        <input type="text" class="form-control quantity-stok" disabled>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Diskon Persen</label>
                        <input type="text" class="form-control diskon-persen">
                    </div>

                    <div class="mb-3">
                        <label class="form-label ">Diskon Rupiah</label>
                        <input type="text" class="form-control diskon-rupiah mata-uang">
                    </div>

                    <div class="btn-update-content d-none">
                        <button type="button" class="btn btn-success btn-update-detail"><i
                                class="fas fa-spinner fa-spin d-none"></i>
                            Perbarui</button>
                        <button type="button" class="btn btn-danger btn-delete-detail"><i
                                class="fas fa-spinner fa-spin d-none"></i>
                            Hapus</button>
                    </div>
                </div>

            </div>
        </form> --}}
    </div>
    <button type="button" class="btn btn-dark my-3" id="btnTambahPembagian"><i class="fa fa-plus"></i> Tambah
    </button>
@endsection

@push('script')
    <script>
        let detailBeli
        // inisialisasi form select 2
        $(".form-select").select2({
            theme: "bootstrap-5",
            containerCssClass: "select2--medium",
            dropdownCssClass: "select2--medium",
        });

        // load data header
        function loadDataHeader() {
            let idHeader = {!! $id !!}
            $.ajax({
                url: `/pembelian/${idHeader}/edit-json`,
                dataType: 'json', // what to expect back from the server
                cache: false,
                async: false,
                contentType: false,
                processData: false,
                type: 'GET',

                success: function(response) {
                    detailBeli = response.detail_beli
                    // default value supplier
                    $(`#selectSupplier`).val(response.id_supplier)
                    $(`#selectSupplier`).trigger('change');

                    // default tgl_beli                
                    $(`input[name='tgl_beli']`).val(response.tgl_beli.split("-").reverse().join("-"));

                    // default total bruto
                    $(`#inputBruto`).val(response.total_bruto);

                    // default potongan harga
                    $(`input[name='potongan_harga']`).val(response.potongan_harga);

                    // default total netto
                    $(`#inputNetto`).val(response.total_netto);

                    $('input.mata-uang').priceFormat({
                        prefix: 'Rp ',
                        centsLimit: 0,
                        thousandsSeparator: '.'
                    });

                },
                error: function(response) { // handle the error
                    try {} catch (err) {

                    }

                },

            })
        }
        loadDataHeader()

        // handle form_header
        $("#formHeader").on("submit", function(e) { //id of form 
            console.log('masuk');
            $('#btnSimpanHeader').attr('disabled', 'disabled')
            $('#btnSimpanHeader').children().removeClass('d-none')

            e.preventDefault();
            let action = $(this).attr("action"); //get submit action from form
            let method = $(this).attr("method"); // get submit method
            let form_data = new FormData($(this)[0]); // convert form into formdata        

            // hilangkan karakter RP dan titik
            form_data.set('potongan_harga', $(`input[name='potongan_harga']`).val().replace("Rp ", "").replace(
                /\./g, ""));

            $.ajax({
                url: action,
                dataType: 'json', // what to expect back from the server
                cache: false,
                contentType: false,
                processData: false,
                data: form_data,
                type: method,

                success: function(response) {
                    if (response.success != undefined) {
                        $('#btnSimpanHeader').removeAttr('disabled')
                        $('#btnSimpanHeader').children().addClass('d-none')
                        $('.status-header').removeClass('d-none')
                        $('.status-header span').html(response.success)
                        setTimeout(function() {
                            $(".status-header").addClass("d-none");
                        }, 3000);
                        loadDataHeader();
                    }
                },
                error: function(response) { // handle the error            
                    $('#btnSimpanHeader').removeAttr('disabled')
                    $('#btnSimpanHeader').children().addClass('d-none')
                    $('.status-error-header').removeClass('d-none')
                    $('.status-error-header span').html(response.responseText)
                    setTimeout(function() {
                        $(".status-error-header").addClass("d-none");
                    }, 3000);
                    loadDataHeader();
                },

            })


        });

        // membuat element detail beli
        function loadElementDetailBeli() {
            detailBeli.forEach((item, index) => {
                let form = $(
                    `<form name="form_detail${index}" id="formDetail${index}" method="POST" action="/pembelian/detail/${item.id}/edit" class=" mb-5">
                    <div class="card mb-4"></div>    
                    </form>`
                )
                let cardHeader = $(
                    `<div class="card-header border d-flex justify-content-between align-items-center">
                            <div class="fw-bold">
                                <span class="me-2 title">Detail Beli ${index+1}</span>
                                <label class="text-success fw-bold status-header d-none mb-2">
                                    <i class="fa fa-check" aria-hidden="true"></i>
                                    <span></span>
                                </label>
                                <label class="text-danger fw-bold status-error-header d-none  mb-2">
                                    <i class="fa fa-exclamation-triangle" aria-hidden="true"></i>
                                    <span></span>
                                </label>
                            </div>
                    </div>`
                )

                let cardBody = $(
                    `<div class="card-body border">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Pilih Produk</label>
                            <select class="form-select produk${index}" data-placeholder="Pilih Produk">
                                <option></option>
                                @foreach ($produk as $value)
                                    <option value="{{ $value->id }}">
                                        {{ $value->nama }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Harga Satuan</label>
                            <input type="text" class="form-control mata-uang harga-satuan" name="harga_satuan" value="${item.harga_satuan}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Quantity</label>
                            <input type="text" class="form-control quantity" name="quantity" value="${item.quantity}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Quantity Stok</label>
                            <input type="text" class="form-control quantity-stok" disabled value="${item.quantity_stok}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Diskon Persen</label>
                            <input type="text" class="form-control diskon-persen" name="diskon_persen" value="${item.diskon_persen ?? ''}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label ">Diskon Rupiah</label>
                            <input type="text" class="form-control diskon-rupiah mata-uang" name="diskon_rupiah" value="${item.diskon_rupiah ?? ''}">
                        </div>

                        <div class="btn-update-content">
                            <button type="submit" class="btn btn-success" id="btnUpdateDetail${index}">
                                <i class="fas fa-spinner fa-spin d-none"></i>
                                Perbarui
                            </button>
                            <button type="button" class="btn btn-danger" id="btnDeleteDetail${index}">
                                <i class="fas fa-spinner fa-spin d-none"></i>
                                Hapus
                            </button>
                        </div>
                    </div>`
                )

                $('#detail').append(form)
                $(`#formDetail${index} .card`).append(cardHeader, cardBody)

                // inisialisasi form select 2
                $(".form-select").select2({
                    theme: "bootstrap-5",
                    containerCssClass: "select2--medium",
                    dropdownCssClass: "select2--medium",
                });
                $(`.produk${index}`).val(item.id_produk.toString())
                $(`.produk${index}`).trigger('change');
                $(`.produk${index}`).select2("enable", false)

                // handle sumbit
                $(`#formDetail${index}`).on("submit", function(e) { //id of form 
                    e.preventDefault();
                    $(`#btnUpdateDetail${index}`).attr('disabled', 'disabled')
                    $(`#btnUpdateDetail${index}`).children().removeClass('d-none')
                    $(`#btnDeleteDetail${index}`).attr('disabled', 'disabled')

                    let action = $(this).attr("action"); //get submit action from form
                    let method = $(this).attr("method"); // get submit method
                    let form_data = new FormData($(this)[0]); // convert form into formdata        

                    // hilangkan karakter RP dan titik
                    form_data.set('harga_satuan', $(`#formDetail${index} input[name='harga_satuan']`).val()
                        .replace(/[^0-9]/g, ''));
                    form_data.set('diskon_rupiah', $(`#formDetail${index} input[name='diskon_rupiah']`)
                        .val().replace(/[^0-9]/g, ''));

                    $.ajax({
                        url: action,
                        dataType: 'json', // what to expect back from the server
                        cache: false,
                        contentType: false,
                        processData: false,
                        data: form_data,
                        type: method,

                        success: function(response) {
                            console.log(response);
                            if (response.success != undefined) {
                                $(`#btnUpdateDetail${index}`).removeAttr('disabled')
                                $(`#btnUpdateDetail${index}`).children().addClass('d-none')
                                $(`#btnDeleteDetail${index}`).removeAttr('disabled')
                                $(`#formDetail${index} .status-header`).removeClass('d-none')
                                $(`#formDetail${index} .status-header span`).html(response
                                    .success)
                                setTimeout(function() {
                                    $(`#formDetail${index} .status-header`).addClass(
                                        "d-none");
                                }, 3000);
                                loadDataHeader();

                            }
                        },
                        error: function(response) { // handle the error            
                            // $('#btnSimpanHeader').removeAttr('disabled')
                            // $('#btnSimpanHeader').children().addClass('d-none')
                            // $('.status-error-header').removeClass('d-none')
                            // $('.status-error-header span').html(response.responseText)
                            // setTimeout(function() {
                            //     $(".status-error-header").addClass("d-none");
                            // }, 3000);
                            // loadDataHeader();
                        },

                    })


                });
            });
        }
        loadElementDetailBeli()






        // format mata uang
        $('input.mata-uang').priceFormat({
            prefix: 'Rp ',
            centsLimit: 0,
            thousandsSeparator: '.'
        });
    </script>
@endpush
