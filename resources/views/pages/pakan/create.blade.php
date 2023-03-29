@extends('layouts.admin')
@section('content')
    <h1 class="mt-4">Tambah Produk</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('produk') }}">Produk</a></li>
        <li class="breadcrumb-item active">Tambah Produk</li>
    </ol>

    <div class="mb-3">
        <label class="form-label">Pilih Kategori Produk</label>

        <select class="form-select" id="selectKategori" data-placeholder="Pilih Produk">
            <option></option>
            @foreach ($kategori as $produk)
                <option value="{{ $produk->id }}">{{ $produk->nama }}
                </option>
            @endforeach
        </select>
        @if ($errors->has('id_kategori'))
            <small class="text-danger">*{{ $errors->first('id_kategori') }}</small>
        @endif
    </div>

    <form action="{{ route('produk.store') }}" method="POST">
        @csrf
        <input type="hidden" name="id_kategori" required>
        <div class="mb-3">
            <label for="inputNama" class="form-label">Nama</label>

            <input type="text" class="form-control" id="inputNama" required name="nama" placeholder="Masukkan Nama"
                value="{{ old('nama') }}">
            @if ($errors->has('nama'))
                <small class="text-danger">*{{ $errors->first('nama') }}</small>
            @endif
        </div>
        <div class="mb-3">
            <label for="inputQuantity" id="labelQuantity" class="form-label">Quantity <span
                    class="text-small fst-italic text-secondary">Bibit & Ikan(ekor/pcs) - Pakan(kg)</span></label>
            <input type="number" class="form-control" id="inputQuantity" required name="quantity"
                value="{{ old('quantity') ?? 0 }}" step="0.01" readonly>
            @if ($errors->has('quantity'))
                <small class="text-danger">*{{ $errors->first('quantity') }}</small>
            @endif
        </div>
        <button type="submit" class="btn btn-primary  w-100">Simpan</button>
    </form>
@endsection
@push('script')
    <script>
        $(document).ready(function() {

            $("#selectKategori").select2({
                theme: "bootstrap-5",
                containerCssClass: "select2--medium",
                dropdownCssClass: "select2--medium",
            });


            let idKategori = $("#selectKategori").val();

            let cookieKategori = getCookie('kategori');
            if (cookieKategori == '') {
                document.cookie = `kategori=${idKategori};path=/produk;`;
                $("input[name='id_kategori']").val($("#selectKategori").val());
            } else {
                $("#selectKategori").val(cookieKategori).change();
                $("input[name='id_kategori']").val($("#selectKategori").val());
            }

            // if (getCookie('kategori') == 1) {
            //     $('#labelQuantity').html('Quantity (Kg)')
            // } else {
            //     $('#labelQuantity').html('Quantity (ekor/pcs)')
            // }
            $('#selectKategori').on('change', function(e) {
                let optionSelected = $("option:selected", this);
                idKategori = this.value;
                document.cookie = `kategori=${idKategori};path=/produk;`;
                // if (idKategori == 1) {
                //     $('#labelQuantity').html('Quantity (Kg)')
                // } else {
                //     $('#labelQuantity').html('Quantity (ekor/pcs)')
                // }
                $("input[name='id_kategori']").val($("#selectKategori").val());
            });


        });
    </script>
@endpush
