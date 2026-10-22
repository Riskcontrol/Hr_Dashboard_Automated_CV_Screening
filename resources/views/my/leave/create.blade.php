{{--
    resources/views/my/leave/create.blade.php
    Staff self-service — submit your own leave request (status always starts pending).
--}}
@extends('layouts.app')

@section('content')

<div class="mb-6 flex items-center gap-4">
    <a href="{{ route('my.leave.index') }}" class="text-gray-500 hover:text-gray-700"><i class="fas fa-arrow-left"></i></a>
    <h1 class="text-3xl font-bold text-gray-900">Request Leave</h1>
</div>

@if ($errors->any())
    <div class="alert alert-danger mb-4">
        <ul class="list-disc pl-5">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form method="POST" action="{{ route('my.leave.store') }}" class="bg-white p-6 rounded-lg shadow space-y-5 max-w-2xl">
    @csrf

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Leave Type</label>
        <select name="leave_type" class="form-select w-full" required>
            <option value="annual">Annual Leave</option>
            <option value="sick">Sick Leave</option>
            <option value="casual">Casual Leave</option>
            <option value="maternity">Maternity Leave</option>
            <option value="paternity">Paternity Leave</option>
            <option value="unpaid">Unpaid Leave</option>
        </select>
    </div>

    <div class="grid grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Start Date</label>
            <input type="date" name="start_date" class="form-input w-full" required>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">End Date</label>
            <input type="date" name="end_date" class="form-input w-full" required>
        </div>
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Reason</label>
        <textarea name="reason" rows="3" class="form-textarea w-full" placeholder="Optional"></textarea>
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Who should approve this?</label>
        <select name="approver_type" id="approver_type" class="form-select w-full" required>
            <option value="line_manager">Line Manager</option>
            <option value="hr">HR</option>
        </select>
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Approver Name</label>
        <input type="text" name="approver_name" class="form-input w-full" placeholder="e.g. your line manager's name" required>
    </div>

    <div class="flex justify-end gap-3">
        <a href="{{ route('my.leave.index') }}" class="btn btn-outline">Cancel</a>
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-paper-plane mr-2"></i> Submit Request
        </button>
    </div>
</form>

@endsection