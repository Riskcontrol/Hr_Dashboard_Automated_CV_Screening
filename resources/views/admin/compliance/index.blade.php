{{--
    resources/views/admin/compliance/index.blade.php

    Compliance Tracking — List Page
    Shows all certifications/licenses with expiry alerts.
    Card layout (clickable rows) — same pattern as Staff & Leave.
--}}
@extends('layouts.app')

@section('content')

{{-- Page Header --}}
<div class="mb-6 flex justify-between items-center">
    <div>
        <h1 class="text-3xl font-bold text-gray-900">Compliance Tracking</h1>
        <p class="text-gray-500 mt-1">Certifications, licenses & document expiry alerts</p>
    </div>
    <a href="{{ route('admin.compliance.create') }}" class="btn btn-primary">
        <i class="fas fa-plus mr-2"></i> Add Document
    </a>
</div>

{{-- =====================================================
    STAT CARDS
===================================================== --}}
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">

    <div class="bg-white p-5 rounded-lg shadow flex items-center gap-4">
        <div class="p-3 rounded-full bg-blue-100 text-blue-600">
            <i class="fas fa-shield-alt text-xl"></i>
        </div>
        <div>
            <p class="text-2xl font-bold text-gray-900">{{ $stats['total'] }}</p>
            <p class="text-gray-500 text-sm">Total Documents</p>
        </div>
    </div>

    <div class="bg-white p-5 rounded-lg shadow flex items-center gap-4">
        <div class="p-3 rounded-full bg-green-100 text-green-600">
            <i class="fas fa-check-circle text-xl"></i>
        </div>
        <div>
            <p class="text-2xl font-bold text-gray-900">{{ $stats['valid'] }}</p>
            <p class="text-gray-500 text-sm">Valid</p>
        </div>
    </div>

    <div class="bg-white p-5 rounded-lg shadow flex items-center gap-4">
        <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
            <i class="fas fa-exclamation-triangle text-xl"></i>
        </div>
        <div>
            <p class="text-2xl font-bold text-gray-900">{{ $stats['expiring_soon'] }}</p>
            <p class="text-gray-500 text-sm">Expiring Soon</p>
        </div>
    </div>

    <div class="bg-white p-5 rounded-lg shadow flex items-center gap-4">
        <div class="p-3 rounded-full bg-red-100 text-red-600">
            <i class="fas fa-times-circle text-xl"></i>
        </div>
        <div>
            <p class="text-2xl font-bold text-gray-900">{{ $stats['expired'] }}</p>
            <p class="text-gray-500 text-sm">Expired</p>
        </div>
    </div>

</div>

{{-- Alert banner if anything needs attention --}}
@if($stats['expired'] > 0 || $stats['expiring_soon'] > 0)
<div class="mb-6 p-4 rounded-lg border bg-yellow-50 border-yellow-200">
    <div class="flex items-center gap-2 text-yellow-800">
        <i class="fas fa-bell"></i>
        <p class="text-sm">
            <strong>{{ $stats['expired'] }}</strong> document(s) have expired and
            <strong>{{ $stats['expiring_soon'] }}</strong> are expiring within 30 days.
            Please review and renew as needed.
        </p>
    </div>
</div>
@endif

{{-- =====================================================
    FILTER BAR
===================================================== --}}
<div class="bg-white rounded-lg shadow mb-6 p-4">
    <form method="GET" action="{{ route('admin.compliance.index') }}" class="flex flex-wrap gap-3 items-end">

        <div class="flex-1 min-w-48">
            <label class="form-label">Search</label>
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Document name or staff name..." class="form-input">
        </div>

        <div class="min-w-40">
            <label class="form-label">Document Type</label>
            <select name="document_type" class="form-select">
                <option value="">All Types</option>
                @foreach([
                    'certification'    => 'Certification',
                    'license'          => 'License',
                    'insurance'        => 'Insurance',
                    'contract'         => 'Contract',
                    'id_document'      => 'ID Document',
                    'medical'          => 'Medical Certificate',
                    'background_check' => 'Background Check',
                ] as $val => $label)
                    <option value="{{ $val }}" {{ request('document_type') == $val ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
        </div>

        <div class="min-w-40">
            <label class="form-label">Status</label>
            <select name="status_filter" class="form-select">
                <option value="">All</option>
                <option value="valid"               {{ request('status_filter') == 'valid'               ? 'selected' : '' }}>Valid</option>
                <option value="expiring_soon"        {{ request('status_filter') == 'expiring_soon'        ? 'selected' : '' }}>Expiring Soon</option>
                <option value="expired"              {{ request('status_filter') == 'expired'              ? 'selected' : '' }}>Expired</option>
                <option value="renewal_in_progress"  {{ request('status_filter') == 'renewal_in_progress'  ? 'selected' : '' }}>Renewal In Progress</option>
            </select>
        </div>

        <button type="submit" class="btn btn-primary">
            <i class="fas fa-search mr-2"></i> Filter
        </button>

        @if(request()->hasAny(['search', 'document_type', 'status_filter']))
            <a href="{{ route('admin.compliance.index') }}" class="btn btn-outline">Clear</a>
        @endif

    </form>
</div>

{{-- =====================================================
    RECORDS LIST — Card layout, click to view details
===================================================== --}}
<div class="space-y-4">

    @if($records->count())

        @foreach($records as $record)
        <a href="{{ route('admin.compliance.show', $record) }}"
           class="block bg-white rounded-lg shadow hover:shadow-md hover:border-primary-300
                  border border-transparent transition-all duration-200 cursor-pointer">
            <div class="p-5">
                <div class="flex flex-wrap items-center justify-between gap-4">

                    {{-- Staff Member --}}
                    <div class="flex items-center gap-3 min-w-48">
                        <div class="w-10 h-10 rounded-full bg-primary-100 text-primary-700
                                    flex items-center justify-center font-bold text-sm flex-shrink-0">
                            {{ $record->staffProfile->initials ?? '?' }}
                        </div>
                        <div>
                            <p class="font-semibold text-gray-900">
                                {{ $record->staffProfile->full_name ?? 'Unknown' }}
                            </p>
                            <p class="text-xs text-gray-500">{{ $record->staffProfile->department ?? '' }}</p>
                        </div>
                    </div>

                    {{-- Document Name & Type --}}
                    <div class="min-w-48">
                        <p class="text-xs text-gray-400 uppercase mb-1">Document</p>
                        <p class="text-sm font-medium text-gray-900">{{ $record->document_name }}</p>
                        <p class="text-xs text-gray-500">{{ $record->document_type_label }}</p>
                    </div>

                    {{-- Issuing Body --}}
                    <div class="min-w-32">
                        <p class="text-xs text-gray-400 uppercase mb-1">Issued By</p>
                        <p class="text-sm text-gray-900">{{ $record->issuing_body ?? '—' }}</p>
                    </div>

                    {{-- Expiry Date --}}
                    <div class="min-w-36">
                        <p class="text-xs text-gray-400 uppercase mb-1">Expiry Date</p>
                        @if($record->expiry_date)
                            <p class="text-sm text-gray-900">{{ $record->expiry_date->format('M d, Y') }}</p>
                            @if($record->computed_status === 'expired')
                                <p class="text-xs text-red-600">
                                    {{ abs($record->days_until_expiry) }} day(s) ago
                                </p>
                            @elseif($record->computed_status === 'expiring_soon')
                                <p class="text-xs text-yellow-600">
                                    in {{ $record->days_until_expiry }} day(s)
                                </p>
                            @endif
                        @else
                            <p class="text-sm text-gray-400 italic">No expiry</p>
                        @endif
                    </div>

                    {{-- Status + Arrow --}}
                    <div class="flex items-center gap-3">
                        <span class="badge {{ $record->status_color }}">
                            {{ $record->status_label }}
                        </span>
                        <i class="fas fa-chevron-right text-gray-400 text-sm"></i>
                    </div>

                </div>
            </div>
        </a>
        @endforeach

        <div class="mt-4">
            {{ $records->links() }}
        </div>

    @else
        <div class="bg-white rounded-lg shadow text-center py-16">
            <i class="fas fa-shield-alt text-gray-300 text-5xl mb-4"></i>
            <h3 class="text-lg font-medium text-gray-900 mb-2">No compliance records found</h3>
            <p class="text-gray-500 mb-4">Add certifications, licenses, or documents to start tracking.</p>
            <a href="{{ route('admin.compliance.create') }}" class="btn btn-primary">
                <i class="fas fa-plus mr-2"></i> Add Document
            </a>
        </div>
    @endif

</div>

@endsection