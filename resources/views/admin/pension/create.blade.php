@extends('layouts.app')

@section('content')

<div class="mb-6 flex items-center gap-4">
    <a href="{{ route('admin.pension.index') }}" class="text-gray-500 hover:text-gray-700">
        <i class="fas fa-arrow-left"></i>
    </a>
    <h1 class="text-3xl font-bold text-gray-900">Add Pension Record</h1>
</div>

<form method="POST" action="{{ route('admin.pension.store') }}"
      class="bg-white p-6 rounded-lg shadow max-w-2xl space-y-4">
    @csrf

    <div>
        <label class="form-label">Staff Member</label>
        <select name="staff_profile_id" class="form-select" required>
            <option value="">Select staff member</option>
            @foreach($staffWithoutPension as $s)
                <option value="{{ $s->id }}">{{ $s->full_name }} ({{ $s->employee_id }})</option>
            @endforeach
        </select>
    </div>

    <div class="grid grid-cols-2 gap-4">
        <div>
            <label class="form-label">Pension Provider</label>
            <input type="text" name="pension_provider" class="form-input" placeholder="e.g. ARM Pensions">
        </div>
        <div>
            <label class="form-label">Pension PIN</label>
            <input type="text" name="pension_pin" class="form-input">
        </div>
        <div>
            <label class="form-label">RSA Number</label>
            <input type="text" name="rsa_number" class="form-input">
        </div>
        <div>
            <label class="form-label">Enrollment Date</label>
            <input type="date" name="enrollment_date" class="form-input">
        </div>
        <div>
            <label class="form-label">Employee Contribution</label>
            <input type="number" step="0.01" name="employee_contribution" class="form-input" placeholder="8">
        </div>
        <div>
            <label class="form-label">Employer Contribution</label>
            <input type="number" step="0.01" name="employer_contribution" class="form-input" placeholder="10">
        </div>
        <div>
            <label class="form-label">Contribution Type</label>
            <select name="contribution_type" class="form-select" required>
                <option value="percentage">Percentage (%)</option>
                <option value="fixed">Fixed Amount (₦)</option>
            </select>
        </div>
        <div>
            <label class="form-label">Status</label>
            <select name="status" class="form-select" required>
                <option value="active">Active</option>
                <option value="suspended">Suspended</option>
                <option value="exited">Exited</option>
            </select>
        </div>
    </div>

    <div>
        <label class="form-label">Notes</label>
        <textarea name="notes" rows="3" class="form-textarea w-full"></textarea>
    </div>

    <div class="flex justify-end gap-3 pt-4 border-t">
        <a href="{{ route('admin.pension.index') }}" class="btn btn-outline">Cancel</a>
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-save mr-2"></i> Save Record
        </button>
    </div>
</form>

@endsection