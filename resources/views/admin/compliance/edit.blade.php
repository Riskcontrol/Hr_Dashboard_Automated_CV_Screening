{{--
    resources/views/admin/compliance/edit.blade.php

    Compliance Tracking — Edit Page
    Same as create but pre-filled. Shows current file if one exists.
--}}
@extends('layouts.app')

@section('content')

<div class="mb-6 flex items-center gap-4">
    <a href="{{ route('admin.compliance.show', $compliance) }}" class="text-gray-500 hover:text-gray-700">
        <i class="fas fa-arrow-left"></i>
    </a>
    <div>
        <h1 class="text-3xl font-bold text-gray-900">Edit Compliance Document</h1>
        <p class="text-gray-500 mt-1">{{ $compliance->document_name }} — {{ $compliance->staffProfile->full_name }}</p>
    </div>
</div>

<form method="POST" action="{{ route('admin.compliance.update', $compliance) }}" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    <div class="bg-white rounded-lg shadow mb-6">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
            <h2 class="text-lg font-semibold text-gray-900">
                <i class="fas fa-file-alt mr-2 text-primary-600"></i>Document Details
            </h2>
        </div>
        <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">

            <div>
                <label class="form-label">Staff Member <span class="text-red-500">*</span></label>
                <select name="staff_profile_id" class="form-select" required>
                    @foreach($staff as $member)
                        <option value="{{ $member->id }}"
                            {{ old('staff_profile_id', $compliance->staff_profile_id) == $member->id ? 'selected' : '' }}>
                            {{ $member->full_name }}
                            @if($member->job_title) — {{ $member->job_title }} @endif
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="form-label">Document Type <span class="text-red-500">*</span></label>
                <select name="document_type" class="form-select" required>
                    @foreach([
                        'certification'    => 'Certification',
                        'license'          => 'License',
                        'insurance'        => 'Insurance',
                        'contract'         => 'Contract',
                        'id_document'      => 'ID Document',
                        'medical'          => 'Medical Certificate',
                        'background_check' => 'Background Check',
                    ] as $val => $label)
                        <option value="{{ $val }}"
                            {{ old('document_type', $compliance->document_type) == $val ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="form-label">Document Name <span class="text-red-500">*</span></label>
                <input type="text" name="document_name"
                       value="{{ old('document_name', $compliance->document_name) }}"
                       class="form-input" required>
            </div>

            <div>
                <label class="form-label">Document Number</label>
                <input type="text" name="document_number"
                       value="{{ old('document_number', $compliance->document_number) }}" class="form-input">
            </div>

            <div>
                <label class="form-label">Issuing Body</label>
                <input type="text" name="issuing_body"
                       value="{{ old('issuing_body', $compliance->issuing_body) }}" class="form-input">
            </div>

            <div>
                <label class="form-label">Issue Date</label>
                <input type="date" name="issue_date"
                       value="{{ old('issue_date', $compliance->issue_date?->toDateString()) }}" class="form-input">
            </div>

            <div>
                <label class="form-label">Expiry Date
                    <span class="text-gray-400 text-xs">(leave blank if it never expires)</span>
                </label>
                <input type="date" name="expiry_date"
                       value="{{ old('expiry_date', $compliance->expiry_date?->toDateString()) }}" class="form-input">
            </div>

            <div>
                <label class="form-label">Status <span class="text-red-500">*</span></label>
                <select name="status" class="form-select" required>
                    <option value="valid" {{ old('status', $compliance->status) == 'valid' ? 'selected' : '' }}>Valid</option>
                    <option value="renewal_in_progress" {{ old('status', $compliance->status) == 'renewal_in_progress' ? 'selected' : '' }}>
                        Renewal In Progress
                    </option>
                </select>
            </div>

            {{-- File upload — show current file if exists --}}
            <div class="md:col-span-2">
                <label class="form-label">Document File</label>
                @if($compliance->document_file)
                    <div class="flex items-center gap-3 mb-2">
                        <a href="{{ Storage::url($compliance->document_file) }}" target="_blank"
                           class="text-blue-600 hover:underline text-sm">
                            <i class="fas fa-file mr-1"></i> View current file
                        </a>
                        <span class="text-xs text-gray-500">Upload a new file below to replace it.</span>
                    </div>
                @endif
                <input type="file" name="document_file" accept=".pdf,.jpg,.jpeg,.png" class="form-input">
                <p class="text-gray-500 text-xs mt-1">PDF, JPG, PNG — max 5MB</p>
            </div>

            <div class="md:col-span-2">
                <label class="form-label">Notes</label>
                <textarea name="notes" rows="3" class="form-input">{{ old('notes', $compliance->notes) }}</textarea>
            </div>

        </div>
    </div>

    <div class="flex justify-end gap-4">
        <a href="{{ route('admin.compliance.show', $compliance) }}" class="btn btn-outline">Cancel</a>
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-save mr-2"></i> Update Document
        </button>
    </div>

</form>
@endsection