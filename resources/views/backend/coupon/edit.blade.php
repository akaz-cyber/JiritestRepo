@extends('backend.layouts.master')

@section('main-content')
    <div class="card">
        <h5 class="card-header">Edit Coupon</h5>

        <div class="card-body">
            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $e)
                            <li>{{ $e }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('coupon.update', $coupon->id) }}">
                @csrf
                @method('PUT')

                {{-- Code --}}
                <div class="mb-3">
                    <label for="code" class="form-label">Kode</label>
                    <input id="code" name="code" type="text" value="{{ old('code', $coupon->code) }}"
                        class="form-control @error('code') is-invalid @enderror" placeholder="Mis: HEMAT10">
                    @error('code')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <small class="text-muted">Unik. Tidak boleh sama dengan kupon lain.</small>
                </div>

                {{-- Type --}}
                <div class="mb-3">
                    <label class="form-label">Tipe</label>
                    <select name="type" class="form-control @error('type') is-invalid @enderror">
                        <option value="fixed" {{ old('type', $coupon->type) === 'fixed' ? 'selected' : '' }}>Fixed</option>
                        <option value="percent" {{ old('type', $coupon->type) === 'percent' ? 'selected' : '' }}>Percent
                        </option>
                    </select>
                    @error('type')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Value --}}
                <div class="mb-3">
                    <label class="form-label">Nilai</label>
                    <input type="number" step="1" name="value" value="{{ old('value', (int) $coupon->value) }}"
                        class="form-control @error('value') is-invalid @enderror">
                    @error('value')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Starts / Expires --}}
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Mulai</label>
                        <input id="starts_at" type="text" name="starts_at"
                            value="{{ old('starts_at', optional($coupon->starts_at)->format('Y-m-d H:i')) }}"
                            class="form-control @error('starts_at') is-invalid @enderror">
                        @error('starts_at')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Berakhir</label>
                        <input id="expires_at" type="text" name="expires_at"
                            value="{{ old('expires_at', optional($coupon->expires_at)->format('Y-m-d H:i')) }}"
                            class="form-control @error('expires_at') is-invalid @enderror">
                        @error('expires_at')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                {{-- Limits --}}
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Usage Limit (total)</label>
                        <input type="number" name="usage_limit" value="{{ old('usage_limit', $coupon->usage_limit) }}"
                            class="form-control @error('usage_limit') is-invalid @enderror">
                        @error('usage_limit')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Per User Limit</label>
                        <input type="number" name="per_user_limit"
                            value="{{ old('per_user_limit', $coupon->per_user_limit) }}"
                            class="form-control @error('per_user_limit') is-invalid @enderror">
                        @error('per_user_limit')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                {{-- Min/Max --}}
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Min Spend</label>
                        {{-- Ubah step dan value --}}
                        <input type="number" step="1" name="min_spend"
                            value="{{ old('min_spend', (int) $coupon->min_spend) }}"
                            class="form-control @error('min_spend') is-invalid @enderror">
                        @error('min_spend')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Max Discount</label>
                        <input type="number" step="1" name="max_discount"
                            value="{{ old('max_discount', (int) $coupon->max_discount) }}"
                            class="form-control @error('max_discount') is-invalid @enderror">
                        @error('max_discount')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                {{-- Status --}}
                <div class="mb-3">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-control @error('status') is-invalid @enderror">
                        <option value="active" {{ old('status', $coupon->status) === 'active' ? 'selected' : '' }}>Active
                        </option>
                        <option value="inactive" {{ old('status', $coupon->status) === 'inactive' ? 'selected' : '' }}>
                            Inactive</option>
                    </select>
                    @error('status')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- (Opsional) Applies To / Free Shipping, sesuaikan jika kolom ada di DB --}}
                {{--
      <div class="mb-3">
        <label class="form-label">Applies To</label>
        <select name="applies_to" class="form-control">
          <option value="all" {{ old('applies_to', $coupon->applies_to) === 'all' ? 'selected':'' }}>Semua</option>
          <option value="products" {{ old('applies_to', $coupon->applies_to) === 'products' ? 'selected':'' }}>Produk</option>
          <option value="categories" {{ old('applies_to', $coupon->applies_to) === 'categories' ? 'selected':'' }}>Kategori</option>
          <option value="exclude_products" {{ old('applies_to', $coupon->applies_to) === 'exclude_products' ? 'selected':'' }}>Kecualikan Produk</option>
          <option value="exclude_categories" {{ old('applies_to', $coupon->applies_to) === 'exclude_categories' ? 'selected':'' }}>Kecualikan Kategori</option>
        </select>
      </div>

      <div class="form-check mb-3">
        <input class="form-check-input" type="checkbox" id="free_shipping" name="free_shipping" value="1"
               {{ old('free_shipping', $coupon->free_shipping) ? 'checked' : '' }}>
        <label class="form-check-label" for="free_shipping">Free Shipping</label>
      </div>
      --}}

                <div class="d-flex gap-2">
                    <a href="{{ route('coupon.index') }}" class="btn btn-outline-secondary">Batal</a>
                    <button type="submit" class="btn btn btn-success">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>
        flatpickr("#starts_at", {
            enableTime: true,
            dateFormat: "Y-m-d H:i",
            time_24hr: true,
        });

        flatpickr("#expires_at", {
            enableTime: true,
            dateFormat: "Y-m-d H:i",
            time_24hr: true,
        });
    </script>
@endpush
