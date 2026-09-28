@extends('frontend.layouts.master')

@section('title', 'Jirifarm || PRODUCT PAGE')
<link rel="icon" type="image/png" href="{{ asset('frontend/img/logojirifarm.jpg') }}">
@once
    @include('frontend.layouts.wa_fb')
@endonce

@section('main-content')
    <!-- Breadcrumbs -->
    <div class="breadcrumbs">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="bread-inner">
                        {{-- @php
                            $previousUrl = url()->previous();
                            $fallbackUrl = route('product-grids');
                            $backUrl = $fallbackUrl;
                            if (
                                $previousUrl &&
                                $previousUrl != url()->current() &&
                                !Str::contains($previousUrl, '/product-detail/')
                            ) {
                                $backUrl = $previousUrl;
                            }
                        @endphp --}}
                        <ul class="bread-list">
                            <li><a href="{{ route('home') }}">Beranda<i class="ti-arrow-right"></i></a></li>
                            <li><a href="{{ route('product-grids') }}">Produk</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- End Breadcrumbs -->

    <!-- Product Style -->
    <form action="{{ route('shop.filter') }}" method="POST">
        @csrf
        <section class="product-area shop-sidebar shop section">
            <div class="container">
                <div class="row">
                    <div class="col-lg-3 col-md-4 col-12">
                        <div class="shop-sidebar">
                            <!-- Single Widget -->
                            <div class="single-widget category">
                                <h3 class="title">Kategori</h3>
                                <ul class="categor-list">
                                    @php
                                        // $category = new Category();
                                        $menu = App\Models\Category::getAllParentWithChild();
                                    @endphp
                                    @if ($menu)
                                        <li>
                                            @foreach ($menu as $cat_info)
                                                @if ($cat_info->child_cat->count() > 0)
                                        <li><a href="{{ route('product-cat', $cat_info->slug) }}">{{ $cat_info->title }}</a>
                                            <ul>
                                                @foreach ($cat_info->child_cat as $sub_menu)
                                                    <li><a
                                                            href="{{ route('product-sub-cat', [$cat_info->slug, $sub_menu->slug]) }}">{{ $sub_menu->title }}</a>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </li>
                                    @else
                                        <li><a href="{{ route('product-cat', $cat_info->slug) }}">{{ $cat_info->title }}</a>
                                        </li>
                                    @endif
                                    @endforeach
                                    </li>
                                    @endif
                                    {{-- @foreach (Helper::productCategoryList('products') as $cat)
                                            @if ($cat->is_parent == 1)
												<li><a href="{{route('product-cat',$cat->slug)}}">{{$cat->title}}</a></li>
											@endif
                                        @endforeach --}}
                                </ul>
                            </div>
                            <!--/ End Single Widget -->
                            <!-- Shop By Price -->

                            <!--/ End Shop By Price -->
                            <!-- Single Widget -->
                            {{-- <div class="single-widget recent-post">
                                <h3 class="title">Product Terbaru</h3>

                                @foreach ($recent_products as $product)

                                    @php
                                        $photo = explode(',', $product->photo);
                                    @endphp
                                    <div class="single-post first">
                                        <div class="image">
                                            <img src="{{ $photo[0] }}" alt="{{ $photo[0] }}">
                                        </div>
                                        <div class="content">
                                            <h5><a
                                                    href="{{ route('product-detail', $product->slug) }}">{{ $product->title }}</a>
                                            </h5>
                                            @php
                                                $recentVariant = $product->variants->first();
                                                $recent_price = $recentVariant->price ?? 0;
                                            @endphp
                                            <p class="price">Rp{{ number_format($recent_price, 0, ',', '.') }}</p>

                                        </div>
                                    </div>

                                @endforeach
                            </div> --}}
                            <!--/ End Single Widget -->
                        </div>
                    </div>
                    <div class="col-lg-9 col-md-8 col-12">
                        <div class="row">
                            <div class="col-12">
                                <!-- Shop Top -->
                                <div class="shop-top d-flex flex-wrap justify-content-between align-items-center">
                                    <div class="shop-shorter order-1 order-md-2 order-lg-1">
                                        <div class="single-shorter">
                                            {{-- <label>Urutkan :</label> --}}
                                            @php
                                                // Helper untuk membuat URL dengan parameter sort baru tapi tetap mempertahankan filter lain
                                                $build_url = fn($sortBy) => request()->fullUrlWithQuery([
                                                    'sortBy' => $sortBy,
                                                ]);
                                                $current_sort = request()->query('sortBy', 'latest'); // Default sort adalah terbaru
                                            @endphp

                                            {{-- Tampilkan Tombol-tombol terlebih dahulu --}}
                                            <a href="{{ route('product-grids') }}"
                                                class="btn-sort {{ $current_sort == 'latest' ? 'active' : '' }}">Terbaru</a>
                                            <a href="{{ $build_url('popular') }}"
                                                class="btn-sort {{ $current_sort == 'popular' ? 'active' : '' }}">Populer</a>
                                            <a href="{{ $build_url('bestselling') }}"
                                                class="btn-sort {{ $current_sort == 'bestselling' ? 'active' : '' }}">Terlaris</a>

                                            {{-- sort rendah ke tinggi dan tinggi ke rendah --}}
                                            <select class='sortBy' name='sortBy'
                                                onchange="window.location.href = this.value;">
                                                @php
                                                    $price_options = [
                                                        'price_asc' => 'Rendah ke Tinggi',
                                                        'price_desc' => 'Tinggi ke Rendah',
                                                    ];
                                                @endphp

                                                {{-- Tentukan teks yang ditampilkan di kotak --}}
                                                @if ($current_sort == 'price_asc')
                                                    <option value="" selected disabled>Rendah ke Tinggi</option>
                                                @elseif ($current_sort == 'price_desc')
                                                    <option value="" selected disabled>Tinggi ke Rendah</option>
                                                @else
                                                    <option value="" selected disabled>Harga</option>
                                                @endif

                                                {{-- Tampilkan daftar pilihan yang bisa dipilih --}}
                                                @foreach ($price_options as $key => $value)
                                                    {{-- Hanya tampilkan opsi jika BUKAN yang sedang aktif --}}
                                                    @if ($current_sort !== $key)
                                                        <option value="{{ $build_url($key) }}">{{ $value }}
                                                        </option>
                                                    @endif
                                                @endforeach

                                                {{-- Tambahkan opsi untuk reset ke default jika sort harga sedang aktif --}}
                                                @if ($current_sort == 'price_asc' || $current_sort == 'price_desc')
                                                    <option value="{{ $build_url('latest') }}">Harga</option>
                                                @endif
                                            </select>
                                        </div>
                                    </div>
                                    {{-- Custom Pagination Atas menggunakan class .view-mode --}}
                                    <div class="view-mode order-2 order-md-1 order-lg-2">
                                        @if ($products->lastPage() > 1)
                                            <nav>
                                                <div class="custom-pagination">
                                                    {{-- Tombol Previous --}}
                                                    <div
                                                        class="page-item {{ $products->onFirstPage() ? 'disabled' : '' }}">
                                                        @if ($products->onFirstPage())
                                                            <span class="page-text">&lt;</span>
                                                        @else
                                                            <a class="page-link" href="{{ $products->previousPageUrl() }}"
                                                                rel="prev">&lt;</a>
                                                        @endif
                                                    </div>

                                                    {{-- Halaman Saat Ini --}}
                                                    <div class="page-item active">
                                                        <a class="page-link"
                                                            href="javascript:void(0)">{{ $products->currentPage() }}</a>
                                                    </div>

                                                    {{-- Tombol Next --}}
                                                    <div
                                                        class="page-item {{ $products->hasMorePages() ? '' : 'disabled' }}">
                                                        @if ($products->hasMorePages())
                                                            <a class="page-link" href="{{ $products->nextPageUrl() }}"
                                                                rel="next">&gt;</a>
                                                        @else
                                                            <span class="page-text">&gt;</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </nav>
                                        @endif
                                    </div>
                                    {{-- End Custom Pagination Atas --}}
                                </div>
                                <!--/ End Shop Top -->
                            </div>
                        </div>
                        <div class="row"id="product-grid">
                            {{-- {{$products}} --}}
                            @if (count($products) > 0)
                                @foreach ($products as $product)
                                    <div class="col-lg-4 col-md-6 col-6">
                                        <div class="single-product">
                                            <div class="product-img">
                                                <a href="{{ route('product-detail', $product->slug) }}">
                                                    @php
                                                        $firstPhotoPath = '';
                                                        if (!empty($product->photo) && is_string($product->photo)) {
                                                            $allPhotos = array_values(
                                                                array_filter(explode('|', $product->photo)),
                                                            );
                                                            $firstPhotoPath = $allPhotos[0] ?? '';
                                                        }

                                                        // 3. Tentukan URL gambar (dengan fallback)
                                                        if ($firstPhotoPath) {
                                                            $firstImageUrl = asset('storage/' . $firstPhotoPath);
                                                        } else {
                                                            $firstImageUrl =
                                                                'https://via.placeholder.com/600x600?text=No+Image';
                                                        }
                                                    @endphp
                                                    <img class="default-img" src="{{ $firstImageUrl }}"
                                                        alt="{{ $product->title }}">
                                                    <img class="hover-img" src="{{ $firstImageUrl }}"
                                                        alt="{{ $product->title }}">
                                                    @if ($product->discount)
                                                        <span class="price-dec">{{ $product->discount }} % Off</span>
                                                    @endif
                                                </a>
                                                <div class="button-head">
                                                    {{-- <div class="product-action">
                                                                <a data-toggle="modal" data-target="#{{ $product->id }}"
                                                                    title="Quick View" href="#"><i
                                                                        class=" ti-eye"></i><span>Lihat product</span></a>
                                                            </div> --}}
                                                    <div class="product-action-2">
                                                        <a href="{{ route('product-detail', $product->slug) }}">Lihat
                                                            Product</a>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="product-content">
                                                <h3><a
                                                        href="{{ route('product-detail', $product->slug) }}">{{ $product->title }}</a>
                                                </h3>

                                                @php
                                                    // Ambil varian dengan harga JUAL (price) termurah
                                                    $cheapestVariant = $product->variants->sortBy('price')->first();

                                                    // Sediakan nilai default
                                                    $final_price = 0;
                                                    $original_price = null;
                                                    $discount_percent = 0;

                                                    if ($cheapestVariant) {
                                                        // Ambil data dari varian termurah (sesuai schema di ProductController)
                                                        $final_price = $cheapestVariant->price;
                                                        $original_price = $cheapestVariant->original_price; // Harga coret
                                                        $discount_percent = $cheapestVariant->discount_percent; // Persentase diskon
                                                    }

                                                    // Cek apakah ada diskon valid untuk ditampilkan
                                                    $hasDiscount =
                                                        $discount_percent > 0 ||
                                                        ($original_price && $original_price > $final_price);
                                                @endphp

                                                {{-- Tampilkan harga --}}
                                                @if ($hasDiscount)
                                                    <span class="text-danger">
                                                        <b>Rp{{ number_format($final_price, 0, ',', '.') }}</b>
                                                    </span>
                                                    {{-- Hanya tampilkan harga coret jika ada & lebih besar dari harga final --}}
                                                    @if ($original_price && $original_price > $final_price)
                                                        <del
                                                            class="text-muted ml-2">Rp{{ number_format($original_price, 0, ',', '.') }}</del>
                                                    @endif
                                                    @if ($discount_percent > 0)
                                                        <span class="badge badge-danger ml-2"
                                                            style="font-size: 12px; padding: 3px 6px;">-{{ $discount_percent }}%</span>
                                                    @endif
                                                @else
                                                    <span>Rp{{ number_format($final_price, 0, ',', '.') }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <h4 class="text-warning" style="margin:100px auto;">Produk tidak tersedia.</h4>
                            @endif



                        </div>
                        <div class="row">
                            <div class="col-12 text-center mt-4">
                                @if ($products->hasMorePages())
                                    <button class="btn btn-primary" id="load-more-btn"
                                        data-url="{{ $products->nextPageUrl() }}">
                                        <span class="text">Lihat lebih banyak</span>
                                        <span class="loading d-none">
                                            <i class="fa fa-spinner fa-spin"></i> Memuat...
                                        </span>
                                    </button>
                                @endif
                                {{-- Elemen ini untuk menandai akhir halaman bagi script --}}
                                <div id="pagination-end" style="display:none"></div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </section>
    </form>

    <!--/ End Product Style 1  -->



    <!-- Modal -->
    {{-- @if ($products)
        @foreach ($products as $key => $product)
            <div class="modal fade" id="{{ $product->id }}" tabindex="-1" role="dialog">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                    class="ti-close" aria-hidden="true"></span></button>
                        </div>
                        <div class="modal-body">
                            <div class="row no-gutters">
                                @php
                                    $photos = array_values(
                                        array_filter(array_map('trim', explode(',', $product->photo ?? ''))),
                                    );
                                    $firstImage = $photos[0] ?? 'https://via.placeholder.com/600x600?text=No+Image';
                                @endphp
                                <div class="col-lg-7 col-md-12 col-sm-12 col-xs-12">

                                    <div id="modalCarousel-{{ $product->id }}" class="carousel slide"
                                        data-ride="carousel">
                                        <div class="carousel-inner">
                                            @if (count($photos) > 0)
                                                @foreach ($photos as $key => $photo)
                                                    <div class="carousel-item {{ $loop->first ? 'active' : '' }}">
                                                        <img src="{{ $photo }}" class="d-block w-100"
                                                            alt="Product image">
                                                    </div>
                                                @endforeach
                                            @else
                                                <div class="carousel-item active">
                                                    <img src="{{ $firstImage }}" class="d-block w-100"
                                                        alt="Product image">
                                                </div>
                                            @endif
                                        </div>
                                        <a class="carousel-control-prev" href="#modalCarousel-{{ $product->id }}"
                                            role="button" data-slide="prev">
                                            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                            <span class="sr-only">Previous</span>
                                        </a>
                                        <a class="carousel-control-next" href="#modalCarousel-{{ $product->id }}"
                                            role="button" data-slide="next">
                                            <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                            <span class="sr-only">Next</span>
                                        </a>
                                    </div>
                                </div>
                                <div class="col-lg-5 col-md-12 col-sm-12 col-xs-12">
                                    <div class="quickview-content">
                                        <h2>{{ $product->title }}</h2>
                                        <div class="modal-thumbnail-gallery" id="modalThumbnails-{{ $product->id }}">
                                            @if (count($photos) > 1)
                                                @foreach ($photos as $index => $thumb)
                                                    <img src="{{ $thumb }}" class="modal-thumb-item"
                                                        data-carousel-id="modalCarousel-{{ $product->id }}"
                                                        data-index="{{ $index }}" alt="thumbnail">
                                                @endforeach
                                            @endif
                                        </div>
                                        <div class="add-to-cart" style="margin-top: 20px;">
                                            <a href="{{ route('product-detail', $product->slug) }}" class="btn">Lihat
                                                Detail Product</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    @endif --}}
    <!-- Modal end -->

@endsection
@push('styles')
    <style>
        .product-area.shop-sidebar.shop.section {
            padding-top: 30px !important;
            /* Mengurangi jarak atas menjadi 30px */
        }

        .shop .shop-top {
            padding-bottom: 20px !important;
            /* Mengurangi jarak bawah filter menjadi 20px */
        }

        .shop .single-shorter {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
        }

        .shop .single-shorter label {
            margin-bottom: 0;
        }

        .shop .single-shorter .sortBy {
            width: 180px;
            /* UBAH DARI MIN-WIDTH MENJADI WIDTH */
            height: 36px;
            border: 1px solid #ddd;
            border-radius: 3px;
            padding: 0 10px;
            background-position-x: 92%;
        }

        /* sorter */
        .btn-sort {
            padding: 8px 15px;
            border: 1px solid #ddd;
            border-radius: 3px;
            color: #333;
            background-color: #fff;
            text-decoration: none;
            margin-right: 5px;
        }

        .btn-sort.active {
            background-color: #10B5AD;
            color: #fff;
            border-color: #10B5AD;
        }

        .btn-sort:hover {
            color: #333;
        }

        .btn-sort.active:hover {
            color: #fff;
        }

        .filter_button {
            /* height:20px; */
            text-align: center;
            background: #10B5AD;
            padding: 8px 16px;
            margin-top: 10px;
            color: white;
        }

        .custom-pagination {
            display: inline-flex;
            align-items: center;
            padding: 2px;
        }

        .custom-pagination .page-item {
            margin: 0;
        }

        /* Selector dibuat lebih spesifik agar menimpa style bawaan */
        .view-mode .custom-pagination .page-link,
        .view-mode .custom-pagination .page-text {
            display: block;
            padding: 6px 10px;
            text-decoration: none;
            font-weight: bold;
            font-size: 14px;
            border: 1px solid transparent;
            background: transparent;
            color: #333;
        }

        /* .custom-pagination .page-item.active .page-link {} */

        .view-mode .custom-pagination .page-item.disabled .page-text {
            color: #ccc !important;
            cursor: not-allowed;
        }

        @media {}
    </style>
@endpush
@push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>
    <script>
        $(document).ready(function() {
            $(document).on('click', '#load-more-btn', function(e) {
                e.preventDefault();

                var btn = $(this);
                var url = btn.data('url');
                var originalText = btn.find('.text');
                var loadingText = btn.find('.loading');

                if (!url) return;

                // Efek Loading
                btn.prop('disabled', true);
                originalText.addClass('d-none');
                loadingText.removeClass('d-none');

                $.ajax({
                    url: url,
                    type: 'GET',
                    dataType: 'html',
                    success: function(response) {
                        // 1. Ambil produk baru dari respons HTML
                        var newProducts = $(response).find('#product-grid').html();

                        // 2. Tempelkan produk baru ke grid yang sekarang
                        $('#product-grid').append(newProducts);

                        // 3. Cek apakah masih ada halaman berikutnya di respons baru
                        var nextUrl = $(response).find('#load-more-btn').data('url');

                        if (nextUrl) {
                            // Update URL tombol untuk klik berikutnya
                            btn.data('url', nextUrl);
                            btn.prop('disabled', false);
                            originalText.removeClass('d-none');
                            loadingText.addClass('d-none');
                        } else {
                            // Jika tidak ada data lagi, sembunyikan tombol
                            btn.remove();
                        }
                    },
                    error: function() {
                        alert('Gagal memuat produk. Silakan coba lagi.');
                        btn.prop('disabled', false);
                        originalText.removeClass('d-none');
                        loadingText.addClass('d-none');
                    }
                });
            });
            if ($("#slider-range").length > 0) {
                const max_value = parseInt($("#slider-range").data('max')) || 500;
                const min_value = parseInt($("#slider-range").data('min')) || 0;
                const currency = $("#slider-range").data('currency') || '';
                let price_range = min_value + '-' + max_value;
                if ($("#price_range").length > 0 && $("#price_range").val()) {
                    price_range = $("#price_range").val().trim();
                }

                let price = price_range.split('-');
                $("#slider-range").slider({
                    range: true,
                    min: min_value,
                    max: max_value,
                    values: price,
                    slide: function(event, ui) {
                        $("#amount").val(currency + ui.values[0] + " -  " + currency + ui.values[1]);
                        $("#price_range").val(ui.values[0] + "-" + ui.values[1]);
                    }
                });
            }
            if ($("#amount").length > 0) {
                const m_currency = $("#slider-range").data('currency') || '';
                $("#amount").val(m_currency + $("#slider-range").slider("values", 0) +
                    "  -  " + m_currency + $("#slider-range").slider("values", 1));
            }
        });
    </script>
    {{-- <script>
        document.addEventListener('click', function(e) {

            if (e.target && e.target.classList.contains('modal-thumb')) {
                const thumb = e.target;
                const targetSelector = thumb.dataset
                    .target;
                /
                const mainImage = document.querySelector(targetSelector);
                if (mainImage) {
                    mainImage.src = thumb.src;
                }
            }
        });
    </script>

    <script>
        $(document).ready(function() {
            function syncThumbnails(carouselId, activeIndex) {
                var thumbnails = $('.modal-thumb-item[data-carousel-id="' + carouselId + '"]');
                thumbnails.removeClass('active');
                thumbnails.eq(activeIndex).addClass('active');
            }

            // Ketika modal ditampilkan
            $('.modal').on('shown.bs.modal', function(e) {
                var modal = $(this);
                var carousel = modal.find('.carousel');
                var carouselId = carousel.attr('id');

                // Inisialisasi carousel & sinkronisasi thumbnail untuk pertama kali
                carousel.carousel();
                var initialIndex = carousel.find('.carousel-item.active').index();
                syncThumbnails(carouselId, initialIndex);

                // Setiap kali slider berpindah (event dari Bootstrap Carousel)
                carousel.on('slide.bs.carousel', function(event) {
                    // event.to adalah index dari slide yang akan ditampilkan
                    syncThumbnails($(this).attr('id'), event.to);
                });
            });

            // Ketika thumbnail di-klik
            $('.modal-thumb-item').on('click', function() {
                var carouselId = '#' + $(this).data('carousel-id');
                var index = $(this).data('index');
                // Pindahkan slider Bootstrap ke gambar yang sesuai
                $(carouselId).carousel(index);
            });
        });
    </script> --}}
@endpush
