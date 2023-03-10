@extends('layouts.admin')
@section('content')
    <h1 class="mt-4">Informasi Pembagian Bibit</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('pembagian.bibit') }}">Pembagian Bibit</a></li>
        <li class="breadcrumb-item active">Informasi Pembagian Bibit</li>
    </ol>

    <div id="headerPembagian" class="mb-4">
        <div class="bg-info p-2 border-dark border-bottom">
            <label class="fw-bold">Header Pembagian Bibit</label>
        </div>
        <div class=" p-2 ">
            <div class="row border-bottom py-2">
                <div class="col-md-6 col-12  fst-italic fw-bold">
                    Tanggal Pembagian Bibit
                </div>
                <div class="col-md-6 col-12 ">
                    @DateIndo({{ $data->tgl_pembagian }})
                </div>
            </div>
        </div>
        <div class=" p-2 ">
            <div class="row border-bottom py-2">
                <div class="col-md-6 col-12  fst-italic fw-bold">
                    Produk
                </div>
                <div class="col-md-6 col-12 ">
                    {{ $data->detail_beli->produk->nama }}
                </div>
            </div>
        </div>
        <div class=" p-2 ">
            <div class="row border-bottom py-2">
                <div class="col-md-6 col-12  fst-italic fw-bold">
                    Tanggal Beli
                </div>
                <div class="col-md-6 col-12 ">
                    @DateIndo({{ $data->detail_beli->header_beli->tgl_beli }})

                </div>
            </div>
        </div>
        <div class=" p-2 ">
            <div class="row border-bottom py-2">
                <div class="col-md-6 col-12  fst-italic fw-bold">
                    Tanggal Panen
                </div>
                <div class="col-md-6 col-12 ">

                </div>
            </div>
        </div>
    </div>


    <div id="detailPembagian">
        <div class="bg-info p-2 border-dark border-bottom">
            <label class="fw-bold">Detail Pembagian Bibit</label>
        </div>

        @foreach ($data->detail_pembagian_bibit as $key => $item)
            <div class="mb-4 detail-pembagian" id="detailPembagianFirst">
                <div class="card-header border fw-bold">Bagian {{ $key + 1 }}</div>
                <div class="card-body border">
                    <div class="mb-3">
                        <label for="inputQuantity" class="form-label">Quantity</label>
                        <input type="text" class="form-control quantity" disabled value="{{ $item->quantity }}">
                    </div>
                    <div class="mb-3">
                        <label for="inputQuantity" class="form-label">Jaring</label>
                        <input type="text" class="form-control quantity" disabled
                            value="{{ $item->jaring->nama ?? '' }}">
                    </div>
                    <div class="mb-3">
                        <label for="inputQuantity" class="form-label">Kolam</label>
                        <input type="text" class="form-control quantity" disabled value="{{ $item->kolam->nama }}">
                    </div>

                </div>
            </div>
        @endforeach



    </div>
@endsection

@push('script')
@endpush
