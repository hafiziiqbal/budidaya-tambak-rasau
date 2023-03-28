@extends('layouts.admin')
@section('content')
    <h1 class="mt-4">Bibit</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item active">Bibit</li>
    </ol>

    {{-- <a href="{{ route('produk.create') }}" class="btn btn-primary mb-4"><i class="fa fa-plus"></i>&emsp; Tambah Bibit</a> --}}

    @include('components.alert')
    <table id="tblBibit" class="table table-striped  nowrap" style="width:100%">
        <thead>
            <tr>
                <th>No</th>
                <th>Nama</th>
                <th>Quantity</th>
                <th>Sisa Quantity</th>
                <th>Subtotal</th>
                <th>Aksi</th>
            </tr>
        </thead>
    </table>
@endsection

@push('script')
    <script>
        let table = $('#tblBibit').DataTable({
            responsive: true,
            ajax: {
                url: `/bibit/datatable`,
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
                    data: "produk.nama",
                    name: "produk.nama",
                },
                {
                    data: "quantity",
                    name: "quantity",
                },
                {
                    data: "quantity_stok",
                    name: "quantity_stok",
                },
                {
                    data: "subtotal",
                    name: "subtotal",
                },
                {
                    data: "id",
                    render: function(data, type, row, meta) {
                        let share = '';
                        if (row['quantity_stok'] > 0) {
                            share =
                                `<button title="Bagikan Bibit" data-id="${data}" class="btn btn-primary me-2 btn-share"><i class="fa fa-paper-plane"></i></button>`;
                        }

                        let show =
                            `<a title="Info Bibit" href="/bibit/${data}/show" class="btn btn-info me-2"><i class="fa fa-info"></i></a>`;
                        let edit =
                            `<a title="Edit Data" href="/bibit/${data}/edit" class="btn btn-warning me-2"><i class="fa fa-pencil"></i></a>`;
                        let deletebtn =
                            `<a onclick="return confirm('Data ini akan dihapus')" title="Hapus Data" href="/bibit/delete/${data}" class="btn btn-danger"><i class="fa fa-trash"></i></a>`
                        return share
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

        table.on('click', '.btn-share', function() {
            // membuat objek Date dengan tanggal saat ini
            const today = new Date();

            // mengambil tanggal, bulan, dan tahun dari objek Date
            const day = today.getDate();
            const month = today.getMonth() + 1; // ingat bahwa index bulan dimulai dari 0
            const year = today.getFullYear();

            // memformat tanggal dengan format d-m-Y
            const formattedDate = `${day}-${month}-${year}`;

            let id = $(this).data('id');

            document.cookie = `shareBibitTanggal=${formattedDate};path=/pembagian-bibit/create`;
            document.cookie = `shareBibitDetailBeli=${id};path=/pembagian-bibit/create`;
            document.cookie = `shareBibitDetailBeli=${id};path=/pembagian-bibit/create`;
            document.cookie = `shareBibitJenis='bibit';path=/pembagian-bibit/create`;
            document.cookie = `shareBibitUrl=bibit;path=/pembagian-bibit/create`;

            window.location.href = "{{ route('pembagian.bibit.create') }}";
        });
    </script>
@endpush
