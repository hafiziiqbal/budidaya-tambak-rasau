@extends('layouts.admin')
@section('content')
    <h1 class="mt-4">Panen</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item active">Panen</li>
    </ol>

    <a href="{{ route('panen.create') }}" class="btn btn-primary mb-4"><i class="fa fa-plus"></i>&emsp; Tambah
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
        </div>
        <div class="tab-pane fade" id="detail" role="tabpanel" aria-labelledby="detail-tab" tabindex="0">
            <br>
            <button class="btn btn-primary mb-3" disabled id="shareMultiple"><i class="fa fa-paper-plane me-3"></i>Pilih
                Ikan Yang Akan Dijual</button>
            <br>
            <table id="tblDetailPanen" class="table table-striped table-bordered nowrap " style="width:100%">
                <thead>
                    <tr>
                        <th><input class="form-check-input" type="checkbox" id="checkAll"></th>
                        <th>No</th>
                        <th>Tanggal</th>
                        <th>Produk</th>
                        <th>Status</th>
                        <th>Jaring</th>
                        <th>Kolam</th>
                        <th>Quantity (Kg)</th>
                        <th>Quantity (ekor)</th>
                        <th>HPP</th>
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
            document.cookie = `success=;path=/panen`;
        }

        $('#header-tab').click(function() {
            document.cookie = `tabPembagianPakan=header;path=/panen`;
        })
        $('#detail-tab').click(function() {
            document.cookie = `tabPembagianPakan=detail;path=/panen`;
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
                            `<a title="Info Pembelian" href="/panen/${id}/show" class="btn btn-info me-2"><i class="fa fa-info"></i></a>`;
                        let edit =
                            `<a title="Edit Data" href="/panen/${id}/edit" class="btn btn-warning me-2"><i class="fa fa-pencil"></i></a>`;
                        let deletebtn =
                            `<a title="Hapus Data" data-id="${id}" class="btn btn-danger btn-delete"><i class="fa fa-trash"></i></a>`

                        return show + deletebtn

                    },
                },
                {
                    data: "updated_at",
                    name: "updated_at",
                    visible: false,
                }
            ],
        });
        table.on('click', '.btn-delete', function() {
            var dataId = $(this).data('id'); // ambil nilai data-id dari checkbox yang diklik
            // Menampilkan kotak konfirmasi
            const confirmation = confirm(
                "Apakah Anda ingin menghapus data yang ada di produk?");

            // Mengirim permintaan berdasarkan hasil konfirmasi
            if (confirmation) {
                window.location.href = `/panen/delete/${dataId}?isDecrementProduk=true`;
            } else {
                window.location.href = `/panen/delete/${dataId}?isDecrementProduk=false`;
            }

        });

        // $.fn.dataTable.ext.errMode = 'none';

        let tableDetail = $('#tblDetailPanen').DataTable({
            responsive: true,
            ajax: {
                url: "/panen/datatable/detail",
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
                        if (row['quantity'] > 0 && row['status'] == 1) {
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
                    data: "header_panen.tgl_panen",
                    name: "header_panen.tgl_panen",
                },
                {
                    data: "detail_pembagian_bibit.header_pembagian_bibit.detail_beli.produk.nama",
                    name: "detail_pembagian_bibit.header_pembagian_bibit.detail_beli.produk.nama",
                },
                {
                    data: "status",
                    render: function(data, type, row, meta) {
                        if (data == 1) {
                            return '<span class="p-2 bg-success rounded text-white">Siap Jual</span>'
                        } else if (data == 0) {
                            return '<span class="p-2 bg-warning rounded">Sortir</span>'
                        } else if (data == -1) {
                            return '<span class="p-2 bg-danger rounded text-white">Mati</span>'
                        } else {
                            return ''
                        }
                    }
                },
                {
                    data: "nama_jaring",
                    render: function(data, type, row, meta) {
                        if (data == null) {
                            return ''
                        } else {
                            return data
                        }

                    }
                },
                {
                    data: "nama_kolam",
                    name: "nama_kolam",
                },
                {
                    data: "quantity_berat",
                    render: function(data, type, row, meta) {
                        return pembatasKoma(data.toString())
                    }
                },
                {
                    data: "quantity",
                    name: "quantity",
                },
                {
                    data: "hpp",
                    render: function(data, type, row, meta) {
                        hpp = ''
                        if (row['status'] == 1) {
                            hpp = data == null ? 0 : data.hpp
                        } else {
                            hpp = ''
                        }
                        return pembatasKoma(hpp.toString())
                    }
                },
                {
                    data: "id",
                    render: function(id, type, row, meta) {
                        let share = '';
                        if (row['quantity'] > 0 && row['status'] == 1) {
                            share =
                                `<button title="Berikan Pakan" data-id="${id}" class="btn btn-primary me-2 btn-share"><i class="fa fa-paper-plane"></i></button>`;
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

            ]
        });



        tableDetail.on('click', '.btn-share', function() {
            var dataId = $(this).data('id'); // ambil nilai data-id dari checkbox yang diklik
            document.cookie = `sharePanenIkan=${dataId};path=/penjualan/create`;
            document.cookie = `sharePanenUrl=panen;path=/penjualan/create`;
            document.cookie = `sharePanenMultiple=false;path=/penjualan/create`;

            window.location.href = "{{ route('jual.create') }}";
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
            document.cookie = `sharePanenIkan=${id};path=/penjualan/create`;
            document.cookie = `sharePanenUrl=pembagian-bibit;path=/penjualan/create`;
            document.cookie = `sharePanenMultiple=true;path=/penjualan/create`;

            window.location.href = "{{ route('jual.create') }}";
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

        function pembatasKoma(angka) {
            return angka.replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        }
    </script>
@endpush
