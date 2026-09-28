@extends('backend.layouts.master')

@section('main-content')
    <div class="card">
        <h5 class="card-header">Tambahkan pengumuman</h5>
        <div class="card-body">
            <form method="post" action="{{ route('announcements.store') }}">
                @csrf
                <div class="form-group">
                    <label for="content" class="col-form-label">Pengumuman</label>
                    <textarea class="form-control" id="content  " name="content">{{ old('content') }}</textarea>
                    @error('content')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="is_active" class="col-form-label">Status <span class="text-danger">*</span></label>
                    <select name="is_active" class="form-control" id="is_active">
                        <option value="1">Active</option>
                        <option value="0">Inactive</option>
                    </select>
                    @error('is_active')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group mb-3">
                    <button type="reset" class="btn btn-warning">Reset</button>
                    <button class="btn btn-success" type="submit">Submit</button>
                </div>
            </form>
        </div>
    </div>
@endsection
