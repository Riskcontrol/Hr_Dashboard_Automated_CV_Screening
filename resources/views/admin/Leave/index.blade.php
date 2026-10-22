@extends('layouts.app')

@section('content')

<div class="mb-6 flex justify-between items-center">
    <div>
        <h1 class="text-3xl font-bold text-gray-900">Annual Leave Tracking</h1>
        <p class="text-gray-500 mt-1">Manage and approve staff leave requests</p>
    </div>
    <a href="{{ route('admin.leave.create') }}" class="btn btn-primary">
        <i class="fas fa-plus mr-2"></i> New Request
    </a>
</div>

{{-- Default Allowances --}}
<div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
    <p class="text-sm font-semibold text-blue-800 mb-2">
        <i class="fas fa-info-circle mr-2"></i>Default Annual Leave Allowances
    </p>
    <div class="flex flex-wrap gap-3">
        <span class="text-xs bg-white border border-blue-200 text-blue-700 px-3 py-1 rounded-full">Annual — 21 days</span>
        <span class="text-xs bg-white border border-blue-200 text-blue-700 px-3 py-1 rounded-full">Sick — 12 days</span>
        <span class="text-xs bg-white border border-blue-200 text-blue-700 px-3 py-1 rounded-full">Casual — 5 days</span>
        <span class="text-xs bg-white border border-blue-200 text-blue-700 px-3 py-1 rounded-full">Maternity — 90 days</span>
        <span class="text-xs bg-white border border-blue-200 text-blue-700 px-3 py-1 rounded-full">Paternity — 5 days</span>
        <span class="text-xs bg-white border border-blue-200 text-blue-700 px-3 py-1 rounded-full">Unpaid — Unlimited</span>
    </div>
</div>

{{-- Stat Cards --}}
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

{{-- Filters --}}
<div class="bg-white rounded-lg shadow mb-6 p-4">
    <form method="GET" action="{{ route('admin.leave.index') }}"
          class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-3 items-end">

        <div class="lg:col-span-2">
            <label class="form-label">Search Staff Name</label>
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="e.g. Naomi Nosa" class="form-input">
        </div>

        <div>
            <label class="form-label">Status</label>
            <select name="status" class="form-select">
                <option value="">All</option>
                <option value="pending"  {{ request('status') == 'pending'  ? 'selected' : '' }}>Pending</option>
                <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
            </select>
        </div>

        <div>
            <label class="form-label">Leave Type</label>
            <select name="leave_type" class="form-select">
                <option value="">All Types</option>
                @foreach(['annual' => 'Annual', 'sick' => 'Sick', 'casual' => 'Casual', 'maternity' => 'Maternity', 'paternity' => 'Paternity', 'unpaid' => 'Unpaid'] as $val => $label)
                    <option value="{{ $val }}" {{ request('leave_type') == $val ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="form-label">Department</label>
            <select name="department" class="form-select">
                <option value="">All</option>
                @foreach($departments as $dept)
                    <option value="{{ $dept }}" {{ request('department') == $dept ? 'selected' : '' }}>{{ $dept }}</option>
                @endforeach
            </select>
        </div>

        <div class="flex gap-2">
            <button type="submit" class="btn btn-primary flex-1">
                <i class="fas fa-search mr-1"></i> Filter
            </button>
            @if(request()->hasAny(['search','status','leave_type','department','from','to']))
                <a href="{{ route('admin.leave.index') }}" class="btn btn-outline">Clear</a>
            @endif
        </div>

    </form>
</div>

{{-- Leave Table --}}
<div class="bg-white rounded-lg shadow overflow-hidden">
    @if($leaves->count())
    <table class="w-full">
        <thead class="bg-gray-50 border-b">
            <tr>
                <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Staff Member</th>
                <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Leave Type</th>
                <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Dates</th>
                <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Days</th>
                <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Approver</th>
                <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Status</th>
                <th class="px-6 py-3"></th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @foreach($leaves as $leave)
            <tr class="hover:bg-gray-50 cursor-pointer"
                onclick="window.location='{{ route('admin.leave.show', $leave) }}'">
                <td class="px-6 py-4">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-primary-100 text-primary-700 flex items-center justify-center font-bold text-xs flex-shrink-0">
                            {{ $leave->staffProfile->initials ?? '?' }}
                        </div>
                        <div>
                            <p class="font-medium text-gray-900 text-sm">{{ $leave->staffProfile->full_name ?? 'Unknown' }}</p>
                            <p class="text-xs text-gray-500">{{ $leave->staffProfile->department ?? '' }}</p>
                        </div>
                    </div>
                </td>
                <td class="px-6 py-4">
                    <p class="text-sm text-gray-900">{{ $leave->leave_type_label }}</p>
                    @php $allowance = \App\Http\Controllers\Admin\LeaveRequestController::DEFAULT_ALLOWANCES[$leave->leave_type] ?? null; @endphp
                    <p class="text-xs text-gray-400">{{ $allowance ? "Max {$allowance} days/yr" : 'Unlimited' }}</p>
                </td>
                <td class="px-6 py-4">
                    <p class="text-sm text-gray-900">{{ $leave->start_date->format('M d, Y') }}</p>
                    <p class="text-xs text-gray-500">to {{ $leave->end_date->format('M d, Y') }}</p>
                </td>
                <td class="px-6 py-4">
                    <span class="font-bold text-gray-900">{{ $leave->total_days }}</span>
                    <span class="text-xs text-gray-500"> day{{ $leave->total_days != 1 ? 's' : '' }}</span>
                </td>
                <td class="px-6 py-4">
                    <p class="text-sm text-gray-900">{{ $leave->approver_name }}</p>
                    <p class="text-xs text-gray-500">{{ $leave->approver_type === 'hr' ? 'HR' : 'Line Manager' }}</p>
                </td>
                <td class="px-6 py-4">
                    <span class="badge {{ $leave->status_color }}">{{ ucfirst($leave->status) }}</span>
                </td>
                <td class="px-6 py-4">
                    <i class="fas fa-chevron-right text-gray-400 text-sm"></i>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <div class="p-4 border-t">{{ $leaves->withQueryString()->links() }}</div>

    @else
        <div class="text-center py-16">
            <i class="fas fa-calendar-times text-gray-300 text-5xl mb-4"></i>
            <h3 class="text-lg font-medium text-gray-900 mb-2">No leave requests found</h3>
            <p class="text-gray-500 mb-4">
                @if(request()->hasAny(['search','status','leave_type','department']))
                    No results match your filters.
                    <a href="{{ route('admin.leave.index') }}" class="text-blue-600">Clear filters</a>
                @else
                    No requests submitted yet.
                @endif
            </p>
            <a href="{{ route('admin.leave.create') }}" class="btn btn-primary">
                <i class="fas fa-plus mr-2"></i> New Leave Request
            </a>
        </div>
    @endif
</div>

@endsection