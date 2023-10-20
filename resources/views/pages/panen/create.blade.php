@extends('layouts.admin')

@section('content')
    <h1 class="mt-4">Tambah Data Panen</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('panen') }}">Panen</a></li>
        <li class="breadcrumb-item active">Tambah Data Panen</li>
    </ol>


    <form id="formPembagian" name="form_pembagian" action="{{ route('panen.store') }}" method="POST">
        @csrf

        <div id="headerPembelian" class="mb-4">
            <div class="bg-info p-2 border-dark border-bottom mb-3">
                <label class="fw-bold">Header Panen</label>
            </div>
            <input type="hidden" name="type" value="store-all">
            <div class="mb-3">
                <label for="inputTanggalPembagian" class="form-label">Tanggal Panen</label>
                <div class="input-group">
                    <span class="input-group-text" id="basic-addon1"><i class="fa fa-calendar"></i></span>
                    <input type="text" name="tgl_panen" id="inputTanggalPembagian" class="form-control"
                        aria-describedby="basic-addon1" data-date-format="dd-mm-yyyy" data-provide="datepicker">
                </div>
                <small class="text-danger" id="errorTglPanen"></small>

            </div>


        </div>

        <div id="detail">
            <div class="bg-info p-2 border-dark border-bottom mb-3">
                <label class="fw-bold">Detail Panen</label>
            </div>
            <div class="mb-3">
                <label class="form-label">Pilih Kolam</label>
                <br>
                <div class="btn-group" role="group" aria-label="Basic radio toggle button group">
                    @foreach ($kolam as $key => $kolam)
                        <input type="radio" class="btn-check" name="kolam" id="{{ $key }}"
                            value="{{ $kolam->id }}" autocomplete="off">
                        <label class="btn btn-outline-primary" for="{{ $key }}">{{ $kolam->nama }}</label>
                    @endforeach

                </div>
            </div>
            <div id="alertGeneral">
                @include('components.alert')
            </div>
            <div class="error-element">

            </div>


        </div>
        <button type="button" class="btn btn-dark my-3" id="btnTambahPembagian"><i class="fa fa-plus"></i> Tambah
        </button>

        <button type="submit" class="btn btn-primary  w-100" id="btnSimpan">
            <i class="fas fa-spinner fa-spin d-none me-2"></i>Simpan
        </button>
    </form>
@endsection

@push('script')
    <script>
        let index = 0;
        let detailPembagianBibit = []

        // inisialisasi form select 2
        $(".form-select").select2({
            theme: "bootstrap-5",
            containerCssClass: "select2--medium",
            dropdownCssClass: "select2--medium",
        });

        $('input[type=radio][name=kolam]').change(function() {
            $.ajax({
                url: `/panen/pembagian-bibit/${this.value}`,
                dataType: 'json', // what to expect back from the server
                cache: false,
                contentType: false,
                processData: false,
                type: 'GET',

                success: function(response) {
                    detailPembagianBibit = response

                    $('select.select-ikan').find('option').remove()

                    detailPembagianBibit.forEach(element => {
                        console.log(element.id);
                        $('select.select-ikan')
                            .append(
                                `<option value="${element.id}">${element.header_pembagian_bibit.tgl_pembagian} | ${element.header_pembagian_bibit.detail_beli.produk.nama } (${element.quantity}) | ${element.kolam.nama} ${element.jaring == null ? '' : (' & ' + element.jaring.nama)}</option>`
                            )
                    });

                },
                error: function(response) { // handle the error

                },

            })
        });

        // membuat element detail pembagian
        function loadElementDetailBeli(index) {
            let detailParent = $(`<div id="detailPanen${index}" class="mb-5"></div>`)
            let cardHeader = $(
                `<div class="card-header border d-flex justify-content-between align-items-center">
                            <div class="fw-bold">
                                <span class="me-2 title">Detail Panen</span>
                            </div>
                            <button type="button" class="btn-close"  aria-label="Close"></button>
                    </div>`
            )
            let cardBody = $(
                `<div class="card-body border">
                        <div class="mb-3 select-ikan">
                            <label class="form-label">Pilih Ikan</label>
                            <select class="form-select select-ikan" id="selectIkan${index}" data-placeholder="Pilih Ikan" name="detail[${index}][id_detail_pembagian_bibit]" >
                                <option></option>
                            </select>
                            <small class="text-danger" id="errorIkan${index}"></small>
                        </div>
                        <div class="mb-3 select-status">
                            <label class="form-label">Pilih Status</label>
                            <select class="form-select select-status" id="selectStatus${index}" data-placeholder="Pilih Status" name="detail[${index}][status]" >
                                <option value="-1">Mati</option>
                                <option value="0">Sortir</option>
                                <option value="1">Ikan Siap Jual</option>
                            </select>
                            <small class="text-danger" id="errorStatus${index}"></small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Quantity (Ekor)</label>
                            <input type="text" class="form-control quantity" name="detail[${index}][quantity]" required>
                            <small class="text-danger" id="errorQuantity${index}"></small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Total Dalam Satuan KG <span class="fst-italic text-danger" style="font-size:13px">wajib diisi jika status panen adalah IKAN SIAP JUAL</span></label>
                            <input type="text" class="form-control quantity-berat" name="detail[${index}][quantity_berat]" >
                            <small class="text-danger" id="errorQuantityBerat${index}"></small>
                        </div>
                        <div class="mb-3 d-none" id="containSelectIkanSiapJual${index}">
                            <label  class="form-label">Produk Ikan Siap Jual</label>
                            <select  class="form-select" id="selectIkanSiapJual${index}" data-placeholder="Pilih Ikan Siap Jual"
                             name="detail[${index}][id_produk]" >
                            <option></option>
                            @foreach ($produk as $value)
                                <option value="{{ $value->id }}">
                                    {{ $value->nama }}
                                </option>
                            @endforeach
                        </select>
                        <small class="text-danger" id="errorIdProduk${index}"></small>
                        </div>
                    </div>`
            )

            $('#detail').append(detailParent)
            $(`#detailPanen${index}`).append(cardHeader, cardBody)

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

            $(`#detailPanen${index} .btn-close`).click(function() {
                $(this).parent().parent().remove();
            })

        }

        // handle sumbit
        $(`#formPembagian`).on("submit", function(e) { //id of form
            e.preventDefault();
            $(`#btnSimpan`).attr('disabled', 'disabled')
            $(`#btnSimpan`).children().removeClass('d-none')

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
                    $(`#btnSimpan`).removeAttr('disabled')
                    $(`#btnSimpan`).children().addClass('d-none')
                    if (response.success != undefined) {
                        $(".error-element .btn-close").click()

                        document.cookie = `success=Berhasil Panen Ikan;path=/panen`;
                        window.location.href = "{{ route('panen') }}";

                    }
                },
                error: function(response) { // handle the error
                    $(`#btnSimpan`).removeAttr('disabled')
                    $(`#btnSimpan`).children().addClass('d-none')

                    let errors = response.responseJSON.errors
                    $("small[id^='error']").html('');

                    if (errors.general) {
                        $(`#alertGeneral #alertNotifError`).removeClass('d-none');
                        $(`#alertGeneral #alertNotifError span`).html(errors.general);
                        $(`#alertGeneral`).append(`@include('components.alert')`);
                    }

                    if (errors.tgl_panen) {
                        $(`#errorTglPanen`).html(`*${errors.tgl_panen}`)
                    }

                    for (let x = 0; x < index + 1; x++) {
                        if (`detail.${x}.quantity` in errors) {
                            $(`#errorQuantity${x}`).html(`*${errors[`detail.${x}.quantity`]}`)
                        }
                        if (`detail.${x}.quantity_berat` in errors) {
                            $(`#errorQuantityBerat${x}`).html(
                                `*${errors[`detail.${x}.quantity_berat`]}`)
                        }
                        if (`detail.${x}.id_detail_pembagian_bibit` in errors) {
                            $(`#errorIkan${x}`).html(
                                `*${errors[`detail.${x}.id_detail_pembagian_bibit`]}`)
                        }
                        if (`detail.${x}.status` in errors) {
                            $(`#errorStatus${x}`).html(`*${errors[`detail.${x}.status`]}`)
                        }
                        if (`detail.${x}.id_produk` in errors) {
                            $(`#errorIdProduk${x}`).html(`*${errors[`detail.${x}.id_produk`]}`)
                        }
                    }

                    // nilai acuan
                    detailPembagianBibit.forEach(element => {
                        if (`detail.${element.id}.quantity-all` in errors) {
                            // mencari semua elemen select yang memiliki nilai selected sama dengan nilai acuan
                            $('select.select-ikan').each(function() {
                                if ($(this).val() == element.id) {

                                    // $(this).parent().parent().find(
                                    //     "small[id^='errorQuantity']").html(
                                    //     `*${errors[`detail.${element.id}.quantity-all`]}`
                                    // )
                                    $(this).parent().parent().find(
                                        "small[id^='errorQuantity']:not([id^='errorQuantityBerat'])"
                                    ).html(
                                        `*${errors[`detail.${element.id}.quantity-all`]}`
                                    );

                                }
                            });
                        }
                    });
                },

            })
        });

        // tambah element detail
        $('#btnTambahPembagian').click(function() {
            index = index + 1
            loadElementDetailBeli(index)
            $('select.select-ikan').find('option').remove()

            detailPembagianBibit.forEach(element => {
                $('select.select-ikan')
                    .append(
                        `<option value="${element.id}">${element.header_pembagian_bibit.tgl_pembagian} | ${element.header_pembagian_bibit.detail_beli.produk.nama } (${element.quantity}) | ${element.kolam.nama} ${element.jaring == null ? '' : (' & '+element.jaring.nama)}</option>`
                    )
            });

        })

        // start get share bibit
        const cookies = document.cookie.split(";");
        shareIdDetail = getCookie('sharePanenIkan');
        shareTanggal = getCookie('sharePanenTanggal');
        shareUrl = getCookie('sharePanenUrl');
        shareIsMultiple = getCookie('sharePanenMultiple');
        shareKolam = getCookie('shareKolam');

        if (shareIdDetail != '') {
            $('#inputTanggalPembagian').val(getDateNow());
            // $('#inputTanggalPembagian').val(shareTanggal)
            $('ol a').attr('href', `/${shareUrl}`);
            $('ol a').html('Pembagian Bibit');


            if (shareIsMultiple == 'true') {
                let idPanen = shareIdDetail.split(',');
                let idKolam = shareKolam.split(',');

                $("input[type=radio][name=kolam][value=" + idKolam[0] + "]").prop('checked', true).trigger('change');

                idPanen.forEach(item => {

                    $('#btnTambahPembagian').trigger("click");
                    $(`#selectIkan${index}`).val(item);
                    $(`#selectIkan${index}`).select2();
                    $(`#selectIkan${index}`).trigger("change");
                });
            } else if (shareIsMultiple == 'false') {
                $(`input[type=radio][name=kolam][value='${shareKolam}']`).attr('checked', 'checked').trigger('change');
                $('#btnTambahPembagian').trigger("click");
                $(`#selectIkan${index}`).val(shareIdDetail);
                $(`#selectIkan${index}`).select2();
            }

            // Menghapus cookie dengan nama "nama_cookie" dan path "/admin"
            $.removeCookie("sharePanenIkan", {
                path: "/panen/create"
            });
            $.removeCookie("sharePanenTanggal", {
                path: "/panen/create"
            });
            $.removeCookie("sharePanenUrl", {
                path: "/panen/create"
            });
            $.removeCookie("sharePanenMultiple", {
                path: "/panen/create"
            });
        } else {
            $('#inputTanggalPembagian').val(getDateNow());
        }

        function getDateNow() {
            const today = new Date();

            // Dapatkan komponen tanggal, bulan, dan tahun dari objek tanggal
            const tanggal = today.getDate();
            const bulan = today.getMonth() + 1; // Ingatlah bahwa bulan dimulai dari 0 (Januari) hingga 11 (Desember)
            const tahun = today.getFullYear();

            // Buat fungsi untuk menambahkan "0" di depan angka jika kurang dari 10
            const tambahkanNol = (nilai) => (nilai < 10 ? "0" + nilai : nilai);

            // Buat tanggal dalam format "d-m-y"
            let tanggalFormat = `${tambahkanNol(tanggal)}-${tambahkanNol(bulan)}-${tahun}`;
            console.log(tanggalFormat);
            return tanggalFormat;


        }
        // end get share bibit
    </script>
@endpush
