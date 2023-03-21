@extends('layouts.admin')
@section('content')
    <h1 class="mt-4">Tong</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item active">Tong</li>
    </ol>

    <a href="{{ route('tong.create') }}" class="btn btn-primary mb-4"><i class="fa fa-plus"></i>&emsp; Tambah Data</a>

    @include('components.alert')
    <table id="tblTong" class="table table-striped table-bordered nowrap" style="width:100%">
        <thead>
            <tr>
                <th>No</th>
                <th>Nama</th>
                <th>Kolam</th>
                <th>Aksi</th>
            </tr>
        </thead>
    </table>
@endsection

@push('script')
    <script>
        $(document).ready(function() {
            let table = $('#tblTong').DataTable({
                responsive: true,
                ajax: {
                    url: "/tong/datatable",
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
                        data: "tong_nama",
                        name: "tong_nama",
                    },
                    {
                        data: "kolam",
                        render: function(data, type, row, meta) {
                            var produkList =
                                '<select name="kolam" class="form-control" ><option value="" class="fst-italic" selected data-default>Daftar Kolam ▼</option>';
                            $.each(data, function(index, value) {

                                produkList += '<option disabled>' + value +
                                    '</option>';
                            });
                            produkList += '</select>';
                            return produkList;
                        }
                    },

                    {
                        data: "id",
                        render: function(id) {

                            let edit =
                                `<a title="Edit Data" href="/tong/${id}/edit" class="btn btn-warning me-2"><i class="fa fa-pencil"></i></a>`;
                            let deletebtn =
                                `<a title="Hapus Data" href="/tong/delete/${id}" class="btn btn-danger"><i class="fa fa-trash"></i></a>`
                            return edit + deletebtn
                        },
                    },
                    {
                        data: "updated_at",
                        name: "updated_at",
                        visible: false,
                    },
                ]
            });

            $.fn.dataTable.ext.errMode = 'none';
        });
    </script>
@endpush
