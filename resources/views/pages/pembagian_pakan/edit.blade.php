@extends('layouts.admin')
@section('content')
    <h1 class="mt-4">Edit Pembagian Pakan</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('pembagian.pakan') }}">Pembagian Pakan</a></li>
        <li class="breadcrumb-item active">Edit Pembagian Pakan</li>
    </ol>

    {{-- header beli --}}
    <form method="POST" id="formHeader" action="{{ route('pembagian.pakan.update', $id) }}" name="form_header">
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

            <div class="mb-3">
                <label for="inputNama" class="form-label">Tanggal Pembagian</label>
                <div class="input-group mb-3">
                    <span class="input-group-text" id="basic-addon1"><i class="fa fa-calendar"></i></span>
                    <input type="text" name="tgl_pembagian" class="form-control" aria-describedby="basic-addon1"
                        data-date-format="dd-mm-yyyy" data-provide="datepicker" value="">>
                </div>
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
                url: `/pembagian-pakan/${idHeader}/edit-json`,
                dataType: 'json', // what to expect back from the server
                cache: false,
                async: false,
                contentType: false,
                processData: false,
                type: 'GET',

                success: function(response) {
                    detailBagi = response.detail_pembagian_pakan

                    // default tgl_beli                
                    $(`input[name='tgl_pembagian']`).val(response.tgl_pembagian_pakan.split("-").reverse().join(
                        "-"));

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

        // membuat element detail bagi
        function loadElementDetailBagi(item, index) {
            let form = $(
                `<form name="form_detail${index}" id="formDetail${index}" method="POST" action="/pembagian-pakan/detail/${item.id}/edit" class=" mb-5">
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
                        <input type="hidden" class="alt" name="id_detail_beli" value="${item.id_detail_beli}">
                        <input type="hidden" name="id" value="${item.id}">   
                        <input type="hidden" name="id_header_pembagian_pakan" id="idHeader${index}" value="${item.id_header_pembagian_pakan}">                        
                        <div class="mb-3 select-pakan">
                            <label class="form-label">Produk Pakan</label>
                            <select class="form-select select-pakan" id="selectPakan${index}" data-placeholder="Pilih Pakan" name="id_detail_beli" required>
                                <option></option>
                                @foreach ($produkPakan as $value)
                                    <option value="{{ $value->id }}">
                                        {{ $value->produk->nama }}
                                    </option>
                                @endforeach
                            </select>         
                            <small class="text-danger" id="errorPakan${index}"></small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Pilih Tong</label>
                            <select class="form-select" id="selectTong${index}" data-placeholder="Pilih Tong" name="id_tong" >
                                <option></option>
                                @foreach ($tong as $value)
                                    <option value="{{ $value->id }}">
                                        {{ $value->nama }}
                                    </option>
                                @endforeach
                            </select>                            
                            <small class="text-danger" id="errorTong${index}"></small>
                        </div>       
                        <div class="mb-3">
                            <label class="form-label">Quantity</label>
                            <input type="text" class="form-control quantity" name="quantity" required value="${item.quantity}">
                            <small class="text-danger" id="errorQuantity${index}"></small>
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

            $(`#selectPakan${index}`).val(item.id_detail_beli)
            $(`#selectPakan${index}`).select2("enable", false);
            $(`#selectPakan${index}`).trigger('change');
            $(`#selectTong${index}`).val(item.id_tong)
            $(`#selectTong${index}`).trigger('change');


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
                        $("small[id^='error']").html('');
                        if (response.success != undefined) {
                            $(`#selectPakan${index}`).select2("enable", false);
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
                            $(`#formDetail${index} .status-error-header`).removeClass('d-none')
                            $(`#formDetail${index} .status-error-header span`).html(response
                                .error)
                            setTimeout(function() {
                                $(`#formDetail${index} .status-error-header`).addClass(
                                    "d-none");
                            }, 3000);
                            loadDataHeader();
                        }

                        if (response.save_detail != undefined) {
                            $(`#btnUpdateDetail${index}`).removeAttr('disabled')
                            $(`#btnDeleteDetail${index}`).removeAttr('disabled')
                            $(`#btnUpdateDetail${index}`).children().addClass('d-none')
                            $(`#btnDeleteDetail${index}`).children().addClass('d-none')
                            $(`#formDetail${index} .btn-update-content`).removeClass('d-none');
                            $(`#formDetail${index} .btn-store-content`).addClass('d-none');
                            $(`#formDetail${index} .btn-card.btn-close`).addClass('d-none');

                            $(`#formDetail${index} input[name='type']`).val('update-detail');
                            $(`#formDetail${index} input[name='id']`).val(response.id);
                            $(`#formDetail${number} input.alt`).attr('name', 'id_detail_beli');
                            $(`#formDetail${index} input.alt`).val($(`#selectPakan${index}`).val());
                            $(`#formDetail${number} select#selectPakan${number}`).removeAttr('name');
                            $(`#selectPakan${index}`).select2("enable", false);

                            $(`#formDetail${index}`).attr('action',
                                `/pembagian-pakan/detail/${response.id}/edit`)
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

                        if (errors.id_detail_beli) {
                            $(`#errorPakan${index}`).html(
                                `*${errors.id_detail_beli}`)
                        }

                        if (errors.id_tong) {
                            $(`#errorTong${index}`).html(
                                `*${errors.id_tong}`)
                        }

                        if (errors.quantity) {
                            $(`#errorQuantity${index}`).html(
                                `*${errors.quantity}`)
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
                    url: `/pembagian-pakan/detail/delete/${id}`,
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

            $(`#selectPakan${number}`).removeAttr('disabled');
            $(`#idHeader${number}`).val(idHeader);
            $(`#formDetail${number} .card-body`).append(
                `<input type="hidden" value="${idHeader}" name="id_header_beli">`)
            $(`#formDetail${number}`).attr('action', '/pembagian-pakan/detail')
            $(`#formDetail${number} input[name='type']`).val('store-detail');
            $(`#formDetail${number} .btn-update-content`).addClass('d-none');
            $(`#formDetail${number} .btn-store-content`).removeClass('d-none');
            $(`#formDetail${number} .btn-card.btn-close`).removeClass('d-none');

            $(`#formDetail${number} input.alt[name='id_detail_beli']`).removeAttr('name');
            $(`#formDetail${number} select#selectPakan${number}`).attr('name', 'id_detail_beli');


            $(`#formDetail${number} .btn-card.btn-close`).click(function() {
                $(this).parent().parent().parent().remove();
            })

        })
    </script>
@endpush
