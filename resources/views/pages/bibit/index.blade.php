@extends('layouts.admin')
@section('content')
    <h1 class="mt-4">Bibit</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item active">Bibit</li>
    </ol>



    <div id="alert">
        @include('components.alert')
    </div>
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
                            `<button title="Hapus Data" class="btn btn-danger btn-delete" data-id="${data}"><i class="fa fa-trash"></i></button>`
                        return share + edit + deletebtn
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
            const day = today.getDate() < 10 ? ('0' + today.getDate()) : today.getDate();
            const month = (today.getMonth() + 1) < 10 ? ('0' + (today.getMonth() + 1)) : today
                .getMonth(); // ingat bahwa index bulan dimulai dari 0
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

        table.on('click', '.btn-delete', function() {
            if (confirm('Data ini akan dihapus') == true) {
                let id = $(this).data('id');
                $.ajax({
                    url: `/pembelian/detail/delete/${id}`,
                    dataType: 'json', // what to expect back from the server
                    cache: false,
                    contentType: false,
                    processData: false,
                    type: 'GET',

                    success: function(response) {
                        if (response.success != undefined) {
                            $('#alertNotif').removeClass('d-none');
                            $('#alertNotif span').html(response.success);
                            table.ajax.reload();
                        }

                    },
                    error: function(
                        response
                    ) { // handle the error                                                            
                        let errors = response.responseJSON.errors
                        $("small[id^='error']").html('');
                        if (errors.general) {
                            $(`#alert #alertNotifError`).removeClass('d-none');
                            $(`#alert #alertNotifError span`).html(errors.general);
                            $(`#alert`).append(`@include('components.alert')`);
                        }


                    },

                })
            }
        });
    </script>
@endpush
