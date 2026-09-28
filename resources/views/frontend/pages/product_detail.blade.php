@extends('frontend.layouts.master')

@section('meta')
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name='copyright' content=''>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    @php
        // Logika baru: Ambil foto dari string yang dipisah '|'
        $__photos_arr = array_values(array_filter(array_map('trim', explode('|', $product_detail->photo ?? ''))));
        $__first_photo_path = $__photos_arr[0] ?? null;
        // Logika baru: Gunakan asset('storage/...')
        $__og = $__first_photo_path ? asset('storage/' . $__first_photo_path) : '';
    @endphp

    <meta name="keywords" content="online shop, purchase, cart, ecommerce site, best online shopping">
    <meta name="description" content="{{ $product_detail->summary ?? '' }}">
    <meta property="og:url" content="{{ route('product-detail', $product_detail->slug) }}">
    <meta property="og:type" content="article">
    <meta property="og:title" content="{{ $product_detail->title ?? '' }}">
    <meta property="og:image" content="{{ $__og }}">
    <meta property="og:description" content="{{ strip_tags($product_detail->summary ?? '') }}">
@endsection

@section('title', 'Detail Produk')

@section('main-content')
    @once
        @include('frontend.layouts.wa_fb')
    @endonce

    <div class="breadcrumbs">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="bread-inner">
                        <ul class="bread-list">
                            <li><a href="{{ route('home') }}">Beranda<i class="ti-arrow-right"></i></a></li>
                            <li><a href="{{ route('product-grids') }}">Produk<i class="ti-arrow-right"></i></a></li>
                            <li class="active"><a href="#">Detail Produk</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <section class="shop single section">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="row">
                        <div class="col-lg-6 col-12">
                            <div class="product-gallery">
                                @php
                                    // Logika BARU: delimiter '|'
                                    $photos = array_values(
                                        array_filter(array_map('trim', explode('|', $product_detail->photo ?? ''))),
                                    );

                                    // Logika BARU: asset('storage/...')
                                    $firstImageFile = $photos[0] ?? null;
                                    $firstImage = $firstImageFile
                                        ? asset('storage/' . $firstImageFile)
                                        : 'https://via.placeholder.com/600x600?text=No+Image';

                                    // Logika BARU: Video opsional
                                    $videoPath = $product_detail->video
                                        ? asset('storage/' . $product_detail->video)
                                        : null;

                                    $videoThumbnailUrl = $product_detail->video_thumbnail
                                        ? asset('storage/' . $product_detail->video_thumbnail) // 1. Pakai thumbnail kustom jika ada
                                        : $firstImage; // 2. Jika tidak, pakai foto utama produk

                                @endphp

                                {{-- Tampilan Utama (Foto & Video) --}}
                                <div class="mb-3">
                                    <div id="pd-main-wrapper">

                                        {{-- Foto Utama --}}
                                        <img id="pd-main-photo" src="{{ $firstImage }}"
                                            data-fallback-src="{{ $firstImage }}" {{-- PERUBAHAN: Sembunyikan foto jika video ada --}}
                                            style="width:100%;height:auto;border:1px solid #eee;border-radius:8px;display:{{ $videoPath ? 'none' : 'block' }};">

                                        {{-- Video Player --}}
                                        @if ($videoPath)
                                            {{-- PERUBAHAN: Tampilkan video jika ada, dan tambahkan autoplay muted --}}
                                            <video id="pd-main-video" src="{{ $videoPath }}" controls autoplay muted
                                                loop
                                                style="width:100%;height:auto;border:1px solid #eee;border-radius:8px;display:{{ $videoPath ? 'block' : 'none' }};">
                                            </video>
                                        @endif
                                    </div>
                                </div>

                                {{-- Thumbnails (FOTO & VIDEO) --}}
                                @if (count($photos) > 0 || $videoPath)
                                    <div class="d-flex flex-wrap" style="gap:10px;">

                                        {{-- PERBAIKAN: Thumbnail Video dengan background --}}
                                        @if ($videoPath)
                                            <div class="pd-thumb-video" data-type="video" data-src="{{ $videoPath }}"
                                                style="
                                                width:80px;
                                                height:80px;
                                                cursor:pointer;
                                                border:1px solid #eee;
                                                border-radius:6px;
                                                position:relative;
                                                overflow:hidden;

                                                /* --- INI TAMBAHANNYA --- */
                                                background-image: url('{{ $firstImage }}');
                                                background-size: cover;
                                                background-position: center;
                                            ">

                                                {{-- Icon play-nya kita perkecil & tengahkan --}}
                                                <img src="https://img.icons8.com/ios-filled/100/play-button-circled.png"
                                                    alt="Play Video"
                                                    style="
                                                    width: 40px;
                                                    height: 40px;
                                                    opacity: 0.85;
                                                    position: absolute;
                                                    top: 50%;
                                                    left: 50%;
                                                    transform: translate(-50%, -50%);
                                                ">
                                            </div>
                                        @endif

                                        {{-- Thumbnail Foto (Sama seperti sebelumnya) --}}
                                        @foreach ($photos as $thumb)
                                            @php $tUrl = asset('storage/'.$thumb); @endphp
                                            <img class="pd-thumb" src="{{ $tUrl }}" data-src="{{ $tUrl }}"
                                                alt="thumb" loading="lazy"
                                                style="width:80px;height:80px;object-fit:cover;border:1px solid #eee;border-radius:6px;cursor:pointer;">
                                        @endforeach

                                    </div>
                                @endif
                            </div>
                        </div>


                        <div class="col-lg-6 col-12">
                            <div class="product-des">
                                <div class="short">
                                    <h4>{{ $product_detail->title }}</h4>

                                    @php
                                        // siapkan varian aktif
                                        $variants = $product_detail->variants ?? collect();
                                        $firstVariant = $variants->first();
                                        // fallback base price jika memang ada (opsional)
                                        $baseSell = $firstVariant->price ?? ($product_detail->price ?? 0);
                                        $baseStrike = $firstVariant->original_price ?? null;
                                        $baseDisc = $firstVariant->discount_percent ?? null;
                                    @endphp

                                    <div class="d-flex align-items-baseline" style="gap:10px;">
                                        <p class="price mb-0">
                                            <span id="pd-price" data-fallback="{{ $baseSell }}">
                                                {{ $baseSell ? 'Rp' . number_format($baseSell, 0, ',', '.') : '-' }}
                                            </span>
                                        </p>

                                        <p class="mb-0">
                                            <small id="pd-strike" class="text-muted"
                                                style="{{ $baseStrike ? '' : 'display:none;' }}">
                                                <s id="pd-strike-val">
                                                    {{ $baseStrike ? 'Rp' . number_format($baseStrike, 0, ',', '.') : '' }}
                                                </s>
                                            </small>
                                        </p>

                                        <p class="mb-0">
                                            <span id="pd-disc-badge" class="badge badge-danger"
                                                style="{{ $baseDisc ? '' : 'display:none;' }}">
                                                -<span id="pd-disc-val">{{ $baseDisc }}</span>%
                                            </span>
                                        </p>
                                    </div>

                                    <p class="description mt-2">{!! $product_detail->summary !!}</p>
                                </div>

                                <div class="product-buy">
                                    <form action="{{ route('single-add-to-cart') }}" method="POST">
                                        @csrf

                                        <input type="hidden" name="slug" value="{{ $product_detail->slug }}">
                                        <input type="hidden" name="variant_id" id="variant_id_hidden"
                                            value="{{ $firstVariant->id ?? '' }}">

                                        {{-- VARIANT RADIO CARDS --}}
                                        @if ($variants->count())
                                            <div class="product-options mt-3">
                                                <label class="d-block mb-2 font-weight-bold">Pilih Varian:</label>
                                                <div class="variant-radio-group">
                                                    @foreach ($variants as $v)
                                                        @php
                                                            $checked = $loop->first ? 'checked' : '';
                                                            $sell = (int) ($v->price ?? 0);
                                                            $strike = $v->original_price;
                                                            $disc = $v->discount_percent;

                                                            // Logika BARU: path foto varian
                                                            $vPhotoPath = $v->photo ?: null;
                                                            $vPhotoUrl = $vPhotoPath
                                                                ? asset('storage/' . $vPhotoPath)
                                                                : $firstImage; // $firstImage sudah jadi URL (dgn asset)
                                                        @endphp

                                                        <label class="variant-radio-card">
                                                            <input type="radio" name="variant_card"
                                                                value="{{ $v->id }}" {{ $checked }}
                                                                data-id="{{ $v->id }}"
                                                                data-price="{{ $sell }}"
                                                                data-stock="{{ (int) $v->stock }}"
                                                                data-photo="{{ $vPhotoUrl }}" {{-- URL lengkap --}}
                                                                data-original="{{ $strike ?? '' }}"
                                                                data-discount="{{ $disc ?? '' }}">

                                                            <div class="variant-radio-card__body">
                                                                <div class="d-flex align-items-center" style="gap:10px;">
                                                                    <img src="{{ $vPhotoUrl }}" alt="vimg"
                                                                        class="variant-radio-card__thumb" loading="lazy">
                                                                    <div>
                                                                        <div class="variant-radio-card__title">
                                                                            {{ $v->variant_name }}</div>
                                                                        <div class="variant-radio-card__price">
                                                                            Rp{{ number_format($sell, 0, ',', '.') }}
                                                                            @if (!is_null($strike) && $strike > 0)
                                                                                <small
                                                                                    class="text-muted"><s>Rp{{ number_format($strike, 0, ',', '.') }}</s></small>
                                                                            @endif
                                                                            @if (!is_null($disc) && $disc > 0)
                                                                                <span
                                                                                    class="badge badge-danger ml-1">-{{ $disc }}%</span>
                                                                            @endif
                                                                        </div>
                                                                        <div class="text-muted" style="font-size:12px;">
                                                                            Stok: {{ (int) $v->stock }}</div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </label>
                                                    @endforeach
                                                </div>

                                            </div>
                                        @endif
                                        <div class="quantity mt-3">
                                            <h6>Quantity :</h6>
                                            <div class="input-group">
                                                <div class="button minus">
                                                    <button type="button" class="btn btn-primary btn-number"
                                                        disabled="disabled" data-type="minus" data-field="quant[1]">
                                                        <i class="ti-minus"></i>
                                                    </button>
                                                </div>
                                                <input type="text" name="quant[1]" class="input-number"
                                                    data-min="1" data-max="1000" value="1" id="quantity">
                                                <div class="button plus">
                                                    <button type="button" class="btn btn-primary btn-number"
                                                        data-type="plus" data-field="quant[1]">
                                                        <i class="ti-plus"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>

                                        @php
                                            $baseStock = $firstVariant->stock ?? ($product_detail->stock ?? 0);
                                        @endphp

                                        <div class="add-to-cart mt-3 ">
                                            <button type="submit" id="btn-add-to-cart" class="btn"
                                                style="border-radius: 20px; background: #092327;"
                                                {{ $baseStock > 0 ? '' : 'disabled' }}>Masukan Keranjang</button>

                                        </div>
                                        <p class="availability mt-2">
                                            Stok :
                                            <span id="stock-badge"
                                                class="badge {{ $baseStock > 0 ? 'badge-success' : 'badge-danger' }}">
                                                {{ $baseStock }}
                                            </span>
                                        </p>
                                    </form>

                                    @if ($product_detail->cat_info)
                                        <p class="cat mt-3">Kategori :
                                            <a href="{{ route('product-cat', $product_detail->cat_info['slug']) }}">
                                                {{ $product_detail->cat_info['title'] }}
                                            </a>
                                        </p>
                                    @else
                                        <p class="cat mt-3">Kategori :
                                            <span class="text-muted font-italic">Kategori telah dihapus</span>
                                        </p>
                                    @endif
                                    @if ($product_detail->sub_cat_info)
                                        <p class="cat mt-1">Sub Kategori :
                                            @if ($product_detail->cat_info)
                                                <a
                                                    href="{{ route('product-sub-cat', [$product_detail->cat_info['slug'], $product_detail->sub_cat_info['slug']]) }}">
                                                    {{ $product_detail->sub_cat_info['title'] }}
                                                </a>
                                            @else
                                                {{-- Jika parent kategori terhapus, sub-kategori tidak bisa di-klik agar URL tidak error --}}
                                                <span
                                                    class="text-muted">{{ $product_detail->sub_cat_info['title'] }}</span>
                                            @endif
                                        </p>
                                    @endif
                                    <div class="d-flex align-items-center mt-4">
                                        <p class="cat m-0 mr-3">Bagikan:</p>
                                        <div class="share-buttons">
                                            @php
                                                $shareUrl = urlencode(route('product-detail', $product_detail->slug));
                                                $shareText = urlencode($product_detail->title);
                                            @endphp

                                            {{-- Salin Link --}}
                                            <a href="javascript:void(0);" id="copy-link-btn" class="share-btn"
                                                data-link="{{ route('product-detail', $product_detail->slug) }}"
                                                data-toggle="tooltip" data-placement="top" title="Salin Link">
                                                <i class="fa fa-clone"></i>
                                            </a>

                                            {{-- whatsapp --}}
                                            <a href="https://api.whatsapp.com/send?text={{ $shareText }}%20{{ $shareUrl }}"
                                                target="_blank" class="share-btn" title="Bagikan">
                                                <i class="fa fa-whatsapp"></i>
                                            </a>

                                            {{-- Facebook --}}
                                            <a href="https://www.facebook.com/sharer/sharer.php?u={{ $shareUrl }}"
                                                target="_blank" class="share-btn">
                                                <i class="fa fa-facebook"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="product-info">
                                <div class="nav-main">
                                    <ul class="nav nav-tabs" id="myTab" role="tablist">
                                        <li class="nav-item"><a class="nav-link active" data-toggle="tab"
                                                href="#description" role="tab">Keterangan Produk</a></li>
                                        {{-- <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#reviews"
                                                role="tab">Reviews</a></li> --}}
                                    </ul>
                                </div>

                                <div class="tab-content" id="myTabContent">
                                    <div class="tab-pane fade show active" id="description" role="tabpanel">
                                        <div class="tab-single">
                                            <div class="row">
                                                <div class="col-12">
                                                    <div class="single-des">
                                                        <p>{!! $product_detail->description !!}</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="tab-pane fade" id="reviews" role="tabpanel">
                                        <div class="tab-single review-panel">
                                            <div class="row">
                                                <div class="col-12">
                                                    <div class="comment-review">
                                                        <div class="add-review">
                                                            <h5>Add A Review</h5>
                                                            <p>Your email address will not be published. Required fields are
                                                                marked</p>
                                                        </div>
                                                        <h4>Your Rating <span class="text-danger">*</span></h4>
                                                        <div class="review-inner">
                                                            @auth
                                                                <form class="form" method="post"
                                                                    action="{{ route('review.store', $product_detail->slug) }}">
                                                                    @csrf
                                                                    <div class="row">
                                                                        <div class="col-lg-12 col-12">
                                                                            <div class="rating_box">
                                                                                <div class="star-rating">
                                                                                    <div class="star-rating__wrap">
                                                                                        <input class="star-rating__input"
                                                                                            id="star-rating-5" type="radio"
                                                                                            name="rate" value="5">
                                                                                        <label
                                                                                            class="star-rating__ico fa fa-star-o"
                                                                                            for="star-rating-5"
                                                                                            title="5 out of 5 stars"></label>
                                                                                        <input class="star-rating__input"
                                                                                            id="star-rating-4" type="radio"
                                                                                            name="rate" value="4">
                                                                                        <label
                                                                                            class="star-rating__ico fa fa-star-o"
                                                                                            for="star-rating-4"
                                                                                            title="4 out of 5 stars"></label>
                                                                                        <input class="star-rating__input"
                                                                                            id="star-rating-3" type="radio"
                                                                                            name="rate" value="3">
                                                                                        <label
                                                                                            class="star-rating__ico fa fa-star-o"
                                                                                            for="star-rating-3"
                                                                                            title="3 out of 5 stars"></label>
                                                                                        <input class="star-rating__input"
                                                                                            id="star-rating-2" type="radio"
                                                                                            name="rate" value="2">
                                                                                        <label
                                                                                            class="star-rating__ico fa fa-star-o"
                                                                                            for="star-rating-2"
                                                                                            title="2 out of 5 stars"></label>
                                                                                        <input class="star-rating__input"
                                                                                            id="star-rating-1" type="radio"
                                                                                            name="rate" value="1">
                                                                                        <label
                                                                                            class="star-rating__ico fa fa-star-o"
                                                                                            for="star-rating-1"
                                                                                            title="1 out of 5 stars"></label>
                                                                                        @error('rate')
                                                                                            <span
                                                                                                class="text-danger">{{ $message }}</span>
                                                                                        @enderror
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-lg-12 col-12">
                                                                            <div class="form-group">
                                                                                <label>Write a review</label>
                                                                                <textarea name="review" rows="6" placeholder=""></textarea>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-lg-12 col-12">
                                                                            <div class="form-group button5">
                                                                                <button type="submit"
                                                                                    class="btn">Submit</button>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </form>
                                                            @else
                                                                <p class="text-center p-5">
                                                                    You need to <a href="{{ route('login.form') }}"
                                                                        style="color:rgb(54, 54, 204)">Login</a> OR
                                                                    <a style="color:blue"
                                                                        href="{{ route('register.form') }}">Register</a>
                                                                </p>
                                                            @endauth
                                                        </div>
                                                    </div>

                                                    <div class="ratting-main">
                                                        <div class="avg-ratting">
                                                            <h4>{{ ceil($product_detail->getReview->avg('rate')) }}
                                                                <span>(Overall)</span>
                                                            </h4>
                                                            <span>Based on {{ $product_detail->getReview->count() }}
                                                                Comments</span>
                                                        </div>
                                                        @foreach ($product_detail['getReview'] as $data)
                                                            <div class="single-rating">
                                                                <div class="rating-author">
                                                                    @if ($data->user_info['photo'])
                                                                        <img src="{{ $data->user_info['photo'] }}"
                                                                            alt="user"
                                                                            loading="lazy>
@else
<img src="{{ asset('backend/img/avatar.png') }}"
                                                                            alt="Profile.jpg"
                                                                            loading="lazy>
@endif
                                                                </div>
                                                                <div class="rating-des">
                                                                        <h6>{{ $data->user_info['name'] }}</h6>
                                                                        <div class="ratings">
                                                                            <ul class="rating">
                                                                                @for ($i = 1; $i <= 5; $i++)
                                                                                    @if ($data->rate >= $i)
                                                                                        <li><i class="fa fa-star"></i></li>
                                                                                    @else
                                                                                        <li><i class="fa fa-star-o"></i>
                                                                                        </li>
                                                                                    @endif
                                                                                @endfor
                                                                            </ul>
                                                                            <div class="rate-count">
                                                                                (<span>{{ $data->rate }}</span>)</div>
                                                                        </div>
                                                                        <p>{{ $data->review }}</p>
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>

                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <div class="product-area most-popular related-product section">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="section-title">
                        {{-- Logika IF: Jika ada rekomendasi Apriori, ganti judulnya --}}
                        @if (isset($recommendedVariants) && $recommendedVariants->count() > 0)
                            <h2> Rekomendasi Produk Sering Dibeli Bersama</h2>
                        @else
                            <h2>Product Lainnya</h2>
                        @endif
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="owl-carousel popular-slider">
                        @php
                            // Cek apakah ada data apriori (berupa Varian)
                            $hasApriori = isset($recommendedVariants) && $recommendedVariants->count() > 0;
                            // Jika Apriori kosong, fallback ke relasi produk kategori yang sama (berupa Product)
                            $loopItems = $hasApriori ? $recommendedVariants : $product_detail->rel_prods;
                        @endphp

                        @foreach ($loopItems as $item)
                            @php
                                // Siapkan variabel agar fleksibel antara data Varian atau data Produk (Fallback)
                                if ($hasApriori) {
                                    $prodSlug = $item->product->slug;
                                    $title = $item->product->title . ' - ' . $item->variant_name;
                                    $price = $item->price;
                                    $discount = $item->discount_percent;
                                    // Gunakan foto varian jika ada, jika tidak pakai foto produk pertama
                                    $photoPath =
                                        $item->photo ?:
                                        ($item->product->photo
                                            ? explode('|', $item->product->photo)[0]
                                            : '');
                                    // Tambahkan ID varian di akhir URL
                                    $urlTarget = route('product-detail', $prodSlug) . '?variant=' . $item->id;
                                } else {
                                    $prodSlug = $item->slug;
                                    $title = $item->title;
                                    $price = $item->price;
                                    $discount = $item->discount;
                                    $photoPath = $item->photo ? explode('|', $item->photo)[0] : '';
                                    $urlTarget = route('product-detail', $prodSlug);
                                }

                                $after_discount = $price - (($discount ?? 0) * $price) / 100;
                            @endphp

                            {{-- Jangan tampilkan jika slug sama dengan produk yang sedang dibuka (untuk fallback) --}}
                            @if ($prodSlug !== $product_detail->slug)
                                <div class="single-product">
                                    <div class="product-img">
                                        <a href="{{ $urlTarget }}">
                                            <img class="default-img"
                                                src="{{ $photoPath ? asset('storage/' . $photoPath) : 'https://via.placeholder.com/300' }}"
                                                alt="img" loading="lazy">
                                            <img class="hover-img"
                                                src="{{ $photoPath ? asset('storage/' . $photoPath) : 'https://via.placeholder.com/300' }}"
                                                alt="img" loading="lazy">
                                            @if (!is_null($discount) && $discount > 0)
                                                <span class="price-dec">{{ $discount }} % Off</span>
                                            @endif
                                        </a>
                                    </div>
                                    <div class="product-content">
                                        <h3 style="font-size: 14px; line-height:1.4;"><a
                                                href="{{ $urlTarget }}">{{ $title }}</a></h3>
                                        <div class="product-price">
                                            @if (!is_null($price))
                                                @if (!is_null($discount) && $discount > 0)
                                                    <span class="old"
                                                        style="font-size: 12px;">Rp{{ number_format($price, 0, ',', '.') }}</span>
                                                @endif
                                                <span
                                                    style="font-weight: bold; color: #0d6efd;">Rp{{ number_format($after_discount, 0, ',', '.') }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        @media(min-width:576px) {
            .variant-radio-group {
                grid-template-columns: 1fr 1fr;
            }
        }

        .variant-radio-card {
            position: relative;
            display: block;
            cursor: pointer;
        }

        .variant-radio-card input[type="radio"] {
            position: absolute;
            inset: 0;
            opacity: 0;
        }

        .variant-radio-card__body {
            border: 1px solid #e5e7eb;
            border-radius: 10px;
            padding: 10px;
            transition: all .2s ease;
            background: #fff;
        }

        .variant-radio-card:hover .variant-radio-card__body {
            border-color: #b8c2cc;
            box-shadow: 0 1px 8px rgba(0, 0, 0, .06);
        }

        .variant-radio-card input[type="radio"]:checked+.variant-radio-card__body {
            border-color: #0d6efd;
            box-shadow: 0 0 0 2px rgba(13, 110, 253, .15);
        }

        .variant-radio-card__thumb {
            width: 56px;
            height: 56px;
            object-fit: cover;
            border-radius: 8px;
            border: 1px solid #eee;
        }

        .variant-radio-card__title {
            font-weight: 600;
            line-height: 1.2;
        }

        .variant-radio-card__price {
            font-size: 14px;
        }

        .share-buttons {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .share-btn {
            display: flex;
            justify-content: center;
            align-items: center;
            width: 38px;
            height: 38px;
            border-radius: 8px;
            background-color: #f0f2f5;
            color: #65676b;
            font-size: 20px;
            text-decoration: none;
            transition: background-color 0.2s;
        }

        .share-btn:hover {
            background-color: #e4e6e9;
            color: #333;
        }
    </style>
@endpush

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const mainImg = document.getElementById('pd-main-photo');
            const mainVideo = document.getElementById('pd-main-video');
            document.querySelectorAll('.pd-thumb-video').forEach(function(el) {
                el.addEventListener('click', function() {

                    const src = this.dataset.src;

                    if (mainImg) mainImg.style.display = 'none';
                    if (mainVideo) {
                        mainVideo.src = src;
                        mainVideo.style.display = 'block';
                        mainVideo.play(); // Putar otomatis
                    }
                });
            });


            // ============================================================
            //  KLIK THUMBNAIL FOTO
            // ============================================================
            document.querySelectorAll('.pd-thumb').forEach(function(img) {
                img.addEventListener('click', function() {

                    if (mainVideo) {
                        mainVideo.pause();
                        mainVideo.style.display = 'none';
                    }
                    if (mainImg) {
                        mainImg.src = this.dataset.src;
                        mainImg.style.display = 'block';
                    }
                });
            });


            // ============================================================
            // LOGIKA VARIAN
            // ============================================================
            function fmtIDR(n) {
                const x = Number(n || 0);
                return new Intl.NumberFormat('id-ID', {
                    style: 'currency',
                    currency: 'IDR',
                    maximumFractionDigits: 0
                }).format(x);
            }

            function syncByVariantInput(inp, isInitialLoad = false) {
                if (!inp) return;

                const sell = inp.dataset.price || '';
                const stock = Number(inp.dataset.stock || 0);
                const original = inp.dataset.original || '';
                const disc = inp.dataset.discount || '';
                const vPhoto = inp.dataset.photo || ''; // URL Foto varian

                const priceEl = document.getElementById('pd-price');
                const strikeBox = document.getElementById('pd-strike');
                const strikeVal = document.getElementById('pd-strike-val');
                const discBox = document.getElementById('pd-disc-badge');
                const discVal = document.getElementById('pd-disc-val');
                const stockEl = document.getElementById('stock-badge');
                const btnAdd = document.getElementById('btn-add-to-cart');
                const hidVar = document.getElementById('variant_id_hidden');

                // Elemen media (foto & video)
                const mainImg = document.getElementById('pd-main-photo');
                const mainVideo = document.getElementById('pd-main-video');

                // Ambil foto fallback dari atribut data-fallback-src di img utama
                const fallbackPhoto = mainImg ? mainImg.dataset.fallbackSrc : '';

                // --- UPDATE UI (Harga, Stok, Diskon) ---
                if (priceEl) priceEl.textContent = sell ? fmtIDR(sell) : fmtIDR(priceEl.dataset.fallback);


                if (original && Number(original) > 0) {
                    if (strikeBox) strikeBox.style.display = '';
                    if (strikeVal) strikeVal.textContent = fmtIDR(original);
                } else {
                    if (strikeBox) strikeBox.style.display = 'none';
                    if (strikeVal) strikeVal.textContent = '';
                }

                if (disc && Number(disc) > 0) {
                    if (discBox) discBox.style.display = '';
                    if (discVal) discVal.textContent = Number(disc);
                } else {
                    if (discBox) discBox.style.display = 'none';
                    if (discVal) discVal.textContent = '';
                }

                if (stockEl) {
                    stockEl.textContent = stock;
                    stockEl.classList.remove('badge-success', 'badge-danger');
                    stockEl.classList.add(stock > 0 ? 'badge-success' : 'badge-danger');
                }

                if (btnAdd) btnAdd.disabled = (stock <= 0);
                if (hidVar) hidVar.value = inp.value;

                // --- UPDATE MEDIA (Foto/Video) ---
                // Saat ganti varian, SELALU kembali ke mode foto
                // TAPI, jangan jalankan ini saat inisialisasi halaman (isInitialLoad)
                if (isInitialLoad === false) {
                    if (mainVideo) {
                        mainVideo.pause();
                        mainVideo.style.display = 'none';
                    }

                    if (mainImg) {
                        mainImg.style.display = 'block';
                        // Gunakan foto varian JIKA ADA, jika tidak, gunakan foto fallback produk
                        mainImg.src = vPhoto ? vPhoto : fallbackPhoto;
                    }
                }
            }

            // EVENT LISTENER untuk radio varian
            document.querySelectorAll('input[name="variant_card"]').forEach(r => {
                r.addEventListener('change', function() {
                    syncByVariantInput(this);
                });
            });
            const urlParams = new URLSearchParams(window.location.search);
            const requestedVariant = urlParams.get('variant');
            if (requestedVariant) {
                // Cari radio button yang memiliki value sesuai parameter URL
                const targetRadio = document.querySelector(
                    `input[name="variant_card"][value="${requestedVariant}"]`);
                if (targetRadio) {
                    targetRadio.checked = true; // Pilih secara otomatis
                }
            }

            // INISIALISASI saat halaman dimuat
            const firstChecked = document.querySelector('input[name="variant_card"]:checked');
            if (firstChecked) {
                const isFromUrl = requestedVariant !== null;
                syncByVariantInput(firstChecked, !isFromUrl);
            }
        });
    </script>
    <script>
        $(document).ready(function() {
            $('[data-toggle="tooltip"]').tooltip();

            $('#copy-link-btn').on('click', function(e) {
                e.preventDefault();
                var link = $(this).data('link');
                var tempInput = document.createElement("textarea");
                tempInput.style.position = "absolute";
                tempInput.style.left = "-9999px";
                tempInput.value = link;
                document.body.appendChild(tempInput);
                tempInput.select();
                document.execCommand("copy");
                document.body.removeChild(tempInput);
                $(this).attr('data-original-title', 'Link disalin!')
                    .tooltip('show');
                setTimeout(() => {
                    $(this).attr('data-original-title', 'Salin Link');
                }, 2000);
            });
        });
    </script>
@endpush
