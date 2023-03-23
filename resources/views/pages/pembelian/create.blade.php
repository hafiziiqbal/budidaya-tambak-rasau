@extends('layouts.admin')
@section('content')
    <h1 class="mt-4">Tambah Pembelian</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('pembelian') }}">Pembelian</a></li>
        <li class="breadcrumb-item active">Tambah Pembelian</li>
    </ol>


    <form id="formBeli" action="{{ route('pembelian.store') }}" method="POST">
        @csrf
        <div id="headerPembelian" class="mb-4">
            <div class="bg-info p-2 border-dark border-bottom mb-3">
                <label class="fw-bold">Header Pembelian</label>
            </div>
            <div class="mb-3">
                <label for="inputNama" class="form-label">Tanggal Beli</label>
                <div class="input-group">
                    <span class="input-group-text" id="basic-addon1"><i class="fa fa-calendar"></i></span>
                    <input type="text" name="tanggal_beli" class="form-control" aria-describedby="basic-addon1"
                        data-date-format="dd-mm-yyyy" data-provide="datepicker">
                </div>
                <small class="text-danger" id="errorTglBeli"></small>

            </div>

            <div class="mb-3">
                <label for="inputAlamat" class="form-label">Supplier</label>
                <select class="form-select" id="selectSupplier" data-placeholder="Pilih Supplier" name="supplier">
                    <option></option>
                    @foreach ($supplier as $supplier)
                        <option value="{{ $supplier->id }}">
                            {{ $supplier->nama }}
                        </option>
                    @endforeach
                </select>
                <small class="text-danger" id="errorSupplier"></small>
            </div>
            <div class="mb-3">
                <label for="inputTotalBruto" class="form-label">Total Bruto</label>
                <input type="number" class="form-control" id="inputTotalBruto" name="total_bruto" readonly value="0">
                <small class="text-danger" id="errorTotalBruto"></small>
            </div>
            <div class="mb-3">
                <label for="inputPotonganHarga" class="form-label">Potongan Harga</label>
                <input type="number" class="form-control" id="inputPotonganHarga" name="potongan_harga"
                    value="{{ old('potongan_harga') ?? 0 }}">
                <small class="text-danger" id="errorPotonganHarga"></small>
            </div>
            <div class="mb-3">
                <label for="inputTotalNetto" class="form-label">Total Netto</label>
                <input type="number" class="form-control" id="inputTotalNetto" name="total_netto" readonly value="0">
                <small class="text-danger" id="errorTotalNetto"></small>
            </div>

        </div>

        <div id="detail">
            <div class="bg-info p-2 border-dark border-bottom mb-3">
                <label class="fw-bold">Detail Pembelian</label>
            </div>
            <div class="mb-4 detail-pembelian" id="detailPembelianFirst">
                <div class="card-header border d-flex justify-content-end"></div>
                <div class="card-body border">
                    <div class="mb-3">
                        <label for="selectProduk" class="form-label">Produk</label>
                        <select class="form-select produk" data-placeholder="Pilih Produk" required>
                            <option></option>
                            @foreach ($produk as $produk)
                                <option value="{{ $produk->id }}">
                                    {{ $produk->nama }}
                                </option>
                            @endforeach
                        </select>
                        <small class="text-danger error-produk"></small>
                    </div>
                    <div class="mb-3">
                        <label for="inputHargaSatuan" class="form-label">Harga Satuan</label>
                        <input type="number" class="form-control" id="inputHargaSatuan" required value="">
                        <small class="text-danger error-harga-satuan"></small>
                    </div>
                    <div class="mb-3">
                        <label for="inputQuantity" class="form-label">Quantity <span
                                class="text-small fst-italic text-secondary">Bibit(ekor/pcs) -
                                Pakan(kg)</span></label>
                        <input type="number" class="form-control" id="inputQuantity" required value="">
                        <small class="text-danger error-quantity"></small>
                    </div>
                    <div class="mb-3">
                        <label for="inputDiskonPersen" class="form-label">Diskon Persen</label>
                        <input type="number" class="form-control" id="inputDiskonPersen"
                            name="detail_beli[]diskon_persen" value="0">
                        <small class="text-danger error-diskon-persen"></small>
                    </div>
                    <div class="mb-3">
                        <label for="inputDiskonRupiah" class="form-label">Diskon Rupiah</label>
                        <input type="number" class="form-control" id="inputDiskonRupiah"
                            name="detail_beli[]diskon_rupiah" value="0">
                        <small class="text-danger error-diskon-rupiah"></small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Subtotal</label>
                        <input type="text" class="form-control subtotal" id="inputSubtotal" name="detail[][subtotal]"
                            required readonly>
                        <small class="text-danger error-subtotal"></small>
                    </div>
                </div>
            </div>


        </div>
        <button type="button" class="btn btn-dark my-3" id="btnTambahBarang"><i class="fa fa-plus"></i> Tambah
            Barang</button>

        <button type="submit" class="btn btn-primary  w-100" id="btnSimpan">
            <i class="fas fa-spinner fa-spin d-none me-2"></i>Simpan
        </button>
    </form>
@endsection

@push('script')
    <script>
        let i = 0;

        // $('select.produk').attr('name', `detail_beli[${i}]produk`);
        $('select.produk').attr('name', `detail_beli[${i}][id_produk]`);
        $('#inputHargaSatuan').attr('name', `detail_beli[${i}][harga_satuan]`);
        $('#inputQuantity').attr('name', `detail_beli[${i}][quantity]`);
        $('#inputDiskonPersen').attr('name', `detail_beli[${i}][diskon_persen]`);
        $('#inputDiskonRupiah').attr('name', `detail_beli[${i}][diskon_rupiah]`);
        $('#inputSubtotal').attr('name', `detail_beli[${i}][subtotal]`);

        $('.error-produk').attr('id', `errorProduk${i}`);
        $('.error-harga-satuan').attr('id', `errorHargaSatuan${i}`);
        $('.error-quantity').attr('id', `errorQuantity${i}`);
        $('.error-diskon-persen').attr('id', `errorDiskonPersen${i}`);
        $('.error-diskon-rupiah').attr('id', `errorDiskonRupiah${i}`);
        $('.error-subtotal').attr('id', `errorSubtotal${i}`);

        // handle sumbit
        $(`#formBeli`).on("submit", function(e) { //id of form 
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
                        window.location.href = "{{ route('jual') }}";

                    }
                },
                error: function(e) { // handle the error     
                    let errors = e.responseJSON.errors
                    $("small[id^='error']").html('');
                    if (errors.tanggal_beli) {
                        $(`#errorTglBeli`).html(`*${errors.tanggal_beli[0]}`)
                    }
                    if (errors.supplier) {
                        $(`#errorSupplier`).html(`*${errors.supplier[0]}`)
                    }
                    if (errors.total_bruto) {
                        $(`#errorTotalBruto`).html(`*${errors.total_bruto[0]}`)
                    }
                    if (errors.total_netto) {
                        $(`#errorTotalNetto`).html(`*${errors.total_netto[0]}`)
                    }
                    if (errors.potongan_harga) {
                        $(`#errorPotonganHarga`).html(`*${errors.potongan_harga[0]}`)
                    }

                    for (let x = 0; x < i + 1; x++) {
                        if (`detail_beli.${x}.id_produk` in errors) {
                            $(`#errorProduk${x}`).html(`*${errors[`detail_beli.${x}.id_produk`][0]}`)
                        }
                        if (`detail_beli.${x}.harga_satuan` in errors) {
                            $(`#errorHargaSatuan${x}`).html(
                                `*${errors[`detail_beli.${x}.harga_satuan`][0]}`)
                        }
                        if (`detail_beli.${x}.quantity` in errors) {
                            $(`#errorQuantity${x}`).html(`*${errors[`detail_beli.${x}.quantity`][0]}`)
                        }
                        if (`detail_beli.${x}.diskon_persen` in errors) {
                            $(`#errorDiskonPersen${x}`).html(
                                `*${errors[`detail_beli.${x}.diskon_persen`][0]}`)
                        }
                        if (`detail_beli.${x}.diskon_rupiah` in errors) {
                            $(`#errorDiskonRupiah${x}`).html(
                                `*${errors[`detail_beli.${x}.diskon_rupiah`][0]}`)
                        }
                        if (`detail_beli.${x}.subtotal` in errors) {
                            $(`#errorSubtotal${x}`).html(`*${errors[`detail_beli.${x}.subtotal`][0]}`)
                        }
                    }


                    $(`#btnSimpan`).removeAttr('disabled')
                    $(`#btnSimpan`).children().addClass('d-none')
                },

            })
        });

        $("#btnTambahBarang").click(function() {
            let element = $('#detailPembelianFirst');
            element.find('select').select2('destroy')
            let clone = element.clone()
            clone.find('input').val('')
            clone.find('.card-header').append(
                '<button type="button" class="btn-close" aria-label="Close"></button>')

            clone.find('select.produk').attr('name', `detail_beli[${i=i+1}][id_produk]`);
            clone.find('#inputHargaSatuan').attr('name', `detail_beli[${i}][harga_satuan]`);
            clone.find('#inputQuantity').attr('name', `detail_beli[${i}][quantity]`);
            clone.find('#inputDiskonPersen').attr('name', `detail_beli[${i}][diskon_persen]`).val(0);
            clone.find('#inputDiskonRupiah').attr('name', `detail_beli[${i}][diskon_rupiah]`).val(0);
            clone.find('#inputSubtotal').attr('name', `detail_beli[${i}][subtotal]`);

            clone.find('.error-produk').attr('id', `errorProduk${i}`);
            clone.find('.error-harga-satuan').attr('id', `errorHargaSatuan${i}`);
            clone.find('.error-quantity').attr('id', `errorQuantity${i}`);
            clone.find('.error-diskon-persen').attr('id', `errorDiskonPersen${i}`);
            clone.find('.error-diskon-rupiah').attr('id', `errorDiskonRupiah${i}`);
            clone.find('.error-subtotal').attr('id', `errorSubtotal${i}`);

            $("#detail").append(clone);

            $(".form-select").select2({
                theme: "bootstrap-5",
                containerCssClass: "select2--medium",
                dropdownCssClass: "select2--medium",
            });


            $('.btn-close').click(function() {
                $(this).parent().parent().remove();

            })

            // Mengikat event listener untuk perubahan pada input detail
            $("input[name^='detail_beli']").on('input', function() {
                // Mendapatkan index dari input yang diubah
                let index = $(this).attr('name').match(/\[(.*?)\]/)[1];

                // Menghitung subtotal untuk input yang diubah
                hitungSubtotal(index);

                // // Menghitung total bruto
                // hitungTotalBruto();

                // // Menghitung total netto
                // hitungTotalNetto();

                // // Menghitung kembalian
                // hitungKembalian();

            });
        });

        $("#selectSupplier").select2({
            theme: "bootstrap-5",
            containerCssClass: "select2--medium",
            dropdownCssClass: "select2--medium",
        });

        $(".form-select").select2({
            theme: "bootstrap-5",

            containerCssClass: "select2--medium",
            dropdownCssClass: "select2--medium",
        });

        // Fungsi untuk menghitung subtotal
        function hitungSubtotal(index) {

            let harga_satuan = $("input[name='detai_beli[" + index + "][harga_satuan]']").val() != '' ? parseFloat($(
                "input[name='detail_beli[" + index + "][harga_satuan]']").val()) : 0;

            let quantity = $("input[name='detail_beli[" + index + "][quantity]']").val() != '' ? parseFloat($(
                "input[name='detail_beli[" + index + "][quantity]']").val()) : 0;
            let diskon = $("input[name='detail_beli[" + index + "][diskon_persen]']").val() != '' ? parseFloat($(
                "input[name='detail_beli[" +
                index + "][diskon_persen]']").val()) : 0;
            let diskon_rupiah = $("input[name='detail_beli[" + index + "][diskon_rupiah]']").val() != '' ? parseFloat($(
                "input[name='detail_beli[" +
                index + "][diskon_rupiah]']").val()) : 0;
            let diskonhitung = 0
            let total_harga = harga_satuan * quantity;
            if (diskon != 0) {
                diskonhitung = total_harga * (diskon / 100);
            } else {
                diskonhitung = diskon_rupiah;
            }


            let subtotal = total_harga - diskonhitung;
            $("input[name='detail_beli[" + index + "][subtotal]']").val(subtotal);
        }

        // Fungsi untuk menghitung total bruto
        function hitungTotalBruto() {
            var total = 0;

            $("input[name^='detail_beli'][name$='[subtotal]']").each(function() {
                total += parseFloat($(this).val());
            });

            $("input[name='total_bruto']").val(total);
        }

        // Fungsi untuk menghitung total netto
        function hitungTotalNetto() {
            var total_bruto = $("input[name='total_bruto']").val() != '' ? parseFloat($("input[name='total_bruto']")
                .val()) : 0;
            var potongan_harga = $("input[name='potongan_harga']").val() != '' ? parseFloat($(
                "input[name='potongan_harga']").val()) : 0;

            var total_netto = total_bruto - potongan_harga;
            $("input[name='total_netto']").val(total_netto);
        }

        // Mengikat event listener untuk perubahan pada input detail
        $("input[name^='detail_beli']").on('input', function() {
            // Mendapatkan index dari input yang diubah
            let index = $(this).attr('name').match(/\[(.*?)\]/)[1];

            // Menghitung subtotal untuk input yang diubah
            hitungSubtotal(index);

            // Menghitung total bruto
            hitungTotalBruto();

            // Menghitung total netto
            hitungTotalNetto();
        });

        // Mengikat event listener untuk perubahan pada input potongan harga
        $("input[name='potongan_harga']").on('input', function() {
            // Menghitung total netto
            hitungTotalNetto();
        });
    </script>
@endpush
