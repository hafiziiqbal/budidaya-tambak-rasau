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
                placeholder="Masukkan Alamat" value="{{ old('quantity') ?? $produk->quantity }}">
            @if ($errors->has('quantity'))
                <small class="text-danger">*{{ $errors->first('quantity') }}</small>
            @endif
        </div>

        <button type="submit" class="btn btn-primary  w-100">Perbarui</button>
    </form>
@endsection

@push('script')
    <script>
        $(document).ready(function() {



            // $("#inputQuantity").change(function() {
            //     $(this).val(parseFloat($(this).val()).toFixed(2));
            // });

        });
    </script>
@endpush
