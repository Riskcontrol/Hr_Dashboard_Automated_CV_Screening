{{--
    resources/views/my/leave/index.blade.php
    Staff self-service — shows ONLY the logged in staff member's own leave requests.
--}}
@extends('layouts.app')

@section('content')

<div class="mb-6 flex justify-between items-center">
    <div>
        <h1 class="text-3xl font-bold text-gray-900">My Leave</h1>
        <p class="text-gray-500 mt-1">Your own leave requests and their status</p>
    </div>
    <a href="{{ route('my.leave.create') }}" class="btn btn-primary">
        <i class="fas fa-plus mr-2"></i> Request Leave
    </a>
</div>

<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
    <div class="bg-white p-5 rounded-lg shadow flex items-center gap-4">
        <div class="p-3 rounded-full bg-blue-100 text-blue-600"><i class="fas fa-calendar-alt text-xl"></i></div>
        <div><p class="text-2xl font-bold text-gray-900">{{ $stats['total'] }}</p><p class="text-gray-500 text-sm">Total</p></div>
    </div>
    <div class="bg-white p-5 rounded-lg shadow flex items-center gap-4">
        <div class="p-3 rounded-full bg-yellow-100 text-yellow-600"><i class="fas fa-clock text-xl"></i></div>
        <div><p class="text-2xl font-bold text-gray-900">{{ $stats['pending'] }}</p><p class="text-gray-500 text-sm">Pending</p></div>
    </div>
    <div class="bg-white p-5 rounded-lg shadow flex items-center gap-4">
        <div class="p-3 rounded-full bg-green-100 text-green-600"><i class="fas fa-check-circle text-xl"></i></div>
        <div><p class="text-2xl font-bold text-gray-900">{{ $stats['approved'] }}</p><p class="text-gray-500 text-sm">Approved</p></div>
    </div>
    <div class="bg-white p-5 rounded-lg shadow flex items-center gap-4">
        <div class="p-3 rounded-full bg-red-100 text-red-600"><i class="fas fa-times-circle text-xl"></i></div>
        <div><p class="text-2xl font-bold text-gray-900">{{ $stats['rejected'] }}</p><p class="text-gray-500 text-sm">Rejected</p></div>
    </div>
</div>

<div class="bg-white rounded-lg shadow overflow-hidden">
    <table class="w-full">
        <thead class="bg-gray-50">
            <tr>
                <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Type</th>
                <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Dates</th>
                <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Days</th>
                <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Status</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($leaves as $leave)
                <tr class="hover:bg-gray-50 cursor-pointer" onclick="window.location='{{ route('my.leave.show', $leave) }}'">
                    <td class="px-6 py-4">{{ $leave->leave_type_label }}</td>
                    <td class="px-6 py-4">{{ $leave->start_date->format('M d, Y') }} - {{ $leave->end_date->format('M d, Y') }}</td>
                    <td class="px-6 py-4">{{ $leave->total_days }}</td>
                    <td class="px-6 py-4"><span class="badge {{ $leave->status_color }}">{{ ucfirst($leave->status) }}</span></td>
                </tr>
            @empty
                <tr><td colspan="4" class="px-6 py-8 text-center text-gray-500">You haven't requested any leave yet.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">{{ $leaves->links() }}</div>

@endsection