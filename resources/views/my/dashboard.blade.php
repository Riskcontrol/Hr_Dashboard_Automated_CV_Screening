{{--
    resources/views/my/dashboard.blade.php
    Staff self-service dashboard — own leave summary + latest appraisal only.
--}}
@extends('layouts.app')

@section('content')

<div class="mb-6">
    <h1 class="text-3xl font-bold text-gray-900">Welcome, {{ $staffProfile->full_name }}</h1>
    <p class="text-gray-500 mt-1">{{ $staffProfile->job_title }} &middot; {{ $staffProfile->department }}</p>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
    <a href="{{ route('my.leave.index') }}" class="bg-white p-5 rounded-lg shadow flex items-center gap-4 hover:shadow-md transition">
        <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
            <i class="fas fa-clock text-xl"></i>
        </div>
        <div>
            <p class="text-2xl font-bold text-gray-900">{{ $leaveStats['pending'] }}</p>
            <p class="text-gray-500 text-sm">Pending Leave Requests</p>
        </div>
    </a>

    <a href="{{ route('my.leave.index') }}" class="bg-white p-5 rounded-lg shadow flex items-center gap-4 hover:shadow-md transition">
        <div class="p-3 rounded-full bg-green-100 text-green-600">
            <i class="fas fa-check-circle text-xl"></i>
        </div>
        <div>
            <p class="text-2xl font-bold text-gray-900">{{ $leaveStats['approved'] }}</p>
            <p class="text-gray-500 text-sm">Approved Leave</p>
        </div>
    </a>

    <a href="{{ route('my.appraisals.index') }}" class="bg-white p-5 rounded-lg shadow flex items-center gap-4 hover:shadow-md transition">
        <div class="p-3 rounded-full bg-blue-100 text-blue-600">
            <i class="fas fa-clipboard-list text-xl"></i>
        </div>
        <div>
            <p class="text-2xl font-bold text-gray-900">
                {{ $latestAppraisal ? ucfirst($latestAppraisal->status) : 'None yet' }}
            </p>
            <p class="text-gray-500 text-sm">Latest Appraisal</p>
        </div>
    </a>
</div>

<div class="bg-white p-6 rounded-lg shadow">
    <a href="{{ route('my.leave.create') }}" class="btn btn-primary">
        <i class="fas fa-plus mr-2"></i> Request Leave
    </a>
</div>

@endsection
