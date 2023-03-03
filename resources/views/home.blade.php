@extends('layouts.default')
@section('content')
    <div class="container">

        <div class="d-flex align-items-center justify-content-center" style="height:75vh">
            <div>
                @include('components.alert')
                @guest

                    <a href="{{ route('login') }}" class="btn btn-primary my-3">Masuk</a>
                @endguest
                @auth
                    <a href="{{ route('dashboard') }}" class="btn btn-primary my-3">Dashboard</a>
                @endauth

            </div>

        </div>
    </div>
@endsection
