@extends('layouts.admin')
@section('content')
    <h1 class="mt-4">Pembagian Bibit</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item active">Pembagian Bibit</li>
    </ol>

    <a href="{{ route('pembagian.bibit.create') }}" class="btn btn-primary mb-4"><i class="fa fa-plus"></i>&emsp; Tambah
        Data</a>
    @include('components.alert')




    <table id="tblPembagianBibit" class="table table-striped table-bordered nowrap" style="width:100%">
        <thead>
            <tr>
                <th>No</th>
                <th>Tanggal</th>
                <th>Produk</th>
                <th>Tanggal Beli</th>
                <th>Aksi</th>
            </tr>
        </thead>
    </table>
@endsection

@push('script')
    <script>
        alert = getCookie('success');
        if (alert != '') {
            $('#alertNotif').removeClass('d-none');
            $('#alertNotif span').html(alert);
            document.cookie = `success=;path=/pembagian-bibit`;
        }
        $(document).ready(function() {
            let table = $('#tblPembagianBibit').DataTable({
                responsive: true,
                ajax: {
                    url: "/pembagian-bibit/datatable",
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
                        data: "tgl_pembagian",
                        name: "tgl_pembagian",
                    },
                    {
                        data: "detail_beli.produk.nama",
                        name: "detail_beli.produk.nama",
                    },
                    {
                        data: "detail_beli.header_beli.tgl_beli",
                        name: "detail_beli.header_beli.tgl_beli",
                    },
                    {
                        data: "id",
                        render: function(id) {
                            let show =
                                `<a title="Info Pembelian" href="/pembagian-bibit/${id}/show" class="btn btn-info me-2"><i class="fa fa-info"></i></a>`;
                            let edit =
                                `<a title="Edit Data" href="/pembagian-bibit/${id}/edit" class="btn btn-warning me-2"><i class="fa fa-pencil"></i></a>`;
                            let deletebtn =
                                `<a title="Hapus Data" href="/pembagian-bibit/delete/${id}" class="btn btn-danger"><i class="fa fa-trash"></i></a>`
                            return show + edit + deletebtn
                        },
                    },
                    {
                        data: "updated_at",
                        name: "updated_at",
                        visible: false,
                    },
                ]
            });

            // $.fn.dataTable.ext.errMode = 'none';
        });
    </script>
@endpush
