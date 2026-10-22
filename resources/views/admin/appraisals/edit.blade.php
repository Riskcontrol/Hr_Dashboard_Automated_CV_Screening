{{--
    resources/views/admin/appraisals/edit.blade.php
    Edit an existing appraisal — same as create but pre-filled.
    Most common use: filling in the evaluation after the appraisal is done.
--}}
@extends('layouts.app')

@section('content')

<div class="mb-6 flex items-center gap-4">
    <a href="{{ route('admin.appraisals.show', $appraisal) }}" class="text-gray-500 hover:text-gray-700">
        <i class="fas fa-arrow-left"></i>
    </a>
    <div>
        <h1 class="text-3xl font-bold text-gray-900">Edit Appraisal</h1>
        <p class="text-gray-500 mt-1">
            {{ $appraisal->appraisal_type_label }} — {{ $appraisal->staffProfile->full_name }}
        </p>
    </div>
</div>

@if($appraisal->status === 'pending' || $appraisal->status === 'in_progress')
<div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
    <p class="text-blue-800 text-sm">
        <i class="fas fa-info-circle mr-1"></i>
        <strong>Tip:</strong> Fill in the Evaluation section below after conducting the appraisal,
        then change the status to <strong>Completed</strong>.
    </p>
</div>
@endif

<form method="POST" action="{{ route('admin.appraisals.update', $appraisal) }}">
    @csrf
    @method('PUT')

    {{-- Basic Info --}}
    <div class="bg-white rounded-lg shadow mb-6">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
            <h2 class="text-lg font-semibold text-gray-900">
                <i class="fas fa-info-circle mr-2 text-primary-600"></i>Appraisal Details
            </h2>
        </div>
        <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">

            <div>
                <label class="form-label">Staff Member <span class="text-red-500">*</span></label>
                <select name="staff_profile_id" class="form-select" required>
                    @foreach($staff as $member)
                        <option value="{{ $member->id }}"
                            {{ old('staff_profile_id', $appraisal->staff_profile_id) == $member->id ? 'selected' : '' }}>
                            {{ $member->full_name }}
                            @if($member->job_title) — {{ $member->job_title }} @endif
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="form-label">Appraisal Type <span class="text-red-500">*</span></label>
                <select name="appraisal_type" id="appraisal_type" class="form-select"
                        required onchange="toggleProbationOutcome()">
                    @foreach([
                        'probation_3month' => 'Probation Review (3 Months)',
                        'probation_6month' => 'Probation Review (6 Months)',
                        'annual'           => 'Annual Appraisal',
                        'mid_year'         => 'Mid-Year Review',
                        'pip'              => 'Performance Improvement Plan',
                    ] as $val => $label)
                        <option value="{{ $val }}"
                            {{ old('appraisal_type', $appraisal->appraisal_type) == $val ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="form-label">Due Date <span class="text-red-500">*</span></label>
                <input type="date" name="due_date"
                       value="{{ old('due_date', $appraisal->due_date->toDateString()) }}"
                       class="form-input" required>
            </div>

            <div>
                <label class="form-label">Status <span class="text-red-500">*</span></label>
                <select name="status" class="form-select" required>
                    @foreach(['pending' => 'Pending', 'in_progress' => 'In Progress', 'completed' => 'Completed', 'cancelled' => 'Cancelled'] as $val => $label)
                        <option value="{{ $val }}"
                            {{ old('status', $appraisal->status) == $val ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="form-label">Reviewer Name <span class="text-red-500">*</span></label>
                <input type="text" name="reviewer_name"
                       value="{{ old('reviewer_name', $appraisal->reviewer_name) }}"
                       class="form-input" required>
            </div>

            <div>
                <label class="form-label">Reviewer Role</label>
                <input type="text" name="reviewer_role"
                       value="{{ old('reviewer_role', $appraisal->reviewer_role) }}" class="form-input">
            </div>

        </div>
    </div>

    {{-- Evaluation --}}
    <div class="bg-white rounded-lg shadow mb-6 border-2 border-primary-200">
        <div class="px-6 py-4 border-b border-gray-200 bg-primary-50">
            <h2 class="text-lg font-semibold text-gray-900">
                <i class="fas fa-star mr-2 text-primary-600"></i>Evaluation Form
            </h2>
        </div>
        <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">

            <div>
                <label class="form-label">Overall Rating</label>
                <select name="overall_rating" class="form-select">
                    <option value="">Not yet rated</option>
                    @foreach([
                        'excellent'         => 'Excellent',
                        'good'              => 'Good',
                        'satisfactory'      => 'Satisfactory',
                        'needs_improvement' => 'Needs Improvement',
                        'unsatisfactory'    => 'Unsatisfactory',
                    ] as $val => $label)
                        <option value="{{ $val }}"
                            {{ old('overall_rating', $appraisal->overall_rating) == $val ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div id="probation_outcome_div" style="display:none;">
                <label class="form-label">Probation Outcome</label>
                <select name="probation_outcome" class="form-select">
                    <option value="">Select outcome</option>
                    <option value="confirmed"  {{ old('probation_outcome', $appraisal->probation_outcome) == 'confirmed'  ? 'selected' : '' }}>✅ Confirmed as Permanent</option>
                    <option value="extended"   {{ old('probation_outcome', $appraisal->probation_outcome) == 'extended'   ? 'selected' : '' }}>⏳ Probation Extended</option>
                    <option value="terminated" {{ old('probation_outcome', $appraisal->probation_outcome) == 'terminated' ? 'selected' : '' }}>❌ Employment Terminated</option>
                </select>
            </div>

            <div>
                <label class="form-label">Completed Date</label>
                <input type="date" name="completed_date"
                       value="{{ old('completed_date', $appraisal->completed_date?->toDateString()) }}"
                       class="form-input">
            </div>

            <div class="md:col-span-2">
                <label class="form-label">Performance Summary</label>
                <textarea name="performance_summary" rows="3" class="form-input">{{ old('performance_summary', $appraisal->performance_summary) }}</textarea>
            </div>

            <div>
                <label class="form-label">Strengths</label>
                <textarea name="strengths" rows="3" class="form-input">{{ old('strengths', $appraisal->strengths) }}</textarea>
            </div>

            <div>
                <label class="form-label">Areas for Improvement</label>
                <textarea name="areas_for_improvement" rows="3" class="form-input">{{ old('areas_for_improvement', $appraisal->areas_for_improvement) }}</textarea>
            </div>

            <div>
                <label class="form-label">Goals for Next Period</label>
                <textarea name="goals_next_period" rows="3" class="form-input">{{ old('goals_next_period', $appraisal->goals_next_period) }}</textarea>
            </div>

            <div>
                <label class="form-label">Employee Comments</label>
                <textarea name="employee_comments" rows="3" class="form-input">{{ old('employee_comments', $appraisal->employee_comments) }}</textarea>
            </div>

            <div class="md:col-span-2">
                <label class="form-label">Reviewer's Final Comments</label>
                <textarea name="reviewer_comments" rows="3" class="form-input">{{ old('reviewer_comments', $appraisal->reviewer_comments) }}</textarea>
            </div>

        </div>
    </div>

    <div class="flex justify-end gap-4">
        <a href="{{ route('admin.appraisals.show', $appraisal) }}" class="btn btn-outline">Cancel</a>
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-save mr-2"></i> Update Appraisal
        </button>
    </div>

</form>

@push('scripts')
<script>
function toggleProbationOutcome() {
    const type = document.getElementById('appraisal_type').value;
    const div  = document.getElementById('probation_outcome_div');
    div.style.display = (type === 'probation_3month' || type === 'probation_6month') ? 'block' : 'none';
}
toggleProbationOutcome();
</script>
@endpush

@endsection