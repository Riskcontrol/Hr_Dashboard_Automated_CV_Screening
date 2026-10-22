@extends('layouts.app')

@section('content')

<div class="mb-6 flex items-center justify-between">
    <div class="flex items-center gap-4">
        <a href="{{ route('admin.pension.index') }}" class="text-gray-500 hover:text-gray-700">
            <i class="fas fa-arrow-left"></i>
        </a>
        <h1 class="text-3xl font-bold text-gray-900">Pension Record</h1>
    </div>
    <div class="flex gap-3">
        <a href="{{ route('admin.pension.edit', $pension) }}" class="btn btn-primary">
            <i class="fas fa-edit mr-2"></i> Edit
        </a>
        <form method="POST" action="{{ route('admin.pension.destroy', $pension) }}"
              onsubmit="return confirm('Delete this pension record?')">
            @csrf @method('DELETE')
            <button type="submit" class="btn btn-danger">
                <i class="fas fa-trash mr-2"></i> Delete
            </button>
        </form>
    </div>
</div>

<div class="bg-white p-6 rounded-lg shadow max-w-2xl">
    <div class="flex items-center gap-4 mb-6 pb-6 border-b">
        <div class="w-12 h-12 rounded-full bg-primary-100 flex items-center justify-center text-primary-600 font-bold text-lg">
            {{ $pension->staffProfile->initials }}
        </div>
        <div>
            <h2 class="text-xl font-bold text-gray-900">{{ $pension->staffProfile->full_name }}</h2>
            <p class="text-gray-500">{{ $pension->staffProfile->employee_id }} &middot; {{ $pension->staffProfile->job_title }}</p>
        </div>
        <span class="ml-auto badge {{
            $pension->status === 'active' ? 'badge-success' :
            ($pension->status === 'suspended' ? 'badge-warning' : 'badge-secondary')
        }} text-sm px-3 py-1">{{ ucfirst($pension->status) }}</span>
    </div>

    <div class="grid grid-cols-2 gap-6">
        <div><p class="text-sm text-gray-500">Pension Provider</p><p class="font-medium">{{ $pension->pension_provider ?: '—' }}</p></div>
        <div><p class="text-sm text-gray-500">Pension PIN</p><p class="font-medium">{{ $pension->pension_pin ?: '—' }}</p></div>
        <div><p class="text-sm text-gray-500">RSA Number</p><p class="font-medium">{{ $pension->rsa_number ?: '—' }}</p></div>
        <div><p class="text-sm text-gray-500">Enrollment Date</p><p class="font-medium">{{ $pension->enrollment_date?->format('M d, Y') ?: '—' }}</p></div>
        <div>
            <p class="text-sm text-gray-500">Employee Contribution</p>
            <p class="font-medium">{{ $pension->employee_contribution }}{{ $pension->contribution_type === 'percentage' ? '%' : ' (fixed)' }}</p>
        </div>
        <div>
            <p class="text-sm text-gray-500">Employer Contribution</p>
            <p class="font-medium">{{ $pension->employer_contribution }}{{ $pension->contribution_type === 'percentage' ? '%' : ' (fixed)' }}</p>
        </div>
        @if($pension->notes)
        <div class="col-span-2">
            <p class="text-sm text-gray-500">Notes</p>
            <p class="font-medium">{{ $pension->notes }}</p>
        </div>
        @endif
    </div>
</div>

@endsection