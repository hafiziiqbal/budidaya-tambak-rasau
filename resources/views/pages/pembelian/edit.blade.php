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
        <div id="alertJs">
            @include('components.alert')
        </div>
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
                <input type="hidden" name="type" value="update-header">
                <label for="inputNama" class="form-label">Tanggal Beli</label>
                <div class="input-group">
                    <span class="input-group-text" id="basic-addon1"><i class="fa fa-calendar"></i></span>
                    <input type="text" name="tanggal_beli" class="form-control" aria-describedby="basic-addon1"
                        data-date-format="dd-mm-yyyy" data-provide="datepicker" value="">>
                </div>
                <small class="text-danger" id="errorTglBeli"></small>
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
                <small class="text-danger" id="errorSupplier"></small>
            </div>
            <div class="mb-3">
                <label for="inputBruto" class="form-label">Total Bruto</label>
                <input type="text" class="form-control mata-uang" id="inputBruto" value="" readonly
                    name="total_bruto">
                <small class="text-danger" id="errorTotalBruto"></small>
            </div>
            <div class="mb-3">
                <label for="inputPotonganHarga" class="form-label">Potongan Harga</label>
                <input type="text" class="form-control number mata-uang" id="inputPotonganHarga" name="potongan_harga"
                    value="">
                <small class="text-danger" id="errorPotonganHarga"></small>
            </div>
            <div class="mb-3">
                <label for="inputBruto" class="form-label">Total Netto</label>
                <input type="text" class="form-control mata-uang" id="inputNetto" name="total_netto" value=""
                    readonly>
                <small class="text-danger" id="errorTotalNetto"></small>
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
        <label class="info-delete ms-1 mb-3 text-success fw-bold"></label>
    </div>
    <button type="button" class="btn btn-dark my-3" id="btnTambahPembagian"><i class="fa fa-plus"></i> Tambah
    </button>
@endsection

@push('script')
    <script>
        let detailBeli
        let number = 0

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
                    $(`input[name='tanggal_beli']`).val(response.tgl_beli.split("-").reverse().join("-"));

                    // default total bruto
                    $(`#inputBruto`).val(response.total_bruto);

                    // default potongan harga
                    $(`input[name='potongan_harga']`).val(response.potongan_harga);

                    // default total netto
                    $(`#inputNetto`).val(response.total_netto);

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
            $('#btnSimpanHeader').attr('disabled', 'disabled')
            $('#btnSimpanHeader').children().removeClass('d-none')

            e.preventDefault();
            let action = $(this).attr("action"); //get submit action from form
            let method = $(this).attr("method"); // get submit method
            let form_data = new FormData($(this)[0]); // convert form into formdata                    

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
                        $("small[id^='error']").html('');
                        $('#btnSimpanHeader').removeAttr('disabled')
                        $('#btnSimpanHeader').children().addClass('d-none')
                        $('#alertNotif').removeClass('d-none');
                        $('#alertNotif span').html(response.success);
                        $('#alertJs').append(`@include('components.alert')`);
                        loadDataHeader();
                    }
                },
                error: function(response) { // handle the error            
                    let errors = response.responseJSON.errors
                    $("small[id^='error']").html('');
                    if (errors.tanggal_beli) {
                        $(`#errorTglBeli`).html(`*${errors.tanggal_beli[0]}`)
                    }
                    if (errors.supplier) {
                        $(`#errorSupplier`).html(`*${errors.supplier[0]}`)
                    }
                    if (errors.total_bruto) {
                        $(`#errorTotalBruto`).html(`*${errors.total_bruto[0]}`)
                    }
                    if (errors.total_netto) {
                        $(`#errorTotalNetto`).html(`*${errors.total_netto[0]}`)
                    }
                    if (errors.potongan_harga) {
                        $(`#errorPotonganHarga`).html(`*${errors.potongan_harga[0]}`)
                    }

                    $('#btnSimpanHeader').removeAttr('disabled')
                    $('#btnSimpanHeader').children().addClass('d-none')
                    loadDataHeader();
                },

            })


        });

        // membuat element detail beli
        function loadElementDetailBeli(item, index) {
            let form = $(
                `<form name="form_detail${index}" id="formDetail${index}" method="POST" action="/pembelian/detail/${item.id}/edit" class=" mb-5">
                    <div class="card mb-4"></div>    
                    </form>`
            )
            let cardHeader = $(
                `<div class="card-header border d-flex justify-content-between align-items-center">
                            <div class="fw-bold">
                                <span class="me-2 title">Detail Beli</span>
                                <label class="text-success fw-bold status-header d-none mb-2">
                                    <i class="fa fa-check" aria-hidden="true"></i>
                                    <span></span>
                                </label>
                                <label class="text-danger fw-bold status-error-header d-none  mb-2">
                                    <i class="fa fa-exclamation-triangle" aria-hidden="true"></i>
                                    <span></span>
                                </label>
                            </div>
                            <button type="button" class="btn-card btn-close d-none"  aria-label="Close"></button>
                    </div>`
            )
            let cardBody = $(
                `<div class="card-body border">
                        <div id="alert${index}">
                            @include('components.alert')
                        </div>
                        @csrf
                        <input type="hidden" name="type" value="update-detail">
                        <div class="mb-3">
                            <label class="form-label">Pilih Produk</label>
                            <input type="hidden" class="alt" name="id_produk" value="${item.id_produk}">
                            <input type="hidden" name="id_header_beli" value="${item.id_header_beli}">
                            <select class="form-select produk${index}" data-placeholder="Pilih Produk" required>
                                <option></option>
                                @foreach ($produk as $value)
                                    <option value="{{ $value->id }}">
                                        {{ $value->nama }}
                                    </option>
                                @endforeach
                                
                            </select>
                            <small class="text-danger" id="errorProduk${index}"></small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Harga Satuan</label>
                            <input type="text" class="form-control mata-uang harga-satuan" name="harga_satuan" value="${item.harga_satuan}" required>
                            <small class="text-danger" id="errorHargaSatuan${index}"></small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Quantity</label>
                            <input type="number" class="form-control quantity" name="quantity" value="${item.quantity}" required>
                            <small class="text-danger" id="errorQuantity${index}"></small>
                        </div>
                        <div class="mb-3 quantity_stok">
                            <label class="form-label">Quantity Stok</label>
                            <input type="text" class="form-control quantity-stok" disabled value="${item.quantity_stok}">                            
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Diskon Persen</label>
                            <input type="number" class="form-control diskon-persen" name="diskon_persen" value="${item.diskon_persen ?? ''}">
                            <small class="text-danger" id="errorDiskonPersen${index}"></small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label ">Diskon Rupiah</label>
                            <input type="number" class="form-control diskon-rupiah mata-uang" name="diskon_rupiah" value="${item.diskon_rupiah ?? ''}">
                            <small class="text-danger" id="errorDiskonRupiah${index}"></small>
                        </div>

                        <div class="btn-update-content">
                            <button type="submit" class="btn btn-success" id="btnUpdateDetail${index}">
                                <i class="fas fa-spinner fa-spin d-none"></i>
                                Perbarui
                            </button>
                            <button type="button" onclick="return confirm('Data ini akan dihapus')" class="btn btn-danger" data-id="${item.id}" id="btnDeleteDetail${index}">
                                <i class="fas fa-spinner fa-spin d-none"></i>
                                Hapus
                            </button>
                        </div>
                        <div class="btn-store-content d-none">
                            <button type="submit" class="btn btn-primary" id="btnSaveDetail${index}">
                                <i class="fas fa-spinner fa-spin d-none"></i>
                                Simpan
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

            $(`.produk${index}`).val(item.id_produk)
            $(`.produk${index}`).trigger('change');
            $(`.produk${index}`).select2("enable", false);


            // handle sumbit
            $(`#formDetail${index}`).on("submit", function(e) { //id of form 
                e.preventDefault();
                $(`#btnUpdateDetail${index}`).attr('disabled', 'disabled')
                $(`#btnUpdateDetail${index}`).children().removeClass('d-none')
                $(`#btnDeleteDetail${index}`).attr('disabled', 'disabled')

                let action = $(this).attr("action"); //get submit action from form
                let method = $(this).attr("method"); // get submit method
                let form_data = new FormData($(this)[0]); // convert form into formdata        

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
                            $(`#btnSaveDetail${index}`).removeAttr('disabled')
                            $(`#btnSaveDetail${index}`).children().addClass('d-none')
                            $(`#btnDeleteDetail${index}`).removeAttr('disabled')

                            $(`#alert${index} #alertNotif`).removeClass('d-none');
                            $(`#alert${index} #alertNotif span`).html(response.success);
                            $(`#alert${index}`).append(`@include('components.alert')`);
                            loadDataHeader();
                        }

                        if (response.save_detail != undefined) {

                            $(`.produk${index}`).select2("enable", false);
                            $(`#formDetail${index} .btn-update-content`).removeClass('d-none');
                            $(`#formDetail${index} .btn-store-content`).addClass('d-none');
                            $(`#formDetail${index} .btn-card.btn-close`).addClass('d-none');

                            $(`#formDetail${index} input.alt`).val($(`.produk${index}`).val());
                            $(`#formDetail${index} input.alt`).attr('name', 'id_produk');
                            $(`#formDetail${index} select.produk${index}`).removeAttr('name');
                            $(`#formDetail${index} input[name='type']`).val('update-detail');

                            $(`#formDetail${index}`).attr('action',
                                `/pembelian/detail/${response.id}/edit`)
                            $(`#btnDeleteDetail${index}`).attr('data-id', response.id);
                        }
                    },
                    error: function(response) { // handle the error            
                        let errors = response.responseJSON.errors
                        $("small[id^='error']").html('');
                        $(`#btnUpdateDetail${index}`).removeAttr('disabled')
                        $(`#btnSaveDetail${index}`).removeAttr('disabled')
                        $(`#btnUpdateDetail${index}`).children().addClass('d-none')
                        $(`#btnSaveDetail${index}`).children().addClass('d-none')
                        $(`#btnDeleteDetail${index}`).removeAttr('disabled')

                        if (errors.id_produk) {
                            $(`#errorProduk${index}`).html(
                                `*${errors.id_produk}`)
                        }
                        if (errors.harga_satuan) {
                            $(`#errorHargaSatuan${index}`).html(
                                `*${errors.harga_satuan}`)
                        }
                        if (errors.quantity) {
                            $(`#errorQuantity${index}`).html(
                                `*${errors.quantity}`)
                        }
                        if (errors.diskon_persen) {
                            $(`#errorDiskonPersen${index}`).html(
                                `*${errors.diskon_persen}`)
                        }
                        if (errors.diskon_rupiah) {
                            $(`#errorDiskonRupiah${index}`).html(
                                `*${errors.diskon_rupiah}`)
                        }

                        loadDataHeader();
                    },

                })
            });

            $(`#btnDeleteDetail${index}`).click(function(e) {
                let id = $(this).data('id');
                $.ajax({
                    url: `/pembelian/detail/delete/${id}`,
                    dataType: 'json', // what to expect back from the server
                    cache: false,
                    contentType: false,
                    processData: false,
                    type: 'GET',

                    success: function(response) {
                        if (response.success != undefined) {
                            $(`#formDetail${index}`).remove();
                            $(`.info-delete`).html(response
                                .success)
                            setTimeout(function() {
                                $(`.info-delete`).html(
                                    "");
                            }, 3000);
                            loadDataHeader();
                        }

                    },
                    error: function(response) { // handle the error                                    
                        $(`#formDetail${index} .status-error-header`).removeClass('d-none')
                        $(`#formDetail${index} .status-error-header span`).html(response
                            .success)
                        setTimeout(function() {
                            $(`#formDetail${index} .status-error-header`).addClass(
                                "d-none");
                        }, 3000);
                        loadDataHeader();
                    },

                })
            })
        }

        number = detailBeli.length;
        detailBeli.forEach((item, index) => {
            loadElementDetailBeli(item, index)
        });

        // tambah element detail
        $('#btnTambahPembagian').click(function() {
            number = number + 1
            idHeader = {!! $id !!}
            loadElementDetailBeli({
                id_produk: null,
                id_header_beli: idHeader
            }, number)

            // hapus semua nilai input
            $(':input', `#formDetail${number}`)
                .not(':button, :submit, :reset, :hidden')
                .val('')
                .prop('checked', false)
                .prop('selected', false);

            $(`.produk${number}`).removeAttr('disabled');
            $(`#formDetail${number}`).attr('action', '/pembelian/detail')
            $(`#formDetail${number} input[name='type']`).val('store-detail');

            $(`#formDetail${number} .btn-update-content`).addClass('d-none');
            $(`#formDetail${number} .btn-store-content`).removeClass('d-none');
            $(`#formDetail${number} .btn-card.btn-close`).removeClass('d-none');
            $(`#formDetail${number} .quantity_stok`).remove()

            $(`#formDetail${number} input.alt[name='id_produk']`).removeAttr('name');
            $(`#formDetail${number} select.produk${number}`).attr('name', 'id_produk');

            $(`#formDetail${number} .btn-card.btn-close`).click(function() {
                $(this).parent().parent().parent().remove();
            })
        })
    </script>
@endpush
