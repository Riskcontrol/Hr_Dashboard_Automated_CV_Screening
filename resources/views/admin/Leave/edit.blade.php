{{--
    resources/views/admin/leave/edit.blade.php

    Leave Request — Edit Page
    Only pending requests can be edited.
    Same form as create but pre-filled with existing data.
--}}
@extends('layouts.app')

@section('content')

<div class="mb-6 flex items-center gap-4">
    <a href="{{ route('admin.leave.show', $leave) }}" class="text-gray-500 hover:text-gray-700">
        <i class="fas fa-arrow-left"></i>
    </a>
    <div>
        <h1 class="text-3xl font-bold text-gray-900">Edit Leave Request</h1>
        <p class="text-gray-500 mt-1">{{ $leave->staffProfile->full_name }}</p>
    </div>
</div>

{{-- PUT method since HTML forms only support GET/POST --}}
<form method="POST" action="{{ route('admin.leave.update', $leave) }}">
    @csrf
    @method('PUT')

    <div class="bg-white rounded-lg shadow mb-6">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
            <h2 class="text-lg font-semibold text-gray-900">
                <i class="fas fa-calendar mr-2 text-primary-600"></i>Leave Details
            </h2>
        </div>
        <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">

            {{-- Staff member dropdown --}}
            <div>
                <label class="form-label">Staff Member <span class="text-red-500">*</span></label>
                <select name="staff_profile_id" class="form-select" required>
                    <option value="">Select staff member</option>
                    @foreach($staff as $member)
                        <option value="{{ $member->id }}"
                            {{ old('staff_profile_id', $leave->staff_profile_id) == $member->id ? 'selected' : '' }}>
                            {{ $member->full_name }}
                            @if($member->job_title) — {{ $member->job_title }} @endif
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Leave type --}}
            <div>
                <label class="form-label">Leave Type <span class="text-red-500">*</span></label>
                <select name="leave_type" class="form-select" required>
                    @foreach([
                        'annual'    => 'Annual Leave',
                        'sick'      => 'Sick Leave',
                        'casual'    => 'Casual Leave',
                        'maternity' => 'Maternity Leave',
                        'paternity' => 'Paternity Leave',
                        'unpaid'    => 'Unpaid Leave',
                    ] as $val => $label)
                        <option value="{{ $val }}"
                            {{ old('leave_type', $leave->leave_type) == $val ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Start Date --}}
            <div>
                <label class="form-label">Start Date <span class="text-red-500">*</span></label>
                <input type="date" name="start_date"
                       value="{{ old('start_date', $leave->start_date->toDateString()) }}"
                       class="form-input" required>
            </div>

            {{-- End Date --}}
            <div>
                <label class="form-label">End Date <span class="text-red-500">*</span></label>
                <input type="date" name="end_date"
                       value="{{ old('end_date', $leave->end_date->toDateString()) }}"
                       class="form-input" required>
            </div>

            {{-- Reason --}}
            <div class="md:col-span-2">
                <label class="form-label">Reason for Leave</label>
                <textarea name="reason" rows="3" class="form-input">{{ old('reason', $leave->reason) }}</textarea>
            </div>

        </div>
    </div>

    {{-- Approver Section --}}
    <div class="bg-white rounded-lg shadow mb-6">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
            <h2 class="text-lg font-semibold text-gray-900">
                <i class="fas fa-user-check mr-2 text-primary-600"></i>Approver
            </h2>
        </div>
        <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">

            <div>
                <label class="form-label">Approver Type <span class="text-red-500">*</span></label>
                <select name="approver_type" class="form-select" required>
                    <option value="line_manager"
                        {{ old('approver_type', $leave->approver_type) == 'line_manager' ? 'selected' : '' }}>
                        Line Manager
                    </option>
                    <option value="hr"
                        {{ old('approver_type', $leave->approver_type) == 'hr' ? 'selected' : '' }}>
                        HR / Admin
                    </option>
                </select>
            </div>

            <div>
                <label class="form-label">Approver Name <span class="text-red-500">*</span></label>
                <input type="text" name="approver_name"
                       value="{{ old('approver_name', $leave->approver_name) }}"
                       class="form-input" required>
            </div>

        </div>
    </div>

    <div class="flex justify-end gap-4">
        <a href="{{ route('admin.leave.show', $leave) }}" class="btn btn-outline">Cancel</a>
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-save mr-2"></i> Update Leave Request
        </button>
    </div>

</form>

@endsection