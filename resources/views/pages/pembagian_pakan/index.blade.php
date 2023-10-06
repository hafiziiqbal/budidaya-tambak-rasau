@extends('layouts.admin')
@section('content')
    <h1 class="mt-4">Pembagian Pakan</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item active">Pembagian Pakan</li>
    </ol>

    <a href="{{ route('pembagian.pakan.create') }}" class="btn btn-primary mb-4"><i class="fa fa-plus"></i>&emsp; Tambah
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
            <table id="tblPembagianPakan" class="table table-striped table-bordered nowrap " style="width:100%">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Produk</th>
                        <th>Produk</th>
                        <th>Tong</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
            </table>
        </div>
        <div class="tab-pane fade" id="detail" role="tabpanel" aria-labelledby="detail-tab" tabindex="0">
            <br>
            <button class="btn btn-primary mb-3" disabled id="shareMultiple"><i class="fa fa-paper-plane me-3"></i>Bagikan
                Pakan Yang
                Dipilih</button>
            <table id="tblDetailPembagian" class="table table-striped table-bordered nowrap " style="width:100%">
                <thead>
                    <tr>
                        <th><input class="form-check-input" type="checkbox" id="checkAll"></th>
                        <th>No</th>
                        <th>Tanggal</th>
                        <th>Produk</th>
                        <th>Tong</th>
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
        $('#header-tab').click(function() {
            document.cookie = `tabPembagianPakan=header;path=/pembagian-pakan`;
        })
        $('#detail-tab').click(function() {
            document.cookie = `tabPembagianPakan=detail;path=/pembagian-pakan`;
        })

        alert = getCookie('success');
        if (alert != '') {
            $('#alertNotif').removeClass('d-none');
            $('#alertNotif span').html(alert);
            document.cookie = `success=;path=/pembagian-pakan`;
        }

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

        $(document).ready(function() {
            let table = $('#tblPembagianPakan').DataTable({
                responsive: true,
                ajax: {
                    url: "/pembagian-pakan/datatable",
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
                        data: 'tgl_pembagian_pakan',
                        name: 'tgl_pembagian_pakan'
                    },
                    {
                        data: 'detail_pembagian_pakan',
                        name: 'detail_pembagian_pakan',
                        render: function(data, type, row, meta) {
                            var produkList =
                                '<select name="produk" class="form-control" ><option value="" class="fst-italic" selected data-default>Daftar Pakan ▼</option>';
                            $.each(data, function(index, value) {

                                produkList += '<option disabled value="' + value.detail_beli
                                    .produk
                                    .nama + '">' + value.detail_beli.produk.nama + ' | ' +
                                    value.quantity +
                                    '</option>';
                            });
                            produkList += '</select>';
                            return produkList;
                        }
                    },
                    {
                        data: 'detail_pembagian_pakan',
                        name: 'detail_pembagian_pakan',
                        render: function(data, type, row, meta) {
                            var tongList =
                                '<select name="tong" class="form-control"><option value="" class="fst-italic" selected data-default>Daftar Tong ▼</option>';
                            $.each(data, function(index, value) {
                                tongList += '<option disabled value="' + (value.tong ==
                                        null ? value.tong_old.nama : value.tong.nama) +
                                    '">' + (value.tong == null ? value.tong_old.nama : value
                                        .tong.nama) +
                                    '</option>';
                            });
                            tongList += '</select>';
                            return tongList;
                        }
                    }, {
                        data: "id",
                        render: function(id) {
                            let show =
                                `<a title="Info Pembelian" href="/pembagian-pakan/${id}/show" class="btn btn-info me-2"><i class="fa fa-info"></i></a>`;
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

            let tableDetail = $('#tblDetailPembagian').DataTable({
                responsive: true,
                ajax: {
                    url: "/pembagian-pakan/datatable/detail",
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
                        data: 'header_pembagian_pakan.tgl_pembagian_pakan',
                        name: 'header_pembagian_pakan.tgl_pembagian_pakan'
                    },
                    {
                        data: 'detail_beli.produk.nama',
                        name: 'detail_beli.produk.nama',
                    },
                    {
                        data: 'tong',
                        render: function(data, type, row, meta) {
                            result = ''
                            if (data == null) {
                                result = row['tong_old'].nama
                            } else {
                                result = data.nama
                            }
                            return result

                        }
                    },
                    {
                        data: 'quantity_terpakai',
                        render: function(data, type, row, meta) {
                            return pembatasKoma(data.toString())
                        }
                    },
                    {
                        data: 'quantity',
                        render: function(data, type, row, meta) {
                            return pembatasKoma(data.toString())
                        }
                    },
                    {
                        data: "id",
                        render: function(data, type, row, meta) {
                            let share = '';
                            if (row['quantity'] > 0) {
                                share =
                                    `<button title="Berikan Pakan" data-id="${data}" class="btn btn-primary me-2 btn-share"><i class="fa fa-paper-plane"></i></button>`;
                            }
                            let show =
                                `<a title="Info Pembelian" href="/pembagian-pakan/${data}/show" class="btn btn-info me-2"><i class="fa fa-info"></i></a>`;
                            let edit =
                                `<a title="Edit Data" href="/pembagian-pakan/${data}/edit" class="btn btn-warning me-2"><i class="fa fa-pencil"></i></a>`;
                            let deletebtn =
                                `<a title="Hapus Data" href="/pembagian-pakan/delete/${data}" class="btn btn-danger"><i class="fa fa-trash"></i></a>`
                            return share
                        },
                    },
                    {
                        data: "updated_at",
                        name: "updated_at",
                        visible: false,
                    },

                    {
                        data: "tong_old",
                        visible: false,
                    }
                ],


            });

            tableDetail.on('click', '.btn-share', function() {
                let id = $(this).data('id');
                document.cookie = `sharePakanDetailBagi=${id};path=/pemberian-pakan/create`;
                document.cookie = `sharePakanUrl=pembagian-pakan;path=/pemberian-pakan/create`;
                document.cookie = `sharePakanMultiple=false;path=/pemberian-pakan/create`;

                window.location.href = "{{ route('pemberian.pakan.create') }}";
            });

            let id = [];
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

                document.cookie = `sharePakanDetailBagi=${id};path=/pemberian-pakan/create`;
                document.cookie = `sharePakanUrl=pembagian-pakan;path=/pemberian-pakan/create`;
                document.cookie = `sharePakanMultiple=true;path=/pemberian-pakan/create`;

                window.location.href = "{{ route('pemberian.pakan.create') }}";
            })


            // Ketika terjadi perubahan pada setiap element input type check
            tableDetail.on('change', 'input.checkbox', function() {
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
            tableDetail.on('click', '#checkAll', function() {
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

        });

        function pembatasKoma(angka) {
            return angka.replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        }
    </script>
@endpush
