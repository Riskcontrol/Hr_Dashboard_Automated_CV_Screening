{{-- resources/views/admin/kpis/create.blade.php --}}
@extends('layouts.app')
@section('content')

<div class="mb-6 flex items-center gap-4">
    <a href="{{ route('admin.kpis.index') }}" class="text-gray-500 hover:text-gray-700"><i class="fas fa-arrow-left"></i></a>
    <div>
        <h1 class="text-3xl font-bold text-gray-900">Add New KPI</h1>
        <p class="text-gray-500 mt-1">Set a performance target for a staff member</p>
    </div>
</div>

<form method="POST" action="{{ route('admin.kpis.store') }}">
    @csrf

    <div class="bg-white rounded-lg shadow mb-6">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
            <h2 class="text-lg font-semibold text-gray-900"><i class="fas fa-chart-line mr-2 text-primary-600"></i>KPI Details</h2>
        </div>
        <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="form-label">Staff Member <span class="text-red-500">*</span></label>
                <select name="staff_profile_id" class="form-select @error('staff_profile_id') border-red-500 @enderror" required>
                    <option value="">Select staff member</option>
                    @foreach($staff as $m)
                        <option value="{{ $m->id }}" {{ old('staff_profile_id')==$m->id?'selected':'' }}>{{ $m->full_name }}@if($m->job_title) — {{ $m->job_title }}@endif</option>
                    @endforeach
                </select>
                @error('staff_profile_id')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="form-label">KPI Title <span class="text-red-500">*</span></label>
                <input type="text" name="title" value="{{ old('title') }}" class="form-input @error('title') border-red-500 @enderror" placeholder="e.g. Monthly Sales Target" required>
                @error('title')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="form-label">Category</label>
                <input type="text" name="category" value="{{ old('category') }}" class="form-input" placeholder="e.g. Sales, Finance, Operations">
            </div>
            <div>
                <label class="form-label">Department</label>
                <input type="text" name="department" value="{{ old('department') }}" class="form-input">
            </div>
            <div class="md:col-span-2">
                <label class="form-label">Description</label>
                <textarea name="description" rows="2" class="form-input" placeholder="What does this KPI measure?">{{ old('description') }}</textarea>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow mb-6">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
            <h2 class="text-lg font-semibold text-gray-900"><i class="fas fa-calendar mr-2 text-primary-600"></i>Period & Values</h2>
        </div>
        <div class="p-6 grid grid-cols-1 md:grid-cols-3 gap-6">
            <div>
                <label class="form-label">Period Type <span class="text-red-500">*</span></label>
                <select name="period_type" class="form-select" required>
                    <option value="monthly" {{ old('period_type')=='monthly'?'selected':'' }}>Monthly</option>
                    <option value="quarterly" {{ old('period_type')=='quarterly'?'selected':'' }}>Quarterly</option>
                    <option value="annually" {{ old('period_type')=='annually'?'selected':'' }}>Annual</option>
                </select>
            </div>
            <div>
                <label class="form-label">Period Start <span class="text-red-500">*</span></label>
                <input type="date" name="period_start" value="{{ old('period_start') }}" class="form-input" required>
            </div>
            <div>
                <label class="form-label">Period End <span class="text-red-500">*</span></label>
                <input type="date" name="period_end" value="{{ old('period_end') }}" class="form-input" required>
            </div>
            <div>
                <label class="form-label">Target Value <span class="text-red-500">*</span></label>
                <input type="number" name="target_value" value="{{ old('target_value') }}" class="form-input" step="0.01" min="0" required>
            </div>
            <div>
                <label class="form-label">Actual Value <span class="text-gray-400 text-xs">(optional)</span></label>
                <input type="number" name="actual_value" value="{{ old('actual_value') }}" class="form-input" step="0.01" min="0">
            </div>
            <div>
                <label class="form-label">Unit</label>
                <input type="text" name="unit" value="{{ old('unit') }}" class="form-input" placeholder="e.g. units, %, NGN">
            </div>
            <div>
                <label class="form-label">Status <span class="text-red-500">*</span></label>
                <select name="status" class="form-select" required>
                    <option value="active" {{ old('status','active')=='active'?'selected':'' }}>Active</option>
                    <option value="completed" {{ old('status')=='completed'?'selected':'' }}>Completed</option>
                    <option value="cancelled" {{ old('status')=='cancelled'?'selected':'' }}>Cancelled</option>
                </select>
            </div>
            <div>
                <label class="form-label">Reviewed By</label>
                <input type="text" name="reviewed_by" value="{{ old('reviewed_by') }}" class="form-input">
            </div>
            <div class="md:col-span-3">
                <label class="form-label">Notes</label>
                <textarea name="notes" rows="2" class="form-input">{{ old('notes') }}</textarea>
            </div>
        </div>
    </div>

    <div class="flex justify-end gap-4">
        <a href="{{ route('admin.kpis.index') }}" class="btn btn-outline">Cancel</a>
        <button type="submit" class="btn btn-primary"><i class="fas fa-save mr-2"></i> Save KPI</button>
    </div>
</form>
@endsection