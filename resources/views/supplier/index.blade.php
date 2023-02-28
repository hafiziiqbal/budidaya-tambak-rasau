@extends('layouts.admin')
@section('content')
    <h1 class="mt-4">Supplier</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item active">Supplier</li>
    </ol>

    <a href="{{ route('supplier.create') }}" class="btn btn-primary mb-4"><i class="fa fa-plus"></i>&emsp; Tambah Data</a>

    @include('components.alert')
    <table id="tblSupplier" class="table table-striped table-bordered nowrap" style="width:100%">
        <thead>
            <tr>
                <th>No</th>
                <th>Nama</th>
                <th>Alamat</th>
                <th>Telepon</th>
                <th>Aksi</th>
            </tr>
        </thead>
    </table>
@endsection

@push('script')
    <script>
        $(document).ready(function() {
            let table = $('#tblSupplier').DataTable({
                responsive: true,
                order: [
                    [5, 'desc']
                ],
                ajax: {
                    url: "/supplier/datatable",
                    type: "POST",
                    beforeSend: function(xhr, type) {
                        if (!type.crossDomain) {
                            xhr.setRequestHeader('X-CSRF-Token', $('meta[name="csrf-token"]').attr(
                                'content'));
                        }
                    },
                },
                columns: [{
                        data: "id",
                        render: function(data, type, row, meta) {
                            return meta.row + meta.settings._iDisplayStart + 1;
                        },
                    },
                    {
                        data: "nama",
                        name: "nama",
                    },
                    {
                        data: "alamat",
                        name: "alamat",
                    },
                    {
                        data: "telepon",
                        name: "telepon",
                    },
                    {
                        data: "id",
                        name: "id",
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
