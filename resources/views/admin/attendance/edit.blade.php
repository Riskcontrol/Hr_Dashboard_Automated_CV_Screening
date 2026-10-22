{{--
    resources/views/admin/attendance/edit.blade.php
    Edit an existing attendance record — same as create but pre-filled.
--}}
@extends('layouts.app')

@section('content')

<div class="mb-6 flex items-center gap-4">
    <a href="{{ route('admin.attendance.show', $attendance) }}" class="text-gray-500 hover:text-gray-700">
        <i class="fas fa-arrow-left"></i>
    </a>
    <div>
        <h1 class="text-3xl font-bold text-gray-900">Edit Attendance Record</h1>
        <p class="text-gray-500 mt-1">
            {{ $attendance->staffProfile->full_name }} — {{ $attendance->date->format('M d, Y') }}
        </p>
    </div>
</div>

<form method="POST" action="{{ route('admin.attendance.update', $attendance) }}">
    @csrf
    @method('PUT')

    <div class="bg-white rounded-lg shadow mb-6">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
            <h2 class="text-lg font-semibold text-gray-900">
                <i class="fas fa-calendar-check mr-2 text-primary-600"></i>Attendance Details
            </h2>
        </div>
        <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">

            <div>
                <label class="form-label">Staff Member <span class="text-red-500">*</span></label>
                <select name="staff_profile_id" class="form-select" required>
                    @foreach($staff as $member)
                        <option value="{{ $member->id }}"
                            {{ old('staff_profile_id', $attendance->staff_profile_id) == $member->id ? 'selected' : '' }}>
                            {{ $member->full_name }}
                            @if($member->department) ({{ $member->department }}) @endif
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="form-label">Date <span class="text-red-500">*</span></label>
                <input type="date" name="date"
                       value="{{ old('date', $attendance->date->toDateString()) }}"
                       class="form-input" required>
            </div>

            <div>
                <label class="form-label">Status <span class="text-red-500">*</span></label>
                <select name="status" id="status_select" class="form-select"
                        required onchange="toggleAbsenceFields()">
                    @foreach([
                        'present'        => 'Present',
                        'absent'         => 'Absent',
                        'late'           => 'Late',
                        'half_day'       => 'Half Day',
                        'on_leave'       => 'On Leave',
                        'public_holiday' => 'Public Holiday',
                    ] as $val => $label)
                        <option value="{{ $val }}"
                            {{ old('status', $attendance->status) == $val ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div id="absence_type_div"
                 style="display:{{ old('status', $attendance->status) === 'absent' ? 'block' : 'none' }}">
                <label class="form-label">Absence Type</label>
                <select name="absence_type" class="form-select">
                    <option value="">Select reason</option>
                    @foreach([
                        'sick'         => 'Sick Leave',
                        'unauthorised' => 'Unauthorised',
                        'personal'     => 'Personal Reasons',
                        'bereavement'  => 'Bereavement',
                        'maternity'    => 'Maternity/Paternity',
                        'other'        => 'Other',
                    ] as $val => $label)
                        <option value="{{ $val }}"
                            {{ old('absence_type', $attendance->absence_type) == $val ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="form-label">Check In Time</label>
                <input type="time" name="check_in_time"
                       value="{{ old('check_in_time', $attendance->check_in_time) }}" class="form-input">
            </div>

            <div>
                <label class="form-label">Check Out Time</label>
                <input type="time" name="check_out_time"
                       value="{{ old('check_out_time', $attendance->check_out_time) }}" class="form-input">
            </div>

            <div id="minutes_late_div"
                 style="display:{{ old('status', $attendance->status) === 'late' ? 'block' : 'none' }}">
                <label class="form-label">Minutes Late</label>
                <input type="number" name="minutes_late"
                       value="{{ old('minutes_late', $attendance->minutes_late) }}"
                       class="form-input" min="0">
            </div>

            <div>
                <label class="form-label">Recorded By</label>
                <input type="text" name="recorded_by"
                       value="{{ old('recorded_by', $attendance->recorded_by) }}" class="form-input">
            </div>

            <div class="md:col-span-2">
                <label class="form-label">Notes</label>
                <textarea name="notes" rows="2" class="form-input">{{ old('notes', $attendance->notes) }}</textarea>
            </div>

        </div>
    </div>

    <div class="flex justify-end gap-4">
        <a href="{{ route('admin.attendance.show', $attendance) }}" class="btn btn-outline">Cancel</a>
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-save mr-2"></i> Update Record
        </button>
    </div>

</form>

@push('scripts')
<script>
function toggleAbsenceFields() {
    const status = document.getElementById('status_select').value;
    document.getElementById('absence_type_div').style.display = status === 'absent' ? 'block' : 'none';
    document.getElementById('minutes_late_div').style.display = status === 'late'   ? 'block' : 'none';
}
</script>
@endpush

@endsection