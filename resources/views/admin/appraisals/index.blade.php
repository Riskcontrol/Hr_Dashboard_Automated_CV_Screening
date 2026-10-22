{{--
    resources/views/admin/appraisals/index.blade.php
    Probation & Appraisal — List Page
    Card layout, clickable rows, urgent alerts at top.
--}}
@extends('layouts.app')

@section('content')

<div class="mb-6 flex justify-between items-center">
    <div>
        <h1 class="text-3xl font-bold text-gray-900">Probation & Appraisals</h1>
        <p class="text-gray-500 mt-1">Schedule and track staff performance reviews</p>
    </div>
    <a href="{{ route('admin.appraisals.create') }}" class="btn btn-primary">
        <i class="fas fa-plus mr-2"></i> Schedule Appraisal
    </a>
</div>

{{-- STAT CARDS --}}
<div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-6">

    <div class="bg-white p-4 rounded-lg shadow flex items-center gap-3">
        <div class="p-2 rounded-full bg-blue-100 text-blue-600">
            <i class="fas fa-clipboard-list text-lg"></i>
        </div>
        <div>
            <p class="text-xl font-bold text-gray-900">{{ $stats['total'] }}</p>
            <p class="text-gray-500 text-xs">Total</p>
        </div>
    </div>

    <div class="bg-white p-4 rounded-lg shadow flex items-center gap-3">
        <div class="p-2 rounded-full bg-yellow-100 text-yellow-600">
            <i class="fas fa-clock text-lg"></i>
        </div>
        <div>
            <p class="text-xl font-bold text-gray-900">{{ $stats['pending'] }}</p>
            <p class="text-gray-500 text-xs">Pending</p>
        </div>
    </div>

    <div class="bg-white p-4 rounded-lg shadow flex items-center gap-3">
        <div class="p-2 rounded-full bg-red-100 text-red-600">
            <i class="fas fa-exclamation-circle text-lg"></i>
        </div>
        <div>
            <p class="text-xl font-bold text-gray-900">{{ $stats['overdue'] }}</p>
            <p class="text-gray-500 text-xs">Overdue</p>
        </div>
    </div>

    <div class="bg-white p-4 rounded-lg shadow flex items-center gap-3">
        <div class="p-2 rounded-full bg-orange-100 text-orange-600">
            <i class="fas fa-bell text-lg"></i>
        </div>
        <div>
            <p class="text-xl font-bold text-gray-900">{{ $stats['due_soon'] }}</p>
            <p class="text-gray-500 text-xs">Due in 14 Days</p>
        </div>
    </div>

    <div class="bg-white p-4 rounded-lg shadow flex items-center gap-3">
        <div class="p-2 rounded-full bg-green-100 text-green-600">
            <i class="fas fa-check-circle text-lg"></i>
        </div>
        <div>
            <p class="text-xl font-bold text-gray-900">{{ $stats['completed'] }}</p>
            <p class="text-gray-500 text-xs">Completed</p>
        </div>
    </div>

</div>

{{-- Alert banner for overdue --}}
@if($stats['overdue'] > 0)
<div class="mb-6 p-4 rounded-lg border bg-red-50 border-red-200">
    <div class="flex items-center gap-2 text-red-800">
        <i class="fas fa-exclamation-circle"></i>
        <p class="text-sm">
            <strong>{{ $stats['overdue'] }}</strong> appraisal(s) are overdue.
            Please review and complete them as soon as possible.
        </p>
    </div>
</div>
@endif

{{-- FILTER BAR --}}
<div class="bg-white rounded-lg shadow mb-6 p-4">
    <form method="GET" action="{{ route('admin.appraisals.index') }}" class="flex flex-wrap gap-3 items-end">

        <div class="flex-1 min-w-48">
            <label class="form-label">Search Staff</label>
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Staff name..." class="form-input">
        </div>

        <div class="min-w-48">
            <label class="form-label">Appraisal Type</label>
            <select name="appraisal_type" class="form-select">
                <option value="">All Types</option>
                @foreach([
                    'probation_3month' => 'Probation (3 Months)',
                    'probation_6month' => 'Probation (6 Months)',
                    'annual'           => 'Annual Appraisal',
                    'mid_year'         => 'Mid-Year Review',
                    'pip'              => 'Performance Improvement Plan',
                ] as $val => $label)
                    <option value="{{ $val }}" {{ request('appraisal_type') == $val ? 'selected' : '' }}>
                        {{ $label }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="min-w-36">
            <label class="form-label">Status</label>
            <select name="status" class="form-select">
                <option value="">All</option>
                <option value="pending"     {{ request('status') == 'pending'     ? 'selected' : '' }}>Pending</option>
                <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                <option value="completed"   {{ request('status') == 'completed'   ? 'selected' : '' }}>Completed</option>
                <option value="cancelled"   {{ request('status') == 'cancelled'   ? 'selected' : '' }}>Cancelled</option>
            </select>
        </div>

        <button type="submit" class="btn btn-primary">
            <i class="fas fa-search mr-2"></i> Filter
        </button>

        @if(request()->hasAny(['search', 'appraisal_type', 'status']))
            <a href="{{ route('admin.appraisals.index') }}" class="btn btn-outline">Clear</a>
        @endif

    </form>
</div>

{{-- APPRAISALS LIST — clickable cards --}}
<div class="space-y-4">

    @if($appraisals->count())

        @foreach($appraisals as $appraisal)
        <a href="{{ route('admin.appraisals.show', $appraisal) }}"
           class="block bg-white rounded-lg shadow hover:shadow-md
                  border {{ $appraisal->is_overdue ? 'border-red-200' : 'border-transparent' }}
                  hover:border-primary-300 transition-all duration-200 cursor-pointer">
            <div class="p-5">
                <div class="flex flex-wrap items-center justify-between gap-4">

                    {{-- Staff Member --}}
                    <div class="flex items-center gap-3 min-w-48">
                        <div class="w-10 h-10 rounded-full bg-primary-100 text-primary-700
                                    flex items-center justify-center font-bold text-sm flex-shrink-0">
                            {{ $appraisal->staffProfile->initials ?? '?' }}
                        </div>
                        <div>
                            <p class="font-semibold text-gray-900">
                                {{ $appraisal->staffProfile->full_name ?? 'Unknown' }}
                            </p>
                            <p class="text-xs text-gray-500">
                                {{ $appraisal->staffProfile->job_title ?? '' }}
                            </p>
                        </div>
                    </div>

                    {{-- Appraisal Type --}}
                    <div class="min-w-48">
                        <p class="text-xs text-gray-400 uppercase mb-1">Type</p>
                        <p class="text-sm font-medium text-gray-900">{{ $appraisal->appraisal_type_label }}</p>
                    </div>

                    {{-- Due Date --}}
                    <div class="min-w-36">
                        <p class="text-xs text-gray-400 uppercase mb-1">Due Date</p>
                        <p class="text-sm text-gray-900">{{ $appraisal->due_date->format('M d, Y') }}</p>
                        @if($appraisal->is_overdue)
                            <p class="text-xs text-red-600 font-medium">
                                {{ abs($appraisal->days_until_due) }} day(s) overdue
                            </p>
                        @elseif($appraisal->days_until_due <= 14 && $appraisal->status !== 'completed')
                            <p class="text-xs text-orange-600">
                                Due in {{ $appraisal->days_until_due }} day(s)
                            </p>
                        @endif
                    </div>

                    {{-- Reviewer --}}
                    <div class="min-w-36">
                        <p class="text-xs text-gray-400 uppercase mb-1">Reviewer</p>
                        <p class="text-sm text-gray-900">{{ $appraisal->reviewer_name }}</p>
                        <p class="text-xs text-gray-500">{{ $appraisal->reviewer_role ?? '' }}</p>
                    </div>

                    {{-- Rating (if completed) --}}
                    <div class="min-w-24">
                        @if($appraisal->overall_rating)
                            <p class="text-xs text-gray-400 uppercase mb-1">Rating</p>
                            <span class="badge {{ $appraisal->rating_color }}">
                                {{ ucfirst(str_replace('_', ' ', $appraisal->overall_rating)) }}
                            </span>
                        @endif
                    </div>

                    {{-- Status + Arrow --}}
                    <div class="flex items-center gap-3">
                        <span class="badge {{ $appraisal->status_color }}">
                            {{ ucfirst(str_replace('_', ' ', $appraisal->status)) }}
                        </span>
                        <i class="fas fa-chevron-right text-gray-400 text-sm"></i>
                    </div>

                </div>
            </div>
        </a>
        @endforeach

        <div class="mt-4">
            {{ $appraisals->links() }}
        </div>

    @else
        <div class="bg-white rounded-lg shadow text-center py-16">
            <i class="fas fa-clipboard-list text-gray-300 text-5xl mb-4"></i>
            <h3 class="text-lg font-medium text-gray-900 mb-2">No appraisals scheduled</h3>
            <p class="text-gray-500 mb-4">Schedule the first appraisal to get started.</p>
            <a href="{{ route('admin.appraisals.create') }}" class="btn btn-primary">
                <i class="fas fa-plus mr-2"></i> Schedule Appraisal
            </a>
        </div>
    @endif

</div>

@endsection