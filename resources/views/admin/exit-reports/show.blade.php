{{--
    resources/views/admin/exit-reports/show.blade.php
    Full exit report detail page with clearance progress visualization.
--}}
@extends('layouts.app')

@section('content')

<div class="mb-6 flex items-center justify-between">
    <div class="flex items-center gap-4">
        <a href="{{ route('admin.exit-reports.index') }}" class="text-gray-500 hover:text-gray-700">
            <i class="fas fa-arrow-left"></i>
        </a>
        <h1 class="text-3xl font-bold text-gray-900">Exit Report</h1>
    </div>
    <div class="flex gap-3">
        <a href="{{ route('admin.exit-reports.edit', $exitReport) }}" class="btn btn-primary">
            <i class="fas fa-edit mr-2"></i> Edit
        </a>
        <form method="POST" action="{{ route('admin.exit-reports.destroy', $exitReport) }}"
              onsubmit="return confirm('Delete this exit report?')">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger">
                <i class="fas fa-trash mr-2"></i> Delete
            </button>
        </form>
    </div>
</div>

{{-- Header Card --}}
<div class="bg-white rounded-lg shadow mb-6 p-6">
    <div class="flex items-center justify-between flex-wrap gap-4">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 rounded-full bg-gray-200 text-gray-700
                        flex items-center justify-center text-lg font-bold">
                {{ $exitReport->staffProfile->initials ?? '?' }}
            </div>
            <div>
                <p class="font-bold text-gray-900 text-lg">{{ $exitReport->staffProfile->full_name }}</p>
                <p class="text-gray-500 text-sm">{{ $exitReport->staffProfile->job_title ?? '' }}</p>
                <a href="{{ route('admin.staff.show', $exitReport->staffProfile) }}"
                   class="text-blue-600 hover:underline text-xs">View Profile →</a>
            </div>
        </div>
        <div class="flex items-center gap-3">
            <span class="badge {{ $exitReport->exit_type_color }} px-3 py-1">
                {{ $exitReport->exit_type_label }}
            </span>
            <span class="badge {{ $exitReport->status_color }} px-3 py-1">
                {{ ucfirst(str_replace('_', ' ', $exitReport->status)) }}
            </span>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">

    {{-- Exit Details --}}
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
            <h3 class="font-semibold text-gray-900">
                <i class="fas fa-door-open mr-2 text-primary-600"></i>Exit Details
            </h3>
        </div>
        <div class="p-6 space-y-3">
            @php
            $details = [
                'Resignation Date'   => $exitReport->resignation_date?->format('M d, Y'),
                'Last Working Day'   => $exitReport->last_working_day->format('M d, Y'),
                'Notice Period'      => $exitReport->notice_period_days ? $exitReport->notice_period_days . ' days' : null,
                'Processed By'       => $exitReport->processed_by,
            ];
            @endphp
            @foreach($details as $label => $value)
            <div class="flex justify-between py-1 border-b border-gray-100 last:border-0">
                <span class="text-gray-500 text-sm">{{ $label }}</span>
                <span class="text-gray-900 text-sm font-medium">{{ $value ?? '—' }}</span>
            </div>
            @endforeach
        </div>
    </div>

    {{-- Final Settlement --}}
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
            <h3 class="font-semibold text-gray-900">
                <i class="fas fa-money-bill-wave mr-2 text-primary-600"></i>Final Settlement
            </h3>
        </div>
        <div class="p-6 space-y-3">
            @php
            $settlement = [
                'Amount'             => $exitReport->final_settlement_amount ? '₦' . number_format($exitReport->final_settlement_amount, 2) : 'Not set',
                'Status'             => ucfirst($exitReport->settlement_status),
                'Settlement Date'    => $exitReport->settlement_date?->format('M d, Y'),
            ];
            @endphp
            @foreach($settlement as $label => $value)
            <div class="flex justify-between py-1 border-b border-gray-100 last:border-0">
                <span class="text-gray-500 text-sm">{{ $label }}</span>
                <span class="text-gray-900 text-sm font-medium">{{ $value ?? '—' }}</span>
            </div>
            @endforeach
        </div>
    </div>

</div>

{{-- Clearance Checklist with progress --}}
<div class="bg-white rounded-lg shadow mb-6">
    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
        <div class="flex items-center justify-between">
            <h3 class="font-semibold text-gray-900">
                <i class="fas fa-clipboard-check mr-2 text-primary-600"></i>Clearance Checklist
            </h3>
            <span class="text-sm font-medium {{ $exitReport->clearance_percentage == 100 ? 'text-green-600' : 'text-yellow-600' }}">
                {{ $exitReport->clearance_progress }} Complete
            </span>
        </div>
        {{-- Progress bar --}}
        <div class="w-full bg-gray-200 rounded-full h-2 mt-3">
            <div class="{{ $exitReport->clearance_percentage == 100 ? 'bg-green-500' : 'bg-yellow-500' }} h-2 rounded-full transition-all"
                 style="width: {{ $exitReport->clearance_percentage }}%"></div>
        </div>
    </div>
    <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-4">
        @php
        $checklist = [
            'it_clearance'           => ['IT Clearance', 'fa-laptop'],
            'finance_clearance'      => ['Finance Clearance', 'fa-coins'],
            'hr_clearance'           => ['HR Clearance', 'fa-folder'],
            'line_manager_clearance' => ['Line Manager Clearance', 'fa-user-tie'],
            'admin_clearance'        => ['Admin Clearance', 'fa-key'],
        ];
        @endphp
        @foreach($checklist as $field => [$label, $icon])
        <div class="flex items-center gap-3 p-3 border rounded-lg
                    {{ $exitReport->$field ? 'bg-green-50 border-green-200' : 'bg-gray-50 border-gray-200' }}">
            <i class="fas {{ $exitReport->$field ? 'fa-check-circle text-green-600' : 'fa-circle text-gray-300' }} text-xl"></i>
            <div>
                <p class="font-medium text-sm {{ $exitReport->$field ? 'text-green-800' : 'text-gray-600' }}">{{ $label }}</p>
                <p class="text-xs text-gray-500">{{ $exitReport->$field ? 'Cleared' : 'Pending' }}</p>
            </div>
        </div>
        @endforeach
    </div>
</div>

{{-- Exit Interview --}}
@if($exitReport->exit_interview_conducted || $exitReport->reason_for_leaving || $exitReport->feedback_company)
<div class="bg-white rounded-lg shadow">
    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
        <h3 class="font-semibold text-gray-900">
            <i class="fas fa-comments mr-2 text-primary-600"></i>Exit Interview
        </h3>
    </div>
    <div class="p-6 space-y-4">
        <div class="flex items-center gap-6">
            <div class="flex items-center gap-2">
                <i class="fas {{ $exitReport->exit_interview_conducted ? 'fa-check-circle text-green-600' : 'fa-times-circle text-gray-400' }}"></i>
                <span class="text-sm text-gray-700">
                    Interview {{ $exitReport->exit_interview_conducted ? 'conducted' : 'not conducted' }}
                </span>
            </div>
            @if($exitReport->exit_interview_conducted)
            <div class="flex items-center gap-2">
                <i class="fas {{ $exitReport->would_recommend ? 'fa-thumbs-up text-green-600' : 'fa-thumbs-down text-red-500' }}"></i>
                <span class="text-sm text-gray-700">
                    {{ $exitReport->would_recommend ? 'Would recommend company' : 'Would not recommend company' }}
                </span>
            </div>
            @endif
        </div>

        @if($exitReport->exit_interview_date || $exitReport->exit_interview_by)
        <div class="flex gap-6 text-sm text-gray-600">
            @if($exitReport->exit_interview_date)
                <span>Date: {{ $exitReport->exit_interview_date->format('M d, Y') }}</span>
            @endif
            @if($exitReport->exit_interview_by)
                <span>By: {{ $exitReport->exit_interview_by }}</span>
            @endif
        </div>
        @endif

        @if($exitReport->reason_for_leaving)
        <div>
            <p class="text-xs text-gray-400 uppercase mb-1">Reason for Leaving</p>
            <p class="text-sm text-gray-900">{{ $exitReport->reason_for_leaving }}</p>
        </div>
        @endif

        @if($exitReport->feedback_company)
        <div>
            <p class="text-xs text-gray-400 uppercase mb-1">Feedback on Company</p>
            <p class="text-sm text-gray-900">{{ $exitReport->feedback_company }}</p>
        </div>
        @endif

        @if($exitReport->feedback_role)
        <div>
            <p class="text-xs text-gray-400 uppercase mb-1">Feedback on Role</p>
            <p class="text-sm text-gray-900">{{ $exitReport->feedback_role }}</p>
        </div>
        @endif

        @if($exitReport->notes)
        <div class="pt-3 border-t border-gray-100">
            <p class="text-xs text-gray-400 uppercase mb-1">Additional Notes</p>
            <p class="text-sm text-gray-900">{{ $exitReport->notes }}</p>
        </div>
        @endif
    </div>
</div>
@endif

@endsection