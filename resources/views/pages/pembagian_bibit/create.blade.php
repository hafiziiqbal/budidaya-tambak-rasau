@extends('layouts.admin')
@section('content')
    <h1 class="mt-4">Tambah Pembagian Bibit</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('pembagian.bibit') }}">Pembagian Bibit</a></li>
        <li class="breadcrumb-item active">Tambah Pembagian Bibit</li>
    </ol>


    <form action="{{ route('pembagian.bibit.store') }}" method="POST">
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
                @if ($errors->has('tgl_pembagian'))
                    <small class="text-danger">*{{ $errors->first('tgl_pembagian') }}</small>
                @endif
            </div>

            <div class="mb-3">
                <label for="inputDetailBeli" class="form-label">Tanggal Pembelian Bibit</label>
                <select class="form-select" id="inputDetailBeli" data-placeholder="Pilih Tanggal Pembelian Bibit"
                    name="id_detail_beli">
                    <option></option>
                    @foreach ($pembelian as $value)
                        <option value="{{ $value->id }}" data-quantity="{{ $value->quantity }}">
                            @DateIndo($value->header_beli->tgl_beli){{ ' | ' . $value->produk->nama }}
                        </option>
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
            <div class="mb-4 detail-pembagian" id="detailPembagianFirst">
                <div class="card-header border d-flex justify-content-end"></div>
                <div class="card-body border">
                    <div class="mb-3">
                        <label for="inputQuantity" class="form-label">Quantity</label>
                        <input type="number" class="form-control quantity" id="inputQuantity" required
                            value="{{ old('quantity') }}">
                        <small class="text-danger error-quantity"></small>
                    </div>


                    <div class="mb-3">
                        <label for="inputJaring" class="form-label">Pilih Jaring</label>
                        <select class="form-select jaring" data-placeholder="Pilih Jaring">
                            <option></option>
                            @foreach ($jaring as $value)
                                <option value="{{ $value->id }}">
                                    {{ $value->nama }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="inputKolam" class="form-label">Pilih Kolam</label>
                        <select class="form-select kolam" data-placeholder="Pilih Kolam">
                            <option></option>
                            @foreach ($kolam as $value)
                                <option value="{{ $value->id }}">
                                    {{ $value->nama }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>
        <button type="button" class="btn btn-dark my-3" id="btnTambahPembagian"><i class="fa fa-plus"></i> Tambah
        </button>

        <button type="submit" class="btn btn-primary  w-100" id="btnSimpan" disabled>Simpan</button>
    </form>
@endsection

@push('script')
    <script>
        let i = 0;


        $('select.jaring').attr('name', `detail_pembagian_bibit[${i}][id_jaring]`);
        $('select.kolam').attr('name', `detail_pembagian_bibit[${i}][id_kolam]`);
        $('#inputQuantity').attr('name', `detail_pembagian_bibit[${i}][quantity]`);

        $("#btnTambahPembagian").click(function() {
            let element = $('#detailPembagianFirst');
            element.find('select').select2('destroy')
            let clone = element.clone()
            clone.find('input').val('')
            clone.find('.card-header').append(
                '<button type="button" class="btn-close" aria-label="Close"></button>')

            clone.find('#inputQuantity').attr('name', `detail_pembagian_bibit[${i= i + 1}][quantity]`);
            clone.find('select.jaring').attr('name', `detail_pembagian_bibit[${i}][id_jaring]`);
            clone.find('select.kolam').attr('name', `detail_pembagian_bibit[${i}][id_kolam]`);


            $("#detail").append(clone);

            $(".form-select").select2({
                theme: "bootstrap-5",
                containerCssClass: "select2--medium",
                dropdownCssClass: "select2--medium",
            });


            $('.btn-close').click(function() {
                $(this).parent().parent().remove();

            })
            $('.quantity').on('input', function() {
                totalQuantity()
            });
        });



        $(".form-select").select2({
            theme: "bootstrap-5",

            containerCssClass: "select2--medium",
            dropdownCssClass: "select2--medium",
        });

        function totalQuantity() {
            let arrayQuantity = $('.quantity').map(function() {
                return isNaN(parseInt(this.value)) ? 0 : parseInt(this.value);
            }).get();
            let totalQuantity = arrayQuantity.reduce((a, b) => a + b, 0)
            let quantityProduk = parseInt($("#inputDetailBeli").select2().find(":selected").data("quantity"));

            $('#inputDetailBeli').on('select2:select', function(e) {
                quantityProduk = parseInt($("#inputDetailBeli").select2().find(":selected").data("quantity"));
            });
            if (totalQuantity > quantityProduk) {
                $('.error-quantity').html(`*Quantity Melebihi Stok, Total Stok Hanya ${quantityProduk}`)
                $('#btnSimpan').attr('disabled', 'disabled')
            } else if (totalQuantity < quantityProduk) {
                $('.error-quantity').html('')
                $('#btnSimpan').removeAttr('disabled')
            }
            return totalQuantity
        }


        totalQuantity()
        $('.quantity').on('input', function() {
            totalQuantity()
        });
    </script>
@endpush
