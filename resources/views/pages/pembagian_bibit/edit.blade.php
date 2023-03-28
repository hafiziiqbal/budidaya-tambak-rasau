@extends('layouts.admin')
@section('content')
    <h1 class="mt-4">Edit Pembagian Bibit</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('pembagian.bibit') }}">Pembagian Bibit</a></li>
        <li class="breadcrumb-item active">Edit Pembagian Bibit</li>
    </ol>

    {{-- header beli --}}
    <form method="POST" id="formHeader" action="{{ route('pembagian.bibit.update', $id) }}" name="form_header">
        @csrf
        <div id="headerPembagian" class="mb-4">

            <div class="bg-info p-2 border-dark border-bottom mb-3">
                <label class="fw-bold">Header Pembagian</label>
            </div>

            <label class="text-success fw-bold status-header d-none mb-2"><i class="fa fa-check" aria-hidden="true"></i>
                <span></span></label>
            <label class="text-danger fw-bold status-error-header d-none  mb-2"><i class="fa fa-exclamation-triangle"
                    aria-hidden="true"></i>
                <span></span></label>
            <div id="alertHeader">
                @include('components.alert')
            </div>
            <div class="mb-3">
                <label for="inputNama" class="form-label">Tanggal Pembagian</label>
                <div class="input-group mb-3">
                    <span class="input-group-text" id="basic-addon1"><i class="fa fa-calendar"></i></span>
                    <input type="text" name="tgl_pembagian" class="form-control" aria-describedby="basic-addon1"
                        data-date-format="dd-mm-yyyy" data-provide="datepicker" value="" required>
                </div>
            </div>

            <div class="mb-3">
                <label for="inputDetailBeli" class="form-label">Bibit Yang Dibagikan</label>
                <select class="form-select" id="inputDetailBeli" data-placeholder="Pilih Bibit" name="id_detail_beli">
                    <option></option>
                    @foreach ($pembelian as $value)
                        <option value="{{ $value->id }}" data-quantity="{{ $value->quantity }}">
                            @DateIndo($value->header_beli->tgl_beli){{ ' | ' . $value->produk->nama }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="mb-3">
                <label for="inputPanen" class="form-label">Sortir Kembali</label>
                <select class="form-select" id="inputPanen" data-placeholder="Pilih Ikan" name="id_detail_panen">
                    <option></option>
                    @foreach ($sortir as $value)
                        <option value="{{ $value->id }}">
                            @DateIndo($value->header_panen->tgl_panen){{ ' | ' . $value->detail_pembagian_bibit->header_pembagian_bibit->detail_beli->produk->nama }}
                        </option>
                    @endforeach
                </select>
            </div>

            <button type="submit" class="btn btn-primary w-100" id="btnSimpanHeader"><i
                    class="fas fa-spinner fa-spin d-none me-2"></i>Simpan
                Perubahan</button>
        </div>
    </form>
    <div id="detail">
        <div class="bg-info p-2 border-dark border-bottom mb-3 mt-5">
            <label class="fw-bold">Detail Pembagian</label>
        </div>
        <label class="info-delete ms-1 mb-3 text-success fw-bold"></label>
    </div>
    <button type="button" class="btn btn-dark my-3" id="btnTambahPembagian"><i class="fa fa-plus"></i> Tambah
    </button>
@endsection

@push('script')
    <script>
        let detailBagi
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
                url: `/pembagian-bibit/${idHeader}/edit-json`,
                dataType: 'json', // what to expect back from the server
                cache: false,
                async: false,
                contentType: false,
                processData: false,
                type: 'GET',

                success: function(response) {
                    detailBagi = response.detail_pembagian_bibit
                    // default value detail beli
                    $(`#inputDetailBeli`).val(response.id_detail_beli)
                    $(`#inputDetailBeli`).trigger('change');
                    $(`#inputDetailBeli`).select2("enable", false);
                    $(`#inputPanen`).val(response.id_detail_panen)
                    $(`#inputPanen`).trigger('change');
                    $(`#inputPanen`).select2("enable", false);

                    // default tgl_beli                
                    $(`input[name='tgl_pembagian']`).val(response.tgl_pembagian.split("-").reverse().join("-"));

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
                        $('#btnSimpanHeader').removeAttr('disabled')
                        $('#btnSimpanHeader').children().addClass('d-none')
                        $('#alertNotif').removeClass('d-none');
                        $('#alertNotif span').html(response.success);
                        $('#alertHeader').append(`@include('components.alert')`);

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

        // membuat element detail bagi
        function loadElementDetailBagi(item, index) {
            let form = $(
                `<form name="form_detail${index}" id="formDetail${index}" method="POST" action="/pembagian-bibit/detail/${item.id}/edit" class=" mb-5">
                    <div class="card mb-4"></div>    
                    </form>`
            )
            let cardHeader = $(
                `<div class="card-header border d-flex justify-content-between align-items-center">
                            <div class="fw-bold">
                                <span class="me-2 title">Detail Pembagian</span>
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
                        <input type="hidden" name="id_header_pembagian_bibit" id="idHeader${index}" value="${item.id_header_pembagian_bibit}">
                        <div class="mb-3">
                            <label class="form-label">Quantity</label>
                            <input type="text" class="form-control quantity" name="quantity" required value="${item.quantity}">
                            <small class="text-danger" id="errorQuantity${index}"></small>
                        </div>
                        <div class="mb-3 select-jaring">
                            <label class="form-label">Pilih Jaring</label>
                            <select class="form-select select-jaring" id="selectJaring${index}" data-placeholder="Pilih Jaring" name="id_jaring" >
                                <option></option>
                                @foreach ($jaring as $value)
                                    <option value="{{ $value->id }}">
                                        {{ $value->nama }}
                                    </option>
                                @endforeach
                            </select>
                            <small class="text-danger" id="errorJaring${index}"></small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Pilih Kolam</label>
                            <select class="form-select" id="selectKolam${index}" data-placeholder="Pilih Kolam" name="id_kolam" >
                                <option></option>
                                @foreach ($kolam as $value)
                                    <option value="{{ $value->id }}">
                                        {{ $value->nama }}
                                    </option>
                                @endforeach
                            </select>
                            <small class="text-danger" id="errorKolam${index}"></small>
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
                allowClear: true,
                containerCssClass: "select2--medium",
                dropdownCssClass: "select2--medium",
            });



            $(`#selectJaring${index}`).val(item.id_jaring)
            $(`#selectJaring${index}`).trigger('change');
            $(`#selectKolam${index}`).val(item.id_kolam)
            $(`#selectKolam${index}`).trigger('change');


            // handle sumbit
            $(`#formDetail${index}`).on("submit", function(e) { //id of form 
                e.preventDefault();
                // $(`#btnUpdateDetail${index}`).attr('disabled', 'disabled')
                // $(`#btnUpdateDetail${index}`).children().removeClass('d-none')
                // $(`#btnDeleteDetail${index}`).attr('disabled', 'disabled')

                let action = $(this).attr("action"); //get submit action from form
                let method = $(this).attr("method"); // get submit method
                let form_data = new FormData($(this)[0]); // convert form into formdata        
                console.log(form_data);

                $.ajax({
                    url: action,
                    dataType: 'json', // what to expect back from the server
                    cache: false,
                    contentType: false,
                    processData: false,
                    data: form_data,
                    type: method,

                    success: function(response) {
                        $("small[id^='error']").html('');
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

                        if (response.error != undefined) {
                            $(`#btnUpdateDetail${index}`).removeAttr('disabled')
                            $(`#btnUpdateDetail${index}`).children().addClass('d-none')
                            $(`#btnSaveDetail${index}`).removeAttr('disabled')
                            $(`#btnSaveDetail${index}`).children().addClass('d-none')
                            $(`#btnDeleteDetail${index}`).removeAttr('disabled')
                            loadDataHeader();
                        }

                        if (response.save_detail != undefined) {
                            $(`#btnUpdateDetail${index}`).removeAttr('disabled')
                            $(`#btnDeleteDetail${index}`).removeAttr('disabled')
                            $(`#btnUpdateDetail${index}`).children().addClass('d-none')
                            $(`#btnDeleteDetail${index}`).children().addClass('d-none')
                            $(`#formDetail${index} .btn-update-content`).removeClass('d-none');
                            $(`#formDetail${index} input[name='type']`).val('update-detail');
                            $(`#formDetail${index} input[name='id']`).val(response.id);
                            $(`#formDetail${index} .btn-store-content`).addClass('d-none');
                            $(`#formDetail${index} .btn-card.btn-close`).addClass('d-none');
                            $(`#formDetail${index}`).attr('action',
                                `/pembagian-bibit/detail/${response.id}/edit`)
                            $(`#btnDeleteDetail${index}`).attr('data-id', response.id);
                        }
                    },
                    error: function(response) { // handle the error            
                        $(`#btnUpdateDetail${index}`).removeAttr('disabled')
                        $(`#btnSaveDetail${index}`).removeAttr('disabled')
                        $(`#btnUpdateDetail${index}`).children().addClass('d-none')
                        $(`#btnSaveDetail${index}`).children().addClass('d-none')
                        $(`#btnDeleteDetail${index}`).removeAttr('disabled')
                        loadDataHeader();
                        let errors = response.responseJSON.errors
                        $("small[id^='error']").html('');

                        if (errors.quantity) {
                            $(`#errorQuantity${index}`).html(
                                `*${errors.quantity}`)
                        }

                        if (errors.id_jaring) {
                            $(`#errorJaring${index}`).html(
                                `*${errors.id_jaring}`)
                        }

                        if (errors.id_kolam) {
                            $(`#errorKolam${index}`).html(
                                `*${errors.id_kolam}`)
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
                    url: `/pembagian-bibit/detail/delete/${id}`,
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
                        let errors = response.responseJSON.errors
                        $("small[id^='error']").html('');
                        if (errors.general) {
                            $(`#alert${index} #alertNotifError`).removeClass('d-none');
                            $(`#alert${index} #alertNotifError span`).html(errors.general);
                            $(`#alert${index}`).append(`@include('components.alert')`);
                        }
                        loadDataHeader();
                    },

                })
            })
        }

        number = detailBagi.length;
        detailBagi.forEach((item, index) => {
            loadElementDetailBagi(item, index)
        });

        // tambah element detail
        $('#btnTambahPembagian').click(function() {
            number = number + 1
            idHeader = {!! $id !!}
            loadElementDetailBagi({
                id_jaring: null,
                id_kolam: null
            }, number)

            // hapus semua nilai input
            $(':input', `#formDetail${number}`)
                .not(':button, :submit, :reset, :hidden')
                .val('')
                .prop('checked', false)
                .prop('selected', false);

            $(`#idHeader${number}`).val(idHeader);
            $(`#formDetail${number} .card-body`).append(
                `<input type="hidden" value="${idHeader}" name="id_header_beli">`)
            $(`#formDetail${number}`).attr('action', '/pembagian-bibit/detail')
            $(`#formDetail${number} input[name='type']`).val('store-detail');
            $(`#formDetail${number} .btn-update-content`).addClass('d-none');
            $(`#formDetail${number} .btn-store-content`).removeClass('d-none');
            $(`#formDetail${number} .btn-card.btn-close`).removeClass('d-none');

            $(`#formDetail${number} .btn-card.btn-close`).click(function() {
                $(this).parent().parent().parent().remove();
            })

        })
    </script>
@endpush
