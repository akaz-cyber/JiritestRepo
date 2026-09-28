@extends('frontend.layouts.master')

@section('title', 'Jirifarm || REGISTER PAGE')

@section('main-content')
    <div class="breadcrumbs">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="bread-inner">
                        <ul class="bread-list">
                            <li><a href="{{ route('home') }}">Home<i class="ti-arrow-right"></i></a></li>
                            <li class="active"><a href="javascript:void(0);">Register</a></li>
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
                        <h2>Register</h2>
                        <form class="form" method="post" action="{{ route('register.submit') }}">
                            @csrf
                            <div class="row">
                                <div class="col-12">
                                    <div class="form-group">
                                        <label>Nama<span>*</span></label>
                                        <input type="text" name="name" placeholder="" required="required"
                                            value="{{ old('name') }}">
                                        @error('name')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
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

                                {{-- FIELD PASSWORD --}}
                                <div class="col-12">
                                    <div class="form-group">
                                        <label>Password<span>*</span></label>
                                        {{-- Gunakan class custom-password-group agar sejajar --}}
                                        <div class="input-group custom-password-group">
                                            <input type="password" name="password" id="reg_password" placeholder=""
                                                required="required" value="{{ old('password') }}">
                                            <div class="input-group-append">
                                                <span class="input-group-text"
                                                    onclick="togglePassword('reg_password', this)">
                                                    <i class="fas fa-eye"></i>
                                                </span>
                                            </div>
                                        </div>
                                        @error('password')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                {{-- FIELD KONFIRMASI PASSWORD --}}
                                <div class="col-12">
                                    <div class="form-group">
                                        <label>Konfirmasi Password<span>*</span></label>
                                        {{-- Gunakan class custom-password-group agar sejajar --}}
                                        <div class="input-group custom-password-group">
                                            <input type="password" name="password_confirmation" id="reg_password_confirm"
                                                placeholder="" required="required"
                                                value="{{ old('password_confirmation') }}">
                                            <div class="input-group-append">
                                                <span class="input-group-text"
                                                    onclick="togglePassword('reg_password_confirm', this)">
                                                    <i class="fas fa-eye"></i>
                                                </span>
                                            </div>
                                        </div>
                                        @error('password_confirmation')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-12">
                                    <div
                                        class="form-group login-btn d-flex flex-column flex-sm-row justify-content-between align-items-center">
                                        <button class="btn" type="submit">Daftar</button>
                                        <a class="login-link" href="{{ route('login.form') }}">Sudah punya akun?</a>
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

@push('styles')
    <style>
        /* 1. Paksa container menjadi Flexbox agar elemen sejajar */
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

        /* Efek hover pada mata */
        .shop.login .custom-password-group .input-group-text:hover {
            color: #F7941D;
            /* Warna tema (orange) saat disentuh */
        }

        .shop.login .form .btn {
            margin-right: 0;
        }

        @media (max-width: 576px) {
            .shop.login .form .btn {
                width: 100%;
                /* Tombol full width */
                margin-bottom: 15px;
                /* Jarak ke bawah */
                margin-right: 0;
            }

            .shop.login .login-link {
                width: 100%;
                text-align: center;
            }
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
