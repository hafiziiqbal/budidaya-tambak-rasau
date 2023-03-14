@extends('layouts.admin')
@section('content')
    <h1 class="mt-4">Pembagian Pakan</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item active">Pembagian Pakan</li>
    </ol>

    <a href="{{ route('pembagian.pakan.create') }}" class="btn btn-primary mb-4"><i class="fa fa-plus"></i>&emsp; Tambah
        Data</a>
    @include('components.alert')

    <table id="tblPembagianPakan" class="table table-striped table-bordered nowrap" style="width:100%">
        <thead>
            <tr>
                <th>No</th>
                <th>Tanggal</th>
                <th>Produk</th>
                <th>Tong</th>
                <th>Aksi</th>
            </tr>
        </thead>
    </table>
@endsection

@push('script')
    <script>
        $(document).ready(function() {
            let table = $('#tblPembagianPakan').DataTable({
                responsive: true,
                ajax: {
                    url: "/pembagian-pakan/datatable",
                    type: "POST",
                    beforeSend: function(xhr, type) {
                        if (!type.crossDomain) {
                            xhr.setRequestHeader('X-CSRF-Token', $('meta[name="csrf-token"]').attr(
                                'content'));
                        }
                    },
                },
                columns: [{
                        data: "updated_at",
                        render: function(data, type, row, meta) {
                            return row.DT_RowIndex
                        },
                    },
                    {
                        data: 'tgl_pembagian_pakan',
                        name: 'tgl_pembagian_pakan'
                    },
                    {
                        data: 'detail_pembagian_pakan',
                        name: 'detail_pembagian_pakan',
                        render: function(data, type, row, meta) {
                            var produkList =
                                '<select name="produk" class="form-control" ><option value="" class="fst-italic" selected data-default>Daftar Pakan ▼</option>';
                            $.each(data, function(index, value) {
                                produkList += '<option disabled value="' + value.detail_beli
                                    .produk
                                    .nama + '">' + value.detail_beli.produk.nama + ' | ' +
                                    value.quantity +
                                    '</option>';
                            });
                            produkList += '</select>';
                            return produkList;
                        }
                    },
                    {
                        data: 'detail_pembagian_pakan',
                        name: 'detail_pembagian_pakan',
                        render: function(data, type, row, meta) {
                            var tongList =
                                '<select name="tong" class="form-control"><option value="" class="fst-italic" selected data-default>Daftar Tong ▼</option>';
                            $.each(data, function(index, value) {
                                tongList += '<option disabled value="' + value.tong
                                    .nama + '">' + value.tong.nama +
                                    '</option>';
                            });
                            tongList += '</select>';
                            return tongList;
                        }
                    }, {
                        data: "id",
                        render: function(id) {
                            let show =
                                `<a title="Info Pembelian" href="/pembagian-bibit/${id}/show" class="btn btn-info me-2"><i class="fa fa-info"></i></a>`;
                            let edit =
                                `<a title="Edit Data" href="/pembagian-pakan/${id}/edit" class="btn btn-warning me-2"><i class="fa fa-pencil"></i></a>`;
                            let deletebtn =
                                `<a title="Hapus Data" href="/pembagian-pakan/delete/${id}" class="btn btn-danger"><i class="fa fa-trash"></i></a>`
                            return show + edit + deletebtn
                        },
                    },
                    {
                        data: "updated_at",
                        name: "updated_at",
                        visible: false,
                    }
                ],


            });

            // $.fn.dataTable.ext.errMode = 'none';
        });
    </script>
@endpush
