@extends('layouts.admin')
@section('content')
<h1 class="mt-4">Edit Jual</h1>
<ol class="breadcrumb mb-4">
    <li class="breadcrumb-item"><a href="{{ route('jual') }}">Jual</a></li>
    <li class="breadcrumb-item active">Edit Jual</li>
</ol>

{{-- header beli --}}
<form method="POST" id="formHeader" action="{{ route('jual.update', $id) }}" name="form_header">
    @csrf
    <input type="hidden" name="type" value="update-header">
    <div id="headerPembelian" class="mb-4">
        <div class="bg-info p-2 border-dark border-bottom mb-3">
            <label class="fw-bold">Header Jual</label>
        </div>
        <div id="alertJs">
            @include('components.alert')
        </div>
        <label class="text-success fw-bold status-header d-none mb-2"><i class="fa fa-check" aria-hidden="true"></i>
            <span></span></label>
        <label class="text-danger fw-bold status-error-header d-none  mb-2"><i class="fa fa-exclamation-triangle"
                aria-hidden="true"></i>
            <span></span></label>

        <div class="mb-3">
            <label for="selectCustomer" class="form-label">Customer</label>
            <select class="form-select" id="selectCustomer" data-placeholder="Pilih Customer" name="customer">
                <option></option>
                @foreach ($customer as $value)
                <option value="{{ $value->id }}">
                    {{ $value->nama }}
                </option>
                @endforeach
            </select>
            <small class="text-danger" id="errorCustomer"></small>
        </div>
        <div class="mb-3">
            <label for="inputTotalBruto" class="form-label">Total Bruto</label>
            <input type="number" class="form-control money-format" id="inputTotalBruto" name="total_bruto" readonly>
            <small class="text-danger" id="errorTotalBruto"></small>
        </div>
        <div class="mb-3">
            <label for="inputPotonganHarga" class="form-label">Potongan Harga</label>
            <input type="number" class="form-control money-format" id="inputPotonganHarga" name="potongan_harga">
            <small class="text-danger" id="errorPotonganHarga"></small>
        </div>
        <div class="mb-3">
            <label for="inputTotalNetto" class="form-label">Total Netto</label>
            <input type="number" class="form-control money-format" id="inputTotalNetto" name="total_netto" readonly>
            <small class="text-danger" id="errorTotalNetto"></small>
        </div>
        <div class="mb-3">
            <label for="inputPay" class="form-label">Bayar</label>
            <input type="number" class="form-control money-format" id="inputPay" name="pay">
            <small class="text-danger" id="errorPay"></small>
        </div>
        <div class="mb-3">
            <label for="inputChange" class="form-label">Kembali</label>
            <input type="number" class="form-control money-format" id="inputChange" name="change" readonly>
            <small class="text-danger" id="errorChange"></small>
        </div>
        <button type="submit" class="btn btn-primary w-100" id="btnSimpanHeader"><i
                class="fas fa-spinner fa-spin d-none me-2"></i>Simpan
            Perubahan</button>
    </div>
</form>
<div id="detail">

    <div class="bg-info p-2 border-dark border-bottom mb-3 mt-5">
        <label class="fw-bold">Detail Jual</label>
    </div>
    <div id="alertGeneral">
        @include('components.alert')
    </div>
    <label class="info-delete ms-1 mb-3 text-success fw-bold"></label>
</div>
<button type="button" class="btn btn-dark my-3" id="btnTambahPembagian"><i class="fa fa-plus"></i> Tambah
</button>
@endsection

@push('script')
<script>
    let detailJual
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
                url: `/penjualan/${idHeader}/edit-json`,
                dataType: 'json', // what to expect back from the server
                cache: false,
                async: false,
                contentType: false,
                processData: false,
                type: 'GET',

                success: function(response) {
                    detailJual = response.detail_jual
                    // default value supplier
                    $(`#selectCustomer`).val(response.id_customer)
                    $(`#selectCustomer`).trigger('change');

                    // default total bruto
                    $(`#inputTotalBruto`).val(response.total_bruto);

                    // default potongan harga
                    $(`#inputPotonganHarga`).val(response.potongan_harga);

                    // default total netto
                    $(`#inputTotalNetto`).val(response.total_netto);

                    // pay
                    $(`#inputPay`).val(response.pay);

                    // pay
                    $(`#inputChange`).val(response.change);

                    $( 'input.money-format' ).mask('000.000.000.000.000', {reverse: true});

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
            for (var pair of form_data.entries())
                {
                    form_data.set(pair[0], pair[1].toString().replace(/\./g, ''));       
                }
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

                        $('#alertNotif').removeClass('d-none');
                        $('#alertNotif span').html(response.success);
                        $('#alertJs').append(`@include('components.alert')`);
                        loadDataHeader();
                        $( 'input.money-format' ).mask('000.000.000.000.000', {reverse: true});
                    }
                },
                error: function(response) { // handle the error            
                    $('#btnSimpanHeader').removeAttr('disabled')
                    $('#btnSimpanHeader').children().addClass('d-none')
                    loadDataHeader();
                    $( 'input.money-format' ).mask('000.000.000.000.000', {reverse: true});
                    let errors = response.responseJSON.errors
                    $("small[id^='error']").html('');

                    if (errors.general) {
                        $(`#alertNotifError`).removeClass('d-none');
                        $(`#alertNotifError span`).html(errors.general);
                        $(`#alertJs`).append(`@include('components.alert')`);
                    }

                    if (errors.customer) {
                        $(`#errorCustomer`).html(`*${errors.customer}`)
                    }
                    if (errors.total_bruto) {
                        $(`#errorTotalBruto`).html(`*${errors.total_bruto}`)
                    }
                    if (errors.potongan_harga) {
                        $(`#errorPotonganHarga`).html(`*${errors.potongan_harga}`)
                    }
                    if (errors.total_netto) {
                        $(`#errorTotalNetto`).html(`*${errors.total_netto}`)
                    }
                    if (errors.pay) {
                        $(`#errorPay`).html(`*${errors.pay}`)
                    }
                    if (errors.change) {
                        $(`#errorChange`).html(`*${errors.change}`)
                    }

                },

            })


        });

        // membuat element detail beli
        function loadElementDetailJual(item, index) {
            let form = $(
                `<form name="form_detail${index}" id="formDetail${index}" method="POST" action="/penjualan/detail/${item.id}/edit" class=" mb-5">
                    <div class="card mb-4"></div>    
                    </form>`
            )
            let cardHeader = $(
                `<div class="card-header border d-flex justify-content-between align-items-center">
                            <div class="fw-bold">
                                <span class="me-2 title">Detail Jual</span>
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
                        <input type="hidden" name="id" value="${item.id}">                        
                        <input type="hidden" class="alt" name="id_detail_panen" value="${item.id_detail_panen}">
                        <input type="hidden" name="id_header_jual" id="idHeader${index}" value="${item.id_heder_panen}">                        
                        <div class="mb-3">
                            <label class="form-label">Pilih Produk Panen</label>
                            <select class="form-select select-panen${index}" data-placeholder="Pilih Produk Panen" name="id_detail_panen" required>
                                <option></option>
                                @foreach ($panen as $value)
                                    <option value="{{ $value->id }}">
                                        {{ $value->header_panen->tgl_panen . ' | ' . $value->detail_pembagian_bibit->header_pembagian_bibit->detail_beli->produk->nama }}
                                    </option>
                                @endforeach
                            </select>
                            <small class="text-danger" id="errorPanen${index}"></small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Harga Satuan</label>
                            <input type="text" class="form-control money-format harga-satuan" name="harga_satuan" value="${item.harga_satuan}" required>
                            <small class="text-danger" id="errorhargaSatuan${index}"></small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Diskon</label>
                            <input type="text" readonly class="form-control diskon" name="diskon" value="${item.diskon}" required>
                            <small class="text-danger" id="errorDiskon${index}"></small>                            
                        </div>                       
                        <div class="mb-3">
                            <label class="form-label">Quantity</label>
                            <input type="text" class="form-control quantity" name="quantity" value="${item.quantity}" required>                                                
                            <small id="errorQuantity${index}" class="text-danger"></small>
                        </div>                       
                        <div class="mb-3">
                            <label class="form-label">Subtotal</label>
                            <input type="text" class="form-control subtotal money-format" name="subtotal" value="${item.sub_total}" required readonly>
                            <small class="text-danger" id="errorSubtotal${index}"></small>
                        </div>                       
                        <div class="btn-update-content">
                            <button type="submit" class="btn btn-success" id="btnUpdateDetail${index}">
                                <i class="fas fa-spinner fa-spin d-none"></i>
                                Perbarui
                            </button>
                            <button type="button" class="btn btn-danger" data-id="${item.id}" id="btnDeleteDetail${index}">
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
            $(`.select-panen${index}`).val(item.id_detail_panen)
            $(`.select-panen${index}`).select2("enable", false);
            $(`.select-panen${index}`).trigger('change');



            // handle sumbit
            $(`#formDetail${index}`).on("submit", function(e) { //id of form 
                e.preventDefault();
                $(`#btnUpdateDetail${index}`).attr('disabled', 'disabled')
                $(`#btnUpdateDetail${index}`).children().removeClass('d-none')
                $(`#btnDeleteDetail${index}`).attr('disabled', 'disabled')

                let action = $(this).attr("action"); //get submit action from form
                let method = $(this).attr("method"); // get submit method
                let form_data = new FormData($(this)[0]); // convert form into formdata        
                for (var pair of form_data.entries())
                {
                    if (pair[0] != 'quantity' && pair[0] != 'diskon') {
                        form_data.set(pair[0], pair[1].toString().replace(/\./g, ''));           
                    }
                }
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

                            $(`#btnUpdateDetail${index}`).removeAttr('disabled')
                            $(`#btnUpdateDetail${index}`).children().addClass('d-none')
                            $(`#btnSaveDetail${index}`).removeAttr('disabled')
                            $(`#btnSaveDetail${index}`).children().addClass('d-none')
                            $(`#btnDeleteDetail${index}`).removeAttr('disabled')

                            $(`#alert${index} #alertNotif`).removeClass('d-none');
                            $(`#alert${index} #alertNotif span`).html(response.success);
                            $(`#alert${index}`).append(`@include('components.alert')`);

                            setTimeout(function() {
                                loadDataHeader();
                                $('#detail').empty();
                                detailJual.forEach((item, index) => {
                                    loadElementDetailJual(item, index)
                                });
                            }, 500);

                            $( 'input.money-format' ).mask('000.000.000.000.000', {reverse: true});
                        }

                        if (response.save_detail != undefined) {
                            $(`#formDetail${index} .btn-update-content`).removeClass('d-none');
                            $(`#formDetail${index} .btn-store-content`).addClass('d-none');
                            $(`#formDetail${index} .btn-card.btn-close`).addClass('d-none');
                            $(`#formDetail${index}`).attr('action',
                                `/penjualan/detail/${response.id}/edit`)
                            $(`#btnDeleteDetail${index}`).attr('data-id', response.id);

                            $(`.select-panen${index}`).select2("enable", false);

                            $(`#formDetail${index} input.alt`).val($(`.select-panen${index}`).val());
                            $(`#formDetail${index} input.alt`).attr('name', 'id_detail_panen');
                            $(`#formDetail${index} select.select-panen${index}`).removeAttr('name');
                            $(`#formDetail${index} input[name='type']`).val('update-detail');
                            $( 'input.money-format' ).mask('000.000.000.000.000', {reverse: true});
                        }
                    },
                    error: function(response) { // handle the error            
                        $(`#btnUpdateDetail${index}`).removeAttr('disabled')
                        $(`#btnSaveDetail${index}`).removeAttr('disabled')
                        $(`#btnUpdateDetail${index}`).children().addClass('d-none')
                        $(`#btnSaveDetail${index}`).children().addClass('d-none')
                        $(`#btnDeleteDetail${index}`).removeAttr('disabled')
                        loadDataHeader();
                        $( 'input.money-format' ).mask('000.000.000.000.000', {reverse: true});
                        let errors = response.responseJSON.errors
                        $("small[id^='error']").html('');

                        if (errors.id_detail_panen) {
                            $(`#errorPanen${index}`).html(
                                `*${errors.id_detail_panen}`)
                        }
                        if (errors.harga_satuan) {
                            $(`#errorhargaSatuan${index}`).html(
                                `*${errors.id_harga_panen}`)
                        }
                        if (errors.diskon) {
                            $(`#errorDiskon${index}`).html(
                                `*${errors.diskon}`)
                        }
                        if (errors.quantity) {
                            $(`#errorQuantity${index}`).html(
                                `*${errors.quantity}`)
                        }
                        if (errors.subtotal) {
                            $(`#errorSubtotal${index}`).html(
                                `*${errors.subtotal}`)
                        }

                        if (errors.general) {
                            $(`#alert${index} #alertNotifError`).removeClass('d-none');
                            $(`#alert${index} #alertNotifError span`).html(errors.general);
                            $(`#alert${index}`).append(`@include('components.alert')`);
                        }
                    },

                })
            });

            $(`#btnDeleteDetail${index}`).click(function(e) {
                let id = $(this).data('id');
                $.ajax({
                    url: `/penjualan/detail/delete/${id}`,
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
                            $('#detail').empty();
                            detailJual.forEach((item, index) => {
                                loadElementDetailJual(item, index)
                            });

                            $( 'input.money-format' ).mask('000.000.000.000.000', {reverse: true});
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
                        $( 'input.money-format' ).mask('000.000.000.000.000', {reverse: true});
                    },

                })
            })
        }

        number = detailJual.length;
        detailJual.forEach((item, index) => {
            loadElementDetailJual(item, index)

        });
        

        // tambah element detail
        $('#btnTambahPembagian').click(function() {
            number = number + 1
            idHeader = {!! $id !!}
            loadElementDetailJual({
                id_produk: null
            }, number)

            // hapus semua nilai input
            $(':input', `#formDetail${number}`)
                .not(':button, :submit, :reset, :hidden')
                .val('')
                .prop('checked', false)
                .prop('selected', false);

            $(`.select-panen${number}`).removeAttr('disabled');

            $(`#formDetail${number} .card-body`).append(
                `<input type="hidden" value="${idHeader}" name="id_header_jual">`)

            $(`#formDetail${number}`).attr('action', '/penjualan/detail')
            $(`#formDetail${number} input[name='type']`).val('store-detail');

            $(`#formDetail${number} .btn-update-content`).addClass('d-none');
            $(`#formDetail${number} .btn-store-content`).removeClass('d-none');
            $(`#formDetail${number} .btn-card.btn-close`).removeClass('d-none');
            $(`#formDetail${number} .quantity_stok`).remove()

            $(`#formDetail${number} input.alt[name='id_detail_beli']`).removeAttr('name');
            $(`#formDetail${number} select.select.panen${number}`).attr('name', 'id_detail_beli')

            $(`#formDetail${number} .btn-card.btn-close`).click(function() {
                $(this).parent().parent().parent().remove();
            })
            $( 'input.money-format' ).mask('000.000.000.000.000', {reverse: true});
        })
        $( 'input.money-format' ).mask('000.000.000.000.000', {reverse: true});
</script>
@endpush