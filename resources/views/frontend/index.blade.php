@extends('frontend.layouts.master')
@section('title', 'JIRIFARM || Belanja Keperluan Hidroponik dan Aquaponik Online')
@section('main-content')
    @once
        @include('frontend.layouts.wa_fb')
    @endonce
    <!-- Slider Area -->
    @if (count($banners) > 0)
        <section id="Gslider" class="carousel slide" data-ride="carousel">
            <ol class="carousel-indicators">
                @foreach ($banners as $key => $banner)
                    <li data-target="#Gslider" data-slide-to="{{ $key }}" class="{{ $key == 0 ? 'active' : '' }}">
                    </li>
                @endforeach

            </ol>
            {{-- <a href="{{ route('product-grids') }}">
                <img class="first-slide" src="{{ $banner->photo }}" alt="First slide">
            </a> --}}
            <div class="carousel-inner" role="listbox">
                @foreach ($banners as $key => $banner)
                    <div class="carousel-item {{ $key == 0 ? 'active' : '' }}">
                        <a href="{{ $banner->link }}" target="_blank">
                            <img class="first-slide" src="{{ asset('storage/' . $banner->photo) }}"
                                alt="Banner {{ $key + 1 }}">
                        </a>
                    </div>
                @endforeach
            </div>
            <a class="carousel-control-prev" href="#Gslider" role="button" data-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="sr-only">Previous</span>
            </a>
            <a class="carousel-control-next" href="#Gslider" role="button" data-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="sr-only">Next</span>
            </a>
        </section>
    @endif
    <!--/ End Slider Area -->

    <!-- Start Small Banner  -->
    <section class=" mt-5 small-banner section">
        <div class="col-12">
            <div class="section-title">
                <h2>KATEGORI</h2>
            </div>
        </div>
        <div class="container-fluid">
            <div class="row">
                @php
                    $limit = \Illuminate\Support\Facades\Cache::get('home_category_limit', 6);
                    $total_categories = DB::table('categories')
                        ->where('status', 'active')
                        ->where('is_parent', 1)
                        ->count();

                    $category_lists = DB::table('categories')
                        ->where('status', 'active')
                        ->where('is_parent', 1)
                        ->orderBy('position', 'ASC')
                        ->limit($limit)
                        ->get();
                @endphp
                @if ($category_lists)
                    @foreach ($category_lists as $cat)
                        <div class="col-lg-4 col-md-6 col-12 mb-4">
                            <div class="cat-card-hover">
                                <a href="{{ route('product-cat', $cat->slug) }}" class="cat-link">
                                    {{-- Gambar Background --}}
                                    <div class="cat-image-wrapper">
                                        @if ($cat->photo)
                                            <img src="{{ asset('storage/' . $cat->photo) }}" alt="{{ $cat->title }}">
                                        @else
                                            <img src="https://via.placeholder.com/600x370" alt="#">
                                        @endif
                                    </div>
                                    <div class="cat-overlay">
                                        <h3 class="cat-title">{{ $cat->title }}</h3>
                                    </div>
                                </a>

                            </div>
                        </div>
                    @endforeach
                @endif
                @if ($total_categories > 6)
                    <div class="col-12 text-center" style="margin-top: 20px;">
                        {{-- Mengarah ke halaman semua produk (Product Grids) --}}
                        <a href="{{ route('product-grids') }}" class="btn-lihat-semua">
                            Lihat Semua
                        </a>
                    </div>
                @endif
            </div>

        </div>
    </section>
    <!-- End Small Banner -->


    {{-- pendapat pelanggan --}}
    <div class="mt-5 pt-5">
        <div class="container">
            {{-- Header untuk Judul dan Navigasi --}}
            <div
                class="testimonial-header d-flex flex-column flex-md-row justify-content-md-between align-items-md-center text-center text-md-left">
                <h1 class="section-title-underline" style="margin-bottom:0;">Pendapat Pelanggan</h1>
                <div class="testimonial-nav mt-3 mt-md-0">
                    <button class="custom-prev"><i class="bi bi-arrow-left"></i></button>
                    <button class="custom-next"><i class="bi bi-arrow-right"></i></button>
                </div>
            </div>
        </div>
    </div>
    <div class="testimonial-slider owl-carousel mb-5">

        <div class="single-testimonial">
            <div class="rating-stars">
                <i class="bi bi-star-fill"></i>
                <i class="bi bi-star-fill"></i>
                <i class="bi bi-star-fill"></i>
                <i class="bi bi-star-fill"></i>
                <i class="bi bi-star-fill"></i>
            </div>
            <div class="testimonial-author">
                <h4>lilyhutagalung</h4>
                <span class="verified-badge">
                    <i class="bi bi-patch-check-fill"></i>
                </span>
            </div>

            <p>"Barang oke, lumayan tebal jg, paling murah di antara toko yg lain, tq seller, kurir ramah dan sopan tq"</p>
        </div>

        <div class="single-testimonial">
            <div class="rating-stars">
                <i class="bi bi-star-fill"></i>
                <i class="bi bi-star-fill"></i>
                <i class="bi bi-star-fill"></i>
                <i class="bi bi-star-fill"></i>
                <i class="bi bi-star-fill"></i>
            </div>
            <div class="testimonial-author">
                <h4>ponskeeh</h4>
                <span class="verified-badge">
                    <i class="bi bi-patch-check-fill"></i>
                </span>
            </div>
            <p>"Kualitas produk sangat baik. Harga produk sangat baik. Kecepatan pengiriman sangat baik. Respon penjual
                sangat baik. Semua baik, bahkan orang jahatpun aslinya baik, hanya saja mungkin lingkungan dan kondisilah
                yang merubah mereka menjadi tidak baik..."</p>
        </div>

        <div class="single-testimonial">
            <div class="rating-stars">
                <i class="bi bi-star-fill"></i>
                <i class="bi bi-star-fill"></i>
                <i class="bi bi-star-fill"></i>
                <i class="bi bi-star-fill"></i>
                <i class="bi bi-star-fill"></i>
            </div>
            <div class="testimonial-author">
                <h4>temisuhendra</h4>
                <span class="verified-badge">
                    <i class="bi bi-patch-check-fill"></i>
                </span>
            </div>
            <p>"Barang diterima kondisi baik lengkap aman
                Jumlah pas harga lumayan kualitas bagus"</p>
        </div>

    </div>
    {{-- end pendapat pelanggan --}}
@endsection

@push('styles')
    {{-- <script type='text/javascript'
        src='https://platform-api.sharethis.com/js/sharethis.js#property=5f2e5abf393162001291e431&product=inline-share-buttons'
        async='async'></script>
    <script type='text/javascript'
        src='https://platform-api.sharethis.com/js/sharethis.js#property=5f2e5abf393162001291e431&product=inline-share-buttons'
        async='async'></script> --}}
    <style>
        /* untuk corousel  */
        #Gslider .carousel-item {
            height: auto;
            background-color: #f5f5f5;

        }

        #Gslider .carousel-item img {
            width: 100%;
            height: 100%;

        }

        /* slider arrow */
        #Gslider .carousel-control-prev-icon,
        #Gslider .carousel-control-next-icon {
            background-color: transparent;
            width: 5rem;
            height: 5rem;
        }

        #Gslider .carousel-control-next-icon {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%23464545' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='m6 4 4 4-4 4'/%3e%3c/svg%3e");
        }

        #Gslider .carousel-control-prev-icon {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%23464545' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M10 12 6 8l4-4'/%3e%3c/svg%3e");
        }

        /* slider end arrow */

        /* indicator corusel buat yang bulat warna hijau */
        #Gslider .carousel-indicators {
            bottom: 10px;
        }

        #Gslider .carousel-indicators li {
            background-color: #6baf9c;
            width: 15px;
            height: 15px;
            border-radius: 100%;
            margin: 0 5px;
            border: none;
            opacity: 0.7;
        }

        #Gslider .carousel-indicators .active {
            background-color: #006a4e;
            opacity: 1;

        }

        /* end */

        /* CSS untuk Testimonial Responsif */
        .testimonial-header {
            margin-bottom: 30px;
        }

        .section-title-underline {
            font-size: 28px;
            font-weight: 700;
            position: relative;
            padding-bottom: 15px;
            text-transform: uppercase;
        }

        .section-title-underline::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 70px;
            height: 4px;
            background-color: #1a5e4a;
        }

        .testimonial-nav button {
            border: 1px solid #ddd;
            background-color: #fff;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            transition: all 0.3s ease;
        }

        .testimonial-nav button:hover {
            background-color: #1a5e4a;
            color: #fff;
            border-color: #1a5e4a;
        }

        /* ini buat categori */
        .cat-card-hover {
            position: relative;
            width: 100%;
            aspect-ratio: 2 / 1;
            overflow: hidden;
            border-radius: 8px;
        }

        /* Styling Link agar memenuhi area */
        .cat-card-hover .cat-link {
            display: block;
            width: 100%;
            height: 100%;
            position: relative;
        }

        /* Styling Gambar */
        .cat-image-wrapper {
            width: 100%;
            height: 100%;
        }

        .cat-image-wrapper img {
            width: 100%;
            height: 100%;
            object-fit: fill;
            /* Agar gambar memenuhi kotak tanpa gepeng */
            transition: transform 0.5s ease;
            /* Efek zoom halus */
        }

        /* Styling Overlay (Lapisan Gelap + Teks) */
        .cat-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.6);
            /* Hitam transparan (Gelap) */

            /* Flexbox untuk menengahkan teks */
            display: flex;
            justify-content: center;
            align-items: center;

            /* Default: Sembunyi */
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease-in-out;
        }

        /* Styling Judul Teks */
        .cat-overlay .cat-title {
            color: #fff;
            /* Teks Putih */
            font-size: 24px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            text-align: center;
            padding: 0 15px;

            /* Efek geser sedikit dari bawah */
            transform: translateY(20px);
            transition: transform 0.3s ease-in-out;
        }

        /* === EFEK HOVER (Saat mouse diarahkan) === */
        .cat-card-hover:hover .cat-overlay {
            opacity: 1;
            visibility: visible;
        }

        .cat-card-hover:hover .cat-image-wrapper img {
            transform: scale(1.1);
        }

        .cat-card-hover:hover .cat-title {
            transform: translateY(0);
        }

        /* end kategori */



        /* btn lihat semua */

        .btn-lihat-semua {
            display: inline-block;
            background-color: #096a4d;
            color: #fff !important;
            padding: 12px 40px;
            border-radius: 50px;
            font-weight: 600;
            text-transform: uppercase;
            text-decoration: none;
            transition: all 0.3s ease;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
        }

        .btn-lihat-semua:hover {
            background-color: #064e39;
            /* Warna hijau lebih gelap saat hover */
            color: #fff !important;
            transform: translateY(-2px);
            /* Efek naik sedikit saat di-hover */
            box-shadow: 0px 6px 15px rgba(0, 0, 0, 0.2);
        }



        /* Aturan khusus untuk mobile corousel */
        @media (max-width: 991px) {
            #Gslider .carousel-item {
                aspect-ratio: 16 / 9;
                height: auto;
            }


        }

        /* Aturan khusus untuk mobile corusel */
        @media (min-width: 480px) and (max-width: 767px) {
            #Gslider .carousel-item {
                aspect-ratio: 16 / 9;
                height: auto;
            }

            /* Opsional: Sesuaikan ukuran panah slider agar pas */
            #Gslider .carousel-control-prev-icon,
            #Gslider .carousel-control-next-icon {
                width: 3.5rem;
                height: 3.5rem;
            }
        }

        @media (max-width: 767px) {
            .cat-card-hover {
                height: 200px;
                /* Lebih pendek di HP */
            }

            .cat-overlay .cat-title {
                font-size: 18px;
                /* Teks lebih kecil di HP */
            }
        }

        @media (max-width: 479px) {
            #Gslider .carousel-item {
                aspect-ratio: 16 / 9;
                height: auto;
            }

            #Gslider .carousel-control-prev-icon,
            #Gslider .carousel-control-next-icon {
                background-color: transparent;
                width: 2rem;
                height: 2rem;
            }
        }
    </style>
@endpush

@push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>
    <script>
        var $topeContainer = $('.isotope-grid');
        var $filter = $('.filter-tope-group');

        // filter items on button click
        $filter.each(function() {
            $filter.on('click', 'button', function() {
                var filterValue = $(this).attr('data-filter');
                $topeContainer.isotope({
                    filter: filterValue
                });
            });

        });

        // init Isotope
        $(window).on('load', function() {
            var $grid = $topeContainer.each(function() {
                $(this).isotope({
                    itemSelector: '.isotope-item',
                    layoutMode: 'fitRows',
                    percentPosition: true,
                    animationEngine: 'best-available',
                    masonry: {
                        columnWidth: '.isotope-item'
                    }
                });
            });
        });

        var isotopeButton = $('.filter-tope-group button');

        $(isotopeButton).each(function() {
            $(this).on('click', function() {
                for (var i = 0; i < isotopeButton.length; i++) {
                    $(isotopeButton[i]).removeClass('how-active1');
                }

                $(this).addClass('how-active1');
            });
        });
    </script>
    <script>
        function cancelFullScreen(el) {
            var requestMethod = el.cancelFullScreen || el.webkitCancelFullScreen || el.mozCancelFullScreen || el
                .exitFullscreen;
            if (requestMethod) { // cancel full screen.
                requestMethod.call(el);
            } else if (typeof window.ActiveXObject !== "undefined") { // Older IE.
                var wscript = new ActiveXObject("WScript.Shell");
                if (wscript !== null) {
                    wscript.SendKeys("{F11}");
                }
            }
        }

        function requestFullScreen(el) {
            // Supports most browsers and their versions.
            var requestMethod = el.requestFullScreen || el.webkitRequestFullScreen || el.mozRequestFullScreen || el
                .msRequestFullscreen;

            if (requestMethod) { // Native full screen.
                requestMethod.call(el);
            } else if (typeof window.ActiveXObject !== "undefined") { // Older IE.
                var wscript = new ActiveXObject("WScript.Shell");
                if (wscript !== null) {
                    wscript.SendKeys("{F11}");
                }
            }
            return false
        }
    </script>

    <script>
        $(document).ready(function() {
            // Inisialisasi Owl Carousel HANYA SATU KALI dan simpan dalam variabel 'owl'
            var owl = $('.testimonial-slider').owlCarousel({
                loop: true,
                margin: 30,
                autoplay: true,
                autoplayTimeout: 5000,
                autoplayHoverPause: true,
                dots: false,
                nav: false,
                responsive: {
                    0: {
                        items: 1 // Di layar sangat kecil ke atas, tampilkan 1 item
                    },
                    768: {
                        items: 2 // Di layar (tablet) ke atas, tampilkan 2 item
                    },
                    992: {
                        items: 3 // Di layar (desktop) ke atas, tampilkan 3 item
                    }
                }
            });

            // Kode untuk menghubungkan tombol kustom (ini sudah benar)
            // Ketika tombol .custom-next di-klik
            $('.custom-next').click(function() {
                owl.trigger('next.owl.carousel');
            });

            // Ketika tombol .custom-prev di-klik
            $('.custom-prev').click(function() {
                owl.trigger('prev.owl.carousel');
            });


        });
    </script>
@endpush
