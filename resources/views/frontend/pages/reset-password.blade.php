@extends('frontend.layouts.master')

@section('title', 'Reset Password')

@section('main-content')
    <div class="container py-2">
        <div class="row justify-content-center">
            <div class="col-12 col-md-8 col-lg-5">
                <div class="card reset-password-card">
                    <div class="card-body text-center">

                        {{-- Pastikan path logo sesuai --}}
                        <div class="mb-5">
                            @php
                                $settings = DB::table('settings')->get();
                            @endphp
                            <img src="@foreach ($settings as $data) {{ $data->logo }} @endforeach" alt="logo">
                        </div>
                        <h3 class="reset-title">Reset password</h3>
                        <p class="reset-subtitle">Silahkan masukan password baru</p>

                        @error('email')
                            <div class="alert alert-danger text-left mt-3">
                                {{ $message }}
                            </div>
                        @enderror
                        @error('password')
                            <div class="alert alert-danger text-left mt-3">
                                {{ $message }}
                            </div>
                        @enderror

                        <form method="POST" action="{{ route('password.update') }}" class="text-left mt-4">
                            @csrf

                            <input type="hidden" name="token" value="{{ $token }}">

                            <div class="form-group reset-form-group">
                                <label>Email</label>
                                {{-- request()->email akan otomatis mengisi email dari URL jika ada --}}
                                <input type="email" name="email" class="form-control custom-input"
                                    placeholder="Masukan Email Anda" value="{{ request()->email ?? old('email') }}" required
                                    readonly>
                                {{-- Catatan: Saya tambahkan atribut 'readonly' supaya user tidak salah ketik emailnya saat reset, hapus readonly jika user harus mengetik manual --}}
                            </div>

                            <div class="form-group reset-form-group mt-3">
                                <label>Password baru</label>
                                <input type="password" name="password" class="form-control custom-input"
                                    placeholder="Masukan Password baru" required>
                            </div>

                            <div class="form-group reset-form-group mt-3">
                                <label>Konfirmasi password</label>
                                <input type="password" name="password_confirmation" class="form-control custom-input"
                                    placeholder="Konfirmasi password anda" required>
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
        /* Styling Card Utama */
        .reset-password-card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
            /* Bayangan lembut */
            padding: 40px 20px;
            background-color: #ffffff;
        }

        /* Styling Logo */
        .reset-logo {
            max-width: 140px;
            margin-bottom: 25px;
        }

        /* Styling Judul & Subjudul */
        .reset-title {
            font-weight: 700;
            color: #333;
            margin-bottom: 10px;
            font-size: 26px;
        }

        .reset-subtitle {
            color: #666;
            font-size: 15px;
            margin-bottom: 10px;
        }

        /* Styling Label Form */
        .reset-form-group label {
            font-weight: 700;
            color: #222;
            font-size: 14px;
            margin-bottom: 8px;
            display: block;
        }

        /* Styling Input Field */
        .custom-input {
            border-radius: 8px;
            padding: 12px 15px;
            height: auto;
            border: 1px solid #b3b3b3;
            font-size: 14px;
        }

        .custom-input::placeholder {
            color: #a9a9a9;
        }

        .custom-input[readonly] {
            background-color: #f8f9fa;
            cursor: not-allowed;
        }

        /* Styling Tombol Teal dengan Shadow */
        .btn-reset {
            background-color: #119B90;
            /* Warna tosca khas Jirifarm */
            color: #ffffff;
            border-radius: 10px;
            padding: 12px 20px;
            font-weight: 600;
            width: 100%;
            border: none;
            box-shadow: 0 4px 8px rgba(17, 155, 144, 0.3);
            transition: all 0.3s ease;
        }

        .btn-reset:hover {
            background-color: #0e8379;
            color: #ffffff;
            box-shadow: 0 6px 12px rgba(17, 155, 144, 0.4);
        }
    </style>
@endpush
