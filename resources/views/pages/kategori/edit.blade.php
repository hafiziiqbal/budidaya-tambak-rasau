@extends('layouts.admin')
@section('content')
    <h1 class="mt-4">Edit Kategori</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('kategori') }}">Kategori</a></li>
        <li class="breadcrumb-item active">Edit Kategori</li>
    </ol>

    <form action="{{ route('kategori.update', $kategori->id) }}" method="POST">
        @csrf
        <div class="mb-3">
            <label for="inputNama" class="form-label">Nama Kategori</label>
            <input type="text" class="form-control" id="inputNama" required name="nama" placeholder="Masukkan Nama"
                value="{{ old('nama') ?? $kategori->nama }}" required>
            @if ($errors->has('nama'))
                <small class="text-danger">*{{ $errors->first('nama') }}</small>
            @endif
        </div>
        <div class="mb-3">
            <label for="inputDeskripsi" class="form-label">Deskripsi</label>
            <textarea class="form-control" id="inputDeskripsi" rows="3" placeholder="Masukkan Deskripsi" name="deskripsi"
                required>{{ old('deskripsi') ?? $kategori->deskripsi }}</textarea>
            @if ($errors->has('deskripsi'))
                <small class="text-danger">*{{ $errors->first('deskripsi') }}</small>
            @endif
        </div>

        <button type="submit" class="btn btn-primary  w-100">Perbarui</button>
    </form>
@endsection
