@extends('layouts.admin')
@section('content')
    <h1 class="mt-4">Edit Tong</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('tong') }}">Tong</a></li>
        <li class="breadcrumb-item active">Edit Tong</li>
    </ol>


    <form action="{{ route('tong.update', $tong->id) }}" method="POST">
        @csrf

        <div class="mb-3">
            <label for="inputNama" class="form-label">Nama Tong</label>
            <input type="text" class="form-control" id="inputNama" required name="nama" placeholder="Masukkan Nama"
                value="{{ $tong->nama }}">
        </div>
        <div class="mb-3">
            <label for="selectKolam" class="form-label">Kolam</label>
            <div class="row">

                @foreach ($kolam as $key => $value)
                    <div class="col-6 col-sm-4">

                        <div class="card p-2 border rounded my-2">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="{{ $value->id }}"
                                    {{ $checkboxes[$value->id] ? 'checked' : '' }} id="flexCheck{{ $key }}"
                                    style="cursor: pointer" name="id_kolam[{{ $key }}]">
                                <label class="form-check-label" for="flexCheck{{ $key }}" style="cursor: pointer">
                                    {{ $value->nama }}
                                </label>
                            </div>
                        </div>

                    </div>
                @endforeach

            </div>
            {{-- <select class="form-select" id="selectKolam" data-placeholder="Pilih Kolam" name="id_kolam">
                <option></option>
                @foreach ($kolam as $value)
                    <option value="{{ $value->id }}">
                        {{ $value->nama }}
                    </option>
                @endforeach
            </select> --}}
        </div>

        <button type="submit" class="btn btn-primary  w-100">Simpan</button>
    </form>
@endsection

@push('script')
    <script></script>
@endpush
