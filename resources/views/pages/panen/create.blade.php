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
            <div class="mb-3">
                <label for="inputTanggalPembagian" class="form-label">Tanggal Panen</label>
                <div class="input-group mb-3">
                    <span class="input-group-text" id="basic-addon1"><i class="fa fa-calendar"></i></span>
                    <input type="text" name="tgl_pembagian" id="inputTanggalPembagian" class="form-control"
                        aria-describedby="basic-addon1" data-date-format="dd-mm-yyyy" data-provide="datepicker">>
                </div>

            </div>


        </div>

        <div id="detail">
            <div class="bg-info p-2 border-dark border-bottom mb-3">
                <label class="fw-bold">Detail Panen</label>
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
                                <span class="me-2 title">Detail Panen</span>                                
                            </div>
                            <button type="button" class="btn-close"  aria-label="Close"></button>
                    </div>`
            )
            let cardBody = $(
                `<div class="card-body border">                                     
                        <div class="mb-3 select-pakan">
                            <label class="form-label">Pilih Pakan</label>
                            <select class="form-select select-pakan" id="selectPakan${index}" data-placeholder="Pilih Pakan" name="detail[${index}][id_detail_pembagian_bibit]" >
                                <option></option>
                                @foreach ($pembagianBibit as $value)
                                    <option value="{{ $value->id }}">
                                        {{ $value->header_pembagian_bibit->detail_beli->produk->nama }}
                                    </option>
                                @endforeach
                            </select>                            
                        </div>                        
                        <div class="mb-3 select-status">
                            <label class="form-label">Pilih Status</label>
                            <select class="form-select select-pakan" id="selectStatus${index}" data-placeholder="Pilih Status" name="detail[${index}][status]" >
                                <option value="-1">Mati</option>
                                <option value="0">Sortir</option>
                                <option value="1">Ikan</option>                                
                            </select>                            
                        </div>                        
                        <div class="mb-3">
                            <label class="form-label">Quantity</label>
                            <input type="text" class="form-control quantity" name="detail[${index}][quantity]" required>
                            <label class="error-quantity"></label>
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

            // $(`#selectTong${index}`).on('change', function() {
            //     // mengambil nilai opsi yang dipilih
            //     let selectFirst = $(this);
            //     let selectedValue = $(this).val();

            //     // validasi nilai opsi
            //     $('select.select-tong').not(this).each(function() {
            //         // jika nilai opsi sudah dipilih di element select2 lain
            //         if ($(this).val() == selectedValue && $(this).val() != '') {
            //             // menampilkan alert
            //             alert(`Tong ini sudah digunakan`);
            //             // menghapus nilai opsi yang dipilih di element select2 baru
            //             $(this).val('').trigger('change.select2');
            //             return false
            //         }
            //     });
            // });

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

                        {{ Session::flash('success', 'Berhasil Bagikan Pakan') }}
                        window.location.href = "{{ route('panen') }}";

                    }
                },
                error: function(response) { // handle the error            
                    $(`#btnSimpan`).removeAttr('disabled')
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
