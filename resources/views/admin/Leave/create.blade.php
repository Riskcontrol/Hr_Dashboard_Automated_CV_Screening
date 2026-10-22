@extends('layouts.app')

@section('content')

<div class="mb-6 flex items-center gap-4">
    <a href="{{ route('admin.leave.index') }}" class="text-gray-500 hover:text-gray-700">
        <i class="fas fa-arrow-left"></i>
    </a>
    <h1 class="text-3xl font-bold text-gray-900">New Leave Request</h1>
</div>

@if($errors->any())
    <div class="alert alert-danger mb-4">
        <ul class="list-disc pl-5 text-sm">
            @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
        </ul>
    </div>
@endif

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    <div class="lg:col-span-2">
        <form method="POST" action="{{ route('admin.leave.store') }}"
              class="bg-white rounded-lg shadow p-6 space-y-5">
            @csrf

            <div>
                <label class="form-label">Staff Member <span class="text-red-500">*</span></label>
                <select name="staff_profile_id" class="form-select" required>
                    <option value="">Select staff member</option>
                    @foreach($staff as $s)
                        <option value="{{ $s->id }}" {{ old('staff_profile_id') == $s->id ? 'selected' : '' }}>
                            {{ $s->full_name }} — {{ $s->department ?? 'No dept' }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="form-label">Staff Section</label>
                <input type="text" name="staff_section" class="form-input"
                       value="{{ old('staff_section') }}"
                       placeholder="e.g. Finance, Operations, Sales">
                <p class="text-xs text-gray-400 mt-1">Which section this staff member belongs to</p>
            </div>

            <div>
                <label class="form-label">Leave Type <span class="text-red-500">*</span></label>
                <select name="leave_type" class="form-select" required>
                    <option value="">Select leave type</option>
                    @foreach([
                        'annual'    => 'Annual Leave (21 days default)',
                        'sick'      => 'Sick Leave (12 days default)',
                        'casual'    => 'Casual Leave (5 days default)',
                        'maternity' => 'Maternity Leave (90 days default)',
                        'paternity' => 'Paternity Leave (5 days default)',
                        'unpaid'    => 'Unpaid Leave (no limit)',
                    ] as $val => $label)
                        <option value="{{ $val }}" {{ old('leave_type') == $val ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="form-label">Start Date <span class="text-red-500">*</span></label>
                    <input type="date" name="start_date" class="form-input"
                           value="{{ old('start_date') }}" required>
                </div>
                <div>
                    <label class="form-label">End Date <span class="text-red-500">*</span></label>
                    <input type="date" name="end_date" class="form-input"
                           value="{{ old('end_date') }}" required>
                </div>
            </div>

            <div>
                <label class="form-label">Who Should Approve <span class="text-red-500">*</span></label>
                <select name="approver_type" class="form-select" required>
                    <option value="hr" {{ old('approver_type') == 'hr' ? 'selected' : '' }}>HR</option>
                    <option value="line_manager" {{ old('approver_type') == 'line_manager' ? 'selected' : '' }}>Line Manager</option>
                </select>
            </div>

            <div>
                <label class="form-label">Approver Name <span class="text-red-500">*</span></label>
                <input type="text" name="approver_name" class="form-input"
                       value="{{ old('approver_name') }}"
                       placeholder="Full name of the approver" required>
            </div>

            <div>
                <label class="form-label">Reason for Leave</label>
                <textarea name="reason" rows="3" class="form-textarea w-full"
                          placeholder="Optional">{{ old('reason') }}</textarea>
            </div>

            <div>
                <label class="form-label">HR Internal Notes</label>
                <textarea name="hr_notes" rows="3" class="form-textarea w-full"
                          placeholder="Internal notes — not visible to staff">{{ old('hr_notes') }}</textarea>
            </div>

            <div class="flex justify-end gap-3 pt-2 border-t">
                <a href="{{ route('admin.leave.index') }}" class="btn btn-outline">Cancel</a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-paper-plane mr-2"></i> Submit Request
                </button>
            </div>
        </form>
    </div>

    {{-- Sidebar --}}
    <div class="space-y-4">
        <div class="bg-white rounded-lg shadow p-5">
            <h3 class="font-semibold text-gray-900 mb-3">
                <i class="fas fa-info-circle text-blue-500 mr-2"></i>Default Allowances
            </h3>
            <div class="space-y-2">
                <div class="flex justify-between p-2 rounded bg-blue-50 border border-blue-100">
                    <span class="text-sm text-blue-800 font-medium">Annual</span>
                    <span class="text-sm font-bold text-blue-700">21 days</span>
                </div>
                <div class="flex justify-between p-2 rounded bg-red-50 border border-red-100">
                    <span class="text-sm text-red-800 font-medium">Sick</span>
                    <span class="text-sm font-bold text-red-700">12 days</span>
                </div>
                <div class="flex justify-between p-2 rounded bg-yellow-50 border border-yellow-100">
                    <span class="text-sm text-yellow-800 font-medium">Casual</span>
                    <span class="text-sm font-bold text-yellow-700">5 days</span>
                </div>
                <div class="flex justify-between p-2 rounded bg-pink-50 border border-pink-100">
                    <span class="text-sm text-pink-800 font-medium">Maternity</span>
                    <span class="text-sm font-bold text-pink-700">90 days</span>
                </div>
                <div class="flex justify-between p-2 rounded bg-indigo-50 border border-indigo-100">
                    <span class="text-sm text-indigo-800 font-medium">Paternity</span>
                    <span class="text-sm font-bold text-indigo-700">5 days</span>
                </div>
                <div class="flex justify-between p-2 rounded bg-gray-50 border border-gray-200">
                    <span class="text-sm text-gray-800 font-medium">Unpaid</span>
                    <span class="text-sm font-bold text-gray-700">Unlimited</span>
                </div>
            </div>
        </div>
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 text-sm text-yellow-800">
            <p class="font-semibold mb-1"><i class="fas fa-exclamation-triangle mr-1"></i>Note</p>
            <p>Weekends are automatically excluded when calculating total working days.</p>
        </div>
    </div>

</div>

@endsection