{{--
    resources/views/admin/compliance/create.blade.php

    Compliance Tracking — Create Page
    HR adds a certification, license, or document for a staff member
    with an optional expiry date and file upload.
--}}
@extends('layouts.app')

@section('content')

<div class="mb-6 flex items-center gap-4">
    <a href="{{ route('admin.compliance.index') }}" class="text-gray-500 hover:text-gray-700">
        <i class="fas fa-arrow-left"></i>
    </a>
    <div>
        <h1 class="text-3xl font-bold text-gray-900">Add Compliance Document</h1>
        <p class="text-gray-500 mt-1">Track a certification, license, or document for a staff member</p>
    </div>
</div>

{{-- enctype is required for file uploads --}}
<form method="POST" action="{{ route('admin.compliance.store') }}" enctype="multipart/form-data">
    @csrf

    <div class="bg-white rounded-lg shadow mb-6">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
            <h2 class="text-lg font-semibold text-gray-900">
                <i class="fas fa-file-alt mr-2 text-primary-600"></i>Document Details
            </h2>
        </div>
        <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">

            {{-- Staff member --}}
            <div>
                <label class="form-label">Staff Member <span class="text-red-500">*</span></label>
                <select name="staff_profile_id"
                        class="form-select @error('staff_profile_id') border-red-500 @enderror" required>
                    <option value="">Select staff member</option>
                    @foreach($staff as $member)
                        <option value="{{ $member->id }}"
                            {{ old('staff_profile_id') == $member->id ? 'selected' : '' }}>
                            {{ $member->full_name }}
                            @if($member->job_title) — {{ $member->job_title }} @endif
                        </option>
                    @endforeach
                </select>
                @error('staff_profile_id')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Document Type --}}
            <div>
                <label class="form-label">Document Type <span class="text-red-500">*</span></label>
                <select name="document_type"
                        class="form-select @error('document_type') border-red-500 @enderror" required>
                    <option value="">Select type</option>
                    @foreach([
                        'certification'    => 'Certification',
                        'license'          => 'License',
                        'insurance'        => 'Insurance',
                        'contract'         => 'Contract',
                        'id_document'      => 'ID Document',
                        'medical'          => 'Medical Certificate',
                        'background_check' => 'Background Check',
                    ] as $val => $label)
                        <option value="{{ $val }}" {{ old('document_type') == $val ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
                @error('document_type')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Document Name --}}
            <div>
                <label class="form-label">Document Name <span class="text-red-500">*</span></label>
                <input type="text" name="document_name" value="{{ old('document_name') }}"
                       class="form-input @error('document_name') border-red-500 @enderror"
                       placeholder="e.g. ACCA Certificate, Driver's License" required>
                @error('document_name')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Document Number --}}
            <div>
                <label class="form-label">Document Number</label>
                <input type="text" name="document_number" value="{{ old('document_number') }}"
                       class="form-input" placeholder="Reference / serial number">
            </div>

            {{-- Issuing Body --}}
            <div>
                <label class="form-label">Issuing Body</label>
                <input type="text" name="issuing_body" value="{{ old('issuing_body') }}"
                       class="form-input" placeholder="e.g. ICAN, FRSC, Immigration">
            </div>

            {{-- Issue Date --}}
            <div>
                <label class="form-label">Issue Date</label>
                <input type="date" name="issue_date" value="{{ old('issue_date') }}" class="form-input">
            </div>

            {{-- Expiry Date --}}
            <div>
                <label class="form-label">Expiry Date
                    <span class="text-gray-400 text-xs">(leave blank if it never expires)</span>
                </label>
                <input type="date" name="expiry_date" value="{{ old('expiry_date') }}" class="form-input">
                <p class="text-xs text-gray-500 mt-1">
                    The system will automatically flag documents expiring within 30 days.
                </p>
            </div>

            {{-- Status --}}
            <div>
                <label class="form-label">Status <span class="text-red-500">*</span></label>
                <select name="status" class="form-select" required>
                    <option value="valid" {{ old('status', 'valid') == 'valid' ? 'selected' : '' }}>Valid</option>
                    <option value="renewal_in_progress" {{ old('status') == 'renewal_in_progress' ? 'selected' : '' }}>
                        Renewal In Progress
                    </option>
                </select>
                <p class="text-xs text-gray-500 mt-1">
                    "Expiring Soon" and "Expired" are calculated automatically from the expiry date.
                </p>
            </div>

            {{-- File Upload --}}
            <div class="md:col-span-2">
                <label class="form-label">Upload Scanned Copy</label>
                <input type="file" name="document_file" accept=".pdf,.jpg,.jpeg,.png" class="form-input">
                <p class="text-gray-500 text-xs mt-1">PDF, JPG, PNG — max 5MB</p>
            </div>

            {{-- Notes --}}
            <div class="md:col-span-2">
                <label class="form-label">Notes</label>
                <textarea name="notes" rows="3" class="form-input"
                          placeholder="Any additional information about this document">{{ old('notes') }}</textarea>
            </div>

        </div>
    </div>

    <div class="flex justify-end gap-4">
        <a href="{{ route('admin.compliance.index') }}" class="btn btn-outline">Cancel</a>
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-save mr-2"></i> Save Document
        </button>
    </div>

</form>
@endsection