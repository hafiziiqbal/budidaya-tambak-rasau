@extends('layouts.admin')
@section('content')
    <h1 class="mt-4">Tambah Pemberian Pakan</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('pemberian.pakan') }}">Pemberian Pakan</a></li>
        <li class="breadcrumb-item active">Tambah Pemberian Pakan</li>
    </ol>

    <form id="formPembagian" name="form_pembagian" action="{{ route('pemberian.pakan.store') }}" method="POST">
        @csrf

        @include('components.alert')

        <div class="mb-3">
            <label for="inputAlamat" class="form-label">Pembagian Pakan</label>
            <select class="form-select" id="selectBagiPakan" data-placeholder="Pilih Pembagaian Pakan"
                name="id_pembagian_pakan" required>
                <option></option>
                {{ $pembagianPakan }}
                @foreach ($pembagianPakan as $value)
                    <option value="{{ $value->id }}" data-tong="{{ $value->id_tong }}"
                        {{ old('id_pembagian_pakan') == $value->id ? 'selected' : '' }}>
                        {{ $value->header_pembagian_pakan->tgl_pembagian_pakan . ' | ' . $value->detail_beli->produk->nama . ' : ' . $value->quantity }}
                    </option>
                @endforeach
            </select>
            @if ($errors->has('id_pembagian_pakan'))
                <small class="text-danger">*{{ $errors->first('id_pembagian_pakan') }}</small>
            @endif
        </div>

        <div class="mb-3">
            <label for="inputAlamat" class="form-label">Pembagian Bibit</label>
            <select class="form-select" id="selectBagiBibit" data-placeholder="Pilih Pembagaian Bibit"
                name="id_pembagian_bibit">
                <option></option>
            </select>
            @if ($errors->has('id_pembagian_bibit'))
                <small class="text-danger">*{{ $errors->first('id_pembagian_bibit') }}</small>
            @endif
        </div>

        <div class="mb-3">
            <label for="inputQuantity" class="form-label">Quantity</label>
            <input type="number" class="form-control" id="inputQuantity" name="quantity" value="{{ old('quantity') }}">
            @if ($errors->has('quantity'))
                <small class="text-danger">*{{ $errors->first('quantity') }}</small>
            @endif
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

        $('#selectBagiPakan').val({!! old('id_pembagian_pakan') !!})
        if ($('#selectBagiPakan').val() != '') {
            let idTong = $('#selectBagiPakan').find(':selected').data('tong');

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
        }

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

        // start get share bibit
        const cookies = document.cookie.split(";");
        shareIdDetail = getCookie('sharePakanDetailBagi');
        shareUrl = getCookie('sharePakanUrl');
        if (shareIdDetail != '') {
            $('#selectBagiPakan').select2("val", shareIdDetail);
            $('#selectBagiPakan').trigger('change');
            $('ol a').attr('href', `/${shareUrl}`);
            $('ol a').html('Pembagian Bibit');


            $('#btnTambahPembagian').trigger("click");
            // Menghapus cookie dengan nama "nama_cookie" dan path "/admin"
            $.removeCookie("shareBibitDetailBeli", {
                path: "/pembagian-bibit/create"
            });
            $.removeCookie("shareBibitTanggal", {
                path: "/pembagian-bibit/create"
            });
            $.removeCookie("shareBibitJenis", {
                path: "/pembagian-bibit/create"
            });
            $.removeCookie("shareBibitUrl", {
                path: "/pembagian-bibit/create"
            });
        }
        // end get share bibit
    </script>
@endpush
