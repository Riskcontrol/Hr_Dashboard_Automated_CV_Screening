{{--
    resources/views/admin/staff/index.blade.php
    Staff Profiles — List Page (card layout, no horizontal scroll)
--}}
@extends('layouts.app')

@section('content')

{{-- Page Header --}}
<div class="mb-6 flex justify-between items-center">
    <div>
        <h1 class="text-3xl font-bold text-gray-900">Staff Profiles</h1>
        <p class="text-gray-500 mt-1">Central employee database</p>
    </div>
    <a href="{{ route('admin.staff.create') }}" class="btn btn-primary">
        <i class="fas fa-plus mr-2"></i> Add Staff Member
    </a>
</div>

{{-- STAT CARDS --}}
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">

    <div class="bg-white p-5 rounded-lg shadow flex items-center gap-4">
        <div class="p-3 rounded-full bg-blue-100 text-blue-600">
            <i class="fas fa-users text-xl"></i>
        </div>
        <div>
            <p class="text-2xl font-bold text-gray-900">{{ $stats['total'] }}</p>
            <p class="text-gray-500 text-sm">Total Staff</p>
        </div>
    </div>

    <div class="bg-white p-5 rounded-lg shadow flex items-center gap-4">
        <div class="p-3 rounded-full bg-green-100 text-green-600">
            <i class="fas fa-user-check text-xl"></i>
        </div>
        <div>
            <p class="text-2xl font-bold text-gray-900">{{ $stats['active'] }}</p>
            <p class="text-gray-500 text-sm">Active</p>
        </div>
    </div>

    <div class="bg-white p-5 rounded-lg shadow flex items-center gap-4">
        <div class="p-3 rounded-full bg-red-100 text-red-600">
            <i class="fas fa-user-times text-xl"></i>
        </div>
        <div>
            <p class="text-2xl font-bold text-gray-900">{{ $stats['inactive'] }}</p>
            <p class="text-gray-500 text-sm">Inactive</p>
        </div>
    </div>

    <div class="bg-white p-5 rounded-lg shadow flex items-center gap-4">
        <div class="p-3 rounded-full bg-purple-100 text-purple-600">
            <i class="fas fa-user-plus text-xl"></i>
        </div>
        <div>
            <p class="text-2xl font-bold text-gray-900">{{ $stats['this_month'] }}</p>
            <p class="text-gray-500 text-sm">Added This Month</p>
        </div>
    </div>

</div>

{{-- SEARCH & FILTER BAR --}}
<div class="bg-white rounded-lg shadow mb-6 p-4">
    <form method="GET" action="{{ route('admin.staff.index') }}" class="flex flex-wrap gap-3 items-end">

        <div class="flex-1 min-w-48">
            <label class="form-label">Search</label>
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Name, email, ID, title…" class="form-input">
        </div>

        <div class="min-w-40">
            <label class="form-label">Department</label>
            <select name="department" class="form-select">
                <option value="">All Departments</option>
                @foreach($departments as $dept)
                    <option value="{{ $dept }}" {{ request('department') == $dept ? 'selected' : '' }}>
                        {{ $dept }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="min-w-36">
            <label class="form-label">Status</label>
            <select name="status" class="form-select">
                <option value="">All</option>
                <option value="active"     {{ request('status') == 'active'     ? 'selected' : '' }}>Active</option>
                <option value="inactive"   {{ request('status') == 'inactive'   ? 'selected' : '' }}>Inactive</option>
                <option value="suspended"  {{ request('status') == 'suspended'  ? 'selected' : '' }}>Suspended</option>
                <option value="terminated" {{ request('status') == 'terminated' ? 'selected' : '' }}>Terminated</option>
            </select>
        </div>

        <button type="submit" class="btn btn-primary">
            <i class="fas fa-search mr-2"></i> Search
        </button>

        @if(request()->hasAny(['search', 'department', 'status']))
            <a href="{{ route('admin.staff.index') }}" class="btn btn-outline">Clear</a>
        @endif

    </form>
</div>

{{-- STAFF LIST — Card layout, click anywhere to view profile --}}
<div class="space-y-4">

    @if($staff->count())

        @foreach($staff as $member)
        <a href="{{ route('admin.staff.show', $member) }}"
           class="block bg-white rounded-lg shadow hover:shadow-md hover:border-primary-300
                  border border-transparent transition-all duration-200 cursor-pointer">
            <div class="p-5">
                <div class="flex flex-wrap items-center justify-between gap-4">

                    {{-- Avatar + Name + Email --}}
                    <div class="flex items-center gap-3 min-w-56">
                        @if($member->profile_photo)
                            <img src="{{ Storage::url($member->profile_photo) }}"
                                 class="w-10 h-10 rounded-full object-cover flex-shrink-0">
                        @else
                            <div class="w-10 h-10 rounded-full bg-primary-100 text-primary-700
                                        flex items-center justify-center font-bold text-sm flex-shrink-0">
                                {{ $member->initials }}
                            </div>
                        @endif
                        <div>
                            <p class="font-semibold text-gray-900">{{ $member->full_name }}</p>
                            <p class="text-xs text-gray-500">{{ $member->email }}</p>
                        </div>
                    </div>

                    {{-- Employee ID --}}
                    <div class="min-w-24">
                        <p class="text-xs text-gray-400 uppercase mb-1">ID</p>
                        <p class="text-sm font-mono text-gray-900">{{ $member->employee_id }}</p>
                    </div>

                    {{-- Department --}}
                    <div class="min-w-32">
                        <p class="text-xs text-gray-400 uppercase mb-1">Department</p>
                        <p class="text-sm text-gray-900">{{ $member->department ?? '—' }}</p>
                    </div>

                    {{-- Job Title --}}
                    <div class="min-w-40">
                        <p class="text-xs text-gray-400 uppercase mb-1">Job Title</p>
                        <p class="text-sm text-gray-900">{{ $member->job_title ?? '—' }}</p>
                    </div>

                    {{-- Employment Type --}}
                    <div class="min-w-24">
                        <p class="text-xs text-gray-400 uppercase mb-1">Type</p>
                        <span class="badge badge-secondary">{{ $member->employment_type_label }}</span>
                    </div>

                    {{-- Status + Arrow --}}
                    <div class="flex items-center gap-3">
                        @if($member->status === 'active')
                            <span class="badge badge-success">Active</span>
                        @elseif($member->status === 'inactive')
                            <span class="badge badge-secondary">Inactive</span>
                        @elseif($member->status === 'suspended')
                            <span class="badge badge-warning">Suspended</span>
                        @else
                            <span class="badge badge-danger">Terminated</span>
                        @endif
                        <i class="fas fa-chevron-right text-gray-400 text-sm"></i>
                    </div>

                </div>
            </div>
        </a>
        @endforeach

        {{-- Pagination --}}
        <div class="mt-4">
            {{ $staff->links() }}
        </div>

    @else
        <div class="bg-white rounded-lg shadow text-center py-16">
            <i class="fas fa-users text-gray-300 text-5xl mb-4"></i>
            <h3 class="text-lg font-medium text-gray-900 mb-2">No staff profiles yet</h3>
            <p class="text-gray-500 mb-4">Add your first employee to get started.</p>
            <a href="{{ route('admin.staff.create') }}" class="btn btn-primary">
                <i class="fas fa-plus mr-2"></i> Add Staff Member
            </a>
        </div>
    @endif

</div>

@endsection