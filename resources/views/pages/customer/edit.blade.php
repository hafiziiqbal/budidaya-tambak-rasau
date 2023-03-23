@extends('layouts.admin')
@section('content')
    <h1 class="mt-4">Edit Customer</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('customer') }}">Customer</a></li>
        <li class="breadcrumb-item active">Edit Customer</li>
    </ol>

    <form action="{{ route('customer.update', $customer->id) }}" method="POST">
        @csrf
        <div class="mb-3">
            <input type="hidden" name="type" value="update">
            <input type="hidden" name="id" value="{{ $customer->id }}">
            <label for="inputNama" class="form-label">Nama</label>
            <input type="text" class="form-control" id="inputNama" required name="nama" placeholder="Masukkan Nama"
                value="{{ old('nama') ?? $customer->nama }}">
            @if ($errors->has('nama'))
                <small class="text-danger">*{{ $errors->first('name') }}</small>
            @endif
        </div>
        <div class="mb-3">
            <label for="inputAlamat" class="form-label">Alamat</label>
            <input type="text" class="form-control" id="inputAlamat" required name="alamat"
                placeholder="Masukkan Alamat" value="{{ old('alamat') ?? $customer->alamat }}">
            @if ($errors->has('alamat'))
                <small class="text-danger">*{{ $errors->first('alamat') }}</small>
            @endif
        </div>
        <div class="mb-3">
            <label for="inputTelepon" class="form-label">Telepon</label>
            <input type="number" class="form-control" id="inputTelepon" required name="telepon"
                placeholder="Masukkan Nomor Telepon" value="{{ old('telepon') ?? $customer->telepon }}">
            @if ($errors->has('telepon'))
                <small class="text-danger">*{{ $errors->first('telepon') }}</small>
            @endif
        </div>
        <button type="submit" class="btn btn-primary  w-100">Perbarui</button>
    </form>
@endsection
