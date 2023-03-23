@extends('layouts.admin')
@section('content')
    <h1 class="mt-4">Tambah Kolam</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('kolam') }}">Kolam</a></li>
        <li class="breadcrumb-item active">Tambah Kolam</li>
    </ol>


    <form action="{{ route('kolam.store') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label for="inputNama" class="form-label">Nama Kolam</label>
            <input type="text" class="form-control" id="inputNama" required name="nama" placeholder="Masukkan Nama"
                value="{{ old('nama') }}">
            @if ($errors->has('nama'))
                <small class="text-danger">*{{ $errors->first('nama') }}</small>
            @endif
        </div>
        <div class="mb-3">
            <label for="inputPosisi" class="form-label">Posisi</label>
            <textarea class="form-control" id="inputPosisi" rows="3" placeholder="Masukkan Posisi" name="posisi" required>{{ old('posisi') }}</textarea>
            @if ($errors->has('posisi'))
                <small class="text-danger">*{{ $errors->first('posisi') }}</small>
            @endif
        </div>
        <button type="submit" class="btn btn-primary  w-100">Simpan</button>
    </form>
@endsection
