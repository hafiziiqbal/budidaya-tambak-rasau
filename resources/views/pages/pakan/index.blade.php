@extends('layouts.admin')
@section('content')
    <h1 class="mt-4">Pakan</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item active">Pakan</li>
    </ol>

    {{-- <a href="{{ route('produk.create') }}" class="btn btn-primary mb-4"><i class="fa fa-plus"></i>&emsp; Tambah Bibit</a> --}}

    @include('components.alert')
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
                        if (row['quantity'] > 0) {
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
                                `<button title="Bagikan Pakan" data-id="${data}" class="btn btn-primary me-2 btn-share"><i class="fa fa-paper-plane"></i></button>`;
                        }

                        let show =
                            `<a title="Info Pakan" href="/pakan/${data}/show" class="btn btn-info me-2"><i class="fa fa-info"></i></a>`;
                        let edit =
                            `<a title="Edit Data" href="/pakan/${data}/edit" class="btn btn-warning me-2"><i class="fa fa-pencil"></i></a>`;
                        let deletebtn =
                            `<a onclick="return confirm('Data ini akan dihapus')" title="Hapus Data" href="/pakan/delete/${data}" class="btn btn-danger"><i class="fa fa-trash"></i></a>`
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
            } else { // Jika hanya ada 1 element input type check tercentang atau tidak ada sama sekali, maka tambahkan atribut disabled pada button shareMultiple
                $('#shareMultiple').attr('disabled', true);
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
    </script>
@endpush
