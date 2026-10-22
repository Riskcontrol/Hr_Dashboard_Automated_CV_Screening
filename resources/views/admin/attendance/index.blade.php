{{--
    resources/views/admin/attendance/index.blade.php
    Absenteeism Tracking — List Page
    Shows all attendance records with filters and stat cards.
    Card layout — clickable rows.
--}}
@extends('layouts.app')

@section('content')

<div class="mb-6 flex justify-between items-center">
    <div>
        <h1 class="text-3xl font-bold text-gray-900">Absenteeism Tracking</h1>
        <p class="text-gray-500 mt-1">Daily attendance and absence records</p>
    </div>
    <a href="{{ route('admin.attendance.create') }}" class="btn btn-primary">
        <i class="fas fa-plus mr-2"></i> Log Attendance
    </a>
</div>

{{-- STAT CARDS --}}
<div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-6">

    <div class="bg-white p-4 rounded-lg shadow flex items-center gap-3">
        <div class="p-2 rounded-full bg-blue-100 text-blue-600">
            <i class="fas fa-calendar-check text-lg"></i>
        </div>
        <div>
            <p class="text-xl font-bold text-gray-900">{{ $stats['total'] }}</p>
            <p class="text-gray-500 text-xs">Total Records</p>
        </div>
    </div>

    <div class="bg-white p-4 rounded-lg shadow flex items-center gap-3">
        <div class="p-2 rounded-full bg-red-100 text-red-600">
            <i class="fas fa-user-times text-lg"></i>
        </div>
        <div>
            <p class="text-xl font-bold text-gray-900">{{ $stats['absences'] }}</p>
            <p class="text-gray-500 text-xs">Total Absences</p>
        </div>
    </div>

    <div class="bg-white p-4 rounded-lg shadow flex items-center gap-3">
        <div class="p-2 rounded-full bg-yellow-100 text-yellow-600">
            <i class="fas fa-clock text-lg"></i>
        </div>
        <div>
            <p class="text-xl font-bold text-gray-900">{{ $stats['late'] }}</p>
            <p class="text-gray-500 text-xs">Late Arrivals</p>
        </div>
    </div>

    <div class="bg-white p-4 rounded-lg shadow flex items-center gap-3">
        <div class="p-2 rounded-full bg-orange-100 text-orange-600">
            <i class="fas fa-ban text-lg"></i>
        </div>
        <div>
            <p class="text-xl font-bold text-gray-900">{{ $stats['unauthorised'] }}</p>
            <p class="text-gray-500 text-xs">Unauthorised</p>
        </div>
    </div>

    <div class="bg-white p-4 rounded-lg shadow flex items-center gap-3">
        <div class="p-2 rounded-full bg-purple-100 text-purple-600">
            <i class="fas fa-calendar-minus text-lg"></i>
        </div>
        <div>
            <p class="text-xl font-bold text-gray-900">{{ $stats['this_month'] }}</p>
            <p class="text-gray-500 text-xs">This Month</p>
        </div>
    </div>

</div>

{{-- FILTER BAR --}}
<div class="bg-white rounded-lg shadow mb-6 p-4">
    <form method="GET" action="{{ route('admin.attendance.index') }}" class="flex flex-wrap gap-3 items-end">

        <div class="flex-1 min-w-48">
            <label class="form-label">Search Staff</label>
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Staff name..." class="form-input">
        </div>

        <div class="min-w-36">
            <label class="form-label">Status</label>
            <select name="status" class="form-select">
                <option value="">All</option>
                <option value="present"        {{ request('status') == 'present'        ? 'selected' : '' }}>Present</option>
                <option value="absent"         {{ request('status') == 'absent'         ? 'selected' : '' }}>Absent</option>
                <option value="late"           {{ request('status') == 'late'           ? 'selected' : '' }}>Late</option>
                <option value="half_day"       {{ request('status') == 'half_day'       ? 'selected' : '' }}>Half Day</option>
                <option value="on_leave"       {{ request('status') == 'on_leave'       ? 'selected' : '' }}>On Leave</option>
                <option value="public_holiday" {{ request('status') == 'public_holiday' ? 'selected' : '' }}>Public Holiday</option>
            </select>
        </div>

        <div class="min-w-40">
            <label class="form-label">Absence Type</label>
            <select name="absence_type" class="form-select">
                <option value="">All Types</option>
                <option value="sick"         {{ request('absence_type') == 'sick'         ? 'selected' : '' }}>Sick</option>
                <option value="unauthorised" {{ request('absence_type') == 'unauthorised' ? 'selected' : '' }}>Unauthorised</option>
                <option value="personal"     {{ request('absence_type') == 'personal'     ? 'selected' : '' }}>Personal</option>
                <option value="bereavement"  {{ request('absence_type') == 'bereavement'  ? 'selected' : '' }}>Bereavement</option>
                <option value="maternity"    {{ request('absence_type') == 'maternity'    ? 'selected' : '' }}>Maternity/Paternity</option>
            </select>
        </div>

        <div class="min-w-36">
            <label class="form-label">From Date</label>
            <input type="date" name="from" value="{{ request('from') }}" class="form-input">
        </div>

        <div class="min-w-36">
            <label class="form-label">To Date</label>
            <input type="date" name="to" value="{{ request('to') }}" class="form-input">
        </div>

        <button type="submit" class="btn btn-primary">
            <i class="fas fa-search mr-2"></i> Filter
        </button>

        @if(request()->hasAny(['search', 'status', 'absence_type', 'from', 'to']))
            <a href="{{ route('admin.attendance.index') }}" class="btn btn-outline">Clear</a>
        @endif

    </form>
</div>

{{-- RECORDS LIST — clickable cards --}}
<div class="space-y-3">

    @if($records->count())

        @foreach($records as $record)
        <a href="{{ route('admin.attendance.show', $record) }}"
           class="block bg-white rounded-lg shadow hover:shadow-md
                  border {{ $record->status === 'absent' && $record->absence_type === 'unauthorised' ? 'border-red-200' : 'border-transparent' }}
                  hover:border-primary-300 transition-all duration-200 cursor-pointer">
            <div class="p-4">
                <div class="flex flex-wrap items-center justify-between gap-4">

                    {{-- Staff Member --}}
                    <div class="flex items-center gap-3 min-w-48">
                        <div class="w-9 h-9 rounded-full bg-primary-100 text-primary-700
                                    flex items-center justify-center font-bold text-sm flex-shrink-0">
                            {{ $record->staffProfile->initials ?? '?' }}
                        </div>
                        <div>
                            <p class="font-semibold text-gray-900 text-sm">
                                {{ $record->staffProfile->full_name ?? 'Unknown' }}
                            </p>
                            <p class="text-xs text-gray-500">
                                {{ $record->staffProfile->department ?? '' }}
                            </p>
                        </div>
                    </div>

                    {{-- Date --}}
                    <div class="min-w-32">
                        <p class="text-xs text-gray-400 uppercase mb-1">Date</p>
                        <p class="text-sm font-medium text-gray-900">
                            {{ $record->date->format('M d, Y') }}
                        </p>
                        <p class="text-xs text-gray-500">{{ $record->date->format('l') }}</p>
                    </div>

                    {{-- Check In / Out --}}
                    <div class="min-w-32">
                        <p class="text-xs text-gray-400 uppercase mb-1">Time</p>
                        @if($record->check_in_time)
                            <p class="text-sm text-gray-900">
                                In: {{ $record->check_in_time }}
                                @if($record->check_out_time) · Out: {{ $record->check_out_time }} @endif
                            </p>
                        @else
                            <p class="text-sm text-gray-400 italic">—</p>
                        @endif
                        @if($record->minutes_late)
                            <p class="text-xs text-yellow-600">{{ $record->minutes_late }} min late</p>
                        @endif
                    </div>

                    {{-- Absence Type (if absent) --}}
                    <div class="min-w-36">
                        @if($record->absence_type)
                            <p class="text-xs text-gray-400 uppercase mb-1">Absence Reason</p>
                            <p class="text-sm text-gray-900">{{ $record->absence_type_label }}</p>
                        @endif
                    </div>

                    {{-- Status + Arrow --}}
                    <div class="flex items-center gap-3">
                        <span class="badge {{ $record->status_color }}">
                            {{ $record->status_label }}
                        </span>
                        <i class="fas fa-chevron-right text-gray-400 text-sm"></i>
                    </div>

                </div>

                @if($record->notes)
                <div class="mt-2 pt-2 border-t border-gray-100">
                    <p class="text-xs text-gray-500">
                        <i class="fas fa-sticky-note mr-1"></i>{{ $record->notes }}
                    </p>
                </div>
                @endif

            </div>
        </a>
        @endforeach

        <div class="mt-4">
            {{ $records->links() }}
        </div>

    @else
        <div class="bg-white rounded-lg shadow text-center py-16">
            <i class="fas fa-calendar-check text-gray-300 text-5xl mb-4"></i>
            <h3 class="text-lg font-medium text-gray-900 mb-2">No attendance records found</h3>
            <p class="text-gray-500 mb-4">Start logging daily attendance records.</p>
            <a href="{{ route('admin.attendance.create') }}" class="btn btn-primary">
                <i class="fas fa-plus mr-2"></i> Log Attendance
            </a>
        </div>
    @endif

</div>

@endsection