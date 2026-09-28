@extends('frontend.layouts.master')

@section('title', 'Lupa Password')

@section('main-content')
    <div class="container py-3">
        <div class="row justify-content-center">
            <div class="col-12 col-md-8 col-lg-5">
                <div class="card forgot-password-card">
                    <div class="card-body text-center">
                        <div class="mb-5">
                            @php
                                $settings = DB::table('settings')->get();
                            @endphp
                            <img src="@foreach ($settings as $data) {{ $data->logo }} @endforeach" alt="logo">
                        </div>

                        <h3 class="forgot-password-title">Lupa password</h3>
                        <p class="forgot-password-subtitle">Silahkan isi email anda untuk Lupa password</p>

                        @if (session('success'))
                            <div class="alert alert-success text-left">
                                {{ session('success') }}
                            </div>
                        @endif

                        @error('email')
                            <div class="alert alert-danger text-left">
                                {{ $message }}
                            </div>
                        @enderror

                        <form method="POST" action="{{ route('password.email') }}"
                            class="forgot-password-form text-left mt-4">
                            @csrf
                            <div class="form-group">
                                <label>Email</label>
                                <input type="email" name="email" class="form-control" placeholder="Masukan Email"
                                    required value="{{ old('email') }}">
                            </div>

                            <button type="submit" class="btn btn-reset mt-4">
                                Reset Password
                            </button>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        /* Styling khusus halaman Lupa Password agar sesuai desain mockup */
        .forgot-password-card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
            /* Memberikan bayangan halus */
            padding: 30px 20px;
            background-color: #ffffff;
        }

        .forgot-password-logo {
            max-width: 140px;
            margin-bottom: 25px;
        }

        .forgot-password-title {
            font-weight: 700;
            color: #222;
            margin-bottom: 10px;
            font-size: 28px;
        }

        .forgot-password-subtitle {
            color: #777;
            font-size: 15px;
            margin-bottom: 20px;
        }

        .forgot-password-form label {
            font-weight: 700;
            color: #222;
            font-size: 14px;
            margin-bottom: 8px;
        }

        .forgot-password-form .form-control {
            border-radius: 8px;
            padding: 12px 15px;
            height: auto;
            border: 1px solid #b3b3b3;
        }

        .forgot-password-form .form-control::placeholder {
            color: #a9a9a9;
        }

        .forgot-password-form .btn-reset {
            background-color: #119B90;
            /* Warna hijau kebiruan sesuai gambar */
            color: #ffffff;
            border-radius: 8px;
            padding: 12px;
            font-weight: 600;
            width: 100%;
            border: none;
            transition: all 0.3s ease;
        }

        .forgot-password-form .btn-reset:hover {
            background-color: #0e8379;
            /* Warna sedikit lebih gelap saat di-hover */
            color: #ffffff;
        }
    </style>
@endpush
