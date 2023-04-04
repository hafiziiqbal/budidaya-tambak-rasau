@extends('layouts.admin')
@section('content')
    <h1 class="mt-4">Tambah Pembagian Pakan</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('pembagian.pakan') }}">Pembagian Pakan</a></li>
        <li class="breadcrumb-item active">Tambah Pembagian Pakan</li>
    </ol>


    <form id="formPembagian" name="form_pembagian" action="{{ route('pembagian.pakan.store') }}" method="POST">
        @csrf

        <div id="headerPembelian" class="mb-4">
            <div class="bg-info p-2 border-dark border-bottom mb-3">
                <label class="fw-bold">Header Pembagian Pakan</label>
            </div>
            <input type="hidden" name="type" value="store-all">
            <div class="mb-3">
                <label for="inputTanggalPembagian" class="form-label">Tanggal Pembagian</label>
                <div class="input-group">
                    <span class="input-group-text" id="basic-addon1"><i class="fa fa-calendar"></i></span>
                    <input type="text" name="tgl_pembagian" id="inputTanggalPembagian" class="form-control"
                        aria-describedby="basic-addon1" data-date-format="dd-mm-yyyy" data-provide="datepicker">>
                </div>
                <small class="text-danger" id="errorTglBagi"></small>
            </div>


        </div>

        <div id="detail">
            <div class="bg-info p-2 border-dark border-bottom mb-3">
                <label class="fw-bold">Detail Pembagian Pakan</label>
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

        // inisialisasi form select 2
        $(".form-select").select2({
            theme: "bootstrap-5",
            containerCssClass: "select2--medium",
            dropdownCssClass: "select2--medium",
        });

        // membuat element detail pembagian
        function loadElementDetailBeli(index) {
            let detailParent = $(`<div id="detailPakan${index}" class="mb-5"></div>`)
            let cardHeader = $(
                `<div class="card-header border d-flex justify-content-between align-items-center">
                            <div class="fw-bold">
                                <span class="me-2 title">Detail Pembagian</span>                                
                            </div>
                            <button type="button" class="btn-close"  aria-label="Close"></button>
                    </div>`
            )
            let cardBody = $(
                `<div class="card-body border">                                     
                        <div class="mb-3 select-pakan">
                            <label class="form-label">Pilih Pakan</label>
                            <select class="form-select select-pakan" id="selectPakan${index}" data-placeholder="Pilih Pakan" name="detail[${index}][id_detail_beli]" >
                                <option></option>
                                @foreach ($produkPakan as $value)
                                    <option value="{{ $value->id }}">
                                        {{ $value->produk->nama }}
                                    </option>
                                @endforeach
                            </select>    
                            <small class="text-danger" id="errorPakan${index}"></small>
                        </div>
                        <div>
                            <label class="form-label">Pilih Tong</label>
                            <select class="form-select select-tong" id="selectTong${index}" data-placeholder="Pilih Tong" name="detail[${index}][id_tong]" >
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
                            <input type="text" class="form-control quantity" name="detail[${index}][quantity]" required>
                            <small class="text-danger" id="errorQuantity${index}"></small>
                        </div>                       
                    </div>`
            )

            $('#detail').append(detailParent)
            $(`#detailPakan${index}`).append(cardHeader, cardBody)

            // inisialisasi form select 2
            $(".form-select").select2({
                theme: "bootstrap-5",
                allowClear: true,
                containerCssClass: "select2--medium",
                dropdownCssClass: "select2--medium",
            });

            $(`#detailPakan${index} .btn-close`).click(function() {
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
                        document.cookie = `success=Berhasil Membagikan Pakan;path=/pembagian-pakan`;
                        window.location.href = "{{ route('pembagian.pakan') }}";
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

                    if (errors.tgl_pembagian) {
                        $(`#errorTglBagi`).html(`*${errors.tgl_pembagian}`)
                    }

                    for (let x = 0; x < index + 1; x++) {
                        if (`detail.${x}.id_detail_beli` in errors) {
                            $(`#errorPakan${x}`).html(`*${errors[`detail.${x}.id_detail_beli`]}`)
                        }
                        if (`detail.${x}.id_tong` in errors) {
                            $(`#errorTong${x}`).html(`*${errors[`detail.${x}.id_tong`]}`)
                        }
                        if (`detail.${x}.quantity` in errors) {
                            $(`#errorQuantity${x}`).html(`*${errors[`detail.${x}.quantity`]}`)
                        }
                    }
                },

            })
        });

        // tambah element detail
        $('#btnTambahPembagian').click(function() {
            index = index + 1
            loadElementDetailBeli(index)
        })

        // start get share bibit
        const cookies = document.cookie.split(";");
        shareIdDetail = getCookie('sharePakanDetailBeli');
        shareTanggal = getCookie('sharePakanTanggal');
        shareUrl = getCookie('sharePakanUrl');
        shareIsMultiple = getCookie('sharePakanMultiple');
        if (shareIdDetail != '') {
            $('#inputTanggalPembagian').val(shareTanggal)
            $('ol a').attr('href', `/${shareUrl}`);
            $('ol a').html('Pakan');
            if (shareIsMultiple == 'true') {
                let idPakan = shareIdDetail.split(',');
                idPakan.forEach(item => {
                    $('#btnTambahPembagian').trigger("click");
                    $(`#selectPakan${index}`).val(item);
                    $(`#selectPakan${index}`).select2();
                });

            } else if (shareIsMultiple != 'true') {
                $('#btnTambahPembagian').trigger("click");
                $(`#selectPakan${index}`).val(shareIdDetail);
                $(`#selectPakan${index}`).select2();
            }

            // Menghapus cookie dengan nama "nama_cookie" dan path "/admin"
            $.removeCookie("sharePakanDetailBeli", {
                path: "/pembagian-pakan/create"
            });
            $.removeCookie("sharePakanTanggal", {
                path: "/pembagian-pakan/create"
            });
            $.removeCookie("sharePakanUrl", {
                path: "/pembagian-pakan/create"
            });
            $.removeCookie("sharePakanMultiple", {
                path: "/pembagian-pakan/create"
            });
        }
        // end get share bibit
    </script>
@endpush
