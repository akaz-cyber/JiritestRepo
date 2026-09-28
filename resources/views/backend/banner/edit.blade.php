@extends('backend.layouts.master')
@section('title', 'JIRIFARM || Banner Edit')

@section('main-content')

    <div class="card">
        <h5 class="card-header">Edit Banner</h5>
        <div class="card-body">
            <form method="post" action="{{ route('banner.update', $banner->id) }}" enctype="multipart/form-data">
                @csrf
                @method('PATCH')

                {{-- LINK --}}
                <div class="form-group">
                    <label for="inputLink" class="col-form-label">Link <span class="text-danger">*</span></label>
                    <input id="inputLink" type="url" name="link" placeholder="https://example.com"
                        value="{{ $banner->link }}" class="form-control">
                    @error('link')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                {{-- PHOTO UPLOAD --}}
                <div class="form-group mb-3">
                    <label for="photo">Photo *</label>
                    <input type="file" class="form-control" id="photo" name="photo" accept="image/*">

                    <p class="mt-2">Current Image:</p>
                    <div class="mt-2">
                        <img id="currentImage" src="{{ $banner->photo ? asset('storage/' . $banner->photo) : '#' }}"
                            alt="Current Banner Image"
                            style="max-height: 120px; border-radius: 4px; {{ !$banner->photo ? 'display:none;' : '' }}">
                    </div>

                    <div class="mt-2">
                        <img id="newImagePreview" src="#" alt="New Banner Image Preview"
                            style="max-height: 120px; border-radius: 4px; display:none;">
                    </div>

                    @error('photo')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                {{-- STATUS --}}
                <div class="form-group">
                    <label>Status <span class="text-danger">*</span></label>
                    <select name="status" class="form-control">
                        <option value="active" {{ $banner->status == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ $banner->status == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                    @error('status')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                {{-- BUTTON --}}
                <div class="form-group mb-3">
                    <button class="btn btn-success" type="submit">Update</button>
                </div>
            </form>
        </div>
    </div>

@endsection

@push('styles')
    <link rel="stylesheet" href="{{ asset('backend/summernote/summernote.min.css') }}">
@endpush

@push('scripts')
    <script>
        // PREVIEW FOTO BARU
        document.getElementById('photo').addEventListener('change', function(event) {
            let reader = new FileReader();
            reader.onload = function() {
                let currentImage = document.getElementById('currentImage');
                let newImagePreview = document.getElementById('newImagePreview');

                // Sembunyikan gambar lama saat ada gambar baru yang dipilih
                if (currentImage) {
                    currentImage.style.display = 'none';
                }

                // Tampilkan gambar baru di #newImagePreview
                newImagePreview.src = reader.result;
                newImagePreview.style.display = 'block';
            };
            if (event.target.files[0]) {
                reader.readAsDataURL(event.target.files[0]);
            } else {
                // Jika user membatalkan pilihan file, sembunyikan preview baru dan tampilkan lagi yang lama (jika ada)
                let currentImage = document.getElementById('currentImage');
                let newImagePreview = document.getElementById('newImagePreview');
                newImagePreview.style.display = 'none';
                newImagePreview.src = '#'; // Reset src
                if (currentImage.src !== '#' && currentImage.src !== '') { // Cek apakah ada gambar lama
                    currentImage.style.display = 'block';
                }
            }
        });
    </script>
@endpush
