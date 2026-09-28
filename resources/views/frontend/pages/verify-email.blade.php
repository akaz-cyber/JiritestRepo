@extends('frontend.layouts.master')

@section('title', 'Verifikasi Email')

@section('main-content')
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-12 col-md-8 col-lg-5">
                <div class="card verification-card">
                    <div class="card-body text-center">

                        {{-- Pastikan path logo sesuai dengan struktur foldermu --}}
                        <div class="mb-5">
                            @php
                                $settings = DB::table('settings')->get();
                            @endphp
                            <img src="@foreach ($settings as $data) {{ $data->logo }} @endforeach" alt="logo">
                        </div>


                        <h3 class="verification-title">Verifikasi Email</h3>
                        <p class="verification-subtitle">Anda harus memverifikasi email sebelum melakukan transaksi.</p>

                        @if (session('message'))
                            <div class="alert alert-success mt-3 text-left" role="alert">
                                {{ session('message') }}
                            </div>
                        @endif

                        <form method="POST" action="{{ route('verification.send') }}" class="mt-4">
                            @csrf
                            <button type="submit" class="btn btn-verify">
                                Kirim ulang Email Verifikasi
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
        .verification-card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
            /* Bayangan lembut */
            padding: 40px 20px;
            background-color: #ffffff;
        }

        /* Styling Logo */
        .verification-logo {
            max-width: 140px;
            margin-bottom: 25px;
        }

        /* Styling Judul */
        .verification-title {
            font-weight: 700;
            color: #333;
            margin-bottom: 15px;
            font-size: 26px;
        }

        /* Styling Subjudul */
        .verification-subtitle {
            color: #666;
            font-size: 16px;
            margin-bottom: 10px;
            line-height: 1.5;
        }

        /* Styling Tombol Teal dengan Shadow */
        .btn-verify {
            background-color: #119B90;
            /* Warna tosca khas Jirifarm */
            color: #ffffff;
            border-radius: 10px;
            /* Sudut membulat seperti digambar */
            padding: 12px 20px;
            font-weight: 600;
            width: 100%;
            border: none;
            box-shadow: 0 4px 8px rgba(17, 155, 144, 0.3);
            /* Efek drop shadow pada tombol */
            transition: all 0.3s ease;
        }

        .btn-verify:hover {
            background-color: #0e8379;
            /* Warna lebih gelap saat disentuh mouse */
            color: #ffffff;
            box-shadow: 0 6px 12px rgba(17, 155, 144, 0.4);
        }
    </style>
@endpush
