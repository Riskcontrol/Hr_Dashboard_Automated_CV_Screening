{{--
    resources/views/admin/appraisals/create.blade.php
    Schedule a new appraisal or probation review.
--}}
@extends('layouts.app')

@section('content')

<div class="mb-6 flex items-center gap-4">
    <a href="{{ route('admin.appraisals.index') }}" class="text-gray-500 hover:text-gray-700">
        <i class="fas fa-arrow-left"></i>
    </a>
    <div>
        <h1 class="text-3xl font-bold text-gray-900">Schedule Appraisal</h1>
        <p class="text-gray-500 mt-1">Set up a probation review or performance appraisal</p>
    </div>
</div>

<form method="POST" action="{{ route('admin.appraisals.store') }}">
    @csrf

    {{-- SECTION 1: Basic Info --}}
    <div class="bg-white rounded-lg shadow mb-6">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
            <h2 class="text-lg font-semibold text-gray-900">
                <i class="fas fa-info-circle mr-2 text-primary-600"></i>Appraisal Details
            </h2>
        </div>
        <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">

            <div>
                <label class="form-label">Staff Member <span class="text-red-500">*</span></label>
                <select name="staff_profile_id"
                        class="form-select @error('staff_profile_id') border-red-500 @enderror" required>
                    <option value="">Select staff member</option>
                    @foreach($staff as $member)
                        <option value="{{ $member->id }}"
                            {{ old('staff_profile_id') == $member->id ? 'selected' : '' }}>
                            {{ $member->full_name }}
                            @if($member->job_title) — {{ $member->job_title }} @endif
                        </option>
                    @endforeach
                </select>
                @error('staff_profile_id')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="form-label">Appraisal Type <span class="text-red-500">*</span></label>
                <select name="appraisal_type" id="appraisal_type"
                        class="form-select @error('appraisal_type') border-red-500 @enderror"
                        required onchange="toggleProbationOutcome()">
                    <option value="">Select type</option>
                    @foreach([
                        'probation_3month' => 'Probation Review (3 Months)',
                        'probation_6month' => 'Probation Review (6 Months)',
                        'annual'           => 'Annual Appraisal',
                        'mid_year'         => 'Mid-Year Review',
                        'pip'              => 'Performance Improvement Plan',
                    ] as $val => $label)
                        <option value="{{ $val }}" {{ old('appraisal_type') == $val ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
                @error('appraisal_type')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="form-label">Due Date <span class="text-red-500">*</span></label>
                <input type="date" name="due_date" value="{{ old('due_date') }}"
                       class="form-input @error('due_date') border-red-500 @enderror" required>
                @error('due_date')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="form-label">Status <span class="text-red-500">*</span></label>
                <select name="status" class="form-select" required>
                    <option value="pending"     {{ old('status', 'pending') == 'pending'     ? 'selected' : '' }}>Pending</option>
                    <option value="in_progress" {{ old('status') == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                    <option value="completed"   {{ old('status') == 'completed'   ? 'selected' : '' }}>Completed</option>
                    <option value="cancelled"   {{ old('status') == 'cancelled'   ? 'selected' : '' }}>Cancelled</option>
                </select>
            </div>

            <div>
                <label class="form-label">Reviewer Name <span class="text-red-500">*</span></label>
                <input type="text" name="reviewer_name" value="{{ old('reviewer_name') }}"
                       class="form-input @error('reviewer_name') border-red-500 @enderror"
                       placeholder="e.g. John Smith" required>
                @error('reviewer_name')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="form-label">Reviewer Role</label>
                <input type="text" name="reviewer_role" value="{{ old('reviewer_role') }}"
                       class="form-input" placeholder="e.g. Line Manager, HR Manager">
            </div>

        </div>
    </div>

    {{-- SECTION 2: Evaluation Form (filled in when completing the appraisal) --}}
    <div class="bg-white rounded-lg shadow mb-6">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
            <h2 class="text-lg font-semibold text-gray-900">
                <i class="fas fa-star mr-2 text-primary-600"></i>Evaluation
                <span class="text-sm text-gray-500 font-normal ml-2">(complete during/after the appraisal)</span>
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
                        <option value="{{ $val }}" {{ old('overall_rating') == $val ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Probation outcome — only visible for probation types --}}
            <div id="probation_outcome_div" style="display:none;">
                <label class="form-label">Probation Outcome</label>
                <select name="probation_outcome" class="form-select">
                    <option value="">Select outcome</option>
                    <option value="confirmed"  {{ old('probation_outcome') == 'confirmed'  ? 'selected' : '' }}>✅ Confirmed as Permanent</option>
                    <option value="extended"   {{ old('probation_outcome') == 'extended'   ? 'selected' : '' }}>⏳ Probation Extended</option>
                    <option value="terminated" {{ old('probation_outcome') == 'terminated' ? 'selected' : '' }}>❌ Employment Terminated</option>
                </select>
            </div>

            <div>
                <label class="form-label">Completed Date</label>
                <input type="date" name="completed_date" value="{{ old('completed_date') }}" class="form-input">
            </div>

            <div class="md:col-span-2">
                <label class="form-label">Performance Summary</label>
                <textarea name="performance_summary" rows="3" class="form-input"
                          placeholder="Overall summary of the employee's performance during this period">{{ old('performance_summary') }}</textarea>
            </div>

            <div>
                <label class="form-label">Strengths</label>
                <textarea name="strengths" rows="3" class="form-input"
                          placeholder="What does this employee do particularly well?">{{ old('strengths') }}</textarea>
            </div>

            <div>
                <label class="form-label">Areas for Improvement</label>
                <textarea name="areas_for_improvement" rows="3" class="form-input"
                          placeholder="What areas need development or improvement?">{{ old('areas_for_improvement') }}</textarea>
            </div>

            <div>
                <label class="form-label">Goals for Next Period</label>
                <textarea name="goals_next_period" rows="3" class="form-input"
                          placeholder="Goals and targets set for the next review period">{{ old('goals_next_period') }}</textarea>
            </div>

            <div>
                <label class="form-label">Employee Comments</label>
                <textarea name="employee_comments" rows="3" class="form-input"
                          placeholder="Employee's own comments or response to the review">{{ old('employee_comments') }}</textarea>
            </div>

            <div class="md:col-span-2">
                <label class="form-label">Reviewer's Final Comments</label>
                <textarea name="reviewer_comments" rows="3" class="form-input"
                          placeholder="Reviewer's closing remarks">{{ old('reviewer_comments') }}</textarea>
            </div>

        </div>
    </div>

    <div class="flex justify-end gap-4">
        <a href="{{ route('admin.appraisals.index') }}" class="btn btn-outline">Cancel</a>
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-save mr-2"></i> Save Appraisal
        </button>
    </div>

</form>

{{-- Show/hide probation outcome based on type selection --}}
@push('scripts')
<script>
function toggleProbationOutcome() {
    const type = document.getElementById('appraisal_type').value;
    const div  = document.getElementById('probation_outcome_div');
    div.style.display = (type === 'probation_3month' || type === 'probation_6month') ? 'block' : 'none';
}
// Run on page load in case old() value is a probation type
toggleProbationOutcome();
</script>
@endpush

@endsection