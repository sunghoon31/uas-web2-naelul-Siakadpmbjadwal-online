@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Profile Saya</h1>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="mb-3">
        <img src="{{ $user->profile_photo ? asset('storage/profile/' . $user->profile_photo) : asset('default-avatar.png') }}" 
             alt="Profile Photo" class="img-thumbnail" width="150">
    </div>

    <p><strong>Nama:</strong> {{ $user->name }}</p>
    <p><strong>Email:</strong> {{ $user->email }}</p>

    <a href="{{ route('profile.edit') }}" class="btn btn-primary">Edit Profile</a>
</div>
@endsection
