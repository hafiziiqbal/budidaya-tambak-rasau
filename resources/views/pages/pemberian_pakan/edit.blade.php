@extends('layouts.admin')
@section('content')
    <h1 class="mt-4">Edit Pemberian Pakan</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('pemberian.pakan') }}">Pemberian Pakan</a></li>
        <li class="breadcrumb-item active">Edit Pemberian Pakan</li>
    </ol>

    @include('components.alert')
    <form id="formPembagian" name="form_pembagian" action="{{ route('pemberian.pakan.update', $data->id) }}" method="POST">
        @csrf
        <div class="mb-3">
            <label for="inputAlamat" class="form-label">Pembagian Pakan</label>
            <input type="hidden" name="id_pembagian_pakan" value="{{ $data->id_detail_pembagian_pakan }}">
            <select class="form-select" id="selectBagiPakan" data-placeholder="Pilih Pembagaian Pakan">
                <option></option>
                @foreach ($pembagianPakan as $value)
                    <option value="{{ $value->id }}">
                        {{ $value->header_pembagian_pakan->tgl_pembagian_pakan . ' | ' . $value->detail_beli->produk->nama }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label for="inputAlamat" class="form-label">Pembagian Bibit</label>
            <select class="form-select" id="selectBagiBibit" data-placeholder="Pilih Pembagaian Bibit"
                name="id_pembagian_bibit">
                <option></option>
                @foreach ($pembagianBibit as $value)
                    <option value="{{ $value->id }}">
                        {{ $value->header_pembagian_bibit->tgl_pembagian . ' | ' . $value->header_pembagian_bibit->detail_beli->produk->nama . ' (' . $value->quantity . ')' }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label for="inputQuantity" class="form-label">Quantity</label>
            <input type="number" class="form-control" id="inputQuantity" name="quantity" value="{{ $data->quantity }}">
        </div>

        <button type="submit" class="btn btn-primary  w-100" id="btnSimpan">
            <i class="fas fa-spinner fa-spin d-none me-2"></i>Simpan
        </button>
    </form>
@endsection

@push('script')
    <script>
        $('#selectBagiPakan').val({!! $data->id_detail_pembagian_pakan !!})
        $('#selectBagiBibit').val({!! $data->id_detail_pembagian_bibit !!})


        // inisialisasi form select 2
        $(".form-select").select2({
            theme: "bootstrap-5",
            allowClear: true,
            containerCssClass: "select2--medium",
            dropdownCssClass: "select2--medium",
        });

        $('#selectBagiPakan').select2("enable", false);
    </script>
@endpush
