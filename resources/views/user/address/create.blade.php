@extends('user.layouts.master')
@section('title', 'Jirifarm || Tambah Alamat')

@push('styles')
    <link rel="stylesheet" href="{{ asset('frontend/css/nice-select.css') }}">
    <style>
        /* Style untuk form-control di SB Admin 2 */
        .form-control {
            height: 45px;
        }

        /* Mengatasi Nice Select */
        .nice-select {
            width: 100%;
            height: 45px;
            line-height: 43px;
            border: 1px solid #d1d3e2;
            /* Border ala SB Admin 2 */
            border-radius: 0.35rem;
            background-color: #fff;
            float: none;
        }

        .nice-select .list {
            width: 100%;
            border-radius: 0.35rem;
            max-height: 250px;
            overflow-y: auto;
        }

        .nice-select::after {
            border-bottom: 2px solid #555;
            border-right: 2px solid #555;
            height: 8px;
            width: 8px;
            right: 15px;
            margin-top: -5px;
        }
    </style>
@endpush

@section('main-content')
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary float-left">Tambah Alamat Baru</h6>
            <a href="{{ route('addresses.index') }}" class="btn btn-secondary btn-sm float-right">
                <i class="fas fa-arrow-left"></i> Kembali ke Daftar
            </a>
        </div>
        <div class="card-body">

            <form method="POST" action="{{ route('addresses.store') }}">
                @csrf
                <div class="row">
                    {{-- Baris Label & Default --}}
                    <div class="col-md-6 mb-3">
                        <label for="label" class="form-label">Label Alamat (Contoh: Rumah/Kantor)</label>
                        <input type="text" class="form-control @error('label') is-invalid @enderror" id="label"
                            name="label" value="{{ old('label') }}" placeholder="Rumah/Kantor">
                        @error('label')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3 d-flex align-items-end">
                        <div class="form-check pb-1">
                            <input class="form-check-input" type="checkbox" value="1" id="set_default"
                                name="set_default" {{ old('set_default') ? 'checked' : '' }}>
                            <label class="form-check-label" for="set_default">
                                Jadikan Alamat Default
                            </label>
                        </div>
                    </div>
                    {{-- Baris Nama --}}
                    {{-- <div class="col-md-6 mb-3">
                        <label for="first_name" class="form-label">Nama Depan <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('first_name') is-invalid @enderror" id="first_name"
                            name="first_name" value="{{ old('first_name') }}" required>
                        @error('first_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="last_name" class="form-label">Nama Belakang <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('last_name') is-invalid @enderror" id="last_name"
                            name="last_name" value="{{ old('last_name') }}" required>
                        @error('last_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div> --}}
                    <div class="col-md-12 mb-3">
                        <label for="name" class="form-label">Nama Penerima</label>
                        <input type="text" class="form-control" id="name" name="name"
                            value="{{ auth()->user()->name }}" readonly>
                        <small class="text-muted">Nama penerima mengikuti nama pada akun Anda.</small>
                    </div>

                    {{-- Baris Kontak --}}
                    <div class="col-md-6 mb-3">
                        <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror" id="email"
                            name="email" value="{{ old('email', auth()->user()->email) }}" required readonly>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="phone" class="form-label">Nomor Telepon <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('phone') is-invalid @enderror" id="phone"
                            name="phone" value="{{ old('phone') }}" required>
                        @error('phone')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Baris Lokasi 1 --}}
                    <div class="col-md-6 mb-3">
                        <label for="province_id" class="form-label">Provinsi <span class="text-danger">*</span></label>
                        <select class="nice-select @error('province_id') is-invalid-select @enderror" id="province_id"
                            name="province_id" required>
                            <option value="">Pilih Provinsi</option>
                        </select>
                        <input type="hidden" name="province_name" id="province_name" value="{{ old('province_name') }}">
                        @error('province_id')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="city_id" class="form-label">Kota/Kabupaten <span class="text-danger">*</span></label>
                        <select class="nice-select @error('city_id') is-invalid-select @enderror" id="city_id"
                            name="city_id" required disabled>
                            <option value="">Pilih Kota/Kabupaten</option>
                        </select>
                        <input type="hidden" name="city_name" id="city_name" value="{{ old('city_name') }}">
                        @error('city_id')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Baris Lokasi 2 --}}
                    <div class="col-md-6 mb-3">
                        <label for="district_id" class="form-label">Kecamatan <span class="text-danger">*</span></label>
                        <select class="nice-select @error('district_id') is-invalid-select @enderror" id="district_id"
                            name="district_id" required disabled>
                            <option value="">Pilih Kecamatan</option>
                        </select>
                        <input type="hidden" name="district_name" id="district_name" value="{{ old('district_name') }}">
                        @error('district_id')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="sub_district_id" class="form-label">Kelurahan / Desa <span
                                class="text-danger">*</span></label>
                        <select class="nice-select @error('sub_district_id') is-invalid-select @enderror"
                            id="sub_district_id" name="sub_district_id" required disabled>
                            <option value="">Pilih Kelurahan/Desa</option>
                        </select>
                        <input type="hidden" name="sub_district_name" id="sub_district_name"
                            value="{{ old('sub_district_name') }}">
                        @error('sub_district_id')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Baris Kode Pos & Alamat Lengkap --}}
                    <div class="col-md-6 mb-3">
                        <label for="post_code" class="form-label">Kode Pos <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('post_code') is-invalid @enderror"
                            id="post_code" name="post_code" value="{{ old('post_code') }}" readonly required>
                        @error('post_code')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-12 mb-3">
                        <label for="address1" class="form-label">Alamat Lengkap (Nama jalan, nomor rumah/blok) <span
                                class="text-danger">*</span></label>
                        <textarea class="form-control @error('address1') is-invalid @enderror" id="address1" name="address1" rows="3"
                            required>{{ old('address1') }}</textarea>
                        @error('address1')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <button type="submit" class="btn btn-success mt-3">Simpan Alamat</button>
            </form>

        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{ asset('frontend/js/nice-select/js/jquery.nice-select.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            $('select.nice-select').niceSelect();
            const oldProvinceId = "{{ old('province_id') }}";
            const oldCityId = "{{ old('city_id') }}";
            const oldDistrictId = "{{ old('district_id') }}";
            const oldSubDistrictId = "{{ old('sub_district_id') }}";

            // --- FUNGSI LOAD DATA API ---
            function loadProvinces(selectedId = null) {
                return $.getJSON('{{ route('rajaongkir.provinces') }}', function(data) {
                    const $p = $('#province_id');
                    $p.empty().append('<option value="">Pilih Provinsi</option>');
                    data.forEach(v => $p.append(`<option value="${v.id}">${v.name}</option>`));
                    if (selectedId) $p.val(selectedId);
                    $p.niceSelect('update');
                });
            }

            function loadCities(provinceId, selectedCityId = null) {
                const $c = $('#city_id');
                $c.empty().append('<option value="">Pilih Kota/Kabupaten</option>').prop('disabled', true)
                    .niceSelect('update');
                if (!provinceId) return;

                $.getJSON("{{ route('rajaongkir.cities', ['province_id' => ':id']) }}".replace(':id', provinceId),
                    function(data) {
                        data.forEach(v => $c.append(`<option value="${v.id}">${v.name}</option>`));
                        $c.prop('disabled', false).niceSelect('update');
                        if (selectedCityId) {
                            setTimeout(() => {
                                $c.val(selectedCityId).niceSelect('update');
                                // Trigger change untuk meload data berikutnya jika ada old value
                                if (selectedCityId) $c.trigger('change');
                            }, 50);
                        }
                    });
            }

            function loadDistricts(cityId, selectedDistrictId = null) {
                const $d = $('#district_id');
                $d.empty().append('<option value="">Pilih Kecamatan</option>').prop('disabled', true).niceSelect(
                    'update');
                if (!cityId) return;

                $.getJSON("{{ route('rajaongkir.districts', ['city_id' => ':id']) }}".replace(':id', cityId),
                    function(data) {
                        data.forEach(v => $d.append(`<option value="${v.id}">${v.name}</option>`));
                        $d.prop('disabled', false).niceSelect('update');
                        if (selectedDistrictId) {
                            setTimeout(() => {
                                $d.val(selectedDistrictId).niceSelect('update');
                                if (selectedDistrictId) $d.trigger('change');
                            }, 50);
                        }
                    });
            }

            function loadSubDistricts(districtId, selectedSubDistrictId = null) {
                const $s = $('#sub_district_id');
                $s.empty().append('<option value="">Pilih Kelurahan/Desa</option>').prop('disabled', true)
                    .niceSelect('update');
                if (!districtId) return;

                $.getJSON("{{ route('rajaongkir.subdistricts', ['district_id' => ':id']) }}".replace(':id',
                    districtId), function(data) {
                    data.forEach(v => $s.append(
                        `<option value="${v.id}" data-zip="${v.zip_code}">${v.name}</option>`
                    ));
                    $s.prop('disabled', false).niceSelect('update');
                    if (selectedSubDistrictId) {
                        setTimeout(() => {
                            $s.val(selectedSubDistrictId).niceSelect('update');
                            if (selectedSubDistrictId) $s.trigger('change');
                        }, 50);
                    }
                });
            }

            // --- EVENT HANDLER DROPDOWN ---
            $('#province_id').on('change', function() {
                const selectedText = $(this).find('option:selected').text();
                $('#province_name').val(selectedText === 'Pilih Provinsi' ? '' : selectedText);
                loadCities($(this).val());
            });

            $('#city_id').on('change', function() {
                const selectedText = $(this).find('option:selected').text();
                $('#city_name').val(selectedText === 'Pilih Kota/Kabupaten' ? '' : selectedText);
                loadDistricts($(this).val());
            });

            $('#district_id').on('change', function() {
                const selectedText = $(this).find('option:selected').text();
                $('#district_name').val(selectedText === 'Pilih Kecamatan' ? '' : selectedText);
                loadSubDistricts($(this).val());
            });

            $('#sub_district_id').on('change', function() {
                const opt = $(this).find('option:selected');
                const selectedName = opt.text();
                $('#post_code').val(opt.data('zip') || '');
                $('#sub_district_name').val(selectedName === 'Pilih Kelurahan/Desa' ? '' : selectedName);
            });

            // --- INISIALISASI DENGAN OLD VALUE (JIKA ADA VALIDASI ERROR) ---
            loadProvinces(oldProvinceId);
            if (oldProvinceId) {
                // Gunakan promise/callback agar loadCities dipanggil setelah loadProvinces selesai
                loadCities(oldProvinceId, oldCityId);
            }
            if (oldCityId) {
                // Di dalam loadCities akan ada logic trigger/loadDistricts, pastikan urutannya benar
                loadDistricts(oldCityId, oldDistrictId);
            }
            if (oldDistrictId) {
                loadSubDistricts(oldDistrictId, oldSubDistrictId);
            }

        });
    </script>
@endpush
