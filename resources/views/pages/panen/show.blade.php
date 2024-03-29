@extends('layouts.admin')
@section('content')
    <h1 class="mt-4">Info Panen</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('panen') }}">Panen</a></li>
        <li class="breadcrumb-item active">Info Pakan</li>
    </ol>

    {{-- header beli --}}
    <form method="POST" id="formHeader" action="{{ route('panen.update', $id) }}" name="form_header">
        @csrf
        <div id="headerPembagian" class="mb-4">
            <div class="bg-info p-2 border-dark border-bottom mb-3">
                <label class="fw-bold">Header Panen</label>
            </div>

            <label class="text-success fw-bold status-header d-none mb-2"><i class="fa fa-check" aria-hidden="true"></i>
                <span></span></label>
            <label class="text-danger fw-bold status-error-header d-none  mb-2"><i class="fa fa-exclamation-triangle"
                    aria-hidden="true"></i>
                <span></span></label>

            <div class="mb-3">
                <label for="inputNama" class="form-label">Tanggal Panen</label>
                <div class="input-group mb-3">
                    <span class="input-group-text" id="basic-addon1"><i class="fa fa-calendar"></i></span>
                    <input type="text" name="tgl_panen" class="form-control" aria-describedby="basic-addon1"
                        data-date-format="dd-mm-yyyy" data-provide="datepicker" value="" disabled>
                </div>
            </div>
        </div>
    </form>
    <div id="detail">
        <div class="bg-info p-2 border-dark border-bottom mb-3 mt-5">
            <label class="fw-bold">Detail Panen</label>
        </div>
        <label class="info-delete ms-1 mb-3 text-success fw-bold"></label>
    </div>
@endsection

@push('script')
    <script>
        let detailPanen
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
                url: `/panen/${idHeader}/edit-json`,
                dataType: 'json', // what to expect back from the server
                cache: false,
                async: false,
                contentType: false,
                processData: false,
                type: 'GET',

                success: function(response) {
                    detailPanen = response.detail_panen

                    // default tgl_beli
                    $(`input[name='tgl_panen']`).val(response.tgl_panen.split("-").reverse().join(
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

        // membuat element detail panen
        function loadElementDetailBagi(item, index) {
            let form = $(
                `<form name="form_detail${index}" id="formDetail${index}" method="POST" action="/panen/detail/${item.id}/edit" class=" mb-5">
                    <div class="card mb-4"></div>
                    </form>`
            )
            let cardHeader = $(
                `<div class="card-header border d-flex justify-content-between align-items-center">
                            <div class="fw-bold">
                                <span class="me-2 title">Detail Panen</span>
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
                        <input type="hidden" name="id_header_panen" id="idHeader${index}" value="${item.id_header_panen}">
                        <div class="mb-3 select-ikan">
                            <label class="form-label">Pilih Ikan</label>
                            <select class="form-select select-ikan" id="selectIkan${index}" data-placeholder="Pilih Ikan" name="id_detail_pembagian_bibit">
                                <option></option>
                                @foreach ($pembagianBibit as $value)
                                    <option value="{{ $value->id }}" data-hide="{{ $value->quantity > 0 ? 'false' : 'true' }}">
                                        {{ $value->header_pembagian_bibit->detail_beli->produk->nama }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3 select-status">
                            <label class="form-label">Pilih Status</label>
                            <select class="form-select select-status" id="selectStatus${index}" data-placeholder="Pilih Status" name="status" >
                                <option value="-1">Mati</option>
                                <option value="0">Sortir</option>
                                <option value="1">Ikan</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Quantity (Ekor)</label>
                            <input type="text" class="form-control quantity" disabled value="${item.quantity}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Total Dalam Satuan KG</label>
                            <input type="text" class="form-control quantity" name="quantity" value="${item.quantity_berat}" required disabled>
                            <label class="error-quantity"></label>
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

            $(`#selectIkan${index}`).val(item.id_detail_pembagian_bibit)
            $(`.form-select`).select2("enable", false);
            $(`#selectIkan${index}`).trigger('change');
            $(`#selectStatus${index}`).val(item.status)
            $(`#selectStatus${index}`).trigger('change');


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
                            $(`#selectIkan${index}`).select2("enable", false);
                            $(`#btnUpdateDetail${index}`).removeAttr('disabled')
                            $(`#btnUpdateDetail${index}`).children().addClass('d-none')
                            $(`#btnSaveDetail${index}`).removeAttr('disabled')
                            $(`#btnSaveDetail${index}`).children().addClass('d-none')
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
                            $(`#formDetail${index} .btn-close`).addClass('d-none');
                            $(`#formDetail${index}`).attr('action',
                                `/panen/detail/${response.id}/edit`)
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
                    url: `/panen/detail/delete/${id}`,
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

        number = detailPanen.length;
        detailPanen.forEach((item, index) => {
            loadElementDetailBagi(item, index)
        });

        // tambah element detail
        $('#btnTambahPanen').click(function() {
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

            $(`#selectIkan${number}`).removeAttr('disabled');
            $(`#idHeader${number}`).val(idHeader);
            $(`#formDetail${number} .card-body`).append(
                `<input type="hidden" value="${idHeader}" name="id_header_beli">`)
            $(`#formDetail${number}`).attr('action', '/panen/detail')
            $(`#formDetail${number} .btn-update-content`).addClass('d-none');
            $(`#formDetail${number} .btn-store-content`).removeClass('d-none');
            $(`#formDetail${number} .btn-close`).removeClass('d-none');


            $(`#formDetail${number} .btn-close`).click(function() {
                $(this).parent().parent().parent().remove();
            })

        })
    </script>
@endpush
