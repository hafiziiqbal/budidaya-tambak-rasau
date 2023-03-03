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
            <label for="inputNama" class="form-label">Nama Jaring</label>
            <input type="text" class="form-control" id="inputNama" required name="nama" placeholder="Masukkan Nama"
                value="{{ old('nama') ?? $jaring->nama }}">
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
            <label for="inputQuantity" class="form-label">Quantity</label>
            <input type="number" class="form-control" id="inputQuantity" required name="quantity"
                placeholder="Masukkan Jumlah" value="{{ old('quantity') ?? $jaring->quantity }}">
            @if ($errors->has('quantity'))
                <small class="text-danger">*{{ $errors->first('quantity') }}</small>
            @endif
        </div>

        <button type="submit" class="btn btn-primary  w-100">Perbarui</button>
    </form>
@endsection

@push('script')
    <script>
        let valKolam = {!! $jaring->id_kolam !!};
        $('#selectKolam').val(valKolam);
        $("#selectKolam").select2({
            theme: "bootstrap-5",
            containerCssClass: "select2--medium",
            dropdownCssClass: "select2--medium",
        });
    </script>
@endpush
