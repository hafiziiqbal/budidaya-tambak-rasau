@extends('layouts.admin')
@section('content')
    <h1 class="mt-4">Edit Pembelian</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('pembelian') }}">Pembelian</a></li>
        <li class="breadcrumb-item active">Edit Pembelian</li>
    </ol>

    <div id="headerPembelian" class="mb-4">
        <div class="bg-info p-2 border-dark border-bottom mb-3">
            <label class="fw-bold">Header Pembelian</label>
        </div>
        <div class="mb-3">
            <label for="inputNama" class="form-label">Tanggal Beli</label>
            <div class="input-group mb-3">
                <span class="input-group-text" id="basic-addon1"><i class="fa fa-calendar"></i></span>
                <input type="text" name="tanggal_beli" class="form-control" aria-describedby="basic-addon1" disabled
                    data-date-format="dd-mm-yyyy" data-provide="datepicker" value="@DateIndo({{ $pembelian->header_beli->tgl_beli }})">>
            </div>
            @if ($errors->has('tanggal_beli'))
                <small class="text-danger">*{{ $errors->first('tanggal_beli') }}</small>
            @endif
        </div>

        <div class="mb-3">
            <label for="inputAlamat" class="form-label">Supplier</label>
            <input type="text" class="form-control" id="inputSupplier" required name="supplier"
                value="{{ $pembelian->header_beli->supplier->nama }}" disabled>
        </div>
        <div class="mb-3">
            <label for="inputBruto" class="form-label">Total Bruto</label>
            <input type="number" class="form-control" id="inputBruto" required name="bruto"
                value="{{ $pembelian->header_beli->total_bruto }}" disabled>
            @if ($errors->has('bruto'))
                <small class="text-danger">*{{ $errors->first('bruto') }}</small>
            @endif
        </div>
        <div class="mb-3">
            <label for="inputPotonganHarga" class="form-label">Potongan Harga</label>
            <input type="number" class="form-control" id="inputPotonganHarga" required name="potongan_harga" disabled
                value="{{ $pembelian->header_beli->potongan_harga }}">
            @if ($errors->has('potongan_harga'))
                <small class="text-danger">*{{ $errors->first('potongan_harga') }}</small>
            @endif
        </div>
        <div class="mb-3">
            <label for="inputBruto" class="form-label">Total Netto</label>
            <input type="number" class="form-control" id="inputNetto" required name="netto"
                value="{{ $pembelian->header_beli->total_netto }}" disabled>

        </div>

    </div>
    <form action="{{ route('pembelian.update', $pembelian->id) }}" method="POST">
        @csrf
        <input type="hidden" name="id_header_beli" value="{{ $pembelian->header_beli->id }}" required>
        <div id="detail">
            <div class="bg-info p-2 border-dark border-bottom mb-3">
                <label class="fw-bold">Detail Pembelian</label>
            </div>
            <div class="mb-4 detail-pembelian">
                <div class="card-header border d-flex justify-content-end"></div>
                <div class="card-body border">
                    <div class="mb-3">
                        <label for="selectProduk" class="form-label">Produk</label>
                        <select class="form-select produk" data-placeholder="Pilih Produk" required name="id_produk">
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
                        <input type="number" class="form-control" id="inputHargaSatuan" required name="harga_satuan"
                            value="{{ $pembelian->harga_satuan }}">
                        @if ($errors->has('harga_satuan'))
                            <small class="text-danger">*{{ $errors->first('harga_satuan') }}</small>
                        @endif
                    </div>
                    <div class="mb-3">
                        <label for="inputQuantity" class="form-label">Quantity</label>
                        <input type="number" class="form-control" id="inputQuantity" required name="quantity"
                            value="{{ $pembelian->quantity }}">
                        @if ($errors->has('quantity'))
                            <small class="text-danger">*{{ $errors->first('quantity') }}</small>
                        @endif
                    </div>
                    <div class="mb-3">
                        <label for="inputDiskonPersen" class="form-label">Diskon Persen</label>

                        <input type="number" class="form-control" id="inputDiskonPersen" name="diskon_persen"
                            value="{{ $pembelian->diskon_persen }}">

                        @if ($errors->has('diskon_persen'))
                            <small class="text-danger">*{{ $errors->first('diskon_persen') }}</small>
                        @endif
                    </div>
                    <div class="mb-3">
                        <label for="inputDiskonRupiah" class="form-label">Diskon Rupiah</label>
                        <input type="number" class="form-control" id="inputDiskonRupiah" name="diskon_rupiah"
                            value="{{ $pembelian->diskon_rupiah }}">
                        @if ($errors->has('diskon_rupiah'))
                            <small class="text-danger">*{{ $errors->first('diskon_rupiah') }}</small>
                        @endif
                    </div>
                </div>
            </div>


        </div>
        <button type="submit" class="btn btn-primary  w-100">Perbarui</button>
    </form>
@endsection

@push('script')
    <script>
        let valProduk = {!! $pembelian->produk->id !!};
        $('select.produk').val(valProduk);
        $(".form-select").select2({
            theme: "bootstrap-5",

            containerCssClass: "select2--medium",
            dropdownCssClass: "select2--medium",
        });
    </script>
@endpush
