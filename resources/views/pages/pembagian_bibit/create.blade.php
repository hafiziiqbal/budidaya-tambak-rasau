@extends('layouts.admin')
@section('content')
    <div class="position-fixed top-1 end-0 p-3" style="z-index: 11">
        <button type="button" class="btn btn-danger btn-circle " title="info penggunaan data" id="infoDataBtn"><i
                class="fa fa-info"></i>
        </button>
        <div id="infoData" class="toast" role="alert" aria-live="assertive" aria-atomic="true" data-bs-autohide="false">
            <div class="toast-header">
                <div class="rounded me-2 bg-danger p-3"></div>
                <strong class="me-auto">Info Penggunaan Data</strong>
                <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            <div class="toast-body">
                <div class="mb-2" id="infoDetailBeli">
                    <strong>Sisa Quantity <span class="fst-italic">(Tabel Detail Beli)</span></strong>
                    <br>
                    <label>0.00</label>
                </div>
                <div class="mb-2" id="infoProduk">
                    <strong>Sisa Quantity <span class="fst-italic">(Tabel Produk)</span></strong>
                    <br>
                    <label>0.00</label>
                </div>
                <div class="mb-2" id="infoDetailPanen">
                    <strong>Sisa Quantity <span class="fst-italic">(Sortir)</span></strong>
                    <br>
                    <label>0.00</label>
                </div>
            </div>
        </div>
    </div>
    <h1 class="mt-4">Tambah Pembagian Bibit</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('pembagian.bibit') }}">Pembagian Bibit</a></li>
        <li class="breadcrumb-item active">Tambah Pembagian Bibit</li>
    </ol>


    <form id="formPembagian" name="form_pembagian" action="{{ route('pembagian.bibit.store') }}" method="POST">
        @csrf
        <div id="headerPembelian" class="mb-4">
            <div class="bg-info p-2 border-dark border-bottom mb-3">
                <label class="fw-bold">Header Pembagian Bibit</label>
            </div>
            <div class="mb-3">
                <input type="hidden" name="type" value="store-all">
                <label for="inputTanggalPembagian" class="form-label">Tanggal Pembagian</label>
                <div class="input-group">
                    <span class="input-group-text" id="basic-addon1"><i class="fa fa-calendar"></i></span>
                    <input type="text" name="tgl_pembagian" id="inputTanggalPembagian" class="form-control"
                        aria-describedby="basic-addon1" data-date-format="dd-mm-yyyy" data-provide="datepicker">
                </div>
                <small class="text-danger" id="errorTglBagi"></small>
            </div>
            <div class="mb-3">
                <label class="form-label">Pilih Pembagian</label>
                <br>
                <div class="btn-group" role="group" aria-label="Basic radio toggle button group">
                    <input type="radio" class="btn-check" value="bibit" name="jenis_pembagian" id="btnBibit"
                        autocomplete="off" checked>
                    <label class="btn btn-outline-primary" for="btnBibit">Bibit</label>

                    <input type="radio" class="btn-check" value="sortir" name="jenis_pembagian" id="btnSortir"
                        autocomplete="off">
                    <label class="btn btn-outline-primary" for="btnSortir">Sortir</label>
                </div>
            </div>
            <div class="mb-3" id="bibitContainer">
                <label for="selectDetailBeli" class="form-label">Bibit Yang Dibagikan</label>
                <select class="form-select" id="selectDetailBeli" data-placeholder="Pilih Bibit" name="id_detail_beli">
                    <option></option>
                    @foreach ($pembelian as $value)
                        @if ($value->produk->quantity > 0)
                            <option value="{{ $value->id }}" data-bibitquantity="{{ $value->quantity_stok }}"
                                data-produkquantity="{{ $value->produk->quantity }}">
                                @DateIndo($value->header_beli->tgl_beli){{ ' | ' . $value->produk->nama }}
                            </option>
                        @endif
                    @endforeach
                </select>
                <small class="text-danger" id="errorDetailBibit"></small>
            </div>

            <div class="mb-3 d-none" id="sortirContainer">
                <label for="selectDetailPanen" class="form-label">Sortir Kembali</label>
                <select class="form-select" id="selectDetailPanen" data-placeholder="Pilih Ikan" name="id_detail_panen">
                    <option></option>
                    @foreach ($sortir as $value)
                        <option value="{{ $value->id }}" data-sortirquantity="{{ $value->quantity }}">
                            @DateIndo($value->header_panen->tgl_panen){{ ' | ' . $value->detail_pembagian_bibit->header_pembagian_bibit->detail_beli->produk->nama }}
                        </option>
                    @endforeach
                </select>
                <small class="text-danger" id="errorDetailPanen"></small>
            </div>
        </div>

        <div id="detail">
            <div class="bg-info p-2 border-dark border-bottom mb-3">
                <label class="fw-bold">Detail Pembagian</label>
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
        let bibitQuantity = 0
        let produkQuantity = 0

        let toastTrigger = $('#infoDataBtn')
        let toastLiveExample = $('#infoData')
        if (toastTrigger) {
            toastTrigger.on('click', function() {
                $(this).addClass('d-none')
                var toast = new bootstrap.Toast(toastLiveExample)
                toast.show()
            })
        }
        $('#infoData .btn-close').on('click', function() {
            setTimeout(
                function() {
                    toastTrigger.removeClass('d-none');
                }, 200);
        })


        // inisialisasi form select 2
        $(".form-select").select2({
            theme: "bootstrap-5",
            containerCssClass: "select2--medium",
            dropdownCssClass: "select2--medium",
        });

        bibitQuantity = parseFloat($(`#selectDetailBeli`).find(':selected').data('bibitquantity') ?? 0)
        produkQuantity = parseFloat($(`#selectDetailBeli`).find(':selected').data('produkquantity') ?? 0)

        function setToast() {
            $('#infoDetailBeli label').html(bibitQuantity)
            $('#infoProduk label').html(produkQuantity)
        }
        setToast()
        $(`#selectDetailBeli`).on('change', function() {
            bibitQuantity = parseFloat($(`#selectDetailBeli`).find(':selected').data('bibitquantity') ?? 0)
            produkQuantity = parseFloat($(`#selectDetailBeli`).find(':selected').data('produkquantity') ?? 0)
            setToast()
        });


        $('input[type=radio][name=jenis_pembagian]').change(function() {
            if (this.value == 'sortir') {
                $('#bibitContainer').addClass('d-none');
                $('#sortirContainer').removeClass('d-none');
                $('#formPembagian').attr('action', '/pembagian-bibit/sortir')
            } else if (this.value == 'bibit') {
                $('#sortirContainer').addClass('d-none');
                $('#bibitContainer').removeClass('d-none');
                $('#formPembagian').attr('action', '/pembagian-bibit')
            }
        });

        // membuat element detail pembagian
        function loadElementDetailBeli(index) {
            let detailParent = $(`<div id="detailBibit${index}" class="mb-5"></div>`)
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
                        <div class="mb-3">
                            <label class="form-label">Quantity</label>
                            <input type="text" class="form-control quantity" name="detail[${index}][quantity]" required>                            
                            <small class="text-danger" id="errorQuantity${index}"></small>
                        </div>
                        <div class="mb-3 select-jaring">
                            <label class="form-label">Pilih Jaring</label>
                            <select class="form-select select-jaring" id="selectJaring${index}" data-placeholder="Pilih Jaring" name="detail[${index}][id_jaring]" >
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
                            <select class="form-select" data-placeholder="Pilih Kolam" name="detail[${index}][id_kolam]" >
                                <option></option>
                                @foreach ($kolam as $value)
                                    <option value="{{ $value->id }}">
                                        {{ $value->nama }}
                                    </option>
                                @endforeach
                            </select>
                            <small class="text-danger" id="errorKolam${index}"></small>
                        </div>                        
                    </div>`
            )

            $('#detail').append(detailParent)
            $(`#detailBibit${index}`).append(cardHeader, cardBody)

            // inisialisasi form select 2
            $(".form-select").select2({
                theme: "bootstrap-5",
                allowClear: true,
                containerCssClass: "select2--medium",
                dropdownCssClass: "select2--medium",
            });

            $(`#detailBibit${index} .btn-close`).click(function() {
                $(this).parent().parent().remove();
            })

            $('body').on('change', '.quantity', function() {
                // inisialisasi variabel total
                var total = 0;

                // ulangi semua elemen dengan class yang sama
                $('.quantity').each(function() {
                    // tambahkan nilai input ke total
                    total += parseInt($(this).val());
                });


                $('#infoDetailBeli label').html((bibitQuantity - total) < 0 ? 'Quantity melebihi batas' : (
                    bibitQuantity - total))

                $('#infoProduk label').html((bibitQuantity - total) < 0 ? 'Quantity melebihi batas' : (
                    bibitQuantity - total))
            });

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
                        window.location.href = "{{ route('pembagian.bibit') }}";

                    }
                },
                error: function(response) { // handle the error      
                    let errors = response.responseJSON.errors
                    $("small[id^='error']").html('');
                    $(`#btnSimpan`).removeAttr('disabled')
                    $(`#btnSimpan`).children().addClass('d-none')

                    if (errors.general) {
                        $(`#alertGeneral #alertNotifError`).removeClass('d-none');
                        $(`#alertGeneral #alertNotifError span`).html(errors.general);
                        $(`#alertGeneral`).append(`@include('components.alert')`);
                    }

                    if (errors.tgl_pembagian) {
                        $(`#errorTglBagi`).html(`*${errors.tgl_pembagian}`)
                    }
                    if (errors.id_detail_beli) {
                        $(`#errorDetailBibit`).html(`*${errors.id_detail_beli}`)
                    }
                    if (errors.id_detail_panen) {
                        $(`#errorDetailPanen`).html(`*${errors.id_detail_panen}`)
                    }

                    for (let x = 0; x < index + 1; x++) {
                        if (`detail.${x}.quantity` in errors) {
                            $(`#errorQuantity${x}`).html(`*${errors[`detail.${x}.quantity`]}`)
                        }
                        if (`detail.${x}.id_jaring` in errors) {
                            $(`#errorJaring${x}`).html(`*${errors[`detail.${x}.id_jaring`]}`)
                        }
                        if (`detail.${x}.id_kolam` in errors) {
                            $(`#errorKolam${x}`).html(`*${errors[`detail.${x}.id_kolam`]}`)
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
    </script>
@endpush
