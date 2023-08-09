@extends('layouts.admin')
@section('content')
<h1 class="mt-4">Info Jual</h1>
<ol class="breadcrumb mb-4">
    <li class="breadcrumb-item"><a href="{{ route('jual') }}">Jual</a></li>
    <li class="breadcrumb-item active">Info Jual</li>
</ol>

{{-- header beli --}}
<form method="POST" id="formHeader" action="{{ route('jual.update', $id) }}" name="form_header">
    @csrf
    <div id="headerPembelian" class="mb-4">
        <div class="bg-info p-2 border-dark border-bottom mb-3">
            <label class="fw-bold">Header Jual</label>
        </div>

        <label class="text-success fw-bold status-header d-none mb-2"><i class="fa fa-check" aria-hidden="true"></i>
            <span></span></label>
        <label class="text-danger fw-bold status-error-header d-none  mb-2"><i class="fa fa-exclamation-triangle"
                aria-hidden="true"></i>
            <span></span></label>

        <div class="mb-3">
            <label for="selectCustomer" class="form-label">Customer</label>
            <select class="form-select" id="selectCustomer" data-placeholder="Pilih Customer" name="customer" disabled>
                <option></option>
                @foreach ($customer as $value)
                <option value="{{ $value->id }}">
                    {{ $value->nama }}
                </option>
                @endforeach
            </select>
        </div>
        <div class="mb-3">
            <label for="inputTotalBruto" class="form-label">Total Bruto</label>
            <input type="number" class="form-control money-format" id="inputTotalBruto" name="total_bruto" disabled>
        </div>
        <div class="mb-3">
            <label for="inputPotonganHarga" class="form-label">Potongan Harga</label>
            <input type="number" class="form-control money-format" id="inputPotonganHarga" name="potongan_harga"
                disabled>
        </div>
        <div class="mb-3">
            <label for="inputTotalNetto" class="form-label">Total Netto</label>
            <input type="number" class="form-control money-format" id="inputTotalNetto" name="total_netto" disabled>
        </div>
        <div class="mb-3">
            <label for="inputPay" class="form-label">Bayar</label>
            <input type="number" class="form-control money-format" id="inputPay" name="pay" disabled>
        </div>
        <div class="mb-3">
            <label for="inputChange" class="form-label">Kembali</label>
            <input type="number" class="form-control money-format" id="inputChange" name="change" disabled>
        </div>

    </div>
</form>
<div id="detail">

    <div class="bg-info p-2 border-dark border-bottom mb-3 mt-5">
        <label class="fw-bold">Detail Jual</label>
    </div>
    <label class="info-delete ms-1 mb-3 text-success fw-bold"></label>
</div>
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
                            <button type="button" class="btn-close d-none"  aria-label="Close"></button>
                    </div>`
            )
            let cardBody = $(
                `<div class="card-body border">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Pilih Produk</label>
                            <select class="form-select produk${index}" data-placeholder="Pilih Produk" name="id_produk" required>
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
                            <input type="text" class="form-control money-format harga-satuan" name="harga_satuan" value="${item.harga_satuan}" required disabled>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Diskon</label>
                            <input type="text" class="form-control diskon" name="diskon" value="${item.diskon}" required disabled>                    
                        </div>                       
                        <div class="mb-3">
                            <label class="form-label">Quantity</label>
                            <input type="text" class="form-control quantity" name="quantity" value="${item.quantity}" required disabled>                                                
                        </div>                       
                        <div class="mb-3">
                            <label class="form-label">Subtotal</label>
                            <input type="text" class="form-control subtotal money-format" name="subtotal" value="${item.sub_total}" required readonly disabled>                    
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
                            $(`#formDetail${index} .status-header`).removeClass('d-none')
                            $(`#formDetail${index} .status-header span`).html(response
                                .success)
                            loadDataHeader();
                            $('#detail').empty();
                            detailJual.forEach((item, index) => {
                                loadElementDetailJual(item, index)
                            });
                            setTimeout(function() {
                                $(`#formDetail${index} .status-header`).addClass(
                                    "d-none");

                            }, 3000);

                        }

                        if (response.save_detail != undefined) {
                            $(`#formDetail${index} .btn-update-content`).removeClass('d-none');
                            $(`#formDetail${index} .btn-store-content`).addClass('d-none');
                            $(`#formDetail${index} .btn-close`).addClass('d-none');
                            $(`#formDetail${index}`).attr('action',
                                `/pembelian/detail/${response.id}/edit`)
                            $(`#btnDeleteDetail${index}`).attr('data-id', response.id);
                        }
                    },
                    error: function(response) { // handle the error            
                        $(`#btnUpdateDetail${index}`).removeAttr('disabled')
                        $(`#btnSaveDetail${index}`).removeAttr('disabled')
                        $(`#btnUpdateDetail${index}`).children().addClass('d-none')
                        $(`#btnSaveDetail${index}`).children().addClass('d-none')
                        $(`#btnDeleteDetail${index}`).removeAttr('disabled')
                        $(`#formDetail${index} .status-error-header`).removeClass('d-none')
                        $(`#formDetail${index} .status-error-header span`).html(response
                            .error)
                        setTimeout(function() {
                            $(`#formDetail${index} .status-error-header`).addClass(
                                "d-none");
                        }, 3000);
                        loadDataHeader();
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

            $(`.produk${number}`).removeAttr('disabled');
            $(`#formDetail${number} .card-body`).append(
                `<input type="hidden" value="${idHeader}" name="id_header_jual">`)
            $(`#formDetail${number}`).attr('action', '/penjualan/detail')
            $(`#formDetail${number} .btn-update-content`).addClass('d-none');
            $(`#formDetail${number} .btn-store-content`).removeClass('d-none');
            $(`#formDetail${number} .btn-close`).removeClass('d-none');
            $(`#formDetail${number} .quantity_stok`).remove()


            $(`#formDetail${number} .btn-close`).click(function() {
                $(this).parent().parent().parent().remove();
            })
        })

        $( 'input.money-format' ).mask('000.000.000.000.000', {reverse: true});
</script>
@endpush