{{--
    resources/views/admin/exit-reports/index.blade.php
    Exit Reports — List Page
    Shows all offboarding records with clearance progress.
--}}
@extends('layouts.app')

@section('content')

<div class="mb-6 flex justify-between items-center">
    <div>
        <h1 class="text-3xl font-bold text-gray-900">Exit Reports</h1>
        <p class="text-gray-500 mt-1">Offboarding, exit interviews & clearance tracking</p>
    </div>
    <a href="{{ route('admin.exit-reports.create') }}" class="btn btn-primary">
        <i class="fas fa-plus mr-2"></i> Start Exit Process
    </a>
</div>

{{-- STAT CARDS --}}
<div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-6">

    <div class="bg-white p-4 rounded-lg shadow flex items-center gap-3">
        <div class="p-2 rounded-full bg-blue-100 text-blue-600">
            <i class="fas fa-door-open text-lg"></i>
        </div>
        <div>
            <p class="text-xl font-bold text-gray-900">{{ $stats['total'] }}</p>
            <p class="text-gray-500 text-xs">Total Exits</p>
        </div>
    </div>

    <div class="bg-white p-4 rounded-lg shadow flex items-center gap-3">
        <div class="p-2 rounded-full bg-yellow-100 text-yellow-600">
            <i class="fas fa-spinner text-lg"></i>
        </div>
        <div>
            <p class="text-xl font-bold text-gray-900">{{ $stats['in_progress'] }}</p>
            <p class="text-gray-500 text-xs">In Progress</p>
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

    <div class="bg-white p-4 rounded-lg shadow flex items-center gap-3">
        <div class="p-2 rounded-full bg-red-100 text-red-600">
            <i class="fas fa-clipboard-list text-lg"></i>
        </div>
        <div>
            <p class="text-xl font-bold text-gray-900">{{ $stats['pending_clearance'] }}</p>
            <p class="text-gray-500 text-xs">Pending Clearance</p>
        </div>
    </div>

    <div class="bg-white p-4 rounded-lg shadow flex items-center gap-3">
        <div class="p-2 rounded-full bg-purple-100 text-purple-600">
            <i class="fas fa-calendar text-lg"></i>
        </div>
        <div>
            <p class="text-xl font-bold text-gray-900">{{ $stats['this_month'] }}</p>
            <p class="text-gray-500 text-xs">This Month</p>
        </div>
    </div>

</div>

{{-- FILTER BAR --}}
<div class="bg-white rounded-lg shadow mb-6 p-4">
    <form method="GET" action="{{ route('admin.exit-reports.index') }}" class="flex flex-wrap gap-3 items-end">

        <div class="flex-1 min-w-48">
            <label class="form-label">Search Staff</label>
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Staff name..." class="form-input">
        </div>

        <div class="min-w-40">
            <label class="form-label">Exit Type</label>
            <select name="exit_type" class="form-select">
                <option value="">All Types</option>
                @foreach([
                    'resignation'     => 'Resignation',
                    'termination'     => 'Termination',
                    'end_of_contract' => 'End of Contract',
                    'retirement'      => 'Retirement',
                    'redundancy'      => 'Redundancy',
                ] as $val => $label)
                    <option value="{{ $val }}" {{ request('exit_type') == $val ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
        </div>

        <div class="min-w-36">
            <label class="form-label">Status</label>
            <select name="status" class="form-select">
                <option value="">All</option>
                <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                <option value="completed"   {{ request('status') == 'completed'   ? 'selected' : '' }}>Completed</option>
                <option value="cancelled"   {{ request('status') == 'cancelled'   ? 'selected' : '' }}>Cancelled</option>
            </select>
        </div>

        <div class="min-w-40">
            <label class="form-label">Clearance</label>
            <select name="clearance" class="form-select">
                <option value="">All</option>
                <option value="pending"  {{ request('clearance') == 'pending'  ? 'selected' : '' }}>Pending Items</option>
                <option value="complete" {{ request('clearance') == 'complete' ? 'selected' : '' }}>Fully Cleared</option>
            </select>
        </div>

        <button type="submit" class="btn btn-primary">
            <i class="fas fa-search mr-2"></i> Filter
        </button>

        @if(request()->hasAny(['search', 'exit_type', 'status', 'clearance']))
            <a href="{{ route('admin.exit-reports.index') }}" class="btn btn-outline">Clear</a>
        @endif

    </form>
</div>

{{-- EXIT REPORTS LIST — clickable cards --}}
<div class="space-y-4">

    @if($exitReports->count())

        @foreach($exitReports as $report)
        <a href="{{ route('admin.exit-reports.show', $report) }}"
           class="block bg-white rounded-lg shadow hover:shadow-md hover:border-primary-300
                  border border-transparent transition-all duration-200 cursor-pointer">
            <div class="p-5">
                <div class="flex flex-wrap items-center justify-between gap-4">

                    {{-- Staff Member --}}
                    <div class="flex items-center gap-3 min-w-48">
                        <div class="w-10 h-10 rounded-full bg-gray-200 text-gray-700
                                    flex items-center justify-center font-bold text-sm flex-shrink-0">
                            {{ $report->staffProfile->initials ?? '?' }}
                        </div>
                        <div>
                            <p class="font-semibold text-gray-900">
                                {{ $report->staffProfile->full_name ?? 'Unknown' }}
                            </p>
                            <p class="text-xs text-gray-500">
                                {{ $report->staffProfile->job_title ?? '' }}
                            </p>
                        </div>
                    </div>

                    {{-- Exit Type --}}
                    <div class="min-w-32">
                        <p class="text-xs text-gray-400 uppercase mb-1">Exit Type</p>
                        <span class="badge {{ $report->exit_type_color }}">
                            {{ $report->exit_type_label }}
                        </span>
                    </div>

                    {{-- Last Working Day --}}
                    <div class="min-w-36">
                        <p class="text-xs text-gray-400 uppercase mb-1">Last Working Day</p>
                        <p class="text-sm text-gray-900">{{ $report->last_working_day->format('M d, Y') }}</p>
                    </div>

                    {{-- Clearance Progress --}}
                    <div class="min-w-40">
                        <p class="text-xs text-gray-400 uppercase mb-1">Clearance</p>
                        <div class="flex items-center gap-2">
                            <div class="w-20 bg-gray-200 rounded-full h-2">
                                <div class="{{ $report->clearance_percentage == 100 ? 'bg-green-500' : 'bg-yellow-500' }} h-2 rounded-full"
                                     style="width: {{ $report->clearance_percentage }}%"></div>
                            </div>
                            <span class="text-xs font-medium text-gray-700">{{ $report->clearance_progress }}</span>
                        </div>
                    </div>

                    {{-- Status + Arrow --}}
                    <div class="flex items-center gap-3">
                        <span class="badge {{ $report->status_color }}">
                            {{ ucfirst(str_replace('_', ' ', $report->status)) }}
                        </span>
                        <i class="fas fa-chevron-right text-gray-400 text-sm"></i>
                    </div>

                </div>
            </div>
        </a>
        @endforeach

        <div class="mt-4">
            {{ $exitReports->links() }}
        </div>

    @else
        <div class="bg-white rounded-lg shadow text-center py-16">
            <i class="fas fa-door-open text-gray-300 text-5xl mb-4"></i>
            <h3 class="text-lg font-medium text-gray-900 mb-2">No exit reports yet</h3>
            <p class="text-gray-500 mb-4">Start the offboarding process for a departing staff member.</p>
            <a href="{{ route('admin.exit-reports.create') }}" class="btn btn-primary">
                <i class="fas fa-plus mr-2"></i> Start Exit Process
            </a>
        </div>
    @endif

</div>

@endsection