@extends('layouts.admin')
@section('content')
    <h1 class="mt-4">Produk</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item active">Produk</li>
    </ol>

    <div class="mb-5">
        <label class="form-label">Pilih Kategori Produk</label>
        <select class="form-select" id="selectKategori" data-placeholder="Pilih Produk">
            <option></option>
            @foreach ($kategori as $produk)
                <option value="{{ $produk->id }}" {{ $produk->id == 3 ? 'selected="selected"' : '' }}>{{ $produk->nama }}
                </option>
            @endforeach
        </select>
    </div>

    <a href="{{ route('produk.create') }}" class="btn btn-primary mb-4"><i class="fa fa-plus"></i>&emsp; Tambah Produk</a>

    @include('components.alert')
    <table id="tblProduk" class="table table-striped  nowrap" style="width:100%">
        <thead>
            <tr>
                <th>No</th>
                <th>Nama</th>
                <th>Quantity <span id="quantityBerat" class="d-none"> (Kg)</span></th>
                <th>Aksi</th>
            </tr>
        </thead>
    </table>
@endsection

@push('script')
    <script>
        $("#selectKategori").select2({
            theme: "bootstrap-5",
            containerCssClass: "select2--medium",
            dropdownCssClass: "select2--medium",
        });

        let idKategori = $("#selectKategori").find(":selected").val();

        let cookieKategori = getCookie('kategori');
        if (cookieKategori == '') {
            document.cookie = `kategori=${idKategori};path=/produk;`;
        } else {
            $("#selectKategori").val(cookieKategori).change();

            idKategori = cookieKategori
            if (idKategori == 6) {
                $('#quantityBerat').removeClass('d-none');
            } else {
                $('#quantityBerat').addClass('d-none');
            }
        }


        let table = $('#tblProduk').DataTable({
            responsive: true,
            ajax: {
                url: `/produk/${idKategori}/datatable`,
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
                    data: "nama",
                    name: "nama",
                },
                {
                    data: "quantity",
                    render: function(data, type, row, meta) {
                        return pembatasKoma(parseInt(data).toString())
                    }
                },
                {
                    data: "id",
                    render: function(id) {

                        let edit =
                            `<a title="Edit Data" href="/produk/${id}/edit" class="btn btn-warning me-2"><i class="fa fa-pencil"></i></a>`;
                        let deletebtn =
                            `<a onclick="return confirm('Data ini akan dihapus')" title="Hapus Data" href="/produk/delete/${id}" class="btn btn-danger"><i class="fa fa-trash"></i></a>`
                        return edit + deletebtn
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


        $('#selectKategori').on('change', function(e) {
            let optionSelected = $("option:selected", this);
            idKategori = this.value;
            if (idKategori == 6) {
                $('#quantityBerat').removeClass('d-none');
            } else {
                $('#quantityBerat').addClass('d-none');
            }
            table.ajax.url(`/produk/${idKategori}/datatable`).load();
            document.cookie = `kategori=${idKategori};path=/produk;`;
        });

        function pembatasKoma(angka) {
            return angka.replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        }
    </script>
@endpush
