@extends('backend.layouts.master')

@section('main-content')
@if($errors->any())
<div class="alert alert-danger">
    <ul>
        @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif
<div class="card">
    <h5 class="card-header">Edit User</h5>
    <div class="card-body">
      <form method="post" action="{{route('users.update',$user->id)}}">
        @csrf 
        @method('PATCH')
        <div class="form-group">
          <label for="inputTitle" class="col-form-label">Name</label>
        <input id="inputTitle" type="text" name="name" placeholder="Enter name"  value="{{$user->name}}" class="form-control">
        @error('name')
        <span class="text-danger">{{$message}}</span>
        @enderror
        </div>

        <div class="form-group">
            <label for="inputEmail" class="col-form-label">Email</label>
          <input id="inputEmail" type="email" name="email" placeholder="Enter email"  value="{{$user->email}}" class="form-control">
          @error('email')
          <span class="text-danger">{{$message}}</span>
          @enderror
        </div>

        <div class="form-group">
            <label for="password">Password Baru</label>
            <input type="password" id="password" name="password" 
                  class="form-control"
                  placeholder="Biarkan kosong jika tidak ingin mengubah password">

            <label for="password_confirmation" class="mt-2">Konfirmasi Password Baru</label>
            <input type="password" id="password_confirmation" 
                  name="password_confirmation" class="form-control">
        </div>

        <div class="form-group">
            <label for="photo" class="col-form-label">Photo</label>

            {{-- Input file upload --}}
            <input type="file" name="photo" class="form-control">

            @error('photo')
                <span class="text-danger">{{ $message }}</span>
            @enderror

            {{-- Current Image Preview --}}
            @if($user->photo)
                <p class="mt-2 mb-1">Current Image:</p>
                <img src="{{ $user->photo }}"
                    alt="Current Profile Photo"
                    style="max-height: 120px; border-radius: 50%; margin-top: 5px;">
            @else
                <p class="mt-2 text-muted">No photo uploaded.</p>
            @endif
        </div>
        @php 
        $roles=DB::table('users')->select('role')->where('id',$user->id)->get();
        // dd($roles);
        @endphp
        <div class="form-group">
            <label for="role" class="col-form-label">Role</label>
            <select name="role" class="form-control">
                <option value="">-----Select Role-----</option>
                @foreach($roles as $role)
                    <option value="{{$role->role}}" {{(($role->role=='admin') ? 'selected' : '')}}>Admin</option>
                    <option value="{{$role->role}}" {{(($role->role=='user') ? 'selected' : '')}}>User</option>
                    <option value="{{$role->role}}" {{(($role->role=='super_admin') ? 'selected' : '')}}>Super Admin</option>
                @endforeach
            </select>
          @error('role')
          <span class="text-danger">{{$message}}</span>
          @enderror
          </div>
          <div class="form-group">
            <label for="status" class="col-form-label">Status</label>
            <select name="status" class="form-control">
                <option value="active" {{(($user->status=='active') ? 'selected' : '')}}>Active</option>
                <option value="inactive" {{(($user->status=='inactive') ? 'selected' : '')}}>Inactive</option>
            </select>
          @error('status')
          <span class="text-danger">{{$message}}</span>
          @enderror
          </div>
        <div class="form-group mb-3">
           <button class="btn btn-success" type="submit">Update</button>
        </div>
      </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
</script>
@endpush