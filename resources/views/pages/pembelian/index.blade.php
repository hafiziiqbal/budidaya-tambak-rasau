@extends('layouts.admin')
@section('content')
<h1 class="mt-4">Pembelian</h1>
<ol class="breadcrumb mb-4">
    <li class="breadcrumb-item active">Pembelian</li>
</ol>

<a href="{{ route('pembelian.create') }}" class="btn btn-primary mb-4"><i class="fa fa-plus"></i>&emsp; Tambah Data</a>
@include('components.alert')

<div class="tab-content" id="myTabContent">
    <div class="tab-pane fade show active py-3" id="detail" role="tabpanel" aria-labelledby="detail-tab">
        <table id="tblPembelian" class="table table-striped table-bordered nowrap" style="width:100%">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Tanggal</th>
                    <th>Supplier</th>
                    <th>Total Bruto</th>
                    <th>Potongan Harga</th>
                    <th>Total Netto</th>
                    <th>Aksi</th>
                </tr>
            </thead>
        </table>
    </div>
    <div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">...</div>
</div>
@endsection

@push('script')
<script>
    alert = getCookie('success');
        if (alert != '') {
            $('#alertNotif').removeClass('d-none');
            $('#alertNotif span').html(alert);
            document.cookie = `success=;path=/pembelian`;
        }
        $(document).ready(function() {
            let table = $('#tblPembelian').DataTable({
                responsive: true,
                ajax: {
                    url: "/pembelian/datatable",
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
                        data: "tgl_beli",
                        name: "tgl_beli",
                    },
                    {
                        data: "supplier.nama",
                        name: "supplier.nama",
                    },
                    {
                        data: "total_bruto",
                        render: function(data) {
                            return data.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".")
                        }
                    },
                    {
                        data: "potongan_harga",
                        render: function(data) {
                            return data.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".")
                        }
                    },
                    {
                        data: "total_netto",
                        render: function(data) {
                            return data.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".")
                        }
                    },
                    {
                        data: "id",
                        render: function(id) {
                            let show =
                                `<a title="Info Pembelian" href="/pembelian/${id}/show" class="btn btn-info me-2"><i class="fa fa-info"></i></a>`;
                            let edit =
                                `<a title="Edit Data" href="/pembelian/${id}/edit" class="btn btn-warning me-2"><i class="fa fa-pencil"></i></a>`;
                            let deletebtn =
                                `<a onclick="return confirm('Data ini akan dihapus')" title="Hapus Data" href="/pembelian/delete/${id}" class="btn btn-danger"><i class="fa fa-trash"></i></a>`
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