@extends('backend.layouts.master')

@section('main-content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">Ganti Password</div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('change.password.form') }}">
                            @csrf
                            @foreach ($errors->all() as $error)
                                <p class="text-danger">{{ $error }}</p>
                            @endforeach

                            {{-- Password Lama --}}
                            <div class="form-group row">
                                <label for="password" class="col-md-4 col-form-label text-md-right">Password yang
                                    dulu</label>
                                <div class="col-md-6">
                                    <div class="input-group">
                                        <input id="current_password" type="password" class="form-control"
                                            name="current_password" autocomplete="current-password">
                                        <div class="input-group-append">
                                            <span class="input-group-text"
                                                onclick="togglePassword('current_password', this)" style="cursor: pointer;">
                                                <i class="fas fa-eye"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="new_password" class="col-md-4 col-form-label text-md-right">Password
                                    Baru</label>
                                <div class="col-md-6">
                                    <div class="input-group">
                                        <input id="new_password" type="password" class="form-control" name="new_password"
                                            autocomplete="current-password">
                                        <div class="input-group-append">
                                            <span class="input-group-text" onclick="togglePassword('new_password', this)"
                                                style="cursor: pointer;">
                                                <i class="fas fa-eye"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Konfirmasi Password --}}
                            <div class="form-group row">
                                <label for="new_confirm_password" class="col-md-4 col-form-label text-md-right">Konfirmasi
                                    password</label>
                                <div class="col-md-6">
                                    <div class="input-group">
                                        <input id="new_confirm_password" type="password" class="form-control"
                                            name="new_confirm_password" autocomplete="current-password">
                                        <div class="input-group-append">
                                            <span class="input-group-text"
                                                onclick="togglePassword('new_confirm_password', this)"
                                                style="cursor: pointer;">
                                                <i class="fas fa-eye"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group row mb-0">
                                <div class="col-md-8 offset-md-4">
                                    <button type="submit" class="btn btn-primary">
                                        Perbarui Password
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Script Javascript untuk Logika Show/Hide --}}
    @push('scripts')
        <script>
            function togglePassword(inputId, iconElement) {
                var input = document.getElementById(inputId);
                var icon = iconElement.querySelector('i');

                if (input.type === "password") {
                    input.type = "text";
                    // Ubah icon jadi mata dicoret (jika pakai fontawesome)
                    icon.classList.remove("fa-eye");
                    icon.classList.add("fa-eye-slash");
                } else {
                    input.type = "password";
                    // Balikin icon jadi mata biasa
                    icon.classList.remove("fa-eye-slash");
                    icon.classList.add("fa-eye");
                }
            }
        </script>
    @endpush
@endsection
