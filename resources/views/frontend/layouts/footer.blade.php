<!-- Start Footer Area -->
<footer class="footer">
    <!-- Footer Top -->
    <div class="footer-top section">
        <div class="container">
            <div class="row">

                <!-- Logo & Sosial Media -->
                <div class="col-lg-5 col-md-6 col-12 text-center">
                    <!-- Single Widget -->
                    <div class="single-footer about d-flex flex-column align-items-center">
                        <div class="logo mb-3 order-1">
                            @php
                                $settings = DB::table('settings')->get();
                            @endphp
                            <a href="{{ route('home') }}"><img
                                    src="@foreach ($settings as $data) {{ $data->logo }} @endforeach"
                                    alt="logo"></a>
                            {{-- <a href="index.html">
                                <img src="{{ asset('backend/img/logo2.png') }}" alt="Logo" style="max-width: 180px;">
                            </a> --}}
                        </div>

                        <!-- Social Media -->
                        <div class="social-icons mt-3 order-2">
                            <a href="https://www.youtube.com/@JiriFarmTV" target="_blank" class="mx-2"><i
                                    class="bi bi-youtube"></i></a>
                            <a href="https://www.instagram.com/jirifarm/" target="_blank" class="mx-2"><i
                                    class="bi bi-instagram"></i></a>
                            <a href="https://web.facebook.com/jirifarm" target="_blank" class="mx-2"><i
                                    class="bi bi-facebook"></i></a>
                            <a href="https://wa.me/628988199366?text=Halo%20Admin%2C%20saya%20butuh%20bantuan%20terkait%20"
                                target="_blank" class="mx-2">
                                <i class="bi bi-whatsapp"></i>
                            </a>
                        </div>

                        {{--
                        @php
                            $settings=DB::table('settings')->get();
                        @endphp
                        <p class="text">@foreach ($settings as $data) {{$data->short_des}} @endforeach</p>
                        <p class="call">Got Question? Call us 24/7
                            <span>
                                <a href="tel:123456789">@foreach ($settings as $data) {{$data->phone}} @endforeach</a>
                            </span>
                        </p>
                        --}}
                    </div>
                    <!-- End Single Widget -->
                </div>

                <!-- Informasi -->
                <div class="col-lg-2 col-md-6 col-12">
                    <div class="single-footer links">
                        <h4>Informasi</h4>
                        <ul>
                            <li><a href="{{ route('about-us') }}">Tentang Kami</a></li>
                            {{-- <li><a href="#">Socil Media</a></li> --}}
                            <li><a
                                    href="https://docs.google.com/forms/d/e/1FAIpQLSdFbWlNORJruHA6q9EK0apDukAVsqvd3zsL66K8vqVEbN8tAQ/viewform">Karir</a>
                            </li>
                            {{-- <li><a href="#">Linktree</a></li> --}}
                            <li><a href="https://goo.gl/maps/TBi84CTGHAbFcbrp7">Branch Tangerang</a></li>
                            <li><a href="https://maps.app.goo.gl/uHHzZdG5ZjeWVmbPA">Branch Surabaya</a></li>
                        </ul>
                    </div>
                </div>

                <!-- Bantuan -->
                <div class="col-lg-2 col-md-6 col-12">
                    <div class="single-footer links">
                        <h4>Bantuan</h4>
                        <ul>
                            <li><a href="{{ route('about-us') }}">Customer Services</a></li>
                            <li><a href="https://mail.google.com/mail/u/0/?view=cm&fs=1&to=jirifarm@gmail.com&su=Hai%20Saya%20Perlu%20Bantuan"
                                    target="blank">Email</a></li>
                            <li><a href="{{ route('contact') }}">Contact Us</a></li>
                            {{-- <li><a href="#">Help</a></li> --}}
                        </ul>
                    </div>
                </div>

                <!-- Keperluanmu -->
                <div class="col-lg-2 col-md-6 col-12">
                    <div class="single-footer links">
                        <h4>Keperluanmu</h4>
                        <ul>
                            <li><a href="https://linktr.ee/freshpick.id">Pesan Sayuran</a></li>
                            <li><a
                                    href="https://drive.google.com/file/d/1mgXK-4WJTQHEA95xf7-Cmh_wNoV0DU4L/view?usp=sharing">Panduan
                                    Hidroponik</a></li>
                            <li><a href="#"></a></li>
                            <li><a href="#">Shipping</a></li>
                            <li><a href="#">Privacy Policy</a></li>
                        </ul>
                    </div>
                </div>

            </div>

            {{--
            <div class="row">
                <div class="col-lg-3 col-md-6 col-12">
                    <!-- Single Widget -->
                    <div class="single-footer social">
                        <h4>Get In Tuch</h4>
                        <!-- Single Widget -->
                        <div class="contact">
                            <ul>
                                <li>@foreach ($settings as $data) {{$data->email}} @endforeach</li>
                                <li>@foreach ($settings as $data) {{$data->phone}} @endforeach</li>
                            </ul>
                        </div>
                        <!-- End Single Widget -->
                        <div class="sharethis-inline-follow-buttons"></div>
                    </div>
                    <!-- End Single Widget -->
                </div>
            </div>
            --}}
        </div>
    </div>
    <!-- End Footer Top -->

    <!-- Copyright -->
    <div class="copyright">
        <div class="container">
            <div class="inner">
                <div class="row">
                    <div class="col-lg-6 col-12 d-flex justify-content-left align-items-center">
                        <div class="left" style="margin-left: 120px;">
                            <p><a style="text-decoration: none;" href="/" target="_blank">Jirifarm</a> ©
                                {{ date('Y') }}.</p>
                        </div>
                    </div>
                    {{-- <div class="col-lg-6 col-12">
                        <div class="right text-end">
                            <img src="{{ asset('backend/img/payments.png') }}" alt="Payments">
                        </div>
                    </div> --}}
                </div>
            </div>
        </div>
    </div>
</footer>
<!-- /End Footer Area -->

<!-- Jquery -->
<script src="{{ asset('frontend/js/jquery.min.js') }}"></script>
<script src="{{ asset('frontend/js/jquery-migrate-3.0.0.js') }}"></script>
<script src="{{ asset('frontend/js/jquery-ui.min.js') }}"></script>
<!-- Popper JS -->
<script src="{{ asset('frontend/js/popper.min.js') }}"></script>
<!-- Bootstrap JS -->
<script src="{{ asset('frontend/js/bootstrap.min.js') }}"></script>
<!-- Color JS -->
<script src="{{ asset('frontend/js/colors.js') }}"></script>
<!-- Slicknav JS -->
<script src="{{ asset('frontend/js/slicknav.min.js') }}"></script>
<!-- Owl Carousel JS -->
<script src="{{ asset('frontend/js/owl-carousel.js') }}"></script>
<!-- Magnific Popup JS -->
<script src="{{ asset('frontend/js/magnific-popup.js') }}"></script>
<!-- Waypoints JS -->
<script src="{{ asset('frontend/js/waypoints.min.js') }}"></script>
<!-- Countdown JS -->
<script src="{{ asset('frontend/js/finalcountdown.min.js') }}"></script>
<!-- Nice Select JS -->
<script src="{{ asset('frontend/js/nicesellect.js') }}"></script>
<!-- Flex Slider JS -->
<script src="{{ asset('frontend/js/flex-slider.js') }}"></script>
<!-- ScrollUp JS -->
<script src="{{ asset('frontend/js/scrollup.js') }}"></script>
<!-- Onepage Nav JS -->
<script src="{{ asset('frontend/js/onepage-nav.min.js') }}"></script>
{{-- Isotope --}}
<script src="{{ asset('frontend/js/isotope/isotope.pkgd.min.js') }}"></script>
<!-- Easing JS -->
<script src="{{ asset('frontend/js/easing.js') }}"></script>

<!-- Active JS -->
<script src="{{ asset('frontend/js/active.js') }}"></script>


@stack('scripts')
<script>
    $(function() {
        // Inisialisasi SlickNav
        // 1. Targetkan menu utama dari versi desktop (ul.main-menu)
        // 2. appendTo: Tentukan di mana tombol burger menu akan muncul.
        //    Kita letakkan di dalam div .mobile-nav yang sudah kita siapkan.
        $('ul.main-menu').slicknav({
            'appendTo': '.mobile-nav',
            'label': '', // Kosongkan label agar hanya ikon burger yang muncul
            'closedSymbol': '&#9658;', // Panah ke kanan
            'openedSymbol': '&#9660;' // Panah ke bawah
        });

        // Menambahkan fungsionalitas overlay saat sidebar terbuka
        var $body = $('body');
        var $slicknavMenu = $('.slicknav_menu');

        // Buat div untuk overlay
        $body.append('<div class="sidebar-overlay"></div>');
        var $overlay = $('.sidebar-overlay');

        // Toggle class 'sidebar-active' di body saat menu di-klik
        $('.slicknav_btn').on('click', function() {
            $body.toggleClass('sidebar-active');
        });

        // Tutup sidebar saat overlay di-klik
        $overlay.on('click', function() {
            $body.removeClass('sidebar-active');
            // Tutup menu slicknav juga
            $('ul.main-menu').slicknav('close');
        });
    });
</script>


<script>
    setTimeout(function() {
        $('.alert').not('.alert-sticky').slideUp();
    }, 5000);
    $(function() {
        // ------------------------------------------------------- //
        // Multi Level dropdowns
        // ------------------------------------------------------ //
        $("ul.dropdown-menu [data-toggle='dropdown']").on("click", function(event) {
            event.preventDefault();
            event.stopPropagation();

            $(this).siblings().toggleClass("show");


            if (!$(this).next().hasClass('show')) {
                $(this).parents('.dropdown-menu').first().find('.show').removeClass("show");
            }
            $(this).parents('li.nav-item.dropdown.show').on('hidden.bs.dropdown', function(e) {
                $('.dropdown-submenu .show').removeClass("show");
            });

        });
    });
</script>
