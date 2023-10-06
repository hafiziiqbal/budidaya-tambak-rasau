@extends('layouts.admin')
@section('content')
    <h1 class="mt-4">Tambah Pemberian Pakan</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('pemberian.pakan') }}">Pemberian Pakan</a></li>
        <li class="breadcrumb-item active">Tambah Pemberian Pakan</li>
    </ol>

    <form id="formPembagian" name="form_pembagian" action="{{ route('pemberian.pakan.store') }}" method="POST">
        @csrf

        {{-- @include('components.alert') --}}

        <div id="inputsContainer">
            <input type="hidden" name="type" value="store-all">

            <div id="alertGeneral">
                @include('components.alert')
            </div>
            <div class="error-element">

            </div>

        </div>

        <button type="button" class="btn btn-dark my-3" id="btnTambah"><i class="fa fa-plus"></i> Tambah
        </button>

        <button type="submit" class="btn btn-primary  w-100 " disabled id="btnSimpan">
            <i class="fas fa-spinner fa-spin d-none me-2"></i>Simpan
        </button>
    </form>
@endsection

@push('script')
    <script>
        let index = 0;
        // inisialisasi form select 2
        $(".form-select").select2({
            theme: "bootstrap-5",
            allowClear: true,
            containerCssClass: "select2--medium",
            dropdownCssClass: "select2--medium",
        });

        function loadElementInputContainer(index) {

            let containerParent = $(`<div id="container${index}" class="container-input mb-5" ></div>`)
            let cardHeader = $(`
            <div class="card-header border d-flex justify-content-between align-items-center">
                    <div class="fw-bold">
                        <span class="me-2 title">Pemberian Pakan</span>
                    </div>
                    ${index == 1 ? '': '<button type="button" class="btn-close" aria-label="Close"></button>'}
                </div>`)
            let cardBody = $(`
            <div class="card-body border">
                    <div class="mb-3">
                        <label for="inputAlamat" class="form-label">Pembagian Pakan</label>
                        <select  class="form-select" id="selectBagiPakan${index}" data-placeholder="Pilih Pembagian Pakan"
                             name="inputs[${index}][id_pembagian_pakan]" required>
                            <option></option>
                            @foreach ($pembagianPakan as $value)
                                <option value="{{ $value->id }}" data-tong="{{ $value->id_tong }}"
                                    {{ old('id_pembagian_pakan') == $value->id ? 'selected' : '' }}>
                                    {{ $value->tong->nama .
                                        ' | ' .
                                        $value->header_pembagian_pakan->tgl_pembagian_pakan .
                                        ' | ' .
                                        $value->detail_beli->produk->nama .
                                        ' :
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    ' .
                                        $value->quantity }}
                                </option>
                            @endforeach
                        </select>
                        <small class="text-danger" id="errorIdPembagianPakan${index}"></small>
                    </div>

                    <div class="mb-3">
                        <label for="inputAlamat" class="form-label">Pembagian Bibit</label>
                        <select class="form-select" id="selectBagiBibit${index}" data-placeholder="Pilih Pembagian Bibit"
                             name="inputs[${index}][id_pembagian_bibit]">
                            <option></option>
                        </select>
                        <small class="text-danger" id="errorIdPembagianBibit${index}"></small>
                    </div>

                    <div class="mb-3">
                        <label for="inputQuantity" class="form-label">Quantity</label>
                        <input type="number" class="form-control" id="inputQuantity${index}" name="inputs[${index}][quantity]">
                        <small class="text-danger" id="errorQuantity${index}"></small>
                    </div>
                </div>
            `)
            $('#inputsContainer').append(containerParent)
            $(`#container${index}`).append(cardHeader, cardBody)

            // inisialisasi form select 2
            $(".form-select").select2({
                theme: "bootstrap-5",
                allowClear: true,
                containerCssClass: "select2--medium",
                dropdownCssClass: "select2--medium",
            });


            if ($('#selectBagiPakan' + index).val() != '') {
                let idTong = $('#selectBagiPakan' + index).find(':selected').data('tong');

                if (idTong) {
                    $.ajax({
                        url: '/pemberian-pakan/pembagian-bibit-by-tong/' + idTong,
                        dataType: 'json',
                        success: function(data) {

                            var selectTongs = $('#selectBagiBibit' + index);
                            selectTongs.empty();

                            $.each(data, function(index, value) {
                                let jaring = ''
                                if (value.jaring != null) {
                                    jaring = value.jaring.nama
                                }
                                selectTongs.append('<option value="' + value.id + '">' + value
                                    .header_pembagian_bibit.tgl_pembagian + ' | ' +
                                    value
                                    .header_pembagian_bibit.detail_beli.produk.nama + ' : ' +
                                    value.quantity + '(' + value.kolam.nama + '  ' + jaring +
                                    ')</option>');
                            });

                            selectTongs.select2({
                                theme: "bootstrap-5",
                                allowClear: true,
                                containerCssClass: "select2--medium",
                                dropdownCssClass: "select2--medium",
                            });
                        }
                    });
                }
            }

            $('#selectBagiPakan' + index).on('change', function() {
                let idTong = $(this).find(':selected').data('tong');

                if (idTong) {
                    $.ajax({
                        url: '/pemberian-pakan/pembagian-bibit-by-tong/' + idTong,
                        dataType: 'json',
                        success: function(data) {

                            var selectTongs = $('#selectBagiBibit' + index);
                            selectTongs.empty();

                            $.each(data, function(index, value) {
                                let jaring = ''
                                if (value.jaring != null) {
                                    jaring = value.jaring.nama
                                }
                                selectTongs.append('<option value="' + value.id + '">' + value
                                    .header_pembagian_bibit.tgl_pembagian + ' | ' +
                                    value
                                    .header_pembagian_bibit.detail_beli.produk.nama +
                                    ' : ' +
                                    value.quantity + '(' + value.kolam.nama + '  ' +
                                    jaring +
                                    ')</option>');
                            });

                            selectTongs.select2({
                                theme: "bootstrap-5",
                                allowClear: true,
                                containerCssClass: "select2--medium",
                                dropdownCssClass: "select2--medium",
                            });
                        }
                    });
                }
            });

            $(`#container${index} .btn-close`).click(function() {
                $(this).parent().parent().remove();
            })
        }

        // tambah element detail
        $('#btnTambah').click(function() {
            index += 1;
            loadElementInputContainer(index)
            let jumlahContainerInput = $('.container-input').length;
            if (jumlahContainerInput > 0) {
                $('#btnSimpan').removeAttr('disabled')
            } else {
                $('#btnSimpan').attr('disabled', 'disabled')
            }
        })

        const cookies = document.cookie.split(";");
        shareIdDetail = getCookie('sharePakanDetailBagi');
        shareUrl = getCookie('sharePakanUrl');
        shareIsMultiple = getCookie('sharePakanMultiple');
        if (shareIdDetail != '') {
            $('ol a').attr('href', `/${shareUrl}`);
            $('ol a').html('Pembagian Pakan');
            if (shareIsMultiple == 'true') {
                let idPakan = shareIdDetail.split(',');

                idPakan.forEach(item => {
                    $('#btnTambah').trigger("click");
                    $('#selectBagiPakan' + index).val(
                        item); // Change the value or make some change to the internal state
                    $('#selectBagiPakan' + index).trigger('change.select2'); // Noti
                    $('#selectBagiPakan' + index).trigger('change');

                    // $(`#selectPakan${index}`).val(item);
                    // $(`#selectPakan${index}`).select2();
                });

            } else if (shareIsMultiple != 'true') {

                $('#btnTambah').trigger("click");
                $('#selectBagiPakan' + index).val(
                    shareIdDetail); // Change the value or make some change to the internal state
                $('#selectBagiPakan' + index).trigger('change.select2'); // Noti
                $('#selectBagiPakan' + index).trigger('change');
                // $(`#selectPakan${index}`).val(shareIdDetail);
            }

            // Menghapus cookie dengan nama "nama_cookie" dan path "/admin"
            // Menghapus cookie dengan nama "nama_cookie" dan path "/admin"
            $.removeCookie("sharePakanDetailBagi", {
                path: "/pemberian-pakan/create"
            });
            $.removeCookie("sharePakanUrl", {
                path: "/pemberian-pakan/create"
            });
            $.removeCookie("sharePakanMultiple", {
                path: "/pemberian-pakan/create"
            });
        }


        // const cookies = document.cookie.split(";");
        // shareIdDetail = getCookie('sharePakanDetailBagi');
        // shareUrl = getCookie('sharePakanUrl');
        // if (shareIdDetail != '') {
        //     $('#selectBagiPakan').val(shareIdDetail); // Change the value or make some change to the internal state
        //     $('#selectBagiPakan').trigger('change.select2'); // Noti
        //     $('#selectBagiPakan').trigger('change');
        //     $('ol a').attr('href', `/${shareUrl}`);
        //     $('ol a').html('Pembagian Bibit');

        //     // Menghapus cookie dengan nama "nama_cookie" dan path "/admin"
        //     $.removeCookie("sharePakanDetailBagi", {
        //         path: "/pemberian-pakan/create"
        //     });
        //     $.removeCookie("sharePakanUrl", {
        //         path: "/pemberian-pakan/create"
        //     });
        // }
        // end get share bibit


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
                    if (response.error != undefined) {
                        $(".error-element .btn-close").click()
                        console.log(response.error);
                        let errorElement = $(`
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <i class="fa fa-warning"></i>
                                <span>${response.error}</span>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        `)

                        $('.error-element').append(errorElement);
                    }
                    if (response.success != undefined) {
                        $(".error-element .btn-close").click()
                        // Set a cookie
                        document.cookie = `success=Berhasil Memberikan Pakan;path=/pemberian-pakan`;
                        window.location.href = "{{ route('pemberian.pakan') }}";
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


                    for (let x = 0; x < index + 1; x++) {
                        if (`inputs.${x}.id_pembagian_pakan` in errors) {
                            $(`#errorIdPembagianPakan${x}`).html(
                                `*${errors[`inputs.${x}.id_pembagian_pakan`]}`)
                        }
                        if (`inputs.${x}.id_pembagian_bibit` in errors) {
                            $(`#errorIdPembagianBibit${x}`).html(
                                `*${errors[`inputs.${x}.id_pembagian_bibit`]}`)
                        }
                        if (`inputs.${x}.quantity` in errors) {
                            $(`#errorQuantity${x}`).html(`*${errors[`inputs.${x}.quantity`]}`)
                        }
                    }
                },

            })
        });
    </script>
@endpush
