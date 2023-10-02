@extends('layouts.admin')
@section('content')
    <h1 class="mt-4">Pakan</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item active">Pakan</li>
    </ol>

    <div id="alert">
        @include('components.alert')
    </div>
    <button class="btn btn-primary mb-3" disabled id="shareMultiple"><i class="fa fa-paper-plane me-3"></i>Bagikan Pakan Yang
        Dipilih</button>
    <table id="tblPakan" class="table table-striped  nowrap" style="width:100%">
        <thead>
            <tr>
                <th><input class="form-check-input" type="checkbox" id="checkAll"></th>
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
        let table = $('#tblPakan').DataTable({
            responsive: true,
            ajax: {
                url: `/pakan/datatable`,
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
                        if (row['quantity_stok'] > 0) {
                            return `<input class="form-check-input checkbox" data-id="${data}" type="checkbox" >`
                        } else {
                            return ''
                        }

                    },
                    orderable: false
                },
                {
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
                    render: function(data, type, row, meta) {
                        return pembatasKoma(parseInt(data).toString())
                    }
                },
                {
                    data: "quantity_stok",
                    render: function(data, type, row, meta) {
                        return pembatasKoma(parseInt(data).toString())
                    }
                },
                {
                    data: "subtotal",
                    render: function(data, type, row, meta) {
                        return pembatasKoma(parseInt(data).toString())
                    }
                },
                {
                    data: "id",
                    render: function(data, type, row, meta) {
                        let share = '';
                        if (row['quantity_stok'] > 0) {
                            share =
                                `<button title="Bagikan Pakan" data-id="${data}" class="btn btn-primary me-2 btn-share" ><i class="fa fa-paper-plane"></i></button>`;
                        }
                        let edit =
                            `<a title="Edit Data" href="/pakan/${data}/edit" class="btn btn-warning me-2"><i class="fa fa-pencil"></i></a>`;
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

            document.cookie = `sharePakanTanggal=${formattedDate};path=/pembagian-pakan/create`;
            document.cookie = `sharePakanDetailBeli=${id};path=/pembagian-pakan/create`;
            document.cookie = `sharePakanUrl=pakan;path=/pembagian-pakan/create`;
            document.cookie = `sharePakanMultiple=false;path=/pembagian-pakan/create`;

            window.location.href = "{{ route('pembagian.pakan.create') }}";
        });

        let id = []; // array untuk menyimpan nilai data-id yang terseleksi

        // ketika checkbox di klik
        table.on('click', '.checkbox', function() {
            var dataId = $(this).data('id'); // ambil nilai data-id dari checkbox yang diklik

            // jika checkbox terseleksi, tambahkan nilai data-id ke dalam array id
            if ($(this).is(':checked')) {
                id.push(dataId);

            }
            // jika checkbox tidak terseleksi, hapus nilai data-id dari array id
            else {
                id.splice($.inArray(dataId, id), 1);
            }
        });

        // ketika checkbox di header tabel diklik
        table.on('click', '#checkAll', function() {
            // jika checkbox di header tabel terseleksi, centang semua checkbox di baris tabel dan tambahkan nilai data-id ke dalam array id
            if ($(this).is(':checked')) {
                $('.checkbox').prop('checked', true);
                $('.checkbox').each(function() {
                    var dataId = $(this).data('id');

                    if ($.inArray(dataId, id) === -1) {
                        id.push(dataId);
                    }


                });
            }
            // jika checkbox di header tabel tidak terseleksi, hapus semua nilai data-id dari array id dan hilangkan centang dari semua checkbox di baris tabel
            else {
                $('.checkbox').prop('checked', false);
                $('.checkbox').each(function() {
                    var dataId = $(this).data('id');
                    id.splice($.inArray(dataId, id), 1);
                });
            }
        });

        $('#shareMultiple').click(function() {
            // membuat objek Date dengan tanggal saat ini
            const today = new Date();

            // mengambil tanggal, bulan, dan tahun dari objek Date
            const day = today.getDate();
            const month = today.getMonth() + 1; // ingat bahwa index bulan dimulai dari 0
            const year = today.getFullYear();

            // memformat tanggal dengan format d-m-Y
            const formattedDate = `${day}-${month}-${year}`;

            document.cookie = `sharePakanTanggal=${formattedDate};path=/pembagian-pakan/create`;
            document.cookie = `sharePakanDetailBeli=${id};path=/pembagian-pakan/create`;
            document.cookie = `sharePakanUrl=pakan;path=/pembagian-pakan/create`;
            document.cookie = `sharePakanMultiple=true;path=/pembagian-pakan/create`;

            window.location.href = "{{ route('pembagian.pakan.create') }}";
        })

        // Ketika terjadi perubahan pada setiap element input type check
        table.on('change', 'input.checkbox', function() {
            // Mengambil jumlah element input type check yang tercentang
            let checkedCount = $('input.checkbox').filter(':checked').length;
            // Jika terdapat minimal 2 element input type check tercentang, maka hilangkan atribut disabled pada button checkAll
            if (checkedCount >= 2) {
                $('#shareMultiple').removeAttr('disabled');
                $('.btn-share').attr('disabled', 'disabled')
            } else { // Jika hanya ada 1 element input type check tercentang atau tidak ada sama sekali, maka tambahkan atribut disabled pada button shareMultiple
                $('#shareMultiple').attr('disabled', true);
                $('.btn-share').removeAttr('disabled')
            }
        });

        // Ketika terjadi perubahan pada setiap element input type check
        table.on('click', '#checkAll', function() {
            // Mengambil jumlah element input type check yang tercentang
            let checkedCount = $('input.checkbox').filter(':checked').length;
            // Jika terdapat minimal 2 element input type check tercentang, maka hilangkan atribut disabled pada button checkAll
            if (checkedCount >= 2) {
                $('#shareMultiple').removeAttr('disabled');
            } else { // Jika hanya ada 1 element input type check tercentang atau tidak ada sama sekali, maka tambahkan atribut disabled pada button shareMultiple
                $('#shareMultiple').attr('disabled', true);
            }
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

        function pembatasKoma(angka) {
            return angka.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");

        }
    </script>
@endpush
