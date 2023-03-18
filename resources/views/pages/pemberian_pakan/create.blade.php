@extends('layouts.admin')
@section('content')
    <h1 class="mt-4">Tambah Pemberian Pakan</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('pemberian.pakan') }}">Pemberian Pakan</a></li>
        <li class="breadcrumb-item active">Tambah Pemberian Pakan</li>
    </ol>


    <form id="formPembagian" name="form_pembagian" action="{{ route('pemberian.pakan.store') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label for="inputAlamat" class="form-label">Pembagian Pakan</label>
            <select class="form-select" id="selectBagiPakan" data-placeholder="Pilih Pembagaian Pakan"
                name="id_pembagian_pakan">
                <option></option>
                {{ $pembagianPakan }}
                @foreach ($pembagianPakan as $value)
                    <option value="{{ $value->id }}" data-tong="{{ $value->id_tong }}">
                        {{ $value->header_pembagian_pakan->tgl_pembagian_pakan . ' | ' . $value->detail_beli->produk->nama . ' : ' . $value->quantity }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label for="inputAlamat" class="form-label">Pembagian Bibit</label>
            <select class="form-select" id="selectBagiBibit" data-placeholder="Pilih Pembagaian Bibit"
                name="id_pembagian_bibit">
                <option></option>
            </select>
        </div>

        <div class="mb-3">
            <label for="inputQuantity" class="form-label">Quantity</label>
            <input type="number" class="form-control" id="inputQuantity" name="quantity">
        </div>

        <button type="submit" class="btn btn-primary  w-100" id="btnSimpan">
            <i class="fas fa-spinner fa-spin d-none me-2"></i>Simpan
        </button>
    </form>
@endsection

@push('script')
    <script>
        // inisialisasi form select 2
        $(".form-select").select2({
            theme: "bootstrap-5",
            allowClear: true,
            containerCssClass: "select2--medium",
            dropdownCssClass: "select2--medium",
        });

        $('#selectBagiPakan').on('change', function() {
            let idTong = $(this).find(':selected').data('tong');

            if (idTong) {
                $.ajax({
                    url: '/pemberian-pakan/pembagian-bibit-by-tong/' + idTong,
                    dataType: 'json',
                    success: function(data) {


                        var selectTongs = $('#selectBagiBibit');
                        selectTongs.empty();

                        $.each(data, function(index, value) {
                            let jaring = ''
                            if (value.jaring != null) {
                                jaring = value.jaring.nama
                            }
                            selectTongs.append('<option value="' + value.id + '">' + value
                                .header_pembagian_bibit.tgl_pembagian + ' | ' +
                                value
                                .header_pembagian_bibit.detail_beli.produk.nama + ' : ' +
                                value.quantity + '(' + value.kolam.nama + '  ' + jaring +
                                ')</option>');
                        });

                        selectTongs.select2({
                            theme: "bootstrap-5",
                            allowClear: true,
                            containerCssClass: "select2--medium",
                            dropdownCssClass: "select2--medium",
                        });
                    }
                });
            }
        });
    </script>
@endpush
