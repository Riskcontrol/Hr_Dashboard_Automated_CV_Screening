@extends('layouts.app')

@section('content')

<div class="mb-6 flex items-center gap-4">
    <a href="{{ route('admin.dashboard') }}" class="text-gray-500 hover:text-gray-700">
        <i class="fas fa-arrow-left"></i>
    </a>
    <h1 class="text-3xl font-bold text-gray-900">Change My Password</h1>
</div>

<div class="bg-white rounded-lg shadow p-6 max-w-md">

    @if(session('success'))
        <div class="alert alert-success mb-4"><i class="fas fa-check-circle mr-2"></i>{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger mb-4">
            @foreach($errors->all() as $e)<p>{{ $e }}</p>@endforeach
        </div>
    @endif

    <form method="POST" action="{{ route('admin.users.update-own-password') }}" class="space-y-4">
        @csrf
        <div>
            <label class="form-label">Current Password <span class="text-red-500">*</span></label>
            <input type="password" name="current_password" class="form-input" required
                   placeholder="Your current password">
        </div>
        <div>
            <label class="form-label">New Password <span class="text-red-500">*</span></label>
            <input type="password" name="password" class="form-input" required minlength="8"
                   placeholder="Minimum 8 characters">
        </div>
        <div>
            <label class="form-label">Confirm New Password <span class="text-red-500">*</span></label>
            <input type="password" name="password_confirmation" class="form-input" required>
        </div>
        <div class="flex justify-end">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-key mr-2"></i> Update My Password
            </button>
        </div>
    </form>
</div>

@endsection