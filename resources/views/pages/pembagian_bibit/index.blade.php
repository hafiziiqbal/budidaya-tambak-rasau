@extends('layouts.admin')
@section('content')
    <h1 class="mt-4">Pembagian Bibit</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item active">Pembagian Bibit</li>
    </ol>

    <a href="{{ route('pembagian.bibit.create') }}" class="btn btn-primary mb-4"><i class="fa fa-plus"></i>&emsp; Tambah
        Data</a>
    @include('components.alert')

    <nav>
        <div class="nav nav-tabs" id="nav-tab" role="tablist">
            <button class="nav-link active" id="header-tab" data-bs-toggle="tab" data-bs-target="#header" type="button"
                role="tab" aria-controls="header" aria-selected="true">Header</button>
            <button class="nav-link" id="detail-tab" data-bs-toggle="tab" data-bs-target="#detail" type="button"
                role="tab" aria-controls="detail" aria-selected="false">Detail</button>
        </div>
    </nav>
    <div class="tab-content" id="nav-tabContent">
        <div class="tab-pane fade show active" id="header" role="tabpanel" aria-labelledby="header-tab" tabindex="0">
            <br>
            <table id="tblPembagianBibit" class="table table-striped table-bordered nowrap" style="width:100%">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Tanggal</th>
                        <th>Produk</th>
                        <th>Tanggal Beli</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
            </table>
        </div>
        <div class="tab-pane fade" id="detail" role="tabpanel" aria-labelledby="detail-tab" tabindex="0">
            <br>
            <button class="btn btn-primary mb-3" disabled id="shareMultiple"><i class="fa fa-paper-plane me-3"></i>Panen
                Bibit Yang Dipilih</button>
            <br>
            <table id="tblDetailPembagian" class="table table-striped table-bordered nowrap " style="width:100%">
                <thead>
                    <tr>
                        <th><input class="form-check-input" type="checkbox" id="checkAll"></th>
                        <th>No</th>
                        <th>Tanggal</th>
                        <th>Produk</th>
                        <th>Jaring</th>
                        <th>Kolam</th>
                        <th>Quantity</th>
                        <th>Sisa Quantity</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
@endsection

@push('script')
    <script>
        alert = getCookie('success');
        if (alert != '') {
            $('#alertNotif').removeClass('d-none');
            $('#alertNotif span').html(alert);
            document.cookie = `success=;path=/pembagian-bibit`;
        }

        $('#header-tab').click(function() {
            document.cookie = `tabPembagianPakan=header;path=/pembagian-bibit`;
        })
        $('#detail-tab').click(function() {
            document.cookie = `tabPembagianPakan=detail;path=/pembagian-bibit`;
        })

        tab = getCookie('tabPembagianPakan');
        if (tab != '') {
            if (tab == 'detail') {
                $('#detail').addClass('show active');
                $('#header').removeClass('show active')
                $('#header-tab').removeClass('active')
                $('#detail-tab').addClass('active')
            } else {
                $('#header-tab').addClass('active')
                $('#detail').removeClass('show active');
                $('#header').addClass('show active')
                $('#detail-tab').removeClass('active')
            }
        }

        let table = $('#tblPembagianBibit').DataTable({
            responsive: true,
            ajax: {
                url: "/pembagian-bibit/datatable",
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
                    data: "tgl_pembagian",
                    name: "tgl_pembagian",
                },
                {
                    data: "detail_beli.produk.nama",
                    name: "detail_beli.produk.nama",
                },
                {
                    data: "detail_beli.header_beli.tgl_beli",
                    name: "detail_beli.header_beli.tgl_beli",
                },
                {
                    data: "id",
                    render: function(id) {
                        let show =
                            `<a title="Info Pembelian" href="/pembagian-bibit/${id}/show" class="btn btn-info me-2"><i class="fa fa-info"></i></a>`;
                        let edit =
                            `<a title="Edit Data" href="/pembagian-bibit/${id}/edit" class="btn btn-warning me-2"><i class="fa fa-pencil"></i></a>`;
                        let deletebtn =
                            `<a title="Hapus Data" href="/pembagian-bibit/delete/${id}" class="btn btn-danger"><i class="fa fa-trash"></i></a>`
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

        let tableDetail = $('#tblDetailPembagian').DataTable({
            responsive: true,
            ajax: {
                url: "/pembagian-bibit/datatable/detail",
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
                        if (row['quantity'] > 0 && row['detail_pemberian_pakan'].length > 0) {
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
                    data: "header_pembagian_bibit.tgl_pembagian",
                    name: "header_pembagian_bibit.tgl_pembagian",
                },
                {
                    data: "header_pembagian_bibit.detail_beli.produk.nama",
                    name: "header_pembagian_bibit.detail_beli.produk.nama",
                },
                {
                    data: "jaring",
                    render: function(data, type, row, meta) {
                        if (data == null) {
                            let jaringOld = row['jaring_old'];
                            if (jaringOld == null) {
                                return ''
                            } else {
                                return jaringOld.nama
                            }
                        } else {
                            return data.nama
                        }

                    }
                },
                {
                    data: "kolam.nama",
                    name: "kolam.nama",
                },
                {
                    data: "quantity_awal",
                    name: "quantity_awal",
                },
                {
                    data: "quantity",
                    name: "quantity",
                },
                {
                    data: "id",
                    render: function(id, type, row, meta) {

                        let share = '';
                        if (row['quantity'] > 0 && row['detail_pemberian_pakan'].length > 0) {
                            share =
                                `<button title="Panen Bibit" data-id="${id}" class="btn btn-primary me-2 btn-share"><i class="fa fa-paper-plane"></i></button>`;
                        }
                        let show =
                            `<a title="Info Pembelian" href="/pembagian-bibit/${id}/show" class="btn btn-info me-2"><i class="fa fa-info"></i></a>`;
                        let edit =
                            `<a title="Edit Data" href="/pembagian-bibit/${id}/edit" class="btn btn-warning me-2"><i class="fa fa-pencil"></i></a>`;
                        let deletebtn =
                            `<a title="Hapus Data" href="/pembagian-bibit/delete/${id}" class="btn btn-danger"><i class="fa fa-trash"></i></a>`
                        return share
                    },
                },
                {
                    data: "updated_at",
                    name: "updated_at",
                    visible: false,
                },
                {
                    data: "jaring_old",
                    name: "jaring_old",
                    visible: false,
                },
                {
                    data: "detail_pemberian_pakan",
                    name: "detail_pemberian_pakan",
                    visible: false,
                },
            ]
        });



        tableDetail.on('click', '.btn-share', function() {
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

            document.cookie = `sharePanenTanggal=${formattedDate};path=/panen/create`;
            document.cookie = `sharePanenIkan=${id};path=/panen/create`;
            document.cookie = `sharePanenUrl=pembagian-bibit;path=/panen/create`;
            document.cookie = `sharePanenMultiple=false;path=/panen/create`;

            window.location.href = "{{ route('panen.create') }}";
        });

        let id = []; // array untuk menyimpan nilai data-id yang terseleksi

        // ketika checkbox di klik
        tableDetail.on('click', '.checkbox', function() {
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
        tableDetail.on('click', '#checkAll', function() {
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
            const day = today.getDate() < 10 ? ('0' + today.getDate()) : today.getDate();
            const month = (today.getMonth() + 1) < 10 ? ('0' + (today.getMonth() + 1)) : (today.getMonth() +
                1); // ingat bahwa index bulan dimulai dari 0
            const year = today.getFullYear();

            // memformat tanggal dengan format d-m-Y
            const formattedDate = `${day}-${month}-${year}`;
            document.cookie = `sharePanenTanggal=${formattedDate};path=/panen/create`;
            document.cookie = `sharePanenIkan=${id};path=/panen/create`;
            document.cookie = `sharePanenUrl=pembagian-bibit;path=/panen/create`;
            document.cookie = `sharePanenMultiple=true;path=/panen/create`;

            window.location.href = "{{ route('panen.create') }}";
        })

        // Ketika terjadi perubahan pada setiap element input type check
        tableDetail.on('change', 'input.checkbox', function() {
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
        tableDetail.on('click', '#checkAll', function() {
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
