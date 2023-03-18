@extends('layouts.admin')
@section('content')
    <h1 class="mt-4">Panen</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item active">Panen</li>
    </ol>

    <a href="{{ route('panen.create') }}" class="btn btn-primary mb-4"><i class="fa fa-plus"></i>&emsp; Tambah
        Data</a>
    @include('components.alert')

    <table id="tblPembagianPakan" class="table table-striped table-bordered nowrap" style="width:100%">
        <thead>
            <tr>
                <th>No</th>
                <th>Tanggal</th>
                <th>Panen</th>
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
                    url: "/panen/datatable",
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
                        data: 'tgl_panen',
                        name: 'tgl_panen'
                    },
                    {
                        data: 'detail_panen',
                        name: 'detail_panen',
                        render: function(data, type, row, meta) {
                            console.log(data);
                            var panenList =
                                '<select name="bibit" class="form-control" ><option value="" class="fst-italic" selected data-default>Daftar Panen ▼</option>';
                            $.each(data, function(index, value) {
                                panenList += '<option disabled>' + value
                                    .detail_pembagian_bibit.header_pembagian_bibit
                                    .detail_beli.produk
                                    .nama + ' : ' + value.quantity + ' (' + value
                                    .nama_kolam + (value.nama_jaring == null ? '' :
                                        ` & ${value.nama_jaring}`) +
                                    ')</option>';
                            });
                            panenList += '</select>';
                            return panenList;
                        }
                    },
                    {
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
