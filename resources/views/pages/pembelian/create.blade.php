@extends('layouts.admin')
@section('content')
    <h1 class="mt-4">Tambah Pembelian</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('pembelian') }}">Pembelian</a></li>
        <li class="breadcrumb-item active">Tambah Pembelian</li>
    </ol>


    <form action="{{ route('pembelian.store') }}" method="POST">
        @csrf
        <div id="headerPembelian" class="mb-4">
            <div class="bg-info p-2 border-dark border-bottom mb-3">
                <label class="fw-bold">Header Pembelian</label>
            </div>
            <div class="mb-3">
                <label for="inputNama" class="form-label">Tanggal Beli</label>
                <div class="input-group mb-3">
                    <span class="input-group-text" id="basic-addon1"><i class="fa fa-calendar"></i></span>
                    <input type="text" name="tanggal_beli" class="form-control" aria-describedby="basic-addon1"
                        data-date-format="dd-mm-yyyy" data-provide="datepicker">>
                </div>
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
            </div>
            {{-- <div class="mb-3">
                <label for="inputBruto" class="form-label">Total Bruto</label>
                <input type="number" class="form-control" id="inputBruto" required name="bruto"
                    value="{{ old('bruto') }}" readonly>
                @if ($errors->has('bruto'))
                    <small class="text-danger">*{{ $errors->first('bruto') }}</small>
                @endif
            </div> --}}
            <div class="mb-3">
                <label for="inputPotonganHarga" class="form-label">Potongan Harga</label>
                <input type="number" class="form-control" id="inputPotonganHarga" required name="potongan_harga"
                    value="{{ old('potongan_harga') }}">

            </div>
            {{-- <div class="mb-3">
                <label for="inputBruto" class="form-label">Total Netto</label>
                <input type="number" class="form-control" id="inputNetto" required name="netto"
                    value="{{ old('netto') }}" readonly>
                @if ($errors->has('netto'))
                    <small class="text-danger">*{{ $errors->first('netto') }}</small>
                @endif
            </div> --}}

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
                    </div>
                    <div class="mb-3">
                        <label for="inputHargaSatuan" class="form-label">Harga Satuan</label>
                        <input type="number" class="form-control" id="inputHargaSatuan" required value="">

                    </div>
                    <div class="mb-3">
                        <label for="inputQuantity" class="form-label">Quantity</label>
                        <input type="number" class="form-control" id="inputQuantity" required value="">

                    </div>
                    <div class="mb-3">
                        <label for="inputDiskonPersen" class="form-label">Diskon Persen</label>

                        <input type="number" class="form-control" id="inputDiskonPersen" name="detail_beli[]diskon_persen"
                            value="">

                    </div>
                    <div class="mb-3">
                        <label for="inputDiskonRupiah" class="form-label">Diskon Rupiah</label>
                        <input type="number" class="form-control" id="inputDiskonRupiah" name="detail_beli[]diskon_rupiah"
                            value="">

                    </div>
                </div>
            </div>


        </div>
        <button type="button" class="btn btn-dark my-3" id="btnTambahBarang"><i class="fa fa-plus"></i> Tambah
            Barang</button>

        <button type="submit" class="btn btn-primary  w-100">Simpan</button>
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
            clone.find('#inputDiskonPersen').attr('name', `detail_beli[${i}][diskon_persen]`);
            clone.find('#inputDiskonRupiah').attr('name', `detail_beli[${i}][diskon_rupiah]`);

            $("#detail").append(clone);

            $(".form-select").select2({
                theme: "bootstrap-5",
                containerCssClass: "select2--medium",
                dropdownCssClass: "select2--medium",
            });


            $('.btn-close').click(function() {
                $(this).parent().parent().remove();

            })
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
    </script>
@endpush
