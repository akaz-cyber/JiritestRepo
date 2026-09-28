@extends('frontend.layouts.master')

@section('title', 'Jirifarm || Bantuan')

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
                            <li><a href="{{ route('home') }}">Beranda<i class="ti-arrow-right"></i></a></li>
                            <li class="active"><a href="javascript:void(0);">Bantuan</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- End Breadcrumbs -->

    <!-- Start Contact -->
    <section id="contact-us" class="contact-us section">
        <div class="container">
            <div class="contact-head">
                @php
                    $settings = DB::table('settings')->get();
                @endphp
                <div class="row">
                    <div class="col-lg-8 col-12">
                        <div class="form-main">
                            <section class="faq">
                                <div class="container">
                                    <h3 class="mb-4 font-weight-bold">FAQ</h3>

                                    <div class="list-group">
                                        @if (count($faqs) > 0)
                                            @foreach ($faqs as $key => $faq)
                                                {{-- Tombol Pertanyaan --}}
                                                <a class="list-group-item list-group-item-action" data-toggle="collapse"
                                                    href="#faq{{ $key }}" role="button" aria-expanded="false"
                                                    aria-controls="faq{{ $key }}">
                                                    {{ $faq->title }}
                                                </a>

                                                {{-- Isi Jawaban (Collapse) --}}
                                                <div class="collapse mt-2" id="faq{{ $key }}">
                                                    <div class="card card-body">
                                                        {!! $faq->content !!}
                                                    </div>
                                                </div>
                                            @endforeach
                                        @else
                                            <p class="text-center">Belum ada FAQ yang ditambahkan.
                                            </p>
                                        @endif
                                    </div>
                                </div>
                            </section>
                            {{-- <form class="form-contact form contact_form" method="post"
                            action="{{ route('contact.store') }}" id="contactForm" novalidate="novalidate">
                            @csrf
                            <div class="row">
                                <div class="col-lg-6 col-12">
                                    <div class="form-group">
                                        <label>Your Name<span>*</span></label>
                                        <input name="name" id="name" type="text"
                                            placeholder="Enter your name">
                                    </div>
                                </div>
                                <div class="col-lg-6 col-12">
                                    <div class="form-group">
                                        <label>Your Subjects<span>*</span></label>
                                        <input name="subject" type="text" id="subject" placeholder="Enter Subject">
                                    </div>
                                </div>
                                <div class="col-lg-6 col-12">
                                    <div class="form-group">
                                        <label>Your Email<span>*</span></label>
                                        <input name="email" type="email" id="email"
                                            placeholder="Enter email address">
                                    </div>
                                </div>
                                <div class="col-lg-6 col-12">
                                    <div class="form-group">
                                        <label>Your Phone<span>*</span></label>
                                        <input id="phone" name="phone" type="number"
                                            placeholder="Enter your phone">
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-group message">
                                        <label>your message<span>*</span></label>
                                        <textarea name="message" id="message" cols="30" rows="9" placeholder="Enter Message"></textarea>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-group button">
                                        <button type="submit" class="btn ">Send Message</button>
                                    </div>
                                </div>
                            </div>
                        </form> --}}
                        </div>
                    </div>
                    <div class="col-lg-4 col-12">
                        <div class="single-head">
                            <div class="single-info">
                                <i class="fa fa-phone"></i>
                                <h4 class="title">Hubungi kami:</h4>
                                <ul>
                                    <li>
                                        @foreach ($settings as $data)
                                            {{ $data->phone }}
                                        @endforeach
                                    </li>
                                </ul>
                            </div>
                            <div class="single-info">
                                <i class="fa fa-envelope-open"></i>
                                <h4 class="title">Email:</h4>
                                <ul>
                                    <li><a href="mailto:info@yourwebsite.com">
                                            @foreach ($settings as $data)
                                                {{ $data->email }}
                                            @endforeach
                                        </a></li>
                                </ul>
                            </div>
                            <div class="single-info">
                                <i class="fa fa-location-arrow"></i>
                                <h4 class="title">Alamat kami:</h4>
                                <ul>
                                    <li>
                                        @foreach ($settings as $data)
                                            {{ $data->address }}
                                        @endforeach
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!--/ End Contact -->


    <!-- Map Section -->
    <div class="map-section">
        <div id="myMap">
            <iframe
                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d100307.24005907954!2d106.47316095253905!3d-6.261177676184111!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e69fd98d8173a83%3A0xc394ada36491f3dc!2sJirifarm%20Hidroponik!5e1!3m2!1sid!2sid!4v1761548644646!5m2!1sid!2sid"
                width="100%" height="100%" frameborder="0" style="border:0;" allowfullscreen="" aria-hidden="false"
                tabindex="0"></iframe>
        </div>
    </div>
    <!--/ End Map Section -->

    <!-- Start Shop Newsletter  -->

    <!-- End Shop Newsletter -->
    <!--================Contact Success  =================-->
    <div class="modal fade" id="success" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="text-success">Thank you!</h2>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p class="text-success">Your message is successfully sent...</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Modals error -->
    <div class="modal fade" id="error" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="text-warning">Sorry!</h2>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p class="text-warning">Something went wrong.</p>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .modal-dialog .modal-content .modal-header {
            position: initial;
            padding: 10px 20px;
            border-bottom: 1px solid #e9ecef;
        }

        .modal-dialog .modal-content .modal-body {
            height: 100px;
            padding: 10px 20px;
        }

        .modal-dialog .modal-content {
            width: 50%;
            border-radius: 0;
            margin: auto;
        }

        .faq .list-group-item {
            border: none;
            border-bottom: 1px solid #f0f0f0;
            color: #333;
            font-weight: 500;
            font-size: 15px;
            padding: 15px 0;
        }

        .faq .list-group-item:hover {
            color: #ee4d2d;
            background-color: transparent;
        }

        .faq .card-body {
            background-color: #fafafa;
            border-radius: 5px;
            font-size: 14px;
            color: #555;
        }

        .faq h3 {
            color: #ee4d2d;
            border-bottom: 2px solid #ee4d2d;
            display: inline-block;
            padding-bottom: 5px;
        }
    </style>
@endpush
@push('scripts')
    <script src="{{ asset('frontend/js/jquery.form.js') }}"></script>
    <script src="{{ asset('frontend/js/jquery.validate.min.js') }}"></script>
    <script src="{{ asset('frontend/js/contact.js') }}"></script>
@endpush
