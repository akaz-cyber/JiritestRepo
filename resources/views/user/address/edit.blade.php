@extends('user.layouts.master')
@section('title', 'Jirifarm || Edit Alamat')
@section('main-content')
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary float-left">Edit Alamat: {{ $address->label ?: 'Tanpa Label' }}</h6>
            <a href="{{ route('addresses.index') }}" class="btn btn-secondary btn-sm float-right">
                <i class="fas fa-arrow-left"></i> Kembali ke Daftar
            </a>
        </div>
        <div class="card-body">

            <form method="POST" action="{{ route('addresses.update', Crypt::encryptString($address->id)) }}">
                @csrf
                @method('PUT')
                <div class="row">
                    {{-- Baris Label & Default --}}
                    <div class="col-md-6 mb-3">
                        <label for="label" class="form-label">Label Alamat (Contoh: Rumah/Kantor)</label>
                        <input type="text" class="form-control @error('label') is-invalid @enderror" id="label"
                            name="label" value="{{ old('label', $address->label) }}" placeholder="Rumah/Kantor">
                        @error('label')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3 d-flex align-items-end">
                        <div class="form-check pb-1">
                            <input class="form-check-input" type="checkbox" value="1" id="set_default"
                                name="set_default" {{ old('set_default', $address->is_default) ? 'checked' : '' }}>
                            <label class="form-check-label" for="set_default">
                                Jadikan Alamat Default
                            </label>
                        </div>
                    </div>
                    {{-- Baris Nama --}}
                    {{-- <div class="col-md-6 mb-3">
                        <label for="first_name" class="form-label">Nama Depan <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('first_name') is-invalid @enderror" id="first_name"
                            name="first_name" value="{{ old('first_name', $address->first_name) }}" required>
                        @error('first_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="last_name" class="form-label">Nama Belakang <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('last_name') is-invalid @enderror" id="last_name"
                            name="last_name" value="{{ old('last_name', $address->last_name) }}" required>
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
                            name="email" value="{{ old('email', $address->email) }}" required>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="phone" class="form-label">Nomor Telepon <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('phone') is-invalid @enderror" id="phone"
                            name="phone" value="{{ old('phone', $address->phone) }}" required>
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
                        <input type="hidden" name="province_name" id="province_name"
                            value="{{ old('province_name', $address->province_name) }}">
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
                        <input type="hidden" name="city_name" id="city_name"
                            value="{{ old('city_name', $address->city_name) }}">
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
                        <input type="hidden" name="district_name" id="district_name"
                            value="{{ old('district_name', $address->district_name) }}">
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
                            value="{{ old('sub_district_name', $address->sub_district_name) }}">
                        @error('sub_district_id')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Baris Kode Pos & Alamat Lengkap --}}
                    <div class="col-md-6 mb-3">
                        <label for="post_code" class="form-label">Kode Pos <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('post_code') is-invalid @enderror"
                            id="post_code" name="post_code" value="{{ old('post_code', $address->post_code) }}" readonly
                            required>
                        @error('post_code')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-12 mb-3">
                        <label for="address1" class="form-label">Alamat Lengkap (Nama jalan, nomor rumah/blok) <span
                                class="text-danger">*</span></label>
                        <textarea class="form-control @error('address1') is-invalid @enderror" id="address1" name="address1" rows="3"
                            required>{{ old('address1', $address->address1) }}</textarea>
                        @error('address1')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <button type="submit" class="btn btn-success mt-3">Update Alamat</button>
            </form>

        </div>
    </div>
@endsection

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


@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{ asset('frontend/js/nice-select/js/jquery.nice-select.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            $('select.nice-select').niceSelect();

            // Data Alamat Saat Ini
            const current = {
                provinceId: "{{ old('province_id', $address->province_id) }}",
                cityId: "{{ old('city_id', $address->city_id) }}",
                districtId: "{{ old('district_id', $address->district_id) }}",
                subDistrictId: "{{ old('sub_district_id', $address->sub_district_id) }}",
            };

            // --- FUNGSI LOAD DATA API ---

            // 1. Load Province
            function loadProvinces(selectedId = null) {
                // Tambahkan 'return' agar proses bisa ditunggu (await)
                return $.getJSON('{{ route('rajaongkir.provinces') }}', function(data) {
                    const $p = $('#province_id');
                    $p.empty().append('<option value="">Pilih Provinsi</option>');
                    data.forEach(v => $p.append(`<option value="${v.id}">${v.name}</option>`));
                    if (selectedId) $p.val(selectedId);
                    $p.niceSelect('update');
                });
            }

            // 2. Load Cities
            function loadCities(provinceId, selectedCityId = null) {
                const $c = $('#city_id');
                // Reset dropdown dulu
                $c.empty().append('<option value="">Pilih Kota/Kabupaten</option>').prop('disabled', true)
                    .niceSelect('update');

                if (!provinceId) return Promise.resolve(); // Return promise kosong jika tidak ada ID

                // PENTING: Tambahkan 'return' di sini
                return $.getJSON("{{ route('rajaongkir.cities', ['province_id' => ':id']) }}".replace(':id',
                        provinceId),
                    function(data) {
                        data.forEach(v => $c.append(`<option value="${v.id}">${v.name}</option>`));
                        $c.prop('disabled', false).niceSelect('update');
                        if (selectedCityId) {
                            $c.val(selectedCityId).niceSelect('update');
                        }
                    });
            }

            // 3. Load Districts
            function loadDistricts(cityId, selectedDistrictId = null) {
                const $d = $('#district_id');
                $d.empty().append('<option value="">Pilih Kecamatan</option>').prop('disabled', true).niceSelect(
                    'update');

                if (!cityId) return Promise.resolve();

                // PENTING: Tambahkan 'return' di sini
                return $.getJSON("{{ route('rajaongkir.districts', ['city_id' => ':id']) }}".replace(':id',
                        cityId),
                    function(data) {
                        data.forEach(v => $d.append(`<option value="${v.id}">${v.name}</option>`));
                        $d.prop('disabled', false).niceSelect('update');
                        if (selectedDistrictId) {
                            $d.val(selectedDistrictId).niceSelect('update');
                        }
                    });
            }

            // 4. Load SubDistricts
            function loadSubDistricts(districtId, selectedSubDistrictId = null) {
                const $s = $('#sub_district_id');
                $s.empty().append('<option value="">Pilih Kelurahan/Desa</option>').prop('disabled', true)
                    .niceSelect('update');

                if (!districtId) return Promise.resolve();

                // PENTING: Tambahkan 'return' di sini
                return $.getJSON("{{ route('rajaongkir.subdistricts', ['district_id' => ':id']) }}".replace(':id',
                        districtId),
                    function(data) {
                        data.forEach(v => $s.append(
                            `<option value="${v.id}" data-zip="${v.zip_code}">${v.name}</option>`
                        ));
                        $s.prop('disabled', false).niceSelect('update');
                        if (selectedSubDistrictId) {
                            $s.val(selectedSubDistrictId).niceSelect('update');
                        }
                    });
            }

            // --- EVENT HANDLER CHANGE ---
            // Event listener tetap sama, tidak perlu await karena dipicu user manual
            $('#province_id').on('change', function() {
                const selectedText = $(this).find('option:selected').text();
                $('#province_name').val(selectedText === 'Pilih Provinsi' ? '' : selectedText);

                // Reset child dropdowns
                $('#city_id').val('').prop('disabled', true).niceSelect('update');
                $('#district_id').val('').prop('disabled', true).niceSelect('update');
                $('#sub_district_id').val('').prop('disabled', true).niceSelect('update');

                loadCities($(this).val());
            });

            $('#city_id').on('change', function() {
                const selectedText = $(this).find('option:selected').text();
                $('#city_name').val(selectedText === 'Pilih Kota/Kabupaten' ? '' : selectedText);

                $('#district_id').val('').prop('disabled', true).niceSelect('update');
                $('#sub_district_id').val('').prop('disabled', true).niceSelect('update');

                loadDistricts($(this).val());
            });

            $('#district_id').on('change', function() {
                const selectedText = $(this).find('option:selected').text();
                $('#district_name').val(selectedText === 'Pilih Kecamatan' ? '' : selectedText);

                $('#sub_district_id').val('').prop('disabled', true).niceSelect('update');

                loadSubDistricts($(this).val());
            });

            $('#sub_district_id').on('change', function() {
                const opt = $(this).find('option:selected');
                const selectedName = opt.text();
                $('#post_code').val(opt.data('zip') || '');
                $('#sub_district_name').val(selectedName === 'Pilih Kelurahan/Desa' ? '' : selectedName);
            });

            // --- INISIALISASI DATA AWAL UNTUK EDIT (DENGAN ASYNC/AWAIT) ---
            // Kita bungkus dalam async function agar bisa berurutan
            (async function initAddressData() {
                if (current.provinceId) {
                    await loadProvinces(current.provinceId);

                    if (current.cityId) {
                        await loadCities(current.provinceId, current.cityId);

                        if (current.districtId) {
                            await loadDistricts(current.cityId, current.districtId);

                            if (current.subDistrictId) {
                                await loadSubDistricts(current.districtId, current.subDistrictId);
                            }
                        }
                    }
                } else {
                    // Jika tidak ada data lama, load provinsi saja
                    loadProvinces();
                }
            })();
        });
    </script>
@endpush
