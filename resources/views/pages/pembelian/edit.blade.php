@extends('layouts.admin')
@section('content')
    <h1 class="mt-4">Edit Supplier</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('supplier') }}">Supplier</a></li>
        <li class="breadcrumb-item active">Edit Supplier</li>
    </ol>

    <form action="{{ route('supplier.update', $supplier->id) }}" method="POST">
        @csrf
        <div class="mb-3">
            <label for="inputNama" class="form-label">Nama</label>
            <input type="text" class="form-control" id="inputNama" required name="nama" placeholder="Masukkan Nama"
                value="{{ old('nama') ?? $supplier->nama }}">
            @if ($errors->has('nama'))
                <small class="text-danger">*{{ $errors->first('name') }}</small>
            @endif
        </div>
        <div class="mb-3">
            <label for="inputAlamat" class="form-label">Alamat</label>
            <input type="text" class="form-control" id="inputAlamat" required name="alamat"
                placeholder="Masukkan Alamat" value="{{ old('alamat') ?? $supplier->alamat }}">
            @if ($errors->has('alamat'))
                <small class="text-danger">*{{ $errors->first('alamat') }}</small>
            @endif
        </div>
        <div class="mb-3">
            <label for="inputTelepon" class="form-label">Telepon</label>
            <input type="number" class="form-control" id="inputTelepon" required name="telepon"
                placeholder="Masukkan Nomor Telepon" value="{{ old('telepon') ?? $supplier->telepon }}">
            @if ($errors->has('telepon'))
                <small class="text-danger">*{{ $errors->first('telepon') }}</small>
            @endif
        </div>
        <button type="submit" class="btn btn-primary  w-100">Perbarui</button>
    </form>
@endsection
