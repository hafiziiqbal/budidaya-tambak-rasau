@extends('layouts.admin')
@section('content')
    <h1 class="mt-4">Edit Bibit</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('bibit') }}">Bibit</a></li>
        <li class="breadcrumb-item active">Edit Bibit</li>
    </ol>

    <form name="form_detail" id="formDetail" method="POST" action="{{ route('pembelian.update.detail', $detailBeli->id) }}">
        @csrf
        <div id="alert">
            @include('components.alert')
        </div>
        <input type="hidden" name="type" value="update-detail">
        <div class="mb-3">
            <label class="form-label">Pilih Produk</label>
            <input type="hidden" class="alt" name="id_produk" value="{{ $detailBeli->id_produk }}">
            <input type="hidden" name="id_header_beli" value="{{ $detailBeli->id_header_beli }}">
            <input type="text" class="form-control" value="{{ $detailBeli->produk->nama }}" readonly>
            <small class="text-danger" id="errorProduk"></small>
        </div>
        <div class="mb-3">
            <label class="form-label">Harga Satuan</label>
            <input type="text" id="inputHargaSatuan" class="form-control mata-uang harga-satuan" name="harga_satuan"
                value="{{ $detailBeli->harga_satuan }}" required>
            <small class="text-danger" id="errorHargaSatuan"></small>
        </div>
        <div class="mb-3">
            <label class="form-label">Quantity</label>
            <input type="text" class="form-control quantity" id="inputQuantity" name="quantity"
                value="{{ $detailBeli->quantity }}" required>
            <small class="text-danger" id="errorQuantity"></small>
        </div>
        <div class="mb-3 quantity_stok">
            <label class="form-label">Quantity Stok</label>
            <input type="text" id="inputQuantityStok" class="form-control quantity-stok" disabled
                value="{{ $detailBeli->quantity_stok }}">
        </div>
        <div class="mb-3">
            <label class="form-label">Diskon Persen</label>
            <input type="number" id="inputDiskonPersen" class="form-control diskon-persen" name="diskon_persen"
                value="{{ $detailBeli->diskon_persen }}">
            <small class="text-danger" id="errorDiskonPersen"></small>
        </div>
        <div class="mb-3">
            <label class="form-label ">Diskon Rupiah</label>
            <input type="text" class="form-control diskon-rupiah mata-uang" id="inputDiskonRupiah" name="diskon_rupiah"
                value="{{ $detailBeli->diskon_rupiah }}">
            <small class="text-danger" id="errorDiskonRupiah"></small>
        </div>

        <div class="btn-update-content">
            <button type="submit" class="btn btn-success" id="btnUpdateDetail">
                <i class="fas fa-spinner fa-spin d-none"></i>
                Perbarui
            </button>
        </div>
    </form>
@endsection

@push('script')
    <script>
        // handle sumbit
        const inputHargaSatuan = $("#inputHargaSatuan");
        const inputQuantity = $("#inputQuantity");
        const inputQuantityStok = $('#inputQuantityStok')
        const inputDiskonRupiah = $("#inputDiskonRupiah");
        const inputDiskonPersen = $("#inputDiskonPersen");
        inputHargaSatuan.val(pembatasKoma(inputHargaSatuan.val().toString()))
        inputQuantity.val(pembatasKoma(parseInt(inputQuantity.val()).toString()))
        inputQuantityStok.val(pembatasKoma(parseInt(inputQuantityStok.val()).toString()))
        inputDiskonRupiah.val(pembatasKoma(parseInt(inputDiskonRupiah.val()).toString()))
        inputDiskonPersen.val(parseInt(inputDiskonPersen.val()))


        // Tambahkan event listener untuk event "input"
        inputHargaSatuan.on('change',
            function() {
                inputHargaSatuan.val(pembatasKoma(inputHargaSatuan.val().toString()))
            });

        inputQuantity.on('change',
            function() {
                inputQuantity.val(pembatasKoma(inputQuantity.val().toString()))
            });
        inputDiskonRupiah.on('change',
            function() {
                inputDiskonRupiah.val(pembatasKoma(inputDiskonRupiah.val().toString()))
            });

        function pembatasKoma(angka) {
            return angka.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");

        }

        $(`#formDetail`).on("submit", function(e) { //id of form
            e.preventDefault();

            inputHargaSatuan.val(inputHargaSatuan.val().replace(/\./g, ""))
            inputQuantity.val(inputQuantity.val().replace(/\./g, ""))
            inputQuantityStok.val(inputQuantityStok.val().replace(/\./g, ""))
            inputDiskonRupiah.val(inputDiskonRupiah.val().replace(/\./g, ""))
            inputDiskonPersen.val(inputDiskonPersen.val().replace(/\./g, ""))

            $(`#btnUpdateDetail`).attr('disabled', 'disabled')
            $(`#btnUpdateDetail`).children().removeClass('d-none')

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
                        inputHargaSatuan.val(pembatasKoma(inputHargaSatuan.val().toString()))
                        inputQuantity.val(pembatasKoma(parseInt(inputQuantity.val()).toString()))
                        inputQuantityStok.val(pembatasKoma(parseInt(inputQuantityStok.val())
                            .toString()))
                        inputDiskonRupiah.val(pembatasKoma(parseInt(inputDiskonRupiah.val())
                            .toString()))
                        inputDiskonPersen.val(parseInt(inputDiskonPersen.val()))

                        $(`#btnUpdateDetail`).removeAttr('disabled')
                        $(`#btnUpdateDetail`).children().addClass('d-none')

                        $(`#alert #alertNotif`).removeClass('d-none');
                        $(`#alert #alertNotif span`).html(response.success);
                        $(`#alert`).append(`@include('components.alert')`);
                    }
                },
                error: function(response) { // handle the error
                    let errors = response.responseJSON.errors

                    inputHargaSatuan.val(pembatasKoma(inputHargaSatuan.val().toString()))
                    inputQuantity.val(pembatasKoma(parseInt(inputQuantity.val()).toString()))
                    inputQuantityStok.val(pembatasKoma(parseInt(inputQuantityStok.val()).toString()))
                    inputDiskonRupiah.val(pembatasKoma(parseInt(inputDiskonRupiah.val()).toString()))
                    inputDiskonPersen.val(parseInt(inputDiskonPersen.val()))

                    $("small[id^='error']").html('');
                    $(`#btnUpdateDetail`).removeAttr('disabled')
                    $(`#btnUpdateDetail`).children().addClass('d-none')

                    if (errors.id_produk) {
                        $(`#errorProduk`).html(
                            `*${errors.id_produk}`)
                    }
                    if (errors.harga_satuan) {
                        $(`#errorHargaSatuan`).html(
                            `*${errors.harga_satuan}`)
                    }
                    if (errors.quantity) {
                        $(`#errorQuantity`).html(
                            `*${errors.quantity}`)
                    }
                    if (errors.diskon_persen) {
                        $(`#errorDiskonPersen`).html(
                            `*${errors.diskon_persen}`)
                    }
                    if (errors.diskon_rupiah) {
                        $(`#errorDiskonRupiah`).html(
                            `*${errors.diskon_rupiah}`)
                    }

                    if (errors.general) {
                        $(`#alert #alertNotifError`).removeClass('d-none');
                        $(`#alert #alertNotifError span`).html(errors.general);
                        $(`#alert`).append(`@include('components.alert')`);
                    }
                },

            })
        });
    </script>
@endpush
