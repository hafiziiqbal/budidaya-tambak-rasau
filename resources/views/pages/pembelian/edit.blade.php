@extends('layouts.admin')
@section('content')
    <h1 class="mt-4">Edit Pembelian</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('pembelian') }}">Pembelian</a></li>
        <li class="breadcrumb-item active">Edit Pembelian</li>
    </ol>

    {{-- header beli --}}
    <form method="POST" id="formHeader" action="{{ route('pembelian.update', $data->id) }}" name="form_header">
        @csrf
        <div id="headerPembelian" class="mb-4">
            <div class="bg-info p-2 border-dark border-bottom mb-3">
                <label class="fw-bold">Header Pembelian</label>
            </div>
            <div class="mb-3">
                <label for="inputNama" class="form-label">Tanggal Beli</label>
                <div class="input-group mb-3">
                    <span class="input-group-text" id="basic-addon1"><i class="fa fa-calendar"></i></span>
                    <input type="text" name="tgl_beli" class="form-control" aria-describedby="basic-addon1"
                        data-date-format="dd-mm-yyyy" data-provide="datepicker"
                        value="{{ date('d-m-Y', strtotime($data->tgl_beli)) }}">>
                </div>
            </div>

            <div class="mb-3">
                <label for="selectSupplier" class="form-label">Supplier</label>
                <select class="form-select" id="selectSupplier" data-placeholder="Pilih Supplier" name="supplier">
                    <option></option>

                    @foreach ($supplier as $supplier)
                        <option value="{{ $supplier->id }}">
                            {{ $supplier->nama }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="mb-3">
                <label for="inputBruto" class="form-label">Total Bruto</label>
                <input type="number" class="form-control" id="inputBruto" value="{{ $data->total_bruto }}" disabled>

            </div>
            <div class="mb-3">
                <label for="inputPotonganHarga" class="form-label">Potongan Harga</label>
                <input type="number" class="form-control" id="inputPotonganHarga" required name="potongan_harga"
                    value="{{ $data->potongan_harga }}">
            </div>
            <div class="mb-3">
                <label for="inputBruto" class="form-label">Total Netto</label>
                <input type="number" class="form-control" id="inputNetto" value="{{ $data->total_netto }}" disabled>

            </div>
            <button type="submit" class="btn btn-primary w-100">Simpan Perubahan</button>
        </div>
    </form>


    {{-- <form action="{{ route('pembelian.update', $pembelian->id) }}" method="POST">
        @csrf
        <input type="hidden" name="id_header_beli" value="{{ $pembelian->header_beli->id }}" required>
        <div id="detail">
            <div class="bg-info p-2 border-dark border-bottom mb-3">
                <label class="fw-bold">Detail Pembelian</label>
            </div>
            <div class="mb-4 detail-pembelian">
                <div class="card-header border d-flex justify-content-end"></div>
                <div class="card-body border">
                    <div class="mb-3">
                        <label for="selectProduk" class="form-label">Produk</label>
                        <select class="form-select produk" data-placeholder="Pilih Produk" required name="id_produk">
                            <option></option>
                            @foreach ($produk as $produk)
                                <option value="{{ $produk->id }}">
                                    {{ $produk->nama }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="inputHargaSatuan" class="form-label">Harga Satuan</label>
                        <input type="number" class="form-control" id="inputHargaSatuan" required name="harga_satuan"
                            value="{{ $pembelian->harga_satuan }}">
                        @if ($errors->has('harga_satuan'))
                            <small class="text-danger">*{{ $errors->first('harga_satuan') }}</small>
                        @endif
                    </div>
                    <div class="mb-3">
                        <label for="inputQuantity" class="form-label">Quantity</label>
                        <input type="number" class="form-control" id="inputQuantity" required name="quantity"
                            value="{{ $pembelian->quantity }}">
                        @if ($errors->has('quantity'))
                            <small class="text-danger">*{{ $errors->first('quantity') }}</small>
                        @endif
                    </div>
                    <div class="mb-3">
                        <label for="inputDiskonPersen" class="form-label">Diskon Persen</label>

                        <input type="number" class="form-control" id="inputDiskonPersen" name="diskon_persen"
                            value="{{ $pembelian->diskon_persen }}">

                        @if ($errors->has('diskon_persen'))
                            <small class="text-danger">*{{ $errors->first('diskon_persen') }}</small>
                        @endif
                    </div>
                    <div class="mb-3">
                        <label for="inputDiskonRupiah" class="form-label">Diskon Rupiah</label>
                        <input type="number" class="form-control" id="inputDiskonRupiah" name="diskon_rupiah"
                            value="{{ $pembelian->diskon_rupiah }}">
                        @if ($errors->has('diskon_rupiah'))
                            <small class="text-danger">*{{ $errors->first('diskon_rupiah') }}</small>
                        @endif
                    </div>
                </div>
            </div>


        </div>
        <button type="submit" class="btn btn-primary  w-100">Perbarui</button>
    </form> --}}
@endsection

@push('script')
    <script>
        // inisialisasi form select 2
        $(".form-select").select2({
            theme: "bootstrap-5",
            containerCssClass: "select2--medium",
            dropdownCssClass: "select2--medium",
        });

        // nilai default suppplier
        let supplier = {!! $data->supplier->id !!}
        console.log(supplier);
        $(`#selectSupplier`).val(supplier)
        $(`#selectSupplier`).trigger('change');


        // handle form_header
        $("#formHeader").on("submit", function(e) { //id of form 
            e.preventDefault();
            let action = $(this).attr("action"); //get submit action from form
            let method = $(this).attr("method"); // get submit method
            let form_data = new FormData($(this)[0]); // convert form into formdata         
            $.ajax({
                url: action,
                dataType: 'json', // what to expect back from the server
                cache: false,
                contentType: false,
                processData: false,
                data: form_data,
                type: method,

                success: function(response) {
                    if (response.status) {
                        $(".alert-danger").remove();
                        form.trigger("reset");
                        $("#suc_msg").show();
                        var data = `${response.msg}
                                <div>id : ${response.post.id} <br>
                                title :   ${response.post.title} <br>
                                Image : <img src='${response.post.picture}' width=100 /> <br></div>`;
                        $("#suc_msg").html(data);

                    }
                    // display success response from the server
                },
                error: function(response) { // handle the error
                    $(".alert-danger").remove();
                    try {
                        var erroJson = JSON.parse(response.responseText);
                        for (var err in erroJson) {
                            for (var errstr of erroJson[err])
                                $("[name='" + err + "']").after("<div class='alert alert-danger'>" +
                                    errstr + "</div>");
                        }
                        if (options.error) {
                            options.error(response);
                        }
                    } catch (err) {

                    }

                },

            })
        });
    </script>
@endpush
