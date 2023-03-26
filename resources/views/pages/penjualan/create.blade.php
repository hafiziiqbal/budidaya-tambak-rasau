@extends('layouts.admin')
@section('content')
    <h1 class="mt-4">Tambah Penjualan</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('jual') }}">Penjualan</a></li>
        <li class="breadcrumb-item active">Tambah Penjualan</li>
    </ol>


    <form id="formJual" action="{{ route('jual.store') }}" method="POST">
        @csrf
        <div id="headerPembelian" class="mb-4">
            <div class="bg-info p-2 border-dark border-bottom mb-3">
                <label class="fw-bold">Header Penjualan</label>
            </div>
            <input type="hidden" name="type" value="store-all">
            <div class="mb-3">
                <label for="selectCustomer" class="form-label">Customer</label>
                <select class="form-select" id="selectCustomer" data-placeholder="Pilih Customer" name="customer">
                    <option></option>
                    @foreach ($customers as $value)
                        <option value="{{ $value->id }}">
                            {{ $value->nama }}
                        </option>
                    @endforeach
                </select>
                <small class="text-danger" id="errorCustomer"></small>
            </div>
            <div class="mb-3">
                <label for="inputTotalBruto" class="form-label">Total Bruto</label>
                <input type="number" class="form-control" id="inputTotalBruto" name="total_bruto" readonly value="0"
                    required>
                <small class="text-danger" id="errorTotalBruto"></small>
            </div>
            <div class="mb-3">
                <label for="inputPotonganHarga" class="form-label">Potongan Harga</label>
                <input type="number" class="form-control" id="inputPotonganHarga" name="potongan_harga" value="0"
                    required>
                <small class="text-danger" id="errorPotonganHarga"></small>
            </div>
            <div class="mb-3">
                <label for="inputTotalNetto" class="form-label">Total Netto</label>
                <input type="number" class="form-control" id="inputTotalNetto" name="total_netto" readonly value="0"
                    required>
                <small class="text-danger" id="errorTotalNetto"></small>
            </div>
            <div class="mb-3">
                <label for="inputPay" class="form-label">Bayar</label>
                <input type="number" class="form-control" id="inputPay" name="pay" required value="0">
                <small class="text-danger" id="errorPay"></small>
            </div>
            <div class="mb-3">
                <label for="inputChange" class="form-label">Kembali</label>
                <input type="number" class="form-control" id="inputChange" name="change" required value="0">
                <small class="text-danger" id="errorChange"></small>
            </div>
        </div>
        <div id="detail">
            <div class="bg-info p-2 border-dark border-bottom mb-3">
                <label class="fw-bold">Detail Jual</label>
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
        let quantity = 0;
        let panen = {!! $panen !!}

        // inisialisasi form select 2
        $(".form-select").select2({
            theme: "bootstrap-5",
            containerCssClass: "select2--medium",
            dropdownCssClass: "select2--medium",
        });

        // membuat element detail pembagian
        function loadElementDetailBeli(index) {
            let detailParent = $(`<div id="detailJual${index}" class="mb-5"></div>`)
            let cardHeader = $(
                `<div class="card-header border d-flex justify-content-between align-items-center">
                    <div class="fw-bold">
                        <span class="me-2 title">Detail Jual</span>                                
                    </div>
                    <button type="button" class="btn-close" data-index="${index}"  aria-label="Close"></button>
            </div>`
            )
            let cardBody = $(
                `<div class="card-body border">                                     
                <div class="mb-3 ">
                    <label class="form-label">Produk Hasil Panen</label>
                    <select class="form-select select-panen" id="selectProdukPanen${index}" data-index="${index}" data-placeholder="Pilih Produk Hasil Panen" name="detail[${index}][id_detail_panen]">
                        <option></option>
                        @foreach ($panen as $value)
                            <option value="{{ $value->id }}">
                                {{ $value->header_panen->tgl_panen . ' | ' . $value->detail_pembagian_bibit->header_pembagian_bibit->detail_beli->produk->nama . ' (' . $value->quantity . ')' }}
                            </option>
                        @endforeach
                    </select>
                    <small class="text-danger" id="errorPanen${index}"></small>
                </div>                
                <div class="mb-3">
                    <label class="form-label">Harga Satuan</label>
                    <input type="text" class="form-control harga-satuan" name="detail[${index}][harga_satuan]" required>                    
                    <small class="text-danger" id="errorhargaSatuan${index}"></small>
                </div>                       
                <div class="mb-3">
                    <label class="form-label">Diskon</label>
                    <input type="text" class="form-control diskon" name="detail[${index}][diskon]" required>                    
                    <small class="text-danger" id="errorDiskon${index}"></small>
                </div>                       
                <div class="mb-3">
                    <label class="form-label">Quantity</label>
                    <input type="text" class="form-control quantity" name="detail[${index}][quantity]" required>                    
                    <label id="errorQuantity${index}" class="text-danger"></label>
                </div>                       
                <div class="mb-3">
                    <label class="form-label">Subtotal</label>
                    <input type="text" class="form-control subtotal" name="detail[${index}][subtotal]" required readonly>                    
                    <small class="text-danger" id="errorSubtotal${index}"></small>
                </div>                       
            </div>`
            )

            $('#detail').append(detailParent)
            $(`#detailJual${index}`).append(cardHeader, cardBody)

            // inisialisasi form select 2
            $(".form-select").select2({
                theme: "bootstrap-5",
                allowClear: true,
                containerCssClass: "select2--medium",
                dropdownCssClass: "select2--medium",
            });

            $(`#detailJual${index} .btn-close`).click(function() {
                $(this).parent().parent().remove();
                var index = $(this).data('index');
                var subtotal = parseFloat($('input[name="detail[' + index + '][subtotal]"]').val());
                var totalBruto = parseFloat($('input[name="total_bruto"]').val()) - subtotal;
                $('input[name="total_bruto"]').val(totalBruto);
                $(this).closest('tr').remove();
                hitungTotalNetto();
            })



            // Mengikat event listener untuk perubahan pada input detail
            $("input[name^='detail']").on('input', function() {
                // Mendapatkan index dari input yang diubah
                var index = $(this).attr('name').match(/\[(.*?)\]/)[1];

                // Menghitung subtotal untuk input yang diubah
                hitungSubtotal(index);

                // Menghitung total bruto
                hitungTotalBruto();

                // Menghitung total netto
                hitungTotalNetto();

                // Menghitung kembalian
                hitungKembalian();

            });

            // Mengikat event listener untuk perubahan pada input potongan harga
            $("input[name='potongan_harga']").on('input', function() {
                // Menghitung total netto
                hitungTotalNetto();

                // Menghitung kembalian
                hitungKembalian();
            });

            // Mengikat event listener untuk perubahan pada input pay
            $("input[name='pay']").on('input', function() {
                // Menghitung kembalian
                hitungKembalian();
            });
        }

        // handle sumbit
        $(`#formJual`).on("submit", function(e) { //id of form 
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
                        document.cookie = `success=Berhasil Menjual Produk;path=/jual`;
                        window.location.href = "{{ route('jual') }}";

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

                    if (errors.customer) {
                        $(`#errorCustomer`).html(`*${errors.customer}`)
                    }
                    if (errors.total_bruto) {
                        $(`#errorTotalBruto`).html(`*${errors.total_bruto}`)
                    }
                    if (errors.potongan_harga) {
                        $(`#errorPotonganHarga`).html(`*${errors.potongan_harga}`)
                    }
                    if (errors.total_netto) {
                        $(`#errorTotalNetto`).html(`*${errors.total_netto}`)
                    }
                    if (errors.pay) {
                        $(`#errorPay`).html(`*${errors.pay}`)
                    }
                    if (errors.change) {
                        $(`#errorChange`).html(`*${errors.change}`)
                    }

                    for (let x = 0; x < index + 1; x++) {
                        if (`detail.${x}.id_detail_panen` in errors) {
                            $(`#errorPanen${x}`).html(`*${errors[`detail.${x}.id_detail_panen`]}`)
                        }
                        if (`detail.${x}.harga_satuan` in errors) {
                            $(`#errorhargaSatuan${x}`).html(
                                `*${errors[`detail.${x}.harga_satuan`]}`)
                        }
                        if (`detail.${x}.diskon` in errors) {
                            $(`#errorDiskon${x}`).html(`*${errors[`detail.${x}.diskon`]}`)
                        }
                        if (`detail.${x}.quantity` in errors) {
                            $(`#errorQuantity${x}`).html(`*${errors[`detail.${x}.quantity`]}`)
                        }
                        if (`detail.${x}.subtotal` in errors) {
                            $(`#errorSubtotal${x}`).html(`*${errors[`detail.${x}.subtotal`]}`)
                        }
                    }

                    panen.forEach(element => {
                        if (`detail.${element.id}.quantity-all` in errors) {
                            // mencari semua elemen select yang memiliki nilai selected sama dengan nilai acuan
                            $('select.select-panen').each(function() {
                                if ($(this).val() == element.id) {

                                    $(this).parent().parent().find(
                                        "label[id^='errorQuantity']").html(
                                        `*${errors[`detail.${element.id}.quantity-all`]}`
                                    )
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
        })


        // Fungsi untuk menghitung subtotal
        function hitungSubtotal(index) {
            var harga_satuan = $("input[name='detail[" + index + "][harga_satuan]']").val() != '' ? parseFloat($(
                "input[name='detail[" + index + "][harga_satuan]']").val()) : 0;
            var quantity = $("input[name='detail[" + index + "][quantity]']").val() != '' ? parseFloat($(
                "input[name='detail[" + index + "][quantity]']").val()) : 0;
            var diskon = $("input[name='detail[" + index + "][diskon]']").val() != '' ? parseFloat($("input[name='detail[" +
                index + "][diskon]']").val()) : 0;

            var total_harga = harga_satuan * quantity;
            var diskonhitung = total_harga * (diskon / 100);
            var subtotal = total_harga - diskonhitung;
            $("input[name='detail[" + index + "][subtotal]']").val(subtotal);
        }

        // Fungsi untuk menghitung total bruto
        function hitungTotalBruto() {
            var total = 0;

            $("input[name^='detail'][name$='[subtotal]']").each(function() {
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

        // Fungsi untuk menghitung kembalian
        function hitungKembalian() {
            var pay = parseFloat($("input[name='pay']").val());
            var total_netto = parseFloat($("input[name='total_netto']").val());
            var change = 0;
            if (pay > total_netto) {
                change = pay - total_netto;
            }

            $("input[name='change']").val(change);
        }

        // Mengikat event listener untuk perubahan pada input detail
        $("input[name^='detail']").on('input', function() {
            // Mendapatkan index dari input yang diubah
            var index = $(this).attr('name').match(/\[(.*?)\]/)[1];

            // Menghitung subtotal untuk input yang diubah
            hitungSubtotal(index);

            // Menghitung total bruto
            hitungTotalBruto();

            // Menghitung total netto
            hitungTotalNetto();

            // Menghitung kembalian
            hitungKembalian();

        });



        // Mengikat event listener untuk perubahan pada input potongan harga
        $("input[name='potongan_harga']").on('input', function() {
            // Menghitung total netto
            hitungTotalNetto();

            // Menghitung kembalian
            hitungKembalian();
        });

        // Mengikat event listener untuk perubahan pada input pay
        $("input[name='pay']").on('input', function() {
            // Menghitung kembalian
            hitungKembalian();
        });
    </script>
@endpush
