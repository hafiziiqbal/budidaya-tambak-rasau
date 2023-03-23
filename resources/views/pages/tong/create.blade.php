@extends('layouts.admin')
@section('content')
    <h1 class="mt-4">Tambah Tong</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('tong') }}">Tong</a></li>
        <li class="breadcrumb-item active">Tambah Tong</li>
    </ol>


    <form action="{{ route('tong.store') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label for="inputNama" class="form-label">Nama Tong</label>
            <input type="text" class="form-control" id="inputNama" required name="nama" placeholder="Masukkan Nama"
                value="{{ old('nama') }}">
            @if ($errors->has('nama'))
                <small class="text-danger">*{{ $errors->first('nama') }}</small>
            @endif
        </div>
        <div class="mb-3">
            <label for="selectKolam" class="form-label">Kolam</label>
            <div class="row">
                @foreach ($kolam as $key => $value)
                    <div class="col-6 col-sm-4">

                        <div class="card p-2 border rounded my-2">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="{{ $value->id }}"
                                    id="flexCheck{{ $key }}" style="cursor: pointer"
                                    name="id_kolam[{{ $key }}]">
                                <label class="form-check-label" for="flexCheck{{ $key }}" style="cursor: pointer">
                                    {{ $value->nama }}
                                </label>
                            </div>
                        </div>

                    </div>
                @endforeach
            </div>
            @if ($errors->has('id_kolam'))
                <small class="text-danger">*{{ $errors->first('id_kolam') }}</small>
            @endif
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
    <script>
        $('#selectKolam').click(function(e) {
            // Cek apakah element yang diklik adalah row di dalam tabel
            console.log(e.target.tagName);
            // if (e.target.tagName === "TD") {
            //     // Jika iya, maka ambil isi dari row tersebut
            //     var row = e.target.parentNode.cells;
            //     var isiRow = "";
            //     for (var i = 0; i < row.length; i++) {
            //         isiRow += row[i].textContent + " ";
            //     }
            //     // Tampilkan isi row pada alert
            //     alert("Isi row yang diklik: " + isiRow);
            // }
        });
    </script>
@endpush
