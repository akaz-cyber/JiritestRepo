{{-- ini halaman untuk mengatur tampilan navbar --}}
<header class="header shop">
    {{-- alert promo terkini atau pengumuman penting --}}
    @if (isset($activeAnnouncement) && $activeAnnouncement)
        <div class="alert alert-sticky w-100 alert-dismissible fade show text-center mb-0 rounded-0" role="alert"
            style="background-color: #1a5e4a; color: white;">

            <div class="css-marquee-container">
                <div class="css-marquee-content">
                    {{ strip_tags($activeAnnouncement->content) }}
                </div>
            </div>

            <button type="button" class="close" data-dismiss="alert" aria-label="Close"
                style="color: white; opacity: 0.7;">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif
    {{-- end alert --}}


    {{-- tampilan navbar mobile --}}
    <div class="middle-inner d-lg-none mobile-header-wrapper">
        <div class="container d-flex justify-content-between align-items-center py-2">
            <div class="mobile-nav"></div>
            <div class="logo">
                @php
                    $settings = DB::table('settings')->get();
                @endphp
                <a href="{{ route('home') }}"><img src="@foreach ($settings as $data) {{ $data->logo }} @endforeach"
                        alt="logo"></a>
            </div>
            <div class="mobile-right-bar d-flex align-items-center">
                <div class="mobile-right-bar d-flex align-items-center">

                    <div class="mobile-search">
                        <div class="top-search"><a href="#0"><i class="ti-search"></i></a></div>
                        <form class="search-form" method="GET" action="{{ route('product.search') }}">
                            <input type="search" placeholder="Cari di sini..." name="search">
                            <button value="search" type="submit"><i class="ti-search"></i></button>
                        </form>
                    </div>

                    <div class="mobile-user-actions">
                        <div class="mobile-action-item shopping">
                            <a href="{{ route('cart') }}" class="single-icon"><i class="ti-bag"></i><span
                                    class="total-count">{{ Helper::cartCount() }}</span></a>
                        </div>
                        <div class="mobile-action-item">
                            @auth
                                <a href="{{ route('user') }}" class="single-icon"><i class="fa fa-user-circle-o"
                                        aria-hidden="true"></i></a>
                            @else
                                <a href="{{ route('login.form') }}" class="single-icon"><i class="fa fa-user-circle-o"
                                        aria-hidden="true"></i></a>
                            @endauth
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
    {{-- end navbar mobile --}}

    {{-- Navbar desktop --}}
    <div class="header-inner d-none d-lg-block">
        <div class="container">
            <div class="cat-nav-head">
                <div class="row">
                    <div class="col-12">
                        <div class="menu-area">
                            <nav class="navbar navbar-expand-lg">
                                <div class="navbar-collapse">
                                    <div class="nav-inner">
                                        <div class="nav-left-container">
                                            <div class="logo">
                                                <a href="{{ route('home') }}"><img
                                                        src="@foreach ($settings as $data) {{ $data->logo }} @endforeach"
                                                        alt="logo"></a>
                                            </div>
                                            <ul class="nav main-menu menu navbar-nav">
                                                <li class="d-lg-none {{ Request::path() == '/' ? 'active' : '' }}">
                                                    <a href="{{ route('home') }}">Beranda</a>
                                                </li>
                                                {{ Helper::getHeaderCategory() }}
                                                <li class="{{ Request::path() == 'about-us' ? 'active' : '' }}"><a
                                                        href="{{ route('about-us') }}">Tentang Kami</a></li>
                                                <li class="{{ Request::path() == 'contact' ? 'active' : '' }}"><a
                                                        href="{{ route('contact') }}">Bantuan</a></li>
                                            </ul>
                                        </div>
                                        <div class="nav-center-container">
                                            <form class="search-form-middle" method="GET"
                                                action="{{ route('product.search') }}">
                                                {{-- <button type="submit"><i class="ti-search"></i></button> --}}
                                                <input name="search" placeholder="Cari produk yang di inginkan..."
                                                    type="search">
                                            </form>
                                        </div>
                                        <div class="right-bar">
                                            <div class="sinlge-bar shopping">
                                                <a href="{{ route('cart') }}" class="single-icon "><i
                                                        class="ti-bag"></i><span
                                                        class="total-count">{{ Helper::cartCount() }}</span></a>
                                                @auth
                                                    <div class="shopping-item">
                                                        @php
                                                            $formatRp = fn($n) => 'Rp' .
                                                                number_format((float) $n, 0, ',', '.');
                                                            $items = collect(Helper::getAllProductFromCart() ?? []);
                                                        @endphp

                                                        <div class="dropdown-cart-header d-flex justify-content-between">
                                                            <span>{{ $items->count() }} Produk</span>
                                                            <a href="{{ route('cart') }}">Liat Keranjang</a>
                                                        </div>

                                                        <ul class="shopping-list">
                                                            @forelse ($items as $data)
                                                                @php
                                                                    // Variabel untuk URL gambar final
                                                                    $thumbUrl = '';

                                                                    // 1. Cek dulu apakah ada foto spesifik untuk varian
                                                                    if (!empty($data->variant?->photo)) {
                                                                        // Jika foto varian ada, gunakan itu (dan tambahkan storage path)
                                                                        $thumbUrl = asset(
                                                                            'storage/' . $data->variant->photo,
                                                                        );

                                                                        // 2. Jika tidak ada foto varian, baru ambil dari foto produk
                                                                    } else {
                                                                        $firstPhotoPath = '';
                                                                        $productPhotoData =
                                                                            $data->product['photo'] ?? null;

                                                                        if (
                                                                            !empty($productPhotoData) &&
                                                                            is_string($productPhotoData)
                                                                        ) {
                                                                            // 3. Memecah string foto (yang dipisah pipe |)
                                                                            $allPhotos = array_values(
                                                                                array_filter(
                                                                                    explode('|', $productPhotoData),
                                                                                ),
                                                                            );
                                                                            // 4. Mengambil FOTO PERTAMA (index [0])
                                                                            $firstPhotoPath = $allPhotos[0] ?? '';
                                                                        }

                                                                        // 5. Buat URL lengkap jika path foto ditemukan
                                                                        if ($firstPhotoPath) {
                                                                            $thumbUrl = asset(
                                                                                'storage/' . $firstPhotoPath,
                                                                            );
                                                                        }
                                                                    }

                                                                    // 6. Fallback jika $thumbUrl masih kosong
                                                                    if (empty($thumbUrl)) {
                                                                        $thumbUrl =
                                                                            'https://via.placeholder.com/100x100?text=No+Image';
                                                                    }
                                                                @endphp
                                                                <li>
                                                                    {{-- hapus item (pakai route yang sudah ada di project Anda) --}}
                                                                    <a href="{{ route('cart-delete', $data->id) }}"
                                                                        class="remove" title="Remove this item">
                                                                        <i class="fa fa-remove"></i>
                                                                    </a>

                                                                    <a class="cart-img"
                                                                        href="{{ route('product-detail', $data->product['slug']) }}">
                                                                        {{-- Gunakan $thumbUrl yang sudah final --}}
                                                                        <img src="{{ $thumbUrl }}" alt="thumb">
                                                                    </a>

                                                                    <h4 class="mb-0">
                                                                        <a href="{{ route('product-detail', $data->product['slug']) }}"
                                                                            target="_blank">
                                                                            {{ $data->product['title'] }}
                                                                        </a>
                                                                    </h4>

                                                                    {{-- nama varian kalau ada --}}
                                                                    @if ($data->variant)
                                                                        <div class="text-muted small"
                                                                            style="line-height:1.1;">
                                                                            Varian: {{ $data->variant->variant_name }}
                                                                        </div>
                                                                    @endif

                                                                    {{-- qty × harga satuan dalam Rp --}}
                                                                    <p class="quantity mb-0">
                                                                        {{ (int) $data->quantity }} ×
                                                                        <span
                                                                            class="amount">{{ $formatRp($data->price) }}</span>
                                                                    </p>
                                                                </li>
                                                            @empty
                                                                <li class="text-center text-muted p-3">Keranjang masih
                                                                    kosong</li>
                                                            @endforelse
                                                        </ul>

                                                        @php
                                                            // Total: fallback ke price*qty jika amount belum terset
                                                            $miniTotal = $items->sum(function ($c) {
                                                                $unit = (float) ($c->price ?? 0);
                                                                $qty = (int) ($c->quantity ?? 0);
                                                                return (float) ($c->amount ?? $unit * $qty);
                                                            });
                                                        @endphp

                                                        <div
                                                            class="bottom d-flex justify-content-between align-items-center">
                                                            <div class="total">
                                                                <span>Total : </span>
                                                                <span
                                                                    class="total-amount mx-1">{{ $formatRp($miniTotal) }}</span>
                                                            </div>

                                                        </div>
                                                        <div class="cart-checkout-wrapper">
                                                            <a href="{{ route('checkout') }}"
                                                                class="cart-checkout-button">Checkout</a>
                                                        </div>
                                                    </div>
                                                @endauth
                                            </div>
                                            <div class="sinlge-bar shopping">
                                                @auth
                                                    <a href="#" class="single-icon"><i class="fa fa-user-circle-o"
                                                            aria-hidden="true"></i></a>
                                                    <div class="shopping-item">
                                                        <div class="dropdown-cart-header">
                                                            <span>Halo, {{ Auth::user()->name }}</span>
                                                        </div>
                                                        <ul class="shopping-list">
                                                            <li>
                                                                @if (Auth::user()->role == 'admin')
                                                                    <a href="{{ route('admin') }}" target="_blank"
                                                                        style="font-weight: 500; padding: 5px 0;">Dashboard</a>
                                                                @elseif (Auth::user()->role == 'super_admin')
                                                                    <a href="{{ route('admin') }}" target="_blank"
                                                                        style="font-weight: 500; padding: 5px 0;">Dashboard</a>
                                                                @else
                                                                    <a href="{{ route('user') }}" target="_blank"
                                                                        style="font-weight: 500; padding: 5px 0;">Dashboard</a>
                                                                @endif
                                                            </li>
                                                            <li>
                                                                <a href="{{ route('user.logout') }}"
                                                                    style="font-weight: 500; padding: 5px 0;">Logout</a>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                @else
                                                    <a href="{{ route('login.form') }}" class="single-icon"><i
                                                            class="fa fa-user-circle-o" aria-hidden="true"></i>
                                                    </a>
                                                @endauth
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{-- Navbar desktop --}}
</header>
