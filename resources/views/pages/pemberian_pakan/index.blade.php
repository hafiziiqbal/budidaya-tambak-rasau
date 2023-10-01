@extends('layouts.admin')
@section('content')
    <h1 class="mt-4">Pemberian Pakan</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item active">Pemberian Pakan</li>
    </ol>

    <a href="{{ route('pemberian.pakan.create') }}" class="btn btn-primary mb-4"><i class="fa fa-plus"></i>&emsp; Tambah
        Data</a>
    @include('components.alert')

    <table id="tblPemberianPakan" class="table table-striped table-bordered nowrap" style="width:100%">
        <thead>
            <tr>
                <th>No</th>
                <th>Tong</th>
                <th>Bibit</th>
                <th>Pakan</th>
                <th>Quantity</th>
                <th>Aksi</th>
            </tr>
        </thead>
    </table>
@endsection

@push('script')
    <script>
        $(document).ready(function() {
            let table = $('#tblPemberianPakan').DataTable({
                responsive: true,
                ajax: {
                    url: "/pemberian-pakan/datatable",
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
                        data: 'detail_pembagian_pakan',
                        render: function(data) {
                            if (data.tong == null) {
                                return data.tong_old.nama
                            } else {
                                return data.tong.nama
                            }
                        }
                    },
                    {
                        data: 'detail_pembagian_bibit.header_pembagian_bibit.detail_beli.produk.nama',
                        name: 'detail_pembagian_bibit.header_pembagian_bibit.detail_beli.produk.nama'
                    },
                    {
                        data: 'detail_pembagian_pakan.detail_beli.produk.nama',
                        name: 'detail_pembagian_pakan.detail_beli.produk.nama'
                    },
                    {
                        data: 'quantity',
                        render: function(data, type, row, meta) {
                            return pembatasKoma(data.toString())
                        }
                    },
                    {
                        data: "id",
                        render: function(id) {
                            let show =
                                `<a title="Info Pembelian" href="/pembagian-bibit/${id}/show" class="btn btn-info me-2"><i class="fa fa-info"></i></a>`;
                            let edit =
                                `<a title="Edit Data" href="/pemberian-pakan/${id}/edit" class="btn btn-warning me-2"><i class="fa fa-pencil"></i></a>`;
                            let deletebtn =
                                `<a title="Hapus Data" href="/pemberian-pakan/delete/${id}" class="btn btn-danger"><i class="fa fa-trash"></i></a>`
                            return edit + deletebtn
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

        function pembatasKoma(angka) {
            return angka.replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        }
    </script>
@endpush
