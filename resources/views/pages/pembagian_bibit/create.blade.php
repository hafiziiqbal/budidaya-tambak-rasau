@extends('layouts.admin')
@section('content')
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
                <label for="inputTanggalPembagian" class="form-label">Tanggal Pembagian</label>
                <div class="input-group mb-3">
                    <span class="input-group-text" id="basic-addon1"><i class="fa fa-calendar"></i></span>
                    <input type="text" name="tgl_pembagian" id="inputTanggalPembagian" class="form-control"
                        aria-describedby="basic-addon1" data-date-format="dd-mm-yyyy" data-provide="datepicker">>
                </div>

            </div>

            <div class="mb-3">
                <label for="inputDetailBeli" class="form-label">Bibit Yang Dibagikan</label>
                <select class="form-select" id="inputDetailBeli" data-placeholder="Pilih Bibit" name="id_detail_beli">
                    <option></option>
                    @foreach ($pembelian as $value)
                        @if ($value->produk->quantity > 0)
                            <option value="{{ $value->id }}" data-quantity="{{ $value->quantity }}">
                                @DateIndo($value->header_beli->tgl_beli){{ ' | ' . $value->produk->nama }}
                            </option>
                        @endif
                    @endforeach
                </select>
            </div>

            <div class="mb-3">
                <label for="inputPanen" class="form-label">Sortir Kembali</label>
                <select class="form-select" id="inputPanen" data-placeholder="Pilih Ikan" name="id_panen">
                    <option></option>
                    {{-- @foreach ($pembelian as $value)
                        <option value="{{ $value->id }}">
                            @DateIndo($value->header_beli->tgl_beli){{ ' | ' . $value->produk->nama }}
                        </option>
                    @endforeach --}}
                </select>
            </div>


        </div>

        <div id="detail">
            <div class="bg-info p-2 border-dark border-bottom mb-3">
                <label class="fw-bold">Detail Pembagian Bibit</label>
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
                            <label class="error-quantity"></label>
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
                            <label class="error-jaring"></label>
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
                            <label class="error-kolam"></label>
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

            $(`#selectJaring${index}`).on('change', function() {
                // mengambil nilai opsi yang dipilih
                let selectFirst = $(this);
                let selectedValue = $(this).val();

                // validasi nilai opsi
                $('select.select-jaring').not(this).each(function() {
                    // jika nilai opsi sudah dipilih di element select2 lain
                    if ($(this).val() == selectedValue && $(this).val() != '') {
                        // menampilkan alert
                        alert(`Jaring ini sudah digunakan`);
                        // menghapus nilai opsi yang dipilih di element select2 baru
                        $(this).val('').trigger('change.select2');
                        return false
                    }
                });
            });

            $(`#detailBibit${index} .btn-close`).click(function() {
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

                        {{ Session::flash('success', 'Berhasil Bagikan Bibit') }}
                        window.location.href = "{{ route('pembagian.bibit') }}";

                    }
                },
                error: function(response) { // handle the error            
                    $(`#btnSimpan`).remove('disabled')
                    $(`#btnSimpan`).children().addClass('d-none')
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
