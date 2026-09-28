@extends('frontend.layouts.master')

@section('title', 'Jirifarm || Login Page')

@section('main-content')
    <div class="breadcrumbs">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="bread-inner">
                        <ul class="bread-list">
                            <li><a href="{{ route('home') }}">Home<i class="ti-arrow-right"></i></a></li>
                            <li class="active"><a href="javascript:void(0);">Login</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <section class="shop login section">
        <div class="container">
            <div class="row">
                <div class="col-lg-6 offset-lg-3 col-12">
                    <div class="login-form">
                        <h2>Login</h2>
                        <form class="form" method="post" action="{{ route('login.submit') }}">
                            @csrf
                            <div class="row">
                                <div class="col-12">
                                    <div class="form-group">
                                        <label>Email<span>*</span></label>
                                        <input type="email" name="email" placeholder="" required="required"
                                            value="{{ old('email') }}">
                                        @error('email')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-group">
                                        <label>Password<span>*</span></label>
                                        {{-- STRUKTUR INPUT GROUP YANG SUDAH DIPERBAIKI --}}
                                        <div class="input-group custom-password-group">
                                            <input type="password" name="password" id="login_password" placeholder=""
                                                required="required" value="{{ old('password') }}">
                                            <div class="input-group-append">
                                                <span class="input-group-text"
                                                    onclick="togglePassword('login_password', this)">
                                                    <i class="fas fa-eye"></i>
                                                </span>
                                            </div>
                                        </div>
                                        {{-- END STRUKTUR --}}
                                        @error('password')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-group login-btn mb-3"> <button class="btn"
                                            type="submit">Masuk</button>
                                    </div>

                                    <div class="mt-4 d-flex justify-content-between align-items-center">
                                        <a href="{{ route('password.request') }}">Lupa Password</a>
                                        <a class="register-link" href="{{ route('register.form') }}">Tidak punya akun?</a>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

{{-- CSS PENYELAMAT AGAR SEJAJAR --}}
@push('styles')
    <style>
        /* 1. Paksa container menjadi Flexbox */
        .shop.login .custom-password-group {
            display: flex !important;
            flex-wrap: nowrap !important;
            width: 100%;
            position: relative;
        }

        /* 2. Atur Input agar mengisi ruang sisa & tingginya pas */
        .shop.login .custom-password-group input {
            flex: 1 1 auto;
            width: 1% !important;
            /* Trik agar input tidak memaksa lebar 100% */
            height: 45px !important;
            border-top-right-radius: 0;
            border-bottom-right-radius: 0;
            margin-bottom: 0 !important;
            border-right: none;
            /* Hilangkan garis kanan biar nyambung */
        }

        /* 3. Atur wadah icon di sebelah kanan */
        .shop.login .custom-password-group .input-group-append {
            display: flex;
            align-items: center;
        }

        /* 4. Styling Kotak Icon (Background Abu-abu seperti Admin) */
        .shop.login .custom-password-group .input-group-text {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 45px !important;
            /* Samakan tinggi dengan input */
            padding: 0 15px;
            background-color: #e9ecef;
            /* Warna abu-abu khas Bootstrap */
            border: 1px solid #ced4da;
            /* Sesuaikan warna border template */
            border-left: none;
            /* Hilangkan border kiri */
            border-top-right-radius: 4px;
            border-bottom-right-radius: 4px;
            color: #555;
            cursor: pointer;
            font-size: 16px;
        }

        .shop.login .custom-password-group .input-group-text:hover {
            color: #096a4d;
        }

        .shop.login .form .btn {
            margin-right: 0;
        }
    </style>
@endpush

@push('scripts')
    <script>
        function togglePassword(inputId, iconSpan) {
            const input = document.getElementById(inputId);
            const icon = iconSpan.querySelector('i');

            if (input.type === "password") {
                input.type = "text";
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = "password";
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }
    </script>
@endpush
