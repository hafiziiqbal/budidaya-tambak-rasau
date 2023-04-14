@extends('layouts.admin')
@section('content')
    <h1 class="mt-4">Ikan Dalam Kolam</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('kolam') }}">Kolam</a></li>
        <li class="breadcrumb-item active">Ikan Dalam Kolam</li>
    </ol>

    <div class="bg-info p-2 border-dark border-bottom mb-3">
        <label class="fw-bold">{{ $kolam->nama . ' (' . $kolam->posisi . ')' }}</label>
    </div>
    <table class="table table-striped table-hover">
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Ikan</th>
                <th>Quantity</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($daftarIkan as $key => $ikan)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $ikan->nama }}</td>
                    <td>{{ $ikan->quantity }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection
