{{--
    resources/views/admin/attendance/staff_report.blade.php
    Full attendance report for a single staff member.
    Shows monthly summary with all records.
--}}
@extends('layouts.app')

@section('content')

<div class="mb-6 flex items-center justify-between">
    <div class="flex items-center gap-4">
        <a href="{{ route('admin.attendance.index') }}" class="text-gray-500 hover:text-gray-700">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Attendance Report</h1>
            <p class="text-gray-500 mt-1">{{ $staff->full_name }}</p>
        </div>
    </div>
    <a href="{{ route('admin.staff.show', $staff) }}" class="btn btn-outline">
        <i class="fas fa-user mr-2"></i> View Profile
    </a>
</div>

{{-- Year filter --}}
<div class="bg-white rounded-lg shadow mb-6 p-4">
    <form method="GET" class="flex items-end gap-3">
        <div>
            <label class="form-label">Year</label>
            <select name="year" class="form-select">
                @for($y = now()->year; $y >= now()->year - 3; $y--)
                    <option value="{{ $y }}" {{ request('year', now()->year) == $y ? 'selected' : '' }}>
                        {{ $y }}
                    </option>
                @endfor
            </select>
        </div>
        <button type="submit" class="btn btn-primary">View</button>
    </form>
</div>

{{-- Summary Cards --}}
<div class="grid grid-cols-2 md:grid-cols-6 gap-4 mb-6">
    @php
    $summaryCards = [
        ['label' => 'Total Days',    'value' => $summary['total_days'],   'color' => 'bg-blue-100 text-blue-600'],
        ['label' => 'Present',       'value' => $summary['present'],      'color' => 'bg-green-100 text-green-600'],
        ['label' => 'Absent',        'value' => $summary['absent'],       'color' => 'bg-red-100 text-red-600'],
        ['label' => 'Late',          'value' => $summary['late'],         'color' => 'bg-yellow-100 text-yellow-600'],
        ['label' => 'On Leave',      'value' => $summary['on_leave'],     'color' => 'bg-gray-100 text-gray-600'],
        ['label' => 'Unauthorised',  'value' => $summary['unauthorised'], 'color' => 'bg-orange-100 text-orange-600'],
    ];
    @endphp
    @foreach($summaryCards as $card)
    <div class="bg-white p-4 rounded-lg shadow text-center">
        <div class="w-10 h-10 rounded-full {{ $card['color'] }} flex items-center justify-center mx-auto mb-2">
            <span class="font-bold text-sm">{{ $card['value'] }}</span>
        </div>
        <p class="text-xs text-gray-500">{{ $card['label'] }}</p>
    </div>
    @endforeach
</div>

{{-- Records Table --}}
<div class="bg-white rounded-lg shadow">
    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
        <h3 class="font-semibold text-gray-900">
            All Records — {{ request('year', now()->year) }}
        </h3>
    </div>

    @if($records->count())
    <div class="divide-y divide-gray-100">
        @foreach($records as $record)
        <a href="{{ route('admin.attendance.show', $record) }}"
           class="px-6 py-3 flex items-center justify-between hover:bg-gray-50 block">
            <div>
                <p class="text-sm font-medium text-gray-900">{{ $record->date->format('l, M d, Y') }}</p>
                @if($record->absence_type_label)
                    <p class="text-xs text-gray-500">{{ $record->absence_type_label }}</p>
                @endif
                @if($record->notes)
                    <p class="text-xs text-gray-400 italic">{{ $record->notes }}</p>
                @endif
            </div>
            <div class="flex items-center gap-3">
                @if($record->check_in_time)
                    <span class="text-xs text-gray-500">In: {{ $record->check_in_time }}</span>
                @endif
                @if($record->minutes_late)
                    <span class="text-xs text-yellow-600">{{ $record->minutes_late }}min late</span>
                @endif
                <span class="badge {{ $record->status_color }}">{{ $record->status_label }}</span>
            </div>
        </a>
        @endforeach
    </div>
    @else
    <div class="text-center py-12">
        <i class="fas fa-calendar text-gray-300 text-4xl mb-3"></i>
        <p class="text-gray-500">No attendance records found for this year.</p>
    </div>
    @endif
</div>

@endsection