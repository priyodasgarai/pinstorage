@extends('layouts.web_master')

@section('content')
    <section>
        <div class="banner-inner">
            <img src="{{ asset('assets/web/images/about-banner.png')}}" alt="">
            <div class="banner-inner-content">
                <ul class="breadcrumb-item-content mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('index') }}">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">About Us</li>
                </ul>
                <h1>About Us</h1>
            </div>
        </div>
    </section>
@endsection
