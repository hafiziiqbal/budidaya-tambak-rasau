@extends('layouts.admin')
@section('content')
    <h1 class="mt-4">Edit Pembagian Bibit</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('pembagian.bibit') }}">Pembagian Bibit</a></li>
        <li class="breadcrumb-item active">Edit Pembagian Bibit</li>
    </ol>

    @include('components.alert')
    <div id="headerPembelian" class="mb-5">
        <div class="bg-info p-2 border-dark border-bottom mb-3">
            <label class="fw-bold">Header Pembagian Bibit</label>
        </div>
        <form action="{{ route('pembagian.bibit.update', $data->id) }}" method="POST">
            @csrf
            <div class="mb-3">
                <label for="inputTanggalPembagian" class="form-label">Tanggal Pembagian</label>
                <div class="input-group mb-3">
                    <span class="input-group-text" id="basic-addon1"><i class="fa fa-calendar"></i></span>
                    <input type="text" name="tgl_pembagian" id="inputTanggalPembagian" class="form-control"
                        aria-describedby="basic-addon1" data-date-format="dd-mm-yyyy" data-provide="datepicker"
                        value="{{ date('d-m-Y', strtotime($data->tgl_pembagian)) }}">>
                </div>
                @if ($errors->has('tgl_pembagian'))
                    <small class="text-danger">*{{ $errors->first('tgl_pembagian') }}</small>
                @endif
            </div>

            <div class="mb-3">
                <label for="inputDetailBeli" class="form-label">Tanggal Pembelian Bibit</label>
                <select class="form-select" id="inputDetailBeli" data-placeholder="Pilih Tanggal Pembelian Bibit"
                    name="id_detail_beli">
                    <option></option>
                    @foreach ($detailBeli as $value)
                        @if ($value->produk->quantity > 0)
                            <option value="{{ $value->id }}" data-quantity="{{ $value->quantity }}">
                                @DateIndo($value->header_beli->tgl_beli){{ ' | ' . $value->produk->nama }}
                            </option>
                        @endif
                        @if ($value->id == $data->id_detail_beli)
                            <option value="{{ $value->id }}" data-quantity="{{ $value->quantity }}">
                                @DateIndo($value->header_beli->tgl_beli){{ ' | ' . $value->produk->nama }}
                            </option>
                        @endif
                    @endforeach
                </select>
            </div>

            <div class="mb-3">
                <label for="inputPanen" class="form-label">Sortir Kembali</label>
                <select class="form-select" id="inputPanen" data-placeholder="Pilih Ikan" name="id_panen">
                    <option></option>
                    {{-- @foreach ($pembelian as $value)
                        <option value="{{ $value->id }}">
                            @DateIndo($value->header_beli->tgl_beli){{ ' | ' . $value->produk->nama }}
                        </option>
                    @endforeach --}}
                </select>
            </div>
            <div class="mb-3">
                <button type="submit" class="btn btn-primary  w-100" id="btnSimpan">Perbarui</button>
            </div>
        </form>
    </div>

    <div id="detail">
        <div class="bg-info p-2 border-dark border-bottom mb-3">
            <label class="fw-bold">Detail Pembagian Bibit</label>
        </div>
        <div class="mb-4 detail-pembagian d-none" id="detailPembagianFirst">
            <div class="card-header border d-flex justify-content-between align-items-center">
                <span class="fw-bold"><label class="pembagian"></label><label class="text-success"
                        id="status"></label></span>
                <div class="btn-content">
                    <button class="btn btn-primary simpan-detail-baru me-2"> <i
                            class="fas fa-spinner fa-spin d-none me-2"></i>Simpan</button>
                </div>
                <div class="btn-update-content d-none">
                    <button class="btn btn-success btn-update-detail"><i class="fas fa-spinner fa-spin d-none"></i>
                        Perbarui</button>
                    <button class="btn btn-danger">Hapus</button>
                </div>
            </div>
            <div class="card-body border">
                <div class="mb-3">
                    <label for="inputQuantity" class="form-label">Quantity</label>
                    <input type="number" class="form-control quantity" id="inputQuantity" required>
                    <label class="alert-quantity text-danger"></label>
                </div>
                <div class="mb-3">
                    <label for="inputJaring" class="form-label">Pilih Jaring</label>
                    <select class="form-select jaring" data-placeholder="Pilih Jaring">
                        <option value="null">Lepas Jaring</option>
                        @foreach ($jaring as $value)
                            <option value="{{ $value->id }}">
                                {{ $value->nama }}
                            </option>
                        @endforeach
                    </select>
                    <label class="alert-jaring text-danger"></label>

                </div>

                <div class="mb-3">
                    <label for="inputKolam" class="form-label">Pilih Kolam</label>
                    <select class="form-select kolam" data-placeholder="Pilih Kolam">
                        <option></option>
                        @foreach ($kolam as $value)
                            <option value="{{ $value->id }}">
                                {{ $value->nama }}
                            </option>
                        @endforeach
                    </select>
                    <label class="alert-kolam text-danger"></label>
                </div>
            </div>
        </div>
        @foreach ($data->detail_pembagian_bibit as $key => $item)
            <div class="mb-4 detail-pembagian">
                <div class="card-header border d-flex justify-content-between align-items-center">
                    <span class="fw-bold">Pembagian Ke {{ $key + 1 }} <label class="text-success"
                            id="status{{ $key }}"></label></span>
                    <div>
                        <button class="btn btn-success btn-update-detail" data-key="{{ $key }}"
                            data-id="{{ $item->id }}"><i class="fas fa-spinner fa-spin d-none"></i> Perbarui</button>
                        <button class="btn btn-danger">Hapus</button>
                    </div>
                </div>
                <div class="card-body border">
                    <div class="mb-3">
                        <label for="inputQuantity" class="form-label">Quantity</label>
                        <input type="number" class="form-control quantity"
                            name="detail_pembagian_bibit[{{ $key }}][quantity]" id="inputQuantity" required
                            value="{{ $item->quantity }}">
                        <label id="alertQuantity{{ $key }}" class="text-danger"></label>

                    </div>

                    <div class="mb-3">
                        <label for="inputJaring" class="form-label">Pilih Jaring</label>
                        <select class="form-select jaring" data-placeholder="Pilih Jaring"
                            name="detail_pembagian_bibit[{{ $key }}][id_jaring]">
                            <option value="null">Lepas Jaring</option>
                            @foreach ($jaring as $value)
                                <option value="{{ $value->id }}">
                                    {{ $value->nama }}
                                </option>
                            @endforeach
                        </select>
                        <label id="alertJaring{{ $key }}" class="text-danger"></label>
                    </div>

                    <div class="mb-3">
                        <label for="inputKolam" class="form-label">Pilih Kolam</label>
                        <select class="form-select kolam" data-placeholder="Pilih Kolam"
                            name="detail_pembagian_bibit[{{ $key }}][id_kolam]">
                            <option></option>
                            @foreach ($kolam as $value)
                                <option value="{{ $value->id }}">
                                    {{ $value->nama }}
                                </option>
                            @endforeach
                        </select>
                        <label id="alertKolam{{ $key }}" class="text-danger"></label>
                    </div>
                </div>
            </div>
        @endforeach

    </div>
    <button type="button" class="btn btn-dark my-3" id="btnTambahPembagian"><i class="fa fa-plus"></i> Tambah
    </button>
@endsection

@push('script')
    <script>
        let detailPembagian = {!! $data->detail_pembagian_bibit !!}
        let item = detailPembagian.length - 1;

        $(".form-select").select2({
            theme: "bootstrap-5",

            containerCssClass: "select2--medium",
            dropdownCssClass: "select2--medium",
        });


        // nilai default detail beli
        $('#inputDetailBeli').val({!! $data->id_detail_beli !!});
        $('#inputDetailBeli').trigger('change');

        // nilai default detail pembagian
        detailPembagian.forEach((e, i) => {
            $(`.jaring[name='detail_pembagian_bibit[${i}][id_jaring]']`).val(e.id_jaring == null ? 'null' : e
                .id_jaring)
            $(`.jaring[name='detail_pembagian_bibit[${i}][id_jaring]']`).trigger('change');

            $(`.kolam[name='detail_pembagian_bibit[${i}][id_kolam]']`).val(e.id_kolam)
            $(`.kolam[name='detail_pembagian_bibit[${i}][id_kolam]']`).trigger('change');
        });

        // tambah element detail pembagian
        $("#btnTambahPembagian").click(function() {
            let element = $('#detailPembagianFirst');
            element.find('select').select2('destroy')
            let clone = element.clone()
            clone.removeClass('d-none');
            clone.find('input').val('')
            clone.find('.simpan-detail-baru').attr("data-key", item = item + 1);
            clone.find('.simpan-detail-baru').attr("id", `btnSimpanDetail${item}`);

            clone.find('.btn-update-detail').attr("data-key", item);
            clone.find('.btn-update-detail').attr("id", `btnUpdate${item}`);
            clone.find('.btn-content').attr("id", `btnContent${item}`);
            clone.find('.btn-update-content').attr("id", `btnUpdateContent${item}`);


            clone.find('.alert-quantity').attr('id', `alertQuantity${item}`)
            clone.find('.alert-jaring').attr('id', `alertJaring${item}`)
            clone.find('.alert-kolam').attr('id', `alertKolam${item}`)
            clone.find('#status').attr('id', `status${item}`)

            clone.find('.btn-content').append(
                '<button type="button" class="btn-close" aria-label="Close"></button>')
            clone.find('label.pembagian').html(`Pembagian Ke ${item + 1}`)

            $(`.jaring[name='detail_pembagian_bibit[${item}][id_jaring]']`).val('null')
            $(`.jaring[name='detail_pembagian_bibit[${item}][id_jaring]']`).trigger('change');


            clone.find('#inputQuantity').attr('name', `detail_pembagian_bibit[${item}][quantity]`);
            clone.find('select.jaring').attr('name', `detail_pembagian_bibit[${item}][id_jaring]`);
            clone.find('select.kolam').attr('name', `detail_pembagian_bibit[${item}][id_kolam]`);


            $("#detail").append(clone);

            $(".form-select").select2({
                theme: "bootstrap-5",
                containerCssClass: "select2--medium",
                dropdownCssClass: "select2--medium",
            });


            $('.btn-close').click(function() {
                $(this).parent().parent().parent().remove();

            })

            // simpan detail baru
            $(`#btnSimpanDetail${item}`).on("click", function(e) {
                // diisable button
                $(this).attr('disabled', 'disabled')
                $(this).children().removeClass('d-none')

                let key = $(this).data('key')
                let thisButton = $(this)

                let data = {
                    "_token": $('meta[name="csrf-token"]').attr('content'),
                    "id_header_pembagian_bibit": {!! $data->id !!}
                }

                data["quantity"] = $(
                    `input[name="detail_pembagian_bibit[${$(this).data('key')}][quantity]"]`).val();
                data["id_jaring"] = $(
                    `.jaring[name='detail_pembagian_bibit[${$(this).data('key')}][id_jaring]']`).val();
                data["id_kolam"] = $(
                    `.kolam[name='detail_pembagian_bibit[${$(this).data('key')}][id_kolam]']`).val()

                $.post(`/pembagian-bibit/detail`, data)
                    .done(function(data, statusText, xhr) {
                        let status = statusText;
                        if (status == 'success') {
                            thisButton.removeAttr('disabled')
                            thisButton.children().addClass('d-none')
                        }
                        console.log(data);

                        if (data.sukses != undefined) {
                            $(`#status${key}`).html(` (${data.sukses})`)
                            $(`#btnContent${key}`).remove()
                            $(`#btnUpdate${key}`).attr('data-id', data.id)
                            $(`#btnUpdateContent${key}`).removeClass('d-none')
                            $(`#btnUpdateContent${key}`).removeClass('d-none')
                        }

                        // jika ada alert jaring
                        if (data.alert_jaring != undefined) {
                            $(`#alertJaring${key}`).html('*' + data.alert_jaring)

                        } else {
                            $(`#alertJaring${key}`).html('')

                        }

                        // jika ada alert quantity
                        if (data.alert_quantity != undefined) {
                            $(`#alertQuantity${key}`).html('*' + data.alert_quantity)
                        } else {
                            $(`#alertQuantity${key}`).html('')
                        }

                        // jika ada alert kolam
                        if (data.alert_kolam != undefined) {
                            $(`#alertKolam${key}`).html('*' + data.alert_kolam)
                        } else {
                            $(`#alertKolam${key}`).html('')
                        }
                    });
            });

            $(".btn-update-detail").click(function() {
                // diisable button
                $(this).attr('disabled', 'disabled')
                $(this).children().removeClass('d-none')


                let id = $(this).data('id');
                let key = $(this).data('key')
                let thisButton = $(this)

                $(`[id^=status]`).html('')

                let data = {
                    "_token": $('meta[name="csrf-token"]').attr('content'),
                }
                data["quantity"] = $(
                    `input[name="detail_pembagian_bibit[${$(this).data('key')}][quantity]"]`).val();
                data["id_jaring"] = $(
                    `.jaring[name='detail_pembagian_bibit[${$(this).data('key')}][id_jaring]']`).val();
                data["id_kolam"] = $(
                    `.kolam[name='detail_pembagian_bibit[${$(this).data('key')}][id_kolam]']`).val()


                $.post(`/pembagian-bibit/${id}/update-detail`, data)
                    .done(function(data, statusText, xhr) {
                        let status = statusText;
                        if (status == 'success') {
                            thisButton.removeAttr('disabled')
                            thisButton.children().addClass('d-none')
                        }

                        if (data.sukses != undefined) {
                            $(`#status${key}`).html(` (${data.sukses})`)
                            $(`btnUpdate${key}`).attr('data-id', data.id)
                        }

                        // jika ada alert jaring
                        if (data.alert_jaring != undefined) {
                            $(`#alertJaring${key}`).html('*' + data.alert_jaring)

                        } else {
                            $(`#alertJaring${key}`).html('')

                        }

                        // jika ada alert quantity
                        if (data.alert_quantity != undefined) {
                            $(`#alertQuantity${key}`).html('*' + data.alert_quantity)
                        } else {
                            $(`#alertQuantity${key}`).html('')
                        }

                        // jika ada alert kolam
                        if (data.alert_kolam != undefined) {
                            $(`#alertKolam${key}`).html('*' + data.alert_kolam)
                        } else {
                            $(`#alertKolam${key}`).html('')
                        }
                    });
            });
        });



        // update detail pembagian
        $(".btn-update-detail").click(function() {
            // diisable button
            $(this).attr('disabled', 'disabled')
            $(this).children().removeClass('d-none')


            let id = $(this).data('id');
            let key = $(this).data('key')
            let thisButton = $(this)

            $(`[id^=status]`).html('')

            let data = {
                "_token": $('meta[name="csrf-token"]').attr('content'),
            }
            data["quantity"] = $(`input[name="detail_pembagian_bibit[${$(this).data('key')}][quantity]"]`).val();
            data["id_jaring"] = $(`.jaring[name='detail_pembagian_bibit[${$(this).data('key')}][id_jaring]']`)
                .val();
            data["id_kolam"] = $(`.kolam[name='detail_pembagian_bibit[${$(this).data('key')}][id_kolam]']`).val()


            $.post(`/pembagian-bibit/${id}/update-detail`, data)
                .done(function(data, statusText, xhr) {
                    let status = statusText;
                    if (status == 'success') {
                        thisButton.removeAttr('disabled')
                        thisButton.children().addClass('d-none')
                    }

                    if (data.sukses != undefined) {
                        $(`#status${key}`).html(` (${data.sukses})`)
                        $(`btnUpdate${key}`).attr('data-id', data.id)
                    }

                    // jika ada alert jaring
                    if (data.alert_jaring != undefined) {
                        $(`#alertJaring${key}`).html('*' + data.alert_jaring)

                    } else {
                        $(`#alertJaring${key}`).html('')

                    }

                    // jika ada alert quantity
                    if (data.alert_quantity != undefined) {
                        $(`#alertQuantity${key}`).html('*' + data.alert_quantity)
                    } else {
                        $(`#alertQuantity${key}`).html('')
                    }

                    // jika ada alert kolam
                    if (data.alert_kolam != undefined) {
                        $(`#alertKolam${key}`).html('*' + data.alert_kolam)
                    } else {
                        $(`#alertKolam${key}`).html('')
                    }
                });
        });
    </script>
@endpush
