@extends('backend.layouts.master')

@section('main-content')
    <div class="card">
        <h5 class="card-header">Edit pengumuman</h5>
        <div class="card-body">
            <form method="post" action="{{ route('announcements.update', $announcement->id) }}">
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label for="content" class="col-form-label">Pengumuman</label>
                    <textarea class="form-control" id="content" name="content">{{ old('content', $announcement->content) }}</textarea>
                    @error('content')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="is_active" class="col-form-label">Status <span class="text-danger">*</span></label>
                    <select name="is_active" class="form-control" id="is_active">
                        <option value="1" {{ old('is_active', $announcement->is_active) == 1 ? 'selected' : '' }}>
                            Active
                        </option>
                        <option value="0" {{ old('is_active', $announcement->is_active) == 0 ? 'selected' : '' }}>
                            Inactive
                        </option>
                    </select>
                    @error('is_active')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group mb-3">
                    <button type="reset" class="btn btn-warning">Reset</button>
                    <button class="btn btn-success" type="submit">Update</button>
                </div>
            </form>
        </div>
    </div>
@endsection
