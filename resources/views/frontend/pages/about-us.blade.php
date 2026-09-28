@extends('frontend.layouts.master')

@section('title', 'Jirifarm || Tentang Kami')

@section('main-content')
    @once
        @include('frontend.layouts.wa_fb')
    @endonce

    <!-- Breadcrumbs -->
    <div class="breadcrumbs">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="bread-inner">
                        <ul class="bread-list">
                            <li><a href="index1.html">Beranda<i class="ti-arrow-right"></i></a></li>
                            <li class="active"><a href="blog-single.html">Tentang Kami</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- End Breadcrumbs -->

    <!-- About Us -->
    <section class="about-us section">
        <div class="container">
            <div class="row">
                <div class="col-lg-6 col-12">
                    <div class="about-content">
                        @php
                            $settings = DB::table('settings')->get();
                        @endphp
                        @foreach ($settings as $data)
                            <h3>{!! $data->short_des !!}</h3>
                        @endforeach
                        {{-- <h3>Selamat datang di <span>Jirifarm</span></h3> --}}
                        <p>
                            @foreach ($settings as $data)
                                {{ $data->description }}
                            @endforeach
                        </p>
                    </div>
                </div>
                <div class="col-lg-6 col-12">
                    <div class="about-img overlay">
                        {{-- <div class="button">
								<a href="https://www.youtube.com/watch?v=nh2aYrGMrIE" class="video video-popup mfp-iframe"><i class="fa fa-play"></i></a>
							</div> --}}
                        <img src="@foreach ($settings as $data) {{ $data->photo }} @endforeach"
                            alt="@foreach ($settings as $data) {{ $data->photo }} @endforeach">
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- End About Us -->


    <!-- Start Shop Services Area -->

    <!-- End Shop Services Area -->

@endsection
