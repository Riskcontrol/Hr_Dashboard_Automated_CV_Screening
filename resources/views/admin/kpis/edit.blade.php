{{--
    resources/views/admin/exit-reports/edit.blade.php
    Edit an existing exit report — same as create but pre-filled.
    Most common use: ticking off clearance items as departments confirm.
--}}
@extends('layouts.app')

@section('content')

<div class="mb-6 flex items-center gap-4">
    <a href="{{ route('admin.exit-reports.show', $exitReport) }}" class="text-gray-500 hover:text-gray-700">
        <i class="fas fa-arrow-left"></i>
    </a>
    <div>
        <h1 class="text-3xl font-bold text-gray-900">Edit Exit Report</h1>
        <p class="text-gray-500 mt-1">{{ $exitReport->staffProfile->full_name }}</p>
    </div>
</div>

<form method="POST" action="{{ route('admin.exit-reports.update', $exitReport) }}">
    @csrf
    @method('PUT')

    {{-- Exit Details --}}
    <div class="bg-white rounded-lg shadow mb-6">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
            <h2 class="text-lg font-semibold text-gray-900">
                <i class="fas fa-door-open mr-2 text-primary-600"></i>Exit Details
            </h2>
        </div>
        <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">

            <div>
                <label class="form-label">Staff Member <span class="text-red-500">*</span></label>
                <select name="staff_profile_id" class="form-select" required>
                    @foreach($staff as $member)
                        <option value="{{ $member->id }}"
                            {{ old('staff_profile_id', $exitReport->staff_profile_id) == $member->id ? 'selected' : '' }}>
                            {{ $member->full_name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="form-label">Exit Type <span class="text-red-500">*</span></label>
                <select name="exit_type" class="form-select" required>
                    @foreach([
                        'resignation'     => 'Resignation',
                        'termination'     => 'Termination',
                        'end_of_contract' => 'End of Contract',
                        'retirement'      => 'Retirement',
                        'redundancy'      => 'Redundancy',
                    ] as $val => $label)
                        <option value="{{ $val }}"
                            {{ old('exit_type', $exitReport->exit_type) == $val ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="form-label">Resignation Date</label>
                <input type="date" name="resignation_date"
                       value="{{ old('resignation_date', $exitReport->resignation_date?->toDateString()) }}"
                       class="form-input">
            </div>

            <div>
                <label class="form-label">Last Working Day <span class="text-red-500">*</span></label>
                <input type="date" name="last_working_day"
                       value="{{ old('last_working_day', $exitReport->last_working_day->toDateString()) }}"
                       class="form-input" required>
            </div>

            <div>
                <label class="form-label">Notice Period (Days)</label>
                <input type="number" name="notice_period_days"
                       value="{{ old('notice_period_days', $exitReport->notice_period_days) }}"
                       class="form-input" min="0">
            </div>

            <div>
                <label class="form-label">Status <span class="text-red-500">*</span></label>
                <select name="status" class="form-select" required>
                    @foreach(['in_progress' => 'In Progress', 'completed' => 'Completed', 'cancelled' => 'Cancelled'] as $val => $label)
                        <option value="{{ $val }}"
                            {{ old('status', $exitReport->status) == $val ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="form-label">Processed By</label>
                <input type="text" name="processed_by"
                       value="{{ old('processed_by', $exitReport->processed_by) }}" class="form-input">
            </div>

        </div>
    </div>

    {{-- Exit Interview --}}
    <div class="bg-white rounded-lg shadow mb-6">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
            <h2 class="text-lg font-semibold text-gray-900">
                <i class="fas fa-comments mr-2 text-primary-600"></i>Exit Interview
            </h2>
        </div>
        <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">

            <div class="flex items-center gap-2">
                <input type="checkbox" name="exit_interview_conducted" id="exit_interview_conducted" value="1"
                       {{ old('exit_interview_conducted', $exitReport->exit_interview_conducted) ? 'checked' : '' }}
                       class="w-4 h-4 rounded">
                <label for="exit_interview_conducted" class="text-sm text-gray-700">Exit interview conducted</label>
            </div>

            <div class="flex items-center gap-2">
                <input type="checkbox" name="would_recommend" id="would_recommend" value="1"
                       {{ old('would_recommend', $exitReport->would_recommend) ? 'checked' : '' }}
                       class="w-4 h-4 rounded">
                <label for="would_recommend" class="text-sm text-gray-700">Would recommend company</label>
            </div>

            <div>
                <label class="form-label">Interview Date</label>
                <input type="date" name="exit_interview_date"
                       value="{{ old('exit_interview_date', $exitReport->exit_interview_date?->toDateString()) }}"
                       class="form-input">
            </div>

            <div>
                <label class="form-label">Interview Conducted By</label>
                <input type="text" name="exit_interview_by"
                       value="{{ old('exit_interview_by', $exitReport->exit_interview_by) }}" class="form-input">
            </div>

            <div class="md:col-span-2">
                <label class="form-label">Reason for Leaving</label>
                <textarea name="reason_for_leaving" rows="2" class="form-input">{{ old('reason_for_leaving', $exitReport->reason_for_leaving) }}</textarea>
            </div>

            <div>
                <label class="form-label">Feedback on Company</label>
                <textarea name="feedback_company" rows="3" class="form-input">{{ old('feedback_company', $exitReport->feedback_company) }}</textarea>
            </div>

            <div>
                <label class="form-label">Feedback on Role</label>
                <textarea name="feedback_role" rows="3" class="form-input">{{ old('feedback_role', $exitReport->feedback_role) }}</textarea>
            </div>

        </div>
    </div>

    {{-- Clearance Checklist --}}
    <div class="bg-white rounded-lg shadow mb-6 border-2 border-primary-200">
        <div class="px-6 py-4 border-b border-gray-200 bg-primary-50">
            <h2 class="text-lg font-semibold text-gray-900">
                <i class="fas fa-clipboard-check mr-2 text-primary-600"></i>Clearance Checklist
            </h2>
            <p class="text-sm text-primary-700 mt-1">Tick off each department as they confirm clearance</p>
        </div>
        <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-4">

            @php
            $clearances = [
                'it_clearance'           => ['IT Clearance', 'Laptop, accounts, access cards returned'],
                'finance_clearance'      => ['Finance Clearance', 'No outstanding loans or advances'],
                'hr_clearance'           => ['HR Clearance', 'Documents handed over, ID returned'],
                'line_manager_clearance' => ['Line Manager Clearance', 'Handover of duties completed'],
                'admin_clearance'        => ['Admin Clearance', 'Office items, keys returned'],
            ];
            @endphp
            @foreach($clearances as $field => [$label, $desc])
            <div class="flex items-start gap-3 p-3 border border-gray-200 rounded-lg
                        {{ $exitReport->$field ? 'bg-green-50 border-green-300' : '' }}">
                <input type="checkbox" name="{{ $field }}" id="{{ $field }}" value="1"
                       {{ old($field, $exitReport->$field) ? 'checked' : '' }} class="w-5 h-5 rounded mt-0.5">
                <div>
                    <label for="{{ $field }}" class="font-medium text-gray-900 text-sm cursor-pointer">{{ $label }}</label>
                    <p class="text-xs text-gray-500">{{ $desc }}</p>
                </div>
            </div>
            @endforeach

        </div>
    </div>

    {{-- Final Settlement --}}
    <div class="bg-white rounded-lg shadow mb-6">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
            <h2 class="text-lg font-semibold text-gray-900">
                <i class="fas fa-money-bill-wave mr-2 text-primary-600"></i>Final Settlement
            </h2>
        </div>
        <div class="p-6 grid grid-cols-1 md:grid-cols-3 gap-6">

            <div>
                <label class="form-label">Settlement Amount (₦)</label>
                <input type="number" name="final_settlement_amount"
                       value="{{ old('final_settlement_amount', $exitReport->final_settlement_amount) }}"
                       class="form-input" step="0.01" min="0">
            </div>

            <div>
                <label class="form-label">Settlement Status</label>
                <select name="settlement_status" class="form-select">
                    @foreach(['pending' => 'Pending', 'processed' => 'Processed', 'paid' => 'Paid'] as $val => $label)
                        <option value="{{ $val }}"
                            {{ old('settlement_status', $exitReport->settlement_status) == $val ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="form-label">Settlement Date</label>
                <input type="date" name="settlement_date"
                       value="{{ old('settlement_date', $exitReport->settlement_date?->toDateString()) }}"
                       class="form-input">
            </div>

            <div class="md:col-span-3">
                <label class="form-label">Additional Notes</label>
                <textarea name="notes" rows="3" class="form-input">{{ old('notes', $exitReport->notes) }}</textarea>
            </div>

        </div>
    </div>

    <div class="flex justify-end gap-4">
        <a href="{{ route('admin.exit-reports.show', $exitReport) }}" class="btn btn-outline">Cancel</a>
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-save mr-2"></i> Update Exit Report
        </button>
    </div>

</form>
@endsection