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
                value="{{ old('nama') ?? $tong->nama }}">
            @if ($errors->has('nama'))
                <small class="text-danger">*{{ $errors->first('name') }}</small>
            @endif
        </div>
        <div class="mb-3">
            <label for="selectKolam" class="form-label">Kolam</label>
            <select class="form-select" id="selectKolam" data-placeholder="Pilih Kolam" name="id_kolam">
                <option></option>
                @foreach ($kolam as $value)
                    <option value="{{ $value->id }}">
                        {{ $value->nama }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="mb-3">
            <label for="selectJaring" class="form-label">Kolam</label>
            <select class="form-select" id="selectJaring" data-placeholder="Pilih Jaring" name="id_jaring">
                <option></option>
                @foreach ($jaring as $value)
                    <option value="{{ $value->id }}">
                        {{ $value->nama }}
                    </option>
                @endforeach
            </select>
        </div>

        <button type="submit" class="btn btn-primary  w-100">Perbarui</button>
    </form>
@endsection

@push('script')
    <script>
        let valKolam = {!! $tong->id_kolam !!};
        let valJaring = {!! $tong->id_jaring == '' ? '0' : $tong->id_jaring !!};
        $('#selectJaring').val(valJaring);
        $('#selectKolam').val(valKolam);
        $(".form-select").select2({
            theme: "bootstrap-5",
            containerCssClass: "select2--medium",
            dropdownCssClass: "select2--medium",
        });
    </script>
@endpush
