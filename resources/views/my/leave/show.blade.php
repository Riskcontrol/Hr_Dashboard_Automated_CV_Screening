{{--
    resources/views/my/leave/show.blade.php
    Staff self-service — read-only view of one of your own leave requests.
    No approve/reject controls here; only HR has those (in /admin).
--}}
@extends('layouts.app')

@section('content')

<div class="mb-6 flex items-center gap-4">
    <a href="{{ route('my.leave.index') }}" class="text-gray-500 hover:text-gray-700"><i class="fas fa-arrow-left"></i></a>
    <h1 class="text-3xl font-bold text-gray-900">Leave Request Details</h1>
</div>

<div class="mb-6 p-4 rounded-lg border
    {{ $leave->status === 'approved' ? 'bg-green-50 border-green-200' :
       ($leave->status === 'rejected' ? 'bg-red-50 border-red-200' : 'bg-yellow-50 border-yellow-200') }}">
    <div class="flex items-center gap-3">
        <span class="badge {{ $leave->status_color }} text-sm px-3 py-1">{{ ucfirst($leave->status) }}</span>
        <span class="text-sm text-gray-600">
            @if($leave->status === 'pending')
                Awaiting approval from <strong>{{ $leave->approver_name }}</strong>
            @else
                {{ ucfirst($leave->status) }} by <strong>{{ $leave->approved_by }}</strong>
                on {{ $leave->approved_at?->format('M d, Y \a\t g:i A') }}
            @endif
        </span>
    </div>
    @if($leave->status === 'rejected' && $leave->rejection_reason)
        <p class="mt-2 text-sm text-red-700"><strong>Reason:</strong> {{ $leave->rejection_reason }}</p>
    @endif
</div>

<div class="bg-white p-6 rounded-lg shadow grid grid-cols-2 gap-6 max-w-2xl">
    <div><p class="text-sm text-gray-500">Leave Type</p><p class="font-medium">{{ $leave->leave_type_label }}</p></div>
    <div><p class="text-sm text-gray-500">Total Days</p><p class="font-medium">{{ $leave->total_days }}</p></div>
    <div><p class="text-sm text-gray-500">Start Date</p><p class="font-medium">{{ $leave->start_date->format('M d, Y') }}</p></div>
    <div><p class="text-sm text-gray-500">End Date</p><p class="font-medium">{{ $leave->end_date->format('M d, Y') }}</p></div>
    <div class="col-span-2"><p class="text-sm text-gray-500">Reason</p><p class="font-medium">{{ $leave->reason ?: '—' }}</p></div>
</div>

@endsection