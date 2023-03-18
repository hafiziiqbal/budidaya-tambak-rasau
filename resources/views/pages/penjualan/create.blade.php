@extends('layouts.admin')
@section('content')
    <h1 class="mt-4">Tambah Penjualan</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('jual') }}">Penjualan</a></li>
        <li class="breadcrumb-item active">Tambah Penjualan</li>
    </ol>


    <form action="{{ route('jual.store') }}" method="POST">
        @csrf
        <div id="headerPembelian" class="mb-4">
            <div class="bg-info p-2 border-dark border-bottom mb-3">
                <label class="fw-bold">Header Penjualan</label>
            </div>
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
            </div>
            <div class="mb-3">
                <label for="inputTotalBruto" class="form-label">Total Bruto</label>
                <input type="number" class="form-control" id="inputTotalBruto" name="total_bruto" readonly>
            </div>
            <div class="mb-3">
                <label for="inputPotonganHarga" class="form-label">Potongan Harga</label>
                <input type="number" class="form-control" id="inputPotonganHarga" name="potongan_harga">
            </div>
            <div class="mb-3">
                <label for="inputTotalNetto" class="form-label">Total Netto</label>
                <input type="number" class="form-control" id="inputTotalNetto" name="total_netto" readonly>
            </div>
            <div class="mb-3">
                <label for="inputPay" class="form-label">Bayar</label>
                <input type="number" class="form-control" id="inputPay" name="pay">
            </div>
            <div class="mb-3">
                <label for="inputChange" class="form-label">Kembali</label>
                <input type="number" class="form-control" id="inputChange" name="change">
            </div>
        </div>
        <div id="detail">
            <div class="bg-info p-2 border-dark border-bottom mb-3">
                <label class="fw-bold">Detail Jual</label>
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
                    <label class="form-label">Produk</label>
                    <select class="form-select select-produk" id="selectProduk${index}" data-index="${index}" data-placeholder="Pilih Pakan" name="detail[${index}][id_produk]" >
                        <option></option>
                        @foreach ($produk as $value)
                            <option value="{{ $value->id }}">
                                {{ $value->nama }}
                            </option>
                        @endforeach
                    </select>                            
                </div>                
                <div class="mb-3">
                    <label class="form-label">Harga Satuan</label>
                    <input type="text" class="form-control harga-satuan" name="detail[${index}][harga_satuan]" required>                    
                </div>                       
                <div class="mb-3">
                    <label class="form-label">Diskon</label>
                    <input type="text" class="form-control diskon" name="detail[${index}][diskon]" required>                    
                </div>                       
                <div class="mb-3">
                    <label class="form-label">Quantity</label>
                    <input type="text" class="form-control quantity" name="detail[${index}][quantity]" required>                    
                    <label id="errorQuantity${index}" class="text-danger"></label>
                </div>                       
                <div class="mb-3">
                    <label class="form-label">Subtotal</label>
                    <input type="text" class="form-control subtotal" name="detail[${index}][subtotal]" required readonly>                    
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

            cekQuantityProduk()

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

                cekQuantityProduk(index);

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
                        window.location.href = "{{ route('pembagian.pakan') }}";

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

            cekQuantityProduk(index);


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


        function cekQuantityProduk(index) {

            let quantity = $("input[name='detail[" + index + "][quantity]']").val() != '' ? parseFloat($(
                "input[name='detail[" + index + "][quantity]']").val()) : 0;

            let id_produk = $(`#selectProduk${index}`).val();



            var values = $('.select-produk').map(function() {
                let item = $(this).data('index');
                console.log(item);
                return $(this).val();
            }).get();

            var uniqueValues = [...new Set(values)];
            if (uniqueValues.length == 1) {
                total = 0;
                $('.input-quantity').each(function() {
                    total += parseInt($(this).val());
                });
                $('#total').text(total);
            } else {
                $('#total').text('Nilai dari semua Select2 berbeda');
            }

            if (quantity > 0 && (id_produk ?? 0) > 0) {

                $.ajax({
                    url: '/penjualan/cek-stok/' + id_produk + '/' + quantity,
                    type: 'GET',
                    dataType: 'json',
                    success: function(data) {
                        $(`#errorQuantity${index}`).html('');
                        if (data.error != undefined) {
                            $(`#errorQuantity${index}`).html(data.error);
                        }
                        if (data.success != undefined) {
                            $(`#errorQuantity${index}`).html('');
                            // // Reset nilai kuantitas menjadi nilai sebelumnya
                            // $(this).val($(this).data('oldValue'));
                        }
                    }
                });
            }


            // $(document).on('change', 'input[name^="detail"][name$="[quantity]"]', function() {

            //     // Mendapatkan nilai ID produk dan kuantitas
            //     var id_produk = $(this).siblings('select[name$="[id_produk]"]').val();
            //     var quantity = $(this).val();

            //     // Mencari semua input yang memiliki ID produk yang sama
            //     var inputs = $('select[name^="detail"][name$="[id_produk]"][value="' + id_produk + '"]');

            //     // Jumlahkan kuantitas dari semua input yang memiliki ID produk yang sama
            //     var total_quantity = 0;
            //     inputs.each(function() {
            //         total_quantity += parseInt($(this).siblings('input[name$="[quantity]"]').val());
            //     });
            //     console.log(id_produk);

            //     // Tampilkan pesan kesalahan jika kuantitas melebihi stok
            //     if (total_quantity > 0) {

            //         // $.ajax({
            //         //     url: '/cek-stok/' + id_produk + '/' + total_quantity,
            //         //     type: 'GET',
            //         //     dataType: 'json',
            //         //     success: function(data) {
            //         //         if (data.status == 'error') {
            //         //             alert(data.message);
            //         //             // Reset nilai kuantitas menjadi nilai sebelumnya
            //         //             $(this).val($(this).data('oldValue'));
            //         //         }
            //         //     }
            //         // });
            //     }
            // });
        }

        cekQuantityProduk()
    </script>
@endpush
