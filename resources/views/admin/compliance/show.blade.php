{{--
    resources/views/admin/compliance/show.blade.php

    Compliance Tracking — Detail Page
    Shows full document details, expiry status, and uploaded file.
    Also shows other compliance records for the same staff member.
--}}
@extends('layouts.app')

@section('content')

<div class="mb-6 flex items-center justify-between">
    <div class="flex items-center gap-4">
        <a href="{{ route('admin.compliance.index') }}" class="text-gray-500 hover:text-gray-700">
            <i class="fas fa-arrow-left"></i>
        </a>
        <h1 class="text-3xl font-bold text-gray-900">Compliance Document</h1>
    </div>
    <div class="flex gap-3">
        <a href="{{ route('admin.compliance.edit', $compliance) }}" class="btn btn-primary">
            <i class="fas fa-edit mr-2"></i> Edit
        </a>
        <form method="POST" action="{{ route('admin.compliance.destroy', $compliance) }}"
              onsubmit="return confirm('Delete this compliance record?')">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger">
                <i class="fas fa-trash mr-2"></i> Delete
            </button>
        </form>
    </div>
</div>

{{-- Status banner --}}
<div class="mb-6 p-4 rounded-lg border
    {{ $compliance->computed_status === 'valid' ? 'bg-green-50 border-green-200' :
       ($compliance->computed_status === 'expiring_soon' ? 'bg-yellow-50 border-yellow-200' :
       ($compliance->computed_status === 'expired' ? 'bg-red-50 border-red-200' : 'bg-blue-50 border-blue-200')) }}">
    <div class="flex items-center gap-3">
        <span class="badge {{ $compliance->status_color }} text-sm px-3 py-1">
            {{ $compliance->status_label }}
        </span>
        @if($compliance->expiry_date)
            <span class="text-sm text-gray-600">
                @if($compliance->computed_status === 'expired')
                    This document expired <strong>{{ abs($compliance->days_until_expiry) }} day(s) ago</strong>
                    on {{ $compliance->expiry_date->format('M d, Y') }}.
                @elseif($compliance->computed_status === 'expiring_soon')
                    This document expires in <strong>{{ $compliance->days_until_expiry }} day(s)</strong>
                    on {{ $compliance->expiry_date->format('M d, Y') }}.
                @else
                    Valid until {{ $compliance->expiry_date->format('M d, Y') }}.
                @endif
            </span>
        @else
            <span class="text-sm text-gray-600">This document has no expiry date.</span>
        @endif
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">

    {{-- Staff Member --}}
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
            <h3 class="font-semibold text-gray-900">
                <i class="fas fa-user mr-2 text-primary-600"></i>Staff Member
            </h3>
        </div>
        <div class="p-6 flex items-center gap-4">
            <div class="w-14 h-14 rounded-full bg-primary-100 text-primary-700
                        flex items-center justify-center text-lg font-bold">
                {{ $compliance->staffProfile->initials ?? '?' }}
            </div>
            <div>
                <p class="font-semibold text-gray-900 text-lg">{{ $compliance->staffProfile->full_name }}</p>
                <p class="text-gray-500 text-sm">{{ $compliance->staffProfile->job_title ?? 'No title' }}</p>
                <p class="text-gray-500 text-sm">{{ $compliance->staffProfile->department ?? 'No department' }}</p>
                <a href="{{ route('admin.staff.show', $compliance->staffProfile) }}"
                   class="text-blue-600 hover:underline text-sm mt-1 inline-block">
                    View Profile →
                </a>
            </div>
        </div>
    </div>

    {{-- Document Details --}}
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
            <h3 class="font-semibold text-gray-900">
                <i class="fas fa-file-alt mr-2 text-primary-600"></i>Document Information
            </h3>
        </div>
        <div class="p-6 space-y-3">
            @php
            $details = [
                'Document Name'   => $compliance->document_name,
                'Document Type'   => $compliance->document_type_label,
                'Document Number' => $compliance->document_number,
                'Issuing Body'    => $compliance->issuing_body,
                'Issue Date'      => $compliance->issue_date ? $compliance->issue_date->format('M d, Y') : null,
                'Expiry Date'     => $compliance->expiry_date ? $compliance->expiry_date->format('M d, Y') : 'No expiry',
            ];
            @endphp
            @foreach($details as $label => $value)
            <div class="flex justify-between py-1 border-b border-gray-100 last:border-0">
                <span class="text-gray-500 text-sm">{{ $label }}</span>
                <span class="text-gray-900 text-sm font-medium">{{ $value ?? '—' }}</span>
            </div>
            @endforeach

            {{-- Uploaded file link --}}
            @if($compliance->document_file)
            <div class="pt-2">
                <a href="{{ Storage::url($compliance->document_file) }}" target="_blank"
                   class="text-blue-600 hover:underline text-sm">
                    <i class="fas fa-download mr-1"></i> View Uploaded Document
                </a>
            </div>
            @endif

            {{-- Notes --}}
            @if($compliance->notes)
            <div class="pt-2">
                <p class="text-gray-500 text-xs uppercase mb-1">Notes</p>
                <p class="text-gray-900 text-sm whitespace-pre-line">{{ $compliance->notes }}</p>
            </div>
            @endif
        </div>
    </div>

</div>

{{-- Other compliance records for this staff member --}}
@if($otherRecords->count())
<div class="bg-white rounded-lg shadow">
    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
        <h3 class="font-semibold text-gray-900">
            <i class="fas fa-folder-open mr-2 text-primary-600"></i>
            Other Documents for {{ $compliance->staffProfile->full_name }}
        </h3>
    </div>
    <div class="divide-y divide-gray-100">
        @foreach($otherRecords as $other)
        <a href="{{ route('admin.compliance.show', $other) }}"
           class="px-6 py-4 flex items-center justify-between hover:bg-gray-50 block">
            <div>
                <p class="text-sm font-medium text-gray-900">{{ $other->document_name }}</p>
                <p class="text-xs text-gray-500">{{ $other->document_type_label }}</p>
            </div>
            <div class="flex items-center gap-3">
                @if($other->expiry_date)
                    <span class="text-sm text-gray-600">{{ $other->expiry_date->format('M d, Y') }}</span>
                @else
                    <span class="text-sm text-gray-400 italic">No expiry</span>
                @endif
                <span class="badge {{ $other->status_color }}">{{ $other->status_label }}</span>
            </div>
        </a>
        @endforeach
    </div>
</div>
@endif

@endsection