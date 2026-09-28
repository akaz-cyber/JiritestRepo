@extends('backend.layouts.master')

@section('main-content')
    <div class="card">
        <h5 class="card-header">Edit Pertanyaan Yang Sering Diajukan</h5>
        <div class="card-body">
            <form method="post" action="{{ route('faq.update', $faq->id) }}">
                @csrf
                @method('PATCH')
                <div class="form-group">
                    <label for="inputTitle" class="col-form-label">Pertanyaan <span class="text-danger">*</span></label>
                    <input id="inputTitle" type="text" name="title" placeholder="Masukkan Pertanyaan"
                        value="{{ $faq->title }}" class="form-control">
                    @error('title')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="inputDesc" class="col-form-label">Jawaban <span class="text-danger">*</span></label>
                    <textarea class="form-control" id="content" name="content">{{ $faq->content }}</textarea>
                    @error('content')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="status" class="col-form-label">Status <span class="text-danger">*</span></label>
                    <select name="status" class="form-control">
                        <option value="active" {{ $faq->status == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ $faq->status == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                    @error('status')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
                <div class="form-group mb-3">
                    <button class="btn btn-success" type="submit">Update</button>
                </div>
            </form>
        </div>
    </div>
@endsection
