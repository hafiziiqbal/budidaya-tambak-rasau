@extends('layouts.admin')
@section('content')
    <h1 class="mt-4">Informasi Pembelian</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('pembelian') }}">Pembelian</a></li>
        <li class="breadcrumb-item active">Informasi Pembelian</li>
    </ol>

    <div id="headerPembelian" class="mb-4">
        <div class="bg-info p-2 border-dark border-bottom">
            <label class="fw-bold">Header Pembelian</label>
        </div>
        <div class=" p-2 ">
            <div class="row border-bottom py-2">
                <div class="col-md-6 col-12  fst-italic fw-bold">
                    Tanggal Beli
                </div>
                <div class="col-md-6 col-12 ">
                    @DateIndo({{ $pembelian->header_beli->tgl_beli }})

                </div>
            </div>
            <div class="row border-bottom py-2">
                <div class="col-md-6 col-12  fst-italic fw-bold">
                    Supplier
                </div>
                <div class="col-md-6 col-12 ">
                    <a href="#" data-bs-toggle="modal"
                        data-bs-target="#modalProduk">{{ $pembelian->header_beli->supplier->nama }}</a>

                </div>
            </div>
            <div class="row border-bottom py-2">
                <div class="col-md-6 col-12  fst-italic fw-bold">
                    Total Bruto
                </div>
                <div class="col-md-6 col-12 ">
                    {{ $pembelian->header_beli->total_bruto }}
                </div>
            </div>
            <div class="row border-bottom py-2">
                <div class="col-md-6 col-12  fst-italic fw-bold">
                    Total Netto
                </div>
                <div class="col-md-6 col-12 ">
                    {{ $pembelian->header_beli->total_netto }}
                </div>
            </div>
            <div class="row border-bottom py-2">
                <div class="col-md-6 col-12  fst-italic fw-bold">
                    Potongan Harga
                </div>
                <div class="col-md-6 col-12 ">
                    Rp. {{ $pembelian->header_beli->potongan_harga }}
                </div>
            </div>
        </div>
    </div>

    <div id="detailPembelian">
        <div class="bg-info p-2 border-dark border-bottom">
            <label class="fw-bold">Detail Pembelian</label>
        </div>
        <div class=" p-2 ">
            <div class="row border-bottom py-2">
                <div class="col-md-6 col-12  fst-italic fw-bold">
                    Nama Produk
                </div>
                <div class="col-md-6 col-12 ">
                    <a href="#" data-bs-toggle="modal"
                        data-bs-target="#modalProduk">{{ $pembelian->produk->nama }}</a>
                </div>
            </div>
            <div class="row border-bottom py-2">
                <div class="col-md-6 col-12  fst-italic fw-bold">
                    Harga Satuan
                </div>
                <div class="col-md-6 col-12 ">
                    Rp. {{ $pembelian->harga_satuan }}
                </div>
            </div>
            <div class="row border-bottom py-2">
                <div class="col-md-6 col-12  fst-italic fw-bold">
                    Quantity
                </div>
                <div class="col-md-6 col-12 ">
                    {{ $pembelian->quantity }}
                </div>
            </div>
            <div class="row border-bottom py-2">
                <div class="col-md-6 col-12  fst-italic fw-bold">
                    Diskon Persen
                </div>
                <div class="col-md-6 col-12 ">
                    {{ $pembelian->diskon_persen }} %
                </div>
            </div>
            <div class="row border-bottom py-2">
                <div class="col-md-6 col-12  fst-italic fw-bold">
                    Diskon Rupiah
                </div>
                <div class="col-md-6 col-12 ">
                    Rp. {{ $pembelian->diskon_rupiah }}
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Supplier-->
    <div class="modal fade" id="modalSupplier" tabindex="-1" aria-labelledby="modalSupplierLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalSupplierLabel">Supplier</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row py-2">
                        <div class="col-md-6 col-12  fst-italic fw-bold">
                            Nama
                        </div>
                        <div class="col-md-6 col-12 ">
                            {{ $pembelian->header_beli->supplier->nama }}
                        </div>
                    </div>
                    <div class="row py-2">
                        <div class="col-md-6 col-12  fst-italic fw-bold">
                            Alamat
                        </div>
                        <div class="col-md-6 col-12 ">
                            {{ $pembelian->header_beli->supplier->alamat }}
                        </div>
                    </div>
                    <div class="row py-2">
                        <div class="col-md-6 col-12  fst-italic fw-bold">
                            Telepon
                        </div>
                        <div class="col-md-6 col-12 ">
                            {{ $pembelian->header_beli->supplier->telepon }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Produk-->
    <div class="modal fade" id="modalProduk" tabindex="-1" aria-labelledby="modalProdukLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalProdukLabel">Produk</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row py-2">
                        <div class="col-md-6 col-12  fst-italic fw-bold">
                            Nama
                        </div>
                        <div class="col-md-6 col-12 ">
                            {{ $pembelian->produk->nama }}
                        </div>
                    </div>
                    <div class="row py-2">
                        <div class="col-md-6 col-12  fst-italic fw-bold">
                            Kategori
                        </div>
                        <div class="col-md-6 col-12 ">
                            {{ $pembelian->produk->kategori->nama }}
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection
