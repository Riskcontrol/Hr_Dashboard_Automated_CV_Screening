@extends('layouts.app')

@section('content')

<div class="mb-6 flex items-center gap-4">
    <a href="{{ route('my.appraisals.show', $appraisal) }}" class="text-gray-500 hover:text-gray-700">
        <i class="fas fa-arrow-left"></i>
    </a>
    <div>
        <h1 class="text-3xl font-bold text-gray-900">Employee Self Evaluation</h1>
        <p class="text-gray-500 mt-1">
            {{ $appraisal->appraisal_type_label }} &middot; Due {{ $appraisal->due_date?->format('M d, Y') }}
        </p>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success mb-4">
        <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
    </div>
@endif

@if($appraisal->edit_history && count($appraisal->edit_history))
    @php $lastEdit = last($appraisal->edit_history); @endphp
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-3 mb-4 text-sm text-blue-700">
        <i class="fas fa-history mr-2"></i>
        Last saved by <strong>{{ $lastEdit['edited_by'] }}</strong>
        on {{ \Carbon\Carbon::parse($lastEdit['timestamp'])->format('M d, Y \a\t g:i A') }}
        &mdash; {{ count($appraisal->edit_history) }} save(s) total
    </div>
@endif

<form method="POST" action="{{ route('my.appraisals.save', $appraisal) }}"
      class="bg-white rounded-lg shadow p-6 max-w-3xl space-y-6">
    @csrf

    <div class="border-b pb-4">
        <h2 class="text-lg font-semibold text-gray-800">Employee Self Evaluation Form</h2>
        <p class="text-gray-500 text-sm mt-1">
            Complete each section honestly. You can save your progress and come back to update it before the due date.
            Every save is automatically timestamped.
        </p>
    </div>

    <div>
        <label class="form-label">Employee Mission Statement</label>
        <textarea name="self_mission_statement" rows="3" class="form-textarea w-full"
            placeholder="What is your personal mission statement in this role?">{{ old('self_mission_statement', $appraisal->self_mission_statement) }}</textarea>
    </div>

    <div>
        <label class="form-label">
            State your understanding of your main duties and responsibilities
        </label>
        <textarea name="self_duties_understanding" rows="4" class="form-textarea w-full"
            placeholder="Describe your key duties and responsibilities...">{{ old('self_duties_understanding', $appraisal->self_duties_understanding) }}</textarea>
    </div>

    <div>
        <label class="form-label">
            What do you consider your job-specific achievements in the past six (6) months?
        </label>
        <textarea name="self_job_achievements" rows="4" class="form-textarea w-full"
            placeholder="List your key job-specific achievements...">{{ old('self_job_achievements', $appraisal->self_job_achievements) }}</textarea>
    </div>

    <div>
        <label class="form-label">
            What do you consider your other achievements in the past six (6) months?
        </label>
        <textarea name="self_other_achievements" rows="4" class="form-textarea w-full"
            placeholder="List any other achievements outside your core role...">{{ old('self_other_achievements', $appraisal->self_other_achievements) }}</textarea>
    </div>

    <div>
        <label class="form-label">
            What do you like and dislike about working for this organisation?
        </label>
        <textarea name="self_likes_dislikes" rows="4" class="form-textarea w-full"
            placeholder="Be honest — this helps improve the workplace...">{{ old('self_likes_dislikes', $appraisal->self_likes_dislikes) }}</textarea>
    </div>

    <div>
        <label class="form-label">
            What element of your job do you find the most difficult / interesting?
        </label>
        <textarea name="self_most_difficult" rows="4" class="form-textarea w-full"
            placeholder="Describe challenges and what you find most interesting...">{{ old('self_most_difficult', $appraisal->self_most_difficult) }}</textarea>
    </div>

    <div>
        <label class="form-label">
            What action could be taken to improve your performance in your current role?
        </label>
        <textarea name="self_improvement_actions" rows="4" class="form-textarea w-full"
            placeholder="Suggest training, resources, or changes that would help you...">{{ old('self_improvement_actions', $appraisal->self_improvement_actions) }}</textarea>
    </div>

    <div>
        <label class="form-label">Any additional comments</label>
        <textarea name="employee_comments" rows="3" class="form-textarea w-full"
            placeholder="Anything else you'd like HR or your reviewer to know...">{{ old('employee_comments', $appraisal->employee_comments) }}</textarea>
    </div>

    <div class="flex justify-between items-center pt-4 border-t">
        <p class="text-xs text-gray-400 max-w-sm">
            <i class="fas fa-info-circle mr-1"></i>
            Every save is recorded with your name and timestamp.
            You can update your answers any time before the due date.
        </p>
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-save mr-2"></i> Save My Evaluation
        </button>
    </div>
</form>

@endsection