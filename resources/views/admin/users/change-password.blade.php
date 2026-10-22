@extends('layouts.app')

@section('content')

<div class="mb-6 flex items-center gap-4">
    <a href="{{ route('admin.users.index') }}" class="text-gray-500 hover:text-gray-700">
        <i class="fas fa-arrow-left"></i>
    </a>
    <h1 class="text-3xl font-bold text-gray-900">Change Password</h1>
</div>

<div class="bg-white rounded-lg shadow p-6 max-w-md">
    <div class="flex items-center gap-3 mb-6 pb-4 border-b">
        <div class="w-10 h-10 rounded-full bg-primary-100 text-primary-700 flex items-center justify-center font-bold">
            {{ strtoupper(substr($user->name, 0, 2)) }}
        </div>
        <div>
            <p class="font-semibold text-gray-900">{{ $user->name }}</p>
            <p class="text-sm text-gray-500">{{ $user->email }}</p>
        </div>
    </div>

    @if($errors->any())
        <div class="alert alert-danger mb-4">
            @foreach($errors->all() as $e)<p>{{ $e }}</p>@endforeach
        </div>
    @endif

    <form method="POST" action="{{ route('admin.users.update-password', $user) }}" class="space-y-4">
        @csrf
        <div>
            <label class="form-label">New Password <span class="text-red-500">*</span></label>
            <input type="password" name="password" class="form-input" required minlength="8"
                   placeholder="Minimum 8 characters">
        </div>
        <div>
            <label class="form-label">Confirm New Password <span class="text-red-500">*</span></label>
            <input type="password" name="password_confirmation" class="form-input" required>
        </div>
        <div class="bg-yellow-50 border border-yellow-200 rounded p-3 text-sm text-yellow-800">
            <i class="fas fa-exclamation-triangle mr-1"></i>
            Inform the staff member of their new password after saving.
        </div>
        <div class="flex justify-end gap-3 pt-2">
            <a href="{{ route('admin.users.index') }}" class="btn btn-outline">Cancel</a>
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-key mr-2"></i> Update Password
            </button>
        </div>
    </form>
</div>

@endsection