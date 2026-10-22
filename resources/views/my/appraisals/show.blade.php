{{--
    resources/views/my/appraisals/show.blade.php
    Staff self-service — read-only view of one of your own appraisals.
    Note: an "employee fills it in themselves" editable version is coming
    in the appraisal-forms batch — this is the view-only version for now.
--}}
@extends('layouts.app')

@section('content')

<div class="mb-6 flex items-center gap-4">
    <a href="{{ route('my.appraisals.index') }}" class="text-gray-500 hover:text-gray-700"><i class="fas fa-arrow-left"></i></a>
    <h1 class="text-3xl font-bold text-gray-900">{{ ucfirst(str_replace('_', ' ', $appraisal->appraisal_type)) }}</h1>
</div>

<div class="bg-white p-6 rounded-lg shadow max-w-3xl space-y-6">
    <div class="grid grid-cols-2 gap-6">
        <div><p class="text-sm text-gray-500">Status</p><p class="font-medium">{{ ucfirst(str_replace('_', ' ', $appraisal->status)) }}</p></div>
        <div><p class="text-sm text-gray-500">Due Date</p><p class="font-medium">{{ $appraisal->due_date?->format('M d, Y') }}</p></div>
        <div><p class="text-sm text-gray-500">Reviewer</p><p class="font-medium">{{ $appraisal->reviewer_name ?: '—' }}</p></div>
        <div><p class="text-sm text-gray-500">Overall Rating</p><p class="font-medium">{{ $appraisal->overall_rating ? ucfirst(str_replace('_', ' ', $appraisal->overall_rating)) : '—' }}</p></div>
    </div>

    @if($appraisal->performance_summary)
        <div><p class="text-sm text-gray-500 mb-1">Performance Summary</p><p>{{ $appraisal->performance_summary }}</p></div>
    @endif
    @if($appraisal->strengths)
        <div><p class="text-sm text-gray-500 mb-1">Strengths</p><p>{{ $appraisal->strengths }}</p></div>
    @endif
    @if($appraisal->areas_for_improvement)
        <div><p class="text-sm text-gray-500 mb-1">Areas for Improvement</p><p>{{ $appraisal->areas_for_improvement }}</p></div>
    @endif
    @if($appraisal->goals_next_period)
        <div><p class="text-sm text-gray-500 mb-1">Goals for Next Period</p><p>{{ $appraisal->goals_next_period }}</p></div>
    @endif
</div>

@endsection