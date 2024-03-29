@extends('layouts.admin')
@section('content')
    <h1 class="mt-4">Edit Produk</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('produk') }}">Produk</a></li>
        <li class="breadcrumb-item active">Edit Produk</li>
    </ol>

    <form action="{{ route('produk.update', $produk->id) }}" method="POST">
        @csrf
        <div class="mb-3">
            <input type="hidden" name="id_kategori" required value="{{ $produk->id_kategori }}">
            <label for="inputNama" class="form-label">Nama</label>
            <input type="text" class="form-control" id="inputNama" required name="nama" placeholder="Masukkan Nama"
                value="{{ old('nama') ?? $produk->nama }}">
            @if ($errors->has('nama'))
                <small class="text-danger">*{{ $errors->first('nama') }}</small>
            @endif
        </div>
        <div class="mb-3">
            <label for="inputQuantity" class="form-label">Quantity</label>
            <input type="text" class="form-control" id="inputQuantity" required name="quantity"
                placeholder="Masukkan Alamat" value="{{ old('quantity') ?? $produk->quantity }}" readonly>
            @if ($errors->has('quantity'))
                <small class="text-danger">*{{ $errors->first('quantity') }}</small>
            @endif
        </div>
        <button id="submitButton" type="submit" class="d-none"></button>
        <button type="button" id="sendButton" class="btn btn-primary  w-100">Perbarui</button>
    </form>
@endsection

@push('script')
    <script>
        $(document).ready(function() {
            const inputQuantity = $("#inputQuantity");
            inputQuantity.val(pembatasKoma(parseInt(inputQuantity.val()).toString()))

            // $("#inputQuantity").change(function() {
            //     $(this).val(parseFloat($(this).val()).toFixed(2));
            // });

            $('#sendButton').on('click', function() {
                inputQuantity.val(inputQuantity.val().replace(/\./g, ""))
                $("form").trigger("submit");
            })


        });


        function pembatasKoma(angka) {
            return angka.replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        }
    </script>
@endpush
