@extends('frontend.layouts.master')

@section('title', 'e || PRODUCT PAGE')

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
                            <li><a href="{{ route('home') }}">Home<i class="ti-arrow-right"></i></a></li>
                            <li class="active"><a href="javascript:void(0);">Shop List</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- End Breadcrumbs -->
    <form action="{{ route('shop.filter') }}" method="POST">
        @csrf
        <!-- Product Style 1 -->
        <section class="product-area shop-sidebar shop-list shop section">
            <div class="container">
                <div class="row">
                    <div class="col-lg-3 col-md-4 col-12">
                        <div class="shop-sidebar">
                            <!-- Single Widget -->
                            <div class="single-widget category">
                                <h3 class="title">Categories</h3>
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
                            <div class="single-widget recent-post">
                                <h3 class="title">Product Terbaru</h3>
                                {{-- {{dd($recent_products)}} --}}
                                @foreach ($recent_products as $product)
                                    <!-- Single Post -->
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
                                    <!-- End Single Post -->
                                @endforeach
                            </div>
                            <!--/ End Single Widget -->
                        </div>
                    </div>
                    <div class="col-lg-9 col-md-8 col-12">
                        <div class="row">
                            <div class="col-12">
                                <!-- Shop Top -->
                                <div class="shop-top">
                                    <div class="shop-shorter">
                                        <div class="single-shorter">
                                            <label>Urutkan :</label>
                                            @php
                                                // Helper untuk membuat URL dengan parameter sort baru tapi tetap mempertahankan filter lain
                                                $build_url = fn($sortBy) => request()->fullUrlWithQuery([
                                                    'sortBy' => $sortBy,
                                                ]);
                                                $current_sort = request()->query('sortBy', 'latest'); // Default sort adalah terbaru
                                            @endphp

                                            {{-- Tampilkan Tombol-tombol terlebih dahulu --}}
                                            <a href="{{ route('product-lists') }}"
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
                                    <div class="view-mode">
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
                        <div class="row">
                            @if (count($products))
                                @foreach ($products as $product)
                                    {{-- {{$product}} --}}
                                    <!-- Start Single List -->
                                    <div class="col-12">
                                        <div class="row">
                                            <div class="col-lg-4 col-md-6 col-sm-6">
                                                <div class="single-product">
                                                    <div class="product-img">
                                                        <a href="{{ route('product-detail', $product->slug) }}">
                                                            @php
                                                                $photo = explode(',', $product->photo);
                                                            @endphp
                                                            <img class="default-img" src="{{ $photo[0] }}"
                                                                alt="{{ $photo[0] }}">
                                                            <img class="hover-img" src="{{ $photo[0] }}"
                                                                alt="{{ $photo[0] }}">
                                                        </a>
                                                        <div class="button-head">
                                                            <div class="product-action-2">
                                                                <a href="{{ route('product-detail', $product->slug) }}">Lihat
                                                                    Product</a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-8 col-md-6 col-12">
                                                <div class="list-content">
                                                    <div class="product-content">
                                                        <div class="product-price">
                                                            @php
                                                                $firstVariant = $product->variants->first();
                                                                $original_price = 0;
                                                                $discount_percent = 0;
                                                                $final_price = 0;

                                                                if ($firstVariant) {
                                                                    $original_price = $firstVariant->price;
                                                                    $discount_percent = $firstVariant->discount ?? 0;
                                                                    $final_price =
                                                                        $original_price -
                                                                        ($original_price * $discount_percent) / 100;
                                                                }
                                                            @endphp

                                                            {{-- Cek apakah ada diskon --}}
                                                            @if ($discount_percent > 0)
                                                                <span
                                                                    class="text-danger"><b>Rp{{ number_format($final_price, 0, ',', '.') }}</b></span>
                                                                <del
                                                                    class="text-muted ml-2">Rp{{ number_format($original_price, 0, ',', '.') }}</del>
                                                            @else
                                                                <span>Rp{{ number_format($original_price, 0, ',', '.') }}</span>
                                                            @endif
                                                        </div>
                                                        <h3 class="title"><a
                                                                href="{{ route('product-detail', $product->slug) }}">{{ $product->title }}</a>
                                                        </h3>
                                                        {{-- <p>{!! html_entity_decode($product->summary) !!}</p> --}}
                                                    </div>
                                                    <p class="des pt-2">{!! html_entity_decode($product->summary) !!}</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- End Single List -->
                                @endforeach
                            @else
                                <h4 class="text-warning" style="margin:100px auto;">Produk tidak tersedia.</h4>
                            @endif
                        </div>
                        <div class="row">
                            <div class="col-md-12 justify-content-center d-flex">
                                {{ $products->appends(request()->query())->links('pagination::bootstrap-5') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!--/ End Product Style 1  -->
    </form>
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
            min-width: 180px;
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

        .custom-pagination .page-item.active .page-link {}

        /* Selector dibuat lebih spesifik */
        .view-mode .custom-pagination .page-item.disabled .page-text {
            color: #ccc !important;
            /* Tambahkan !important untuk memastikan */
            cursor: not-allowed;
        }

        /* modal */
        /* .quickview-content {
                                                            padding: 20px;
                                                        }

                                                        .modal-thumbnail-gallery {
                                                            margin-top: 15px;
                                                            display: grid;
                                                            grid-template-columns: repeat(auto-fill, minmax(60px, 1fr));
                                                            gap: 8px;
                                                        }

                                                        .modal-thumb-item {
                                                            width: 100%;
                                                            height: 60px;
                                                            object-fit: cover;
                                                            cursor: pointer;
                                                            border: 2px solid #eee;
                                                            border-radius: 5px;
                                                            transition: border-color .2s ease;
                                                        } */

        /* .modal-thumb-item:hover,
                                                        .modal-thumb-item.active {
                                                            border-color: #F7941D;

                                                        } */

        /* Style untuk panah navigasi Owl Carousel */
        /* .modal-product-slider .owl-nav {
                                                            position: absolute;
                                                            top: 50%;
                                                            width: 100%;
                                                            transform: translateY(-50%);
                                                            display: flex;
                                                            justify-content: space-between;
                                                            pointer-events: none;

                                                        } */

        /* .modal-product-slider .owl-nav button {
                                                            pointer-events: all;
                                                            background: rgba(0, 0, 0, 0.4) !important;
                                                            color: white !important;
                                                            border-radius: 50% !important;
                                                            width: 40px;
                                                            height: 40px;
                                                            font-size: 20px !important;
                                                            margin: 0 10px;
                                                        } */

        /* .modal-product-slider .owl-nav button:hover {
                                                            background: rgba(0, 0, 0, 0.7) !important;
                                                        }

                                                        .modal-dialog {
                                                            max-width: 950px !important;
                                                            margin: auto;
                                                            margin-top: 50px;

                                                        } */
    </style>
@endpush
@push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>
    <script>
        $(document).ready(function() {
            /*----------------------------------------------------*/
            /*  Jquery Ui slider js
            /*----------------------------------------------------*/
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
        })
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

            $('.modal').on('shown.bs.modal', function(e) {
                var modal = $(this);
                var carousel = modal.find('.carousel');
                var carouselId = carousel.attr('id');

                carousel.carousel();
                var initialIndex = carousel.find('.carousel-item.active').index();
                syncThumbnails(carouselId, initialIndex);

                carousel.on('slide.bs.carousel', function(event) {
                    syncThumbnails($(this).attr('id'), event.to);
                });
            });

            $('.modal-thumb-item').on('click', function() {
                var carouselId = '#' + $(this).data('carousel-id');
                var index = $(this).data('index');
                $(carouselId).carousel(index);
            });
        });
    </script> --}}
@endpush
