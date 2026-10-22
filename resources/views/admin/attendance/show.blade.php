{{--
    resources/views/admin/attendance/show.blade.php
    Single attendance record detail page.
--}}
@extends('layouts.app')

@section('content')

<div class="mb-6 flex items-center justify-between">
    <div class="flex items-center gap-4">
        <a href="{{ route('admin.attendance.index') }}" class="text-gray-500 hover:text-gray-700">
            <i class="fas fa-arrow-left"></i>
        </a>
        <h1 class="text-3xl font-bold text-gray-900">Attendance Record</h1>
    </div>
    <div class="flex gap-3">
        <a href="{{ route('admin.attendance.edit', $attendance) }}" class="btn btn-primary">
            <i class="fas fa-edit mr-2"></i> Edit
        </a>
        <form method="POST" action="{{ route('admin.attendance.destroy', $attendance) }}"
              onsubmit="return confirm('Delete this record?')">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger">
                <i class="fas fa-trash mr-2"></i> Delete
            </button>
        </form>
    </div>
</div>

{{-- Status Banner --}}
<div class="mb-6 p-4 rounded-lg border
    {{ $attendance->status === 'absent' ? 'bg-red-50 border-red-200' :
       ($attendance->status === 'late' ? 'bg-yellow-50 border-yellow-200' : 'bg-green-50 border-green-200') }}">
    <div class="flex items-center gap-3">
        <span class="badge {{ $attendance->status_color }} text-sm px-3 py-1">
            {{ $attendance->status_label }}
        </span>
        <span class="text-sm text-gray-600">
            {{ $attendance->date->format('l, M d, Y') }}
            @if($attendance->absence_type_label)
                — {{ $attendance->absence_type_label }}
            @endif
        </span>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

    {{-- Staff Info --}}
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
            <h3 class="font-semibold text-gray-900">
                <i class="fas fa-user mr-2 text-primary-600"></i>Staff Member
            </h3>
        </div>
        <div class="p-6 flex items-center gap-4">
            <div class="w-14 h-14 rounded-full bg-primary-100 text-primary-700
                        flex items-center justify-center text-lg font-bold">
                {{ $attendance->staffProfile->initials ?? '?' }}
            </div>
            <div>
                <p class="font-semibold text-gray-900 text-lg">{{ $attendance->staffProfile->full_name }}</p>
                <p class="text-gray-500 text-sm">{{ $attendance->staffProfile->job_title ?? 'No title' }}</p>
                <p class="text-gray-500 text-sm">{{ $attendance->staffProfile->department ?? '' }}</p>
                <a href="{{ route('admin.attendance.staff-report', $attendance->staffProfile) }}"
                   class="text-blue-600 hover:underline text-sm mt-1 inline-block">
                    View Full Attendance Report →
                </a>
            </div>
        </div>
    </div>

    {{-- Record Details --}}
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
            <h3 class="font-semibold text-gray-900">
                <i class="fas fa-calendar-check mr-2 text-primary-600"></i>Record Details
            </h3>
        </div>
        <div class="p-6 space-y-3">
            @php
            $details = [
                'Date'          => $attendance->date->format('l, M d, Y'),
                'Status'        => $attendance->status_label,
                'Absence Type'  => $attendance->absence_type_label,
                'Check In'      => $attendance->check_in_time ?? '—',
                'Check Out'     => $attendance->check_out_time ?? '—',
                'Minutes Late'  => $attendance->minutes_late ? $attendance->minutes_late . ' minutes' : null,
                'Recorded By'   => $attendance->recorded_by,
                'Logged On'     => $attendance->created_at->format('M d, Y \a\t g:i A'),
            ];
            @endphp
            @foreach($details as $label => $value)
            <div class="flex justify-between py-1 border-b border-gray-100 last:border-0">
                <span class="text-gray-500 text-sm">{{ $label }}</span>
                <span class="text-gray-900 text-sm font-medium">{{ $value ?? '—' }}</span>
            </div>
            @endforeach
            @if($attendance->notes)
            <div class="pt-2">
                <p class="text-gray-500 text-xs uppercase mb-1">Notes</p>
                <p class="text-gray-900 text-sm">{{ $attendance->notes }}</p>
            </div>
            @endif
        </div>
    </div>

</div>

{{-- Recent records for this staff member --}}
@if($recentRecords->count())
<div class="bg-white rounded-lg shadow mt-6">
    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
        <h3 class="font-semibold text-gray-900">
            <i class="fas fa-history mr-2 text-primary-600"></i>
            Recent Records for {{ $attendance->staffProfile->full_name }}
        </h3>
    </div>
    <div class="divide-y divide-gray-100">
        @foreach($recentRecords as $recent)
        <a href="{{ route('admin.attendance.show', $recent) }}"
           class="px-6 py-3 flex items-center justify-between hover:bg-gray-50 block">
            <div>
                <p class="text-sm font-medium text-gray-900">{{ $recent->date->format('l, M d, Y') }}</p>
                @if($recent->absence_type_label)
                    <p class="text-xs text-gray-500">{{ $recent->absence_type_label }}</p>
                @endif
            </div>
            <span class="badge {{ $recent->status_color }}">{{ $recent->status_label }}</span>
        </a>
        @endforeach
    </div>
</div>
@endif

@endsection