@extends('layouts.app')

@section('content')

<div class="mb-6 flex items-center gap-4">
    <a href="{{ route('admin.pension.show', $pension) }}" class="text-gray-500 hover:text-gray-700">
        <i class="fas fa-arrow-left"></i>
    </a>
    <h1 class="text-3xl font-bold text-gray-900">Edit Pension Record</h1>
</div>

<form method="POST" action="{{ route('admin.pension.update', $pension) }}"
      class="bg-white p-6 rounded-lg shadow max-w-2xl space-y-4">
    @csrf @method('PUT')

    <div class="bg-gray-50 p-3 rounded-lg border mb-2">
        <p class="text-sm text-gray-600">Staff Member: <strong>{{ $pension->staffProfile->full_name }}</strong>
        ({{ $pension->staffProfile->employee_id }})</p>
    </div>

    <div class="grid grid-cols-2 gap-4">
        <div>
            <label class="form-label">Pension Provider</label>
            <input type="text" name="pension_provider" class="form-input"
                   value="{{ old('pension_provider', $pension->pension_provider) }}">
        </div>
        <div>
            <label class="form-label">Pension PIN</label>
            <input type="text" name="pension_pin" class="form-input"
                   value="{{ old('pension_pin', $pension->pension_pin) }}">
        </div>
        <div>
            <label class="form-label">RSA Number</label>
            <input type="text" name="rsa_number" class="form-input"
                   value="{{ old('rsa_number', $pension->rsa_number) }}">
        </div>
        <div>
            <label class="form-label">Enrollment Date</label>
            <input type="date" name="enrollment_date" class="form-input"
                   value="{{ old('enrollment_date', $pension->enrollment_date?->format('Y-m-d')) }}">
        </div>
        <div>
            <label class="form-label">Employee Contribution</label>
            <input type="number" step="0.01" name="employee_contribution" class="form-input"
                   value="{{ old('employee_contribution', $pension->employee_contribution) }}">
        </div>
        <div>
            <label class="form-label">Employer Contribution</label>
            <input type="number" step="0.01" name="employer_contribution" class="form-input"
                   value="{{ old('employer_contribution', $pension->employer_contribution) }}">
        </div>
        <div>
            <label class="form-label">Contribution Type</label>
            <select name="contribution_type" class="form-select" required>
                <option value="percentage" {{ $pension->contribution_type === 'percentage' ? 'selected' : '' }}>Percentage (%)</option>
                <option value="fixed" {{ $pension->contribution_type === 'fixed' ? 'selected' : '' }}>Fixed Amount (₦)</option>
            </select>
        </div>
        <div>
            <label class="form-label">Status</label>
            <select name="status" class="form-select" required>
                <option value="active" {{ $pension->status === 'active' ? 'selected' : '' }}>Active</option>
                <option value="suspended" {{ $pension->status === 'suspended' ? 'selected' : '' }}>Suspended</option>
                <option value="exited" {{ $pension->status === 'exited' ? 'selected' : '' }}>Exited</option>
            </select>
        </div>
    </div>

    <div>
        <label class="form-label">Notes</label>
        <textarea name="notes" rows="3" class="form-textarea w-full">{{ old('notes', $pension->notes) }}</textarea>
    </div>

    <div class="flex justify-end gap-3 pt-4 border-t">
        <a href="{{ route('admin.pension.show', $pension) }}" class="btn btn-outline">Cancel</a>
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-save mr-2"></i> Save Changes
        </button>
    </div>
</form>

@endsection