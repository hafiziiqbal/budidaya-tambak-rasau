@extends('layouts.admin')
@section('content')
    <h1 class="mt-4">Kolam</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item active">Kolam</li>
    </ol>

    <a href="{{ route('kolam.create') }}" class="btn btn-primary mb-4"><i class="fa fa-plus"></i>&emsp; Tambah Data</a>

    @include('components.alert')
    <table id="tblKolam" class="table table-striped table-bordered nowrap" style="width:100%">
        <thead>
            <tr>
                <th>No</th>
                <th>Nama</th>
                <th>Posisi</th>
                <th>Total Ikan (Ekor)</th>
                <th>Aksi</th>
            </tr>
        </thead>
    </table>
@endsection

@push('script')
    <script>
        $(document).ready(function() {
            let table = $('#tblKolam').DataTable({
                responsive: true,
                ajax: {
                    url: "/kolam/datatable",
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
                        data: "posisi",
                        name: "posisi",
                    },
                    {
                        data: "detail_pembagian_bibit",
                        render: function(data, type, row, meta) {
                            if (data.length == 0) {
                                return 0
                            } else {
                                let quantity = data[0].total_quantity
                                if (data[0].total_quantity % 1 === 0) {
                                    quantity = parseInt(data[0].total_quantity);
                                }
                                return quantity
                            }
                        },
                    },
                    {
                        data: "id",
                        render: function(id) {

                            let edit =
                                `<a title="Edit Data" href="/kolam/${id}/edit" class="btn btn-warning me-2"><i class="fa fa-pencil"></i></a>`;
                            let ikan =
                                `<a title="Ikan Dalam Kolam" href="/kolam/${id}/daftar-ikan" class="btn btn-primary me-2"><i class="fa fa-fish"></i></a>`;
                            let deletebtn =
                                `<a onclick="return confirm('Data ini akan dihapus')" title="Hapus Data" href="/kolam/delete/${id}" class="btn btn-danger"><i class="fa fa-trash"></i></a>`
                            return ikan + edit + deletebtn
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
