@extends('backend.layouts.master')

@section('title', 'JIRIFARM || Banner Create')

@section('main-content')

<div class="card">
    <h5 class="card-header">Add Banner</h5>
    <div class="card-body">
        <form method="post" action="{{ route('banner.store') }}" enctype="multipart/form-data">
            {{ csrf_field() }}

            {{-- LINK --}}
            <div class="form-group">
                <label for="inputLink" class="col-form-label">Link <span class="text-danger">*</span></label>
                <input id="inputLink" type="url" name="link" placeholder="https://example.com"
                       value="{{ old('link') }}" class="form-control">
                @error('link')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

            {{-- PHOTO UPLOAD --}}
            <div class="form-group">
                <label for="photo" class="col-form-label">Photo <span class="text-danger">*</span></label>

                <input type="file" name="photo" id="photo" class="form-control"
                       accept="image/*">

                @error('photo')
                    <small class="text-danger">{{ $message }}</small>
                @enderror

                {{-- PREVIEW IMAGE --}}
                <div class="mt-3">
                    <img id="previewImg" src="#" 
                         style="max-height: 150px; display:none; border:1px solid #ddd; padding:5px; border-radius:5px;">
                </div>
            </div>

            {{-- STATUS --}}
            <div class="form-group">
                <label for="status" class="col-form-label">Status <span class="text-danger">*</span></label>
                <select name="status" class="form-control">
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                </select>
                @error('status')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

            {{-- BUTTON --}}
            <div class="form-group mb-3">
                <button type="reset" class="btn btn-warning">Reset</button>
                <button class="btn btn-success" type="submit">Submit</button>
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
    // LIVE IMAGE PREVIEW
    document.getElementById('photo').addEventListener('change', function(event) {
        let reader = new FileReader();
        reader.onload = function() {
            let preview = document.getElementById('previewImg');
            preview.src = reader.result;
            preview.style.display = 'block';
        };
        reader.readAsDataURL(event.target.files[0]);
    });
</script>
@endpush
