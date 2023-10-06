@extends('layouts.admin')
@section('content')
    <h1 class="mt-4">Edit Panen</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('panen') }}">Panen</a></li>
        <li class="breadcrumb-item active">Edit Pakan</li>
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
                <div class="input-group mb-3">\<input type="hidden" name="type" value="update-header">
                    <span class="input-group-text" id="basic-addon1"><i class="fa fa-calendar"></i></span>
                    <input type="text" name="tgl_panen" class="form-control" aria-describedby="basic-addon1"
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
            <label class="fw-bold">Detail Panen</label>
        </div>
        <label class="info-delete ms-1 mb-3 text-success fw-bold"></label>
    </div>
    <button type="button" class="btn btn-dark my-3" id="btnTambahPanen"><i class="fa fa-plus"></i> Tambah
    </button>
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
                                <label class="text-danger fw-bold status-error-header mb-2 ${item.detail_pembagian_bibit.quantity == 0 ? 'd-none' : ''}">
                                    <i class="fa fa-exclamation-triangle" aria-hidden="true"></i>
                                    <span>Sisa Bibit Masih Tersisa ${item.detail_pembagian_bibit.quantity} di Kolam</span>
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
                        <input type="hidden" class="alt" name="id_detail_pembagian_bibit" value="${item.id_detail_pembagian_bibit}">
                        <input type="hidden" name="id_header_panen" id="idHeader${index}" value="${item.id_header_panen}">
                        <div class="mb-3 select-ikan">
                            <label class="form-label">Pilih Ikan</label>
                            <select class="form-select select-ikan" id="selectIkan${index}" data-placeholder="Pilih Ikan" name="id_detail_pembagian_bibit" >
                                <option></option>
                                @foreach ($pembagianBibit as $value)
                                    <option   value="{{ $value->id }}"  data-hide={{ $value->quantity > 0 ? 'false' : 'true' }}">
                                        {{ $value->header_pembagian_bibit->tgl_pembagian . ' | ' . $value->header_pembagian_bibit->detail_beli->produk->nama . ' (' . $value->quantity . ') ' . ' | ' . $value->kolam->nama . ($value->jaring == null ? '' : '& ' . $value->jaring->nama) . ($value->jaring_old == null ? '' : '& ' . $value->jaring_old->nama) }}
                                    </option>
                                @endforeach
                            </select>
                            <small class="text-danger" id="errorIkan${index}"></small>
                        </div>
                        <div class="mb-3 select-status">
                            <label class="form-label">Pilih Status</label>
                            <select class="form-select select-status" id="selectStatus${index}" data-placeholder="Pilih Status" name="status" >
                                <option value="-1">Mati</option>
                                <option value="0">Sortir</option>
                                <option value="1">Ikan Siap Jual</option>
                            </select>
                            <small class="text-danger" id="errorStatus${index}"></small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Quantity (Ekor)</label>
                            <input type="text" class="form-control quantity" readonly name="quantity" value="${item.quantity}" required>
                            <small class="text-danger" id="errorQuantity${index}"></small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Total Dalam Satuan KG <span class="fst-italic text-danger">wajib diisi jika status panen adalah ikan</span></label>
                            <input type="text" class="form-control quantity-berat" readonly name="quantity_berat" value="${item.quantity_berat}" >
                            <small class="text-danger" id="errorQuantityBerat${index}"></small>
                        </div>
                        <div class="mb-3" id="containSelectIkanSiapJual${index}">
                            <label  class="form-label">Produk Ikan Siap Jual</label>
                            <select  class="form-select" id="selectIkanSiapJual${index}" data-placeholder="Pilih Ikan Siap Jual"
                             name="id_produk" >
                            <option></option>
                            @foreach ($produk as $value)
                                <option value="{{ $value->id }}">
                                    {{ $value->nama }}
                                </option>
                            @endforeach
                        </select>
                        <small class="text-danger" id="errorIdProduk${index}"></small>
                        </div>
                        <div class="btn-update-content">
                            <button type="button" class="btn btn-danger " data-id="${item.id}" id="btnDeleteDetail${index}">
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
            // <button type="submit" class="btn btn-success" id="btnUpdateDetail${index}">
            //                     <i class="fas fa-spinner fa-spin d-none"></i>
            //                     Perbarui
            //                 </button>
            $('#detail').append(form)
            $(`#formDetail${index} .card`).append(cardHeader, cardBody)

            // inisialisasi form select 2
            $(".form-select").select2({
                theme: "bootstrap-5",
                allowClear: true,
                containerCssClass: "select2--medium",
                dropdownCssClass: "select2--medium",
            });

            $(`#selectStatus${index}`).on('change', function() {

                let value = $(`#selectStatus${index}`).find(':selected').val()
                if (value == '1') {

                    $(`#containSelectIkanSiapJual${index}`).removeClass('d-none')
                    $(`#selectIkanSiapJual${index}`).attr('required', 'required')

                } else {
                    $(`#containSelectIkanSiapJual${index}`).addClass('d-none')
                    $(`#selectIkanSiapJual${index}`).removeAttr('required')
                }

            });

            $(`#selectIkan${index}`).val(item.id_detail_pembagian_bibit)
            $(`#selectIkan${index}`).trigger('change');
            $(`#selectIkan${index}`).select2("enable", false);



            $(`#selectIkanSiapJual${index}`).val(item.id_produk)
            $(`#selectIkanSiapJual${index}`).trigger('change');
            $(`#selectIkanSiapJual${index}`).select2("enable", false);

            $(`#selectStatus${index}`).val(item.status)
            $(`#selectStatus${index}`).trigger('change');
            $(`#selectStatus${index}`).select2("enable", false);


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
                            $(`#selectIkan${index}`).select2("enable", false);
                            $(`#btnUpdateDetail${index}`).removeAttr('disabled')
                            $(`#btnUpdateDetail${index}`).children().addClass('d-none')
                            $(`#btnSaveDetail${index}`).removeAttr('disabled')
                            $(`#btnSaveDetail${index}`).children().addClass('d-none')
                            $(`#btnDeleteDetail${index}`).removeAttr('disabled')
                            $(`#alert${index} #alertNotif`).removeClass('d-none');
                            $(`#alert${index} #alertNotif span`).html(response.success);
                            $(`#alert${index}`).append(`@include('components.alert')`);
                            loadDataHeader();

                            setTimeout(function() {
                                loadDataHeader();
                                $('#detail').empty();
                                detailPanen.forEach((item, index) => {
                                    loadElementDetailBagi(item, index)
                                });
                            }, 500);


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
                            $(`#formDetail${index} .btn-store-content`).addClass('d-none');
                            $(`#formDetail${index} .btn-card.btn-close`).addClass('d-none');

                            $(`#formDetail${index} input[name='type']`).val('update-detail');
                            $(`#formDetail${index} input[name='id']`).val(response.id);
                            $(`#formDetail${index} input.alt`).attr('name',
                                'id_detail_pembagian_bibit');
                            $(`#formDetail${index} input.alt`).val($(`#selectIkan${index}`).val());
                            $(`#formDetail${index} select#selectIkan${index}`).removeAttr('name');
                            $(`#selectIkan${index}`).select2("enable", false);

                            $(`#formDetail${index}`).attr('action',
                                `/panen/detail/${response.id}/edit`)
                            $(`#btnDeleteDetail${index}`).attr('data-id', response.id);

                            setTimeout(function() {
                                loadDataHeader();
                                $('#detail').empty();
                                detailPanen.forEach((item, index) => {
                                    loadElementDetailBagi(item, index)
                                });
                            }, 500);

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

                        if (errors.quantity_berat) {
                            $(`#errorQuantityBerat${index}`).html(
                                `*${errors.quantity}`)
                        }

                        if (errors.id_detail_pembagian_bibit) {
                            $(`#errorIkan${index}`).html(
                                `*${errors.id_detail_pembagian_bibit}`)
                        }

                        if (errors.status) {
                            $(`#errorStatus${index}`).html(
                                `*${errors.status}`)
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
                id_kolam: null,
                detail_pembagian_bibit: {
                    quantity: 0
                }
            }, number)

            // hapus semua nilai input
            $(':input', `#formDetail${number}`)
                .not(':button, :submit, :reset, :hidden')
                .val('')
                .prop('checked', false)
                .prop('selected', false);

            $(`#selectIkan${number}`).removeAttr('disabled');
            $(`#selectIkanSiapJual${number}`).removeAttr('disabled');
            $(`#idHeader${number}`).val(idHeader);
            $(`#formDetail${number} .card-body`).append(
                `<input type="hidden" value="${idHeader}" name="id_header_beli">`)
            $(`#formDetail${number}`).attr('action', '/panen/detail')
            $(`#formDetail${number} input[name='type']`).val('store-detail');
            $(`#formDetail${number} .btn-update-content`).addClass('d-none');
            $(`#formDetail${number} .btn-store-content`).removeClass('d-none');
            $(`#formDetail${number} .btn-card.btn-close`).removeClass('d-none');
            $('input').removeAttr('readonly');
            $(`#selectStatus${number}`).removeAttr('disabled');


            $(`#formDetail${number} input.alt[name='id_detail_pembagian_bibit']`).removeAttr('name');
            $(`#formDetail${number} select#selectIkan${number}`).attr('name', 'id_detail_pembagian_bibit');

            $(`#formDetail${number} .btn-card.btn-close`).click(function() {
                $(this).parent().parent().parent().remove();
            })

        })
    </script>
@endpush
