{{--
    resources/views/admin/attendance/create.blade.php
    Log a new attendance/absence record for a staff member.
--}}
@extends('layouts.app')

@section('content')

<div class="mb-6 flex items-center gap-4">
    <a href="{{ route('admin.attendance.index') }}" class="text-gray-500 hover:text-gray-700">
        <i class="fas fa-arrow-left"></i>
    </a>
    <div>
        <h1 class="text-3xl font-bold text-gray-900">Log Attendance</h1>
        <p class="text-gray-500 mt-1">Record a daily attendance or absence entry</p>
    </div>
</div>

<form method="POST" action="{{ route('admin.attendance.store') }}">
    @csrf

    <div class="bg-white rounded-lg shadow mb-6">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
            <h2 class="text-lg font-semibold text-gray-900">
                <i class="fas fa-calendar-check mr-2 text-primary-600"></i>Attendance Details
            </h2>
        </div>
        <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">

            {{-- Staff Member --}}
            <div>
                <label class="form-label">Staff Member <span class="text-red-500">*</span></label>
                <select name="staff_profile_id"
                        class="form-select @error('staff_profile_id') border-red-500 @enderror" required>
                    <option value="">Select staff member</option>
                    @foreach($staff as $member)
                        <option value="{{ $member->id }}"
                            {{ old('staff_profile_id') == $member->id ? 'selected' : '' }}>
                            {{ $member->full_name }}
                            @if($member->department) ({{ $member->department }}) @endif
                        </option>
                    @endforeach
                </select>
                @error('staff_profile_id')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Date --}}
            <div>
                <label class="form-label">Date <span class="text-red-500">*</span></label>
                <input type="date" name="date"
                       value="{{ old('date', now()->toDateString()) }}"
                       class="form-input @error('date') border-red-500 @enderror" required>
                @error('date')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Status --}}
            <div>
                <label class="form-label">Status <span class="text-red-500">*</span></label>
                <select name="status" id="status_select"
                        class="form-select @error('status') border-red-500 @enderror"
                        required onchange="toggleAbsenceFields()">
                    <option value="">Select status</option>
                    <option value="present"        {{ old('status') == 'present'        ? 'selected' : '' }}>Present</option>
                    <option value="absent"         {{ old('status') == 'absent'         ? 'selected' : '' }}>Absent</option>
                    <option value="late"           {{ old('status') == 'late'           ? 'selected' : '' }}>Late</option>
                    <option value="half_day"       {{ old('status') == 'half_day'       ? 'selected' : '' }}>Half Day</option>
                    <option value="on_leave"       {{ old('status') == 'on_leave'       ? 'selected' : '' }}>On Leave</option>
                    <option value="public_holiday" {{ old('status') == 'public_holiday' ? 'selected' : '' }}>Public Holiday</option>
                </select>
                @error('status')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Absence Type — only shown when status = absent --}}
            <div id="absence_type_div" style="display:none;">
                <label class="form-label">Absence Type</label>
                <select name="absence_type" class="form-select">
                    <option value="">Select reason</option>
                    <option value="sick"         {{ old('absence_type') == 'sick'         ? 'selected' : '' }}>Sick Leave</option>
                    <option value="unauthorised" {{ old('absence_type') == 'unauthorised' ? 'selected' : '' }}>Unauthorised</option>
                    <option value="personal"     {{ old('absence_type') == 'personal'     ? 'selected' : '' }}>Personal Reasons</option>
                    <option value="bereavement"  {{ old('absence_type') == 'bereavement'  ? 'selected' : '' }}>Bereavement</option>
                    <option value="maternity"    {{ old('absence_type') == 'maternity'    ? 'selected' : '' }}>Maternity/Paternity</option>
                    <option value="other"        {{ old('absence_type') == 'other'        ? 'selected' : '' }}>Other</option>
                </select>
            </div>

            {{-- Check In Time --}}
            <div id="checkin_div">
                <label class="form-label">Check In Time</label>
                <input type="time" name="check_in_time"
                       value="{{ old('check_in_time') }}" class="form-input">
            </div>

            {{-- Check Out Time --}}
            <div>
                <label class="form-label">Check Out Time</label>
                <input type="time" name="check_out_time"
                       value="{{ old('check_out_time') }}" class="form-input">
            </div>

            {{-- Minutes Late — only shown when status = late --}}
            <div id="minutes_late_div" style="display:none;">
                <label class="form-label">Minutes Late</label>
                <input type="number" name="minutes_late"
                       value="{{ old('minutes_late') }}"
                       class="form-input" min="0" placeholder="e.g. 15">
            </div>

            {{-- Recorded By --}}
            <div>
                <label class="form-label">Recorded By</label>
                <input type="text" name="recorded_by"
                       value="{{ old('recorded_by') }}"
                       class="form-input" placeholder="HR officer name">
            </div>

            {{-- Notes --}}
            <div class="md:col-span-2">
                <label class="form-label">Notes</label>
                <textarea name="notes" rows="2" class="form-input"
                          placeholder="Any additional context or explanation">{{ old('notes') }}</textarea>
            </div>

        </div>
    </div>

    <div class="flex justify-end gap-4">
        <a href="{{ route('admin.attendance.index') }}" class="btn btn-outline">Cancel</a>
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-save mr-2"></i> Save Record
        </button>
    </div>

</form>

@push('scripts')
<script>
// Show/hide absence type and minutes late based on status selection
function toggleAbsenceFields() {
    const status = document.getElementById('status_select').value;
    document.getElementById('absence_type_div').style.display  = status === 'absent' ? 'block' : 'none';
    document.getElementById('minutes_late_div').style.display  = status === 'late'   ? 'block' : 'none';
}
toggleAbsenceFields();
</script>
@endpush

@endsection