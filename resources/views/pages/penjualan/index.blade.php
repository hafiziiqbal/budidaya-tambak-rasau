@extends('layouts.admin')
@section('content')
<h1 class="mt-4">Penjualan</h1>
<ol class="breadcrumb mb-4">
    <li class="breadcrumb-item active">Penjualan</li>
</ol>

<a href="{{ route('jual.create') }}" class="btn btn-primary mb-4"><i class="fa fa-plus"></i>&emsp; Tambah Data</a>
@include('components.alert')
<div class="tab-content" id="myTabContent">
    <div class="tab-pane fade show active py-3" id="detail" role="tabpanel" aria-labelledby="detail-tab">
        <table id="tblPenjualan" class="table table-striped table-bordered nowrap" style="width:100%">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Invoice</th>
                    <th>Customer</th>
                    <th>Total Netto</th>
                    <th>Pay</th>
                    <th>Change</th>
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
            document.cookie = `success=;path=/penjualan`;
        }
        $(document).ready(function() {
            let table = $('#tblPenjualan').DataTable({
                responsive: true,
                ajax: {
                    url: "/penjualan/datatable",
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
                        data: "invoice",
                        name: "invoice",
                    },
                    {
                        data: "customer.nama",
                        name: "customer.nama",
                    },
                    {
                        data: "total_netto",
                        render: function(data) {
                            return data.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".")
                        }
                    },
                    {
                        data: "pay",
                        render: function(data) {
                            return data.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".")
                        }
                    },
                    {
                        data: "change",
                        render: function(data) {
                            return data.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".")
                        }
                    },
                    {
                        data: "id",
                        render: function(id) {
                            let show =
                                `<a title="Info Data" href="/penjualan/${id}/show" class="btn btn-info me-2"><i class="fa fa-info"></i></a>`;
                            let edit =
                                `<a title="Edit Data" href="/penjualan/${id}/edit" class="btn btn-warning me-2"><i class="fa fa-pencil"></i></a>`;
                            let deletebtn =
                                `<a title="Hapus Data" href="/penjualan/delete/${id}" class="btn btn-danger"><i class="fa fa-trash"></i></a>`
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