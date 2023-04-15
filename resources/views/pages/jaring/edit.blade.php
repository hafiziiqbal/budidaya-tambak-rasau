@extends('layouts.admin')
@section('content')
    <h1 class="mt-4">Edit Jaring</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('jaring') }}">Jaring</a></li>
        <li class="breadcrumb-item active">Edit Jaring</li>
    </ol>

    <form action="{{ route('jaring.update', $jaring->id) }}" method="POST">
        @csrf

        <div class="mb-3">
            <input type="hidden" name="type" value="update">
            <label for="inputNama" class="form-label">Nama Jaring</label>
            <input type="text" class="form-control" id="inputNama" required name="nama" placeholder="Masukkan Nama"
                value="{{ old('nama') ?? $jaring->nama }}">
            @if ($errors->has('nama'))
                <small class="text-danger">*{{ $errors->first('name') }}</small>
            @endif
        </div>
        {{-- <div class="mb-3">
            <label for="selectKolam" class="form-label">Kolam</label>
            <select class="form-select" id="selectKolam" data-placeholder="Pilih Kolam" name="id_kolam">
                <option></option>
                @foreach ($kolam as $value)
                    <option value="{{ $value->id }}">
                        {{ $value->nama }}
                    </option>
                @endforeach
            </select>
        </div> --}}
        <div class="mb-3">
            <label for="inputPosisi" class="form-label">Posisi</label>
            <textarea class="form-control" id="inputPosisi" rows="3" placeholder="Masukkan Posisi" name="posisi" required>{{ old('posisi') ?? $jaring->posisi }}</textarea>
            @if ($errors->has('posisi'))
                <small class="text-danger">*{{ $errors->first('posisi') }}</small>
            @endif
        </div>
        <button type="submit" class="btn btn-primary  w-100">Perbarui</button>
    </form>
@endsection

@push('script')
    <script>
        let valKolam = '{!! $jaring->id_kolam !!}';
        $('#selectKolam').val(valKolam);
        $("#selectKolam").select2({
            allowClear: true,
            theme: "bootstrap-5",
            containerCssClass: "select2--medium",
            dropdownCssClass: "select2--medium",
        });
    </script>
@endpush
