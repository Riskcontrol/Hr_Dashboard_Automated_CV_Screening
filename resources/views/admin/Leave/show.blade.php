@extends('layouts.app')

@section('content')

<div class="mb-6 flex items-center justify-between">
    <div class="flex items-center gap-4">
        <a href="{{ route('admin.leave.index') }}" class="text-gray-500 hover:text-gray-700">
            <i class="fas fa-arrow-left"></i>
        </a>
        <h1 class="text-3xl font-bold text-gray-900">Leave Request</h1>
    </div>
    <div class="flex gap-3">
        @if($leave->status === 'pending')
            <a href="{{ route('admin.leave.edit', $leave) }}" class="btn btn-outline">
                <i class="fas fa-edit mr-2"></i> Edit
            </a>
        @endif
        <form method="POST" action="{{ route('admin.leave.destroy', $leave) }}"
              onsubmit="return confirm('Delete this leave request?')">
            @csrf @method('DELETE')
            <button type="submit" class="btn btn-danger">
                <i class="fas fa-trash mr-2"></i> Delete
            </button>
        </form>
    </div>
</div>

{{-- Status Banner --}}
<div class="mb-6 p-4 rounded-lg border
    {{ $leave->status === 'approved' ? 'bg-green-50 border-green-300' :
       ($leave->status === 'rejected' ? 'bg-red-50 border-red-300' : 'bg-yellow-50 border-yellow-300') }}">
    <div class="flex items-center gap-3">
        <span class="badge {{ $leave->status_color }} text-sm px-3 py-1">{{ ucfirst($leave->status) }}</span>
        <span class="text-sm text-gray-700">
            @if($leave->status === 'pending')
                Awaiting approval from <strong>{{ $leave->approver_name }}</strong>
                ({{ $leave->approver_type === 'hr' ? 'HR' : 'Line Manager' }})
            @else
                {{ ucfirst($leave->status) }} by <strong>{{ $leave->approved_by }}</strong>
                on {{ $leave->approved_at?->format('M d, Y \a\t g:i A') }}
            @endif
        </span>
    </div>
    @if($leave->status === 'rejected' && $leave->rejection_reason)
        <p class="mt-2 text-sm text-red-700">
            <strong>Rejection reason:</strong> {{ $leave->rejection_reason }}
        </p>
    @endif
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- LEFT COLUMN --}}
    <div class="lg:col-span-2 space-y-6">

        {{-- Staff Info --}}
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="font-semibold text-gray-900 mb-4 pb-2 border-b">
                <i class="fas fa-user mr-2 text-blue-500"></i>Staff Member
            </h2>
            <div class="flex items-center gap-4 mb-4">
                @if($leave->staffProfile->profile_photo)
                    <img src="{{ Storage::url($leave->staffProfile->profile_photo) }}"
                         class="w-14 h-14 rounded-full object-cover flex-shrink-0">
                @else
                    <div class="w-14 h-14 rounded-full bg-primary-100 text-primary-700 flex items-center justify-center font-bold text-lg flex-shrink-0">
                        {{ $leave->staffProfile->initials }}
                    </div>
                @endif
                <div>
                    <p class="text-xl font-bold text-gray-900">{{ $leave->staffProfile->full_name }}</p>
                    <p class="text-gray-500 text-sm">
                        {{ $leave->staffProfile->employee_id }} &middot;
                        {{ $leave->staffProfile->job_title ?? '—' }}
                    </p>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4 text-sm">
                <div>
                    <p class="text-gray-500">Department</p>
                    <p class="font-medium">{{ $leave->staffProfile->department ?? '—' }}</p>
                </div>
                <div>
                    <p class="text-gray-500">Section</p>
                    <p class="font-medium">{{ $leave->staff_section ?: ($leave->staffProfile->department ?? '—') }}</p>
                </div>
                <div>
                    <p class="text-gray-500">Line Manager</p>
                    <p class="font-medium">{{ $leave->staffProfile->line_manager ?? '—' }}</p>
                </div>
                <div>
                    <p class="text-gray-500">Submitted By</p>
                    <p class="font-medium">{{ ucfirst($leave->submitted_by ?? 'hr') }}</p>
                </div>
            </div>
        </div>

        {{-- Leave Details --}}
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="font-semibold text-gray-900 mb-4 pb-2 border-b">
                <i class="fas fa-calendar-alt mr-2 text-green-500"></i>Leave Details
            </h2>
            <div class="grid grid-cols-2 gap-4 text-sm mb-4">
                <div>
                    <p class="text-gray-500">Leave Type</p>
                    <p class="font-medium text-lg">{{ $leave->leave_type_label }}</p>
                </div>
                <div>
                    <p class="text-gray-500">Total Working Days</p>
                    <p class="font-bold text-2xl text-gray-900">{{ $leave->total_days }}</p>
                </div>
                <div>
                    <p class="text-gray-500">Start Date</p>
                    <p class="font-medium">{{ $leave->start_date->format('l, M d, Y') }}</p>
                </div>
                <div>
                    <p class="text-gray-500">End Date</p>
                    <p class="font-medium">{{ $leave->end_date->format('l, M d, Y') }}</p>
                </div>
            </div>
            @if($leave->reason)
                <div class="bg-gray-50 rounded-lg p-3">
                    <p class="text-xs text-gray-500 uppercase mb-1">Reason Given</p>
                    <p class="text-sm text-gray-700">{{ $leave->reason }}</p>
                </div>
            @endif
        </div>

        {{-- Leave Balance --}}
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="font-semibold text-gray-900 mb-4 pb-2 border-b">
                <i class="fas fa-chart-bar mr-2 text-purple-500"></i>Leave Balance — {{ now()->year }}
            </h2>
            @if($allowance)
                <div class="flex items-center gap-8 mb-4">
                    <div class="text-center">
                        <p class="text-3xl font-bold text-gray-900">{{ $allowance }}</p>
                        <p class="text-xs text-gray-500 mt-1">Total Allowance</p>
                    </div>
                    <div class="text-center">
                        <p class="text-3xl font-bold text-red-500">{{ $usedThisYear }}</p>
                        <p class="text-xs text-gray-500 mt-1">Used This Year</p>
                    </div>
                    <div class="text-center">
                        <p class="text-3xl font-bold text-green-500">{{ $remainingDays }}</p>
                        <p class="text-xs text-gray-500 mt-1">Remaining</p>
                    </div>
                </div>
                @php $pct = $allowance > 0 ? min(100, round(($usedThisYear / $allowance) * 100)) : 0; @endphp
                <div class="bg-gray-100 rounded-full h-3 mb-1">
                    <div class="h-3 rounded-full {{ $pct >= 90 ? 'bg-red-500' : ($pct >= 70 ? 'bg-yellow-500' : 'bg-green-500') }}"
                         style="width: {{ $pct }}%"></div>
                </div>
                <p class="text-xs text-gray-400">{{ $pct }}% of annual {{ $leave->leave_type_label }} used</p>
            @else
                <p class="text-gray-500 text-sm">This leave type has no fixed annual limit (Unpaid).</p>
            @endif
        </div>

    </div>

    {{-- RIGHT COLUMN --}}
    <div class="space-y-6">

        {{-- Approve / Reject --}}
        @if($leave->status === 'pending')
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="font-semibold text-gray-900 mb-4 pb-2 border-b">
                <i class="fas fa-gavel mr-2 text-yellow-500"></i>Take Action
            </h2>

            <form method="POST" action="{{ route('admin.leave.approve', $leave) }}" class="mb-4">
                @csrf
                <div class="mb-3">
                    <label class="form-label">Approved By</label>
                    <input type="text" name="approved_by" class="form-input"
                           placeholder="Your name" required>
                </div>
                <button type="submit" class="btn btn-success w-full"
                        onclick="return confirm('Approve this leave request?')">
                    <i class="fas fa-check mr-2"></i> Approve Leave
                </button>
            </form>

            <hr class="my-4">

            <form method="POST" action="{{ route('admin.leave.reject', $leave) }}">
                @csrf
                <div class="mb-3">
                    <label class="form-label">Rejected By</label>
                    <input type="text" name="approved_by" class="form-input"
                           placeholder="Your name" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Reason for Rejection</label>
                    <textarea name="rejection_reason" rows="3" class="form-textarea w-full"
                              placeholder="Why is this being rejected?" required></textarea>
                </div>
                <button type="submit" class="btn btn-danger w-full"
                        onclick="return confirm('Reject this leave request?')">
                    <i class="fas fa-times mr-2"></i> Reject Leave
                </button>
            </form>
        </div>
        @endif

        {{-- HR Notes --}}
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="font-semibold text-gray-900 mb-1 pb-2 border-b">
                <i class="fas fa-sticky-note mr-2 text-orange-500"></i>HR Notes
            </h2>
            <p class="text-xs text-gray-400 mb-3">Internal only — not visible to the staff member</p>

            <form method="POST" action="{{ route('admin.leave.update', $leave) }}" class="space-y-3">
                @csrf @method('PUT')
                <input type="hidden" name="staff_profile_id" value="{{ $leave->staff_profile_id }}">
                <input type="hidden" name="leave_type"       value="{{ $leave->leave_type }}">
                <input type="hidden" name="start_date"       value="{{ $leave->start_date->format('Y-m-d') }}">
                <input type="hidden" name="end_date"         value="{{ $leave->end_date->format('Y-m-d') }}">
                <input type="hidden" name="reason"           value="{{ $leave->reason }}">
                <input type="hidden" name="approver_type"    value="{{ $leave->approver_type }}">
                <input type="hidden" name="approver_name"    value="{{ $leave->approver_name }}">

                <div>
                    <label class="form-label text-xs">Staff Section</label>
                    <input type="text" name="staff_section" class="form-input"
                           value="{{ $leave->staff_section }}"
                           placeholder="e.g. Finance, Operations, HR">
                </div>
                <div>
                    <label class="form-label text-xs">Internal HR Notes</label>
                    <textarea name="hr_notes" rows="5" class="form-textarea w-full"
                              placeholder="Add internal notes...">{{ $leave->hr_notes }}</textarea>
                </div>
                <button type="submit" class="btn btn-outline w-full text-sm">
                    <i class="fas fa-save mr-2"></i> Save Notes
                </button>
            </form>
        </div>

    </div>
</div>

@endsection