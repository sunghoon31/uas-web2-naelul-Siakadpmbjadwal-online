@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Edit Profile</h1>

    <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="mb-3">
            <label for="name" class="form-label">Nama</label>
            <input type="text" name="name" id="name" class="form-control" value="{{ old('name', $user->name) }}">
            @error('name')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" name="email" id="email" class="form-control" value="{{ old('email', $user->email) }}">
            @error('email')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="profile_photo" class="form-label">Foto Profile</label>
            <input type="file" name="profile_photo" id="profile_photo" class="form-control">
            @error('profile_photo')
                <div class="text-danger">{{ $message }}</div>
            @enderror

            @if($user->profile_photo)
                <img src="{{ asset('storage/profile/' . $user->profile_photo) }}" 
                     alt="Profile Photo" class="img-thumbnail mt-2" width="150">
            @endif
        </div>

        <div class="mb-3">
            <label for="password" class="form-label">Password (kosongkan jika tidak ingin diubah)</label>
            <input type="password" name="password" id="password" class="form-control">
            @error('password')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="password_confirmation" class="form-label">Konfirmasi Password</label>
            <input type="password" name="password_confirmation" id="password_confirmation" class="form-control">
        </div>

        <button type="submit" class="btn btn-success">Simpan Perubahan</button>
        <a href="{{ route('profile.show') }}" class="btn btn-secondary">Batal</a>
    </form>
</div>
@endsection
