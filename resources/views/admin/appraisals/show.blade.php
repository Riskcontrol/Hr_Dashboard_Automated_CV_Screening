@extends('layouts.app')

@section('content')

<div class="mb-6 flex items-center justify-between">
    <div class="flex items-center gap-4">
        <a href="{{ route('my.appraisals.index') }}" class="text-gray-500 hover:text-gray-700">
            <i class="fas fa-arrow-left"></i>
        </a>
        <h1 class="text-3xl font-bold text-gray-900">{{ $appraisal->appraisal_type_label }}</h1>
    </div>
    @if($appraisal->sent_to_employee_at)
        <a href="{{ route('my.appraisals.fill', $appraisal) }}" class="btn btn-primary">
            <i class="fas fa-edit mr-2"></i>
            {{ $appraisal->self_job_achievements ? 'Update My Self-Evaluation' : 'Fill In My Self-Evaluation' }}
        </a>
    @endif
</div>

@if($appraisal->sent_to_employee_at)
    <div class="alert alert-info mb-4">
        <i class="fas fa-envelope mr-2"></i>
        HR sent you this form on {{ $appraisal->sent_to_employee_at->format('M d, Y \a\t g:i A') }}.
        Please fill in your self-evaluation before the due date.
    </div>
@else
    <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 mb-4 text-sm text-gray-500">
        <i class="fas fa-clock mr-2"></i>
        This appraisal form has not been sent to you yet.
        HR will notify you when it is ready to fill in.
    </div>
@endif

@if($appraisal->edit_history && count($appraisal->edit_history))
    @php $lastEdit = last($appraisal->edit_history); @endphp
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-3 mb-4 text-sm text-blue-700">
        <i class="fas fa-history mr-2"></i>
        Last edited by <strong>{{ $lastEdit['edited_by'] }}</strong>
        on {{ \Carbon\Carbon::parse($lastEdit['timestamp'])->format('M d, Y \a\t g:i A') }}
    </div>
@endif

<div class="bg-white p-6 rounded-lg shadow max-w-3xl space-y-6">
    <div class="grid grid-cols-2 gap-6">
        <div>
            <p class="text-sm text-gray-500">Status</p>
            <p class="font-medium">{{ ucfirst(str_replace('_', ' ', $appraisal->status)) }}</p>
        </div>
        <div>
            <p class="text-sm text-gray-500">Due Date</p>
            <p class="font-medium">{{ $appraisal->due_date?->format('M d, Y') }}</p>
        </div>
        <div>
            <p class="text-sm text-gray-500">Reviewer</p>
            <p class="font-medium">{{ $appraisal->reviewer_name ?: '—' }}</p>
        </div>
        <div>
            <p class="text-sm text-gray-500">Overall Rating</p>
            <p class="font-medium">
                {{ $appraisal->overall_rating
                    ? ucfirst(str_replace('_', ' ', $appraisal->overall_rating))
                    : '—' }}
            </p>
        </div>
    </div>

    @if($appraisal->self_job_achievements)
        <div class="border-t pt-4">
            <h3 class="font-semibold text-gray-700 mb-3">Your Self-Evaluation (submitted)</h3>
            @if($appraisal->self_duties_understanding)
                <div class="mb-3">
                    <p class="text-xs text-gray-500 uppercase mb-1">Duties and Responsibilities</p>
                    <p class="text-sm">{{ $appraisal->self_duties_understanding }}</p>
                </div>
            @endif
            @if($appraisal->self_job_achievements)
                <div class="mb-3">
                    <p class="text-xs text-gray-500 uppercase mb-1">Job Achievements</p>
                    <p class="text-sm">{{ $appraisal->self_job_achievements }}</p>
                </div>
            @endif
            @if($appraisal->self_other_achievements)
                <div class="mb-3">
                    <p class="text-xs text-gray-500 uppercase mb-1">Other Achievements</p>
                    <p class="text-sm">{{ $appraisal->self_other_achievements }}</p>
                </div>
            @endif
        </div>
    @endif

    @if($appraisal->performance_summary)
        <div class="border-t pt-4">
            <p class="text-sm text-gray-500 mb-1">Performance Summary (from reviewer)</p>
            <p>{{ $appraisal->performance_summary }}</p>
        </div>
    @endif
    @if($appraisal->strengths)
        <div>
            <p class="text-sm text-gray-500 mb-1">Strengths</p>
            <p>{{ $appraisal->strengths }}</p>
        </div>
    @endif
    @if($appraisal->areas_for_improvement)
        <div>
            <p class="text-sm text-gray-500 mb-1">Areas for Improvement</p>
            <p>{{ $appraisal->areas_for_improvement }}</p>
        </div>
    @endif
    @if($appraisal->goals_next_period)
        <div>
            <p class="text-sm text-gray-500 mb-1">Goals for Next Period</p>
            <p>{{ $appraisal->goals_next_period }}</p>
        </div>
    @endif
</div>

@endsection