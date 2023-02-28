@extends('layouts.default')
@section('content')
    <div class="container">

        <div class="d-flex align-items-center justify-content-center" style="height:75vh">
            <div>
                @include('components.alert')
                <a href="{{ route('login') }}" class="btn btn-primary my-3">Masuk</a>
            </div>

        </div>
    </div>
@endsection
