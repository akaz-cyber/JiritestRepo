@extends('backend.layouts.master')

@section('main-content')
    <div class="card">
        <h5 class="card-header">Tambahkan Pertanyaan Yang Sering Diajukan</h5>
        <div class="card-body">
            <form method="post" action="{{ route('faq.store') }}">
                @csrf
                <div class="form-group">
                    <label for="inputTitle" class="col-form-label">Pertanyaan <span class="text-danger">*</span></label>
                    <input id="inputTitle" type="text" name="title" placeholder="Masukkan Pertanyaan"
                        value="{{ old('title') }}" class="form-control">
                    @error('title')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="inputDesc" class="col-form-label">Jawaban <span class="text-danger">*</span></label>
                    <textarea class="form-control" id="content" name="content">{{ old('content') }}</textarea>
                    @error('content')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

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

                <div class="form-group mb-3">
                    <button type="reset" class="btn btn-warning">Reset</button>
                    <button class="btn btn-success" type="submit">Submit</button>
                </div>
            </form>
        </div>
    </div>
@endsection
