@extends('backend.layouts.master')

@section('main-content')

    <div class="card">
        <h5 class="card-header">Add Coupon</h5>
        <div class="card-body">
            <form method="post" action="{{ route('coupon.store') }}">
                {{ csrf_field() }}

                {{-- Coupon Code --}}
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $e)
                                <li>{{ $e }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="mb-3">
                    <label for="code" class="form-label">Kode</label>
                    <input id="code" name="code" type="text" value="{{ old('code') }}"
                        class="form-control @error('code') is-invalid @enderror">
                    @error('code')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Type --}}
                <div class="form-group">
                    <label for="type" class="col-form-label">
                        Type <span class="text-danger">*</span>
                    </label>
                    <select name="type" id="type" class="form-control">
                        <option value="fixed" {{ old('type') === 'fixed' ? 'selected' : '' }}>Fixed</option>
                        <option value="percent" {{ old('type') === 'percent' ? 'selected' : '' }}>Percent</option>
                    </select>
                    @error('type')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                    <small class="text-muted d-block mt-1">
                        Fixed = potongan rupiah. Percent = potongan persen.
                    </small>
                </div>

                {{-- Periode --}}
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="start_date" class="col-form-label">
                            Tanggal Berlaku <span class="text-danger">*</span>
                        </label>
                        <input type="text" id="starts_at" value="{{ old('starts_at') }}" name="starts_at"
                            class="form-control" placeholder="Pilih Tanggal & Jam...">
                        {{-- <input id="start_date" type="datetime-local" name="starts_at" value="{{ old('starts_at') }}"
                            class="form-control" placeholder="Tanggal Berlaku"> --}}
                        @error('starts_at')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group col-md-6">
                        <label for="end_date" class="col-form-label">
                            Tanggal Kadaluarsa <span class="text-danger">*</span>
                        </label>
                        <input id="expires_at" type="text" name="expires_at" value="{{ old('expires_at') }}"
                            class="form-control" placeholder="Tanggal Kadaluarsa">
                        @error('expires_at')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                {{-- Value --}}
                <div class="form-group">
                    <label for="inputValue" class="col-form-label">
                        Value <span class="text-danger">*</span>
                    </label>
                    <input id="inputValue" type="number" name="value" min="0" step="1"
                        placeholder="Fixed: rupiah (contoh 100000) / Percent: persen (contoh 10)"
                        value="{{ old('value') }}" class="form-control">
                    @error('value')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Tambahan: usage_limit, per_user_limit, min_spend, max_discount --}}
                <div class="form-row">
                    <div class="form-group col-md-3">
                        <label for="usage_limit" class="col-form-label">Qty (usage_limit)</label>
                        <input id="usage_limit" type="number" name="usage_limit" min="0" step="1"
                            placeholder="Kosongkan = tak terbatas" value="{{ old('usage_limit') }}" class="form-control">
                        @error('usage_limit')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                        <small class="text-muted">Total kuota pemakaian kupon (semua user).</small>
                    </div>
                    <div class="form-group col-md-3">
                        <label for="per_user_limit" class="col-form-label">Per User Limit</label>
                        <input id="per_user_limit" type="number" name="per_user_limit" min="0" step="1"
                            placeholder="Kosongkan = tak terbatas" value="{{ old('per_user_limit') }}"
                            class="form-control">
                        @error('per_user_limit')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                        <small class="text-muted">Batas pemakaian per pengguna.</small>
                    </div>
                    <div class="form-group col-md-3">
                        <label for="min_spend" class="col-form-label">Min Spend (Rp)</label>
                        <input id="min_spend" type="number" name="min_spend" min="0" step="1"
                            placeholder="Contoh 150000" value="{{ old('min_spend') }}" class="form-control">
                        @error('min_spend')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                        <small class="text-muted">Subtotal minimal agar kupon berlaku.</small>
                    </div>
                    <div class="form-group col-md-3">
                        <label for="max_discount" class="col-form-label">Max Discount (Rp)</label>
                        <input id="max_discount" type="number" name="max_discount" min="0" step="1"
                            placeholder="Kosongkan jika tanpa batas" value="{{ old('max_discount') }}"
                            class="form-control">
                        @error('max_discount')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                        <small class="text-muted">Batas maksimal nominal potongan.</small>
                    </div>
                </div>

                {{-- Status --}}
                <div class="form-group">
                    <label for="status" class="col-form-label">
                        Status <span class="text-danger">*</span>
                    </label>
                    <select name="status" id="status" class="form-control">
                        <option value="active" {{ old('status') === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                    @error('status')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group mb-3">
                    <button type="reset" class="btn btn-warning">Reset</button>
                    <button class="btn btn btn-success" type="submit">Submit</button>
                </div>
            </form>
        </div>
    </div>

@endsection

@push('styles')
    <link rel="stylesheet" href="{{ asset('backend/summernote/summernote.min.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
@endpush

@push('scripts')
    <script src="/vendor/laravel-filemanager/js/stand-alone-button.js"></script>
    <script src="{{ asset('backend/summernote/summernote.min.js') }}"></script>
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
    <script>
        $('#lfm').filemanager('image');
        $(function() {
            $('#description').summernote({
                placeholder: "Write short description.....",
                tabsize: 2,
                height: 150
            });
        });
    </script>

    <script>
        $(document).ready(function() {
            // Definisikan elemen-elemen yang akan kita gunakan
            const typeSelect = $('#type');
            const maxDiscountInput = $('#max_discount');

            // Buat fungsi untuk mengatur status input Max Discount
            function toggleMaxDiscountState() {
                const selectedType = typeSelect.val();

                if (selectedType === 'fixed') {
                    // Jika tipenya 'fixed', nonaktifkan input Max Discount
                    maxDiscountInput.prop('disabled', true);
                    // Kosongkan nilainya untuk mencegah data tersimpan
                    maxDiscountInput.val('');
                } else { // Jika tipenya 'percent'
                    // Aktifkan kembali input Max Discount
                    maxDiscountInput.prop('disabled', false);
                }
            }

            // Panggil fungsi ini saat dropdown 'type' berubah
            typeSelect.on('change', function() {
                toggleMaxDiscountState();
            });

            // Panggil fungsi ini sekali saat halaman pertama kali dimuat
            // untuk mengatur status awal berdasarkan nilai yang sudah ada (jika ada error validasi)
            toggleMaxDiscountState();
        });
    </script>
@endpush
