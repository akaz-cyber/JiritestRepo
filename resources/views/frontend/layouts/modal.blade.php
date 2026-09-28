{{-- @php
    $settings = DB::table('settings')->get();
@endphp
<div class="modal fade" id="loginModal" tabindex="-1" role="dialog" aria-labelledby="loginModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content custom-modal-content">

            <button type="button" class="close custom-close-button" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>

            <div class="modal-body custom-modal-body">
                <div class="text-center mb-4">
                    <a href="{{ route('home') }}">
                        <img src="@foreach ($settings as $data) {{ $data->logo }} @endforeach" alt="logo"
                            style="max-height: 70px;">
                    </a>
                    <h5 class="modal-title font-weight-bold">MASUK</h5>
                    <p class="text-muted small">Selamat datang kembali!</p>
                </div>

                <form method="POST" action="{{ route('login.submit') }}">
                    @csrf
                    <div class="form-group">
                        <label for="email" class="font-weight-bold small">Email</label>
                        <input type="email" class="form-control custom-form-control" name="email"
                            placeholder="Masukan Email" required="required" value="{{ old('email') }}">
                        @error('email')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="password" class="font-weight-bold small">Password</label>
                        <input type="password" class="form-control custom-form-control" name="password"
                            placeholder="Masukan Password" required="required" value="{{ old('password') }}">
                        @error('password')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <button type="submit" class="btn btn-block custom-btn-masuk mt-4">Masuk</button>
                </form>

                <div class="text-start mt-3">
                    <a href="#" class="small custom-link">Lupa password?</a>
                </div>
            </div>

            <div class="modal-footer custom-modal-footer justify-content-center">
                <span class="small">Belum punya akun? <a href="#"
                        class="font-weight-bold custom-link">Daftar</a></span>
            </div>
        </div>
    </div>
</div> --}}
