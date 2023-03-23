@extends('layouts.admin')
@section('content')
    <h1 class="mt-4">Jaring</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item active">Jaring</li>
    </ol>

    <a href="{{ route('jaring.create') }}" class="btn btn-primary mb-4"><i class="fa fa-plus"></i>&emsp; Tambah Data</a>

    @include('components.alert')
    <table id="tblJaring" class="table table-striped table-bordered nowrap" style="width:100%">
        <thead>
            <tr>
                <th>No</th>
                <th>Nama</th>
                <th>Kolam</th>
                <th>Posisi</th>
                <th>Aksi</th>
            </tr>
        </thead>
    </table>
@endsection

@push('script')
    <script>
        $(document).ready(function() {
            let table = $('#tblJaring').DataTable({
                responsive: true,
                ajax: {
                    url: "/jaring/datatable",
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
                        data: "nama",
                        name: "nama",
                    },
                    {
                        data: "kolam.nama",
                        name: "kolam.nama",
                    },
                    {
                        data: "posisi",
                        name: "posisi",
                    },
                    {
                        data: "id",
                        render: function(id) {

                            let edit =
                                `<a title="Edit Data" href="/jaring/${id}/edit" class="btn btn-warning me-2"><i class="fa fa-pencil"></i></a>`;
                            let deletebtn =
                                `<a onclick="return confirm('Data ini akan dihapus')" title="Hapus Data" href="/jaring/delete/${id}" class="btn btn-danger"><i class="fa fa-trash"></i></a>`
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
