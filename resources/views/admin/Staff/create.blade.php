{{-- 
    resources/views/admin/staff/create.blade.php
    
    Staff Profile — Create / Add New Staff Page
    This form lets HR add a new employee manually.
    It can also be pre-filled from an existing CV application
    by passing ?from_application={id} in the URL.
--}}
@extends('layouts.app')

@section('content')

{{-- Page Header --}}
<div class="mb-6 flex items-center gap-4">
    <a href="{{ route('admin.staff.index') }}" class="text-gray-500 hover:text-gray-700">
        <i class="fas fa-arrow-left"></i>
    </a>
    <div>
        <h1 class="text-3xl font-bold text-gray-900">Add Staff Member</h1>
        <p class="text-gray-500 mt-1">Create a new employee profile</p>
    </div>
</div>

{{-- Info banner shown when pre-filling from an application --}}
@if($application)
<div class="alert alert-info mb-6">
    <i class="fas fa-link mr-2"></i>
    Pre-filled from application by <strong>{{ $application->applicant_name }}</strong>
    for <strong>{{ $application->keywordSet->job_title ?? 'N/A' }}</strong>.
</div>
@endif

{{-- 
    The form posts to admin.staff.store (StaffProfileController@store)
    enctype="multipart/form-data" is required for file uploads (profile photo)
--}}
<form method="POST" action="{{ route('admin.staff.store') }}" enctype="multipart/form-data">
    @csrf {{-- CSRF token — Laravel requires this on all POST forms for security --}}

    {{-- Pass along the application ID if this is being created from an application --}}
    @if($application)
        <input type="hidden" name="application_id" value="{{ $application->id }}">
    @endif

    {{-- ===================================================
        SECTION 1: PERSONAL INFORMATION
    =================================================== --}}
    <div class="bg-white rounded-lg shadow mb-6">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
            <h2 class="text-lg font-semibold text-gray-900">
                <i class="fas fa-user mr-2 text-primary-600"></i>Personal Information
            </h2>
        </div>
        <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">

            {{-- Full Name (required) --}}
            <div>
                <label class="form-label">Full Name <span class="text-red-500">*</span></label>
                {{-- old() restores value if form validation fails and page reloads --}}
                <input type="text" name="full_name"
                       value="{{ old('full_name', $application->applicant_name ?? '') }}"
                       class="form-input @error('full_name') border-red-500 @enderror" required>
                @error('full_name')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Employee ID (required, auto-generated but editable) --}}
            <div>
                <label class="form-label">Employee ID <span class="text-red-500">*</span></label>
                <input type="text" name="employee_id"
                       value="{{ old('employee_id', $nextId) }}"
                       class="form-input @error('employee_id') border-red-500 @enderror" required>
                @error('employee_id')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Login account link — lets this staff member use the staff portal --}}
            <div>
                <label class="form-label">Linked Login Account</label>
                <select name="user_id" class="form-select @error('user_id') border-red-500 @enderror">
                    <option value="">No login account (HR-managed only)</option>
                    @foreach($availableUsers as $user)
                        <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>
                            {{ $user->name }} ({{ $user->email }})
                        </option>
                    @endforeach
                </select>
                <p class="text-gray-500 text-xs mt-1">Link this staff record to a login account so they can view their own Leave and Appraisals.</p>
                @error('user_id')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Gender --}}
            <div>
                <label class="form-label">Gender</label>
                <select name="gender" class="form-select">
                    <option value="">Select</option>
                    <option value="male"   {{ old('gender') == 'male'   ? 'selected' : '' }}>Male</option>
                    <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Female</option>
                    <option value="other"  {{ old('gender') == 'other'  ? 'selected' : '' }}>Other</option>
                </select>
            </div>

            {{-- Date of Birth --}}
            <div>
                <label class="form-label">Date of Birth</label>
                <input type="date" name="date_of_birth" value="{{ old('date_of_birth') }}" class="form-input">
            </div>

            {{-- Marital Status --}}
            <div>
                <label class="form-label">Marital Status</label>
                <select name="marital_status" class="form-select">
                    <option value="">Select</option>
                    @foreach(['single', 'married', 'divorced', 'widowed'] as $ms)
                        <option value="{{ $ms }}" {{ old('marital_status') == $ms ? 'selected' : '' }}>
                            {{ ucfirst($ms) }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Nationality --}}
            <div>
                <label class="form-label">Nationality</label>
                <input type="text" name="nationality" value="{{ old('nationality') }}"
                       class="form-input" placeholder="e.g. Nigerian">
            </div>

            {{-- Profile Photo upload --}}
            <div class="md:col-span-2">
                <label class="form-label">Profile Photo</label>
                <input type="file" name="profile_photo" accept="image/*" class="form-input">
                <p class="text-gray-500 text-xs mt-1">JPG, PNG — max 2MB</p>
            </div>

        </div>
    </div>

    {{-- ===================================================
        SECTION 2: CONTACT DETAILS
    =================================================== --}}
    <div class="bg-white rounded-lg shadow mb-6">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
            <h2 class="text-lg font-semibold text-gray-900">
                <i class="fas fa-address-book mr-2 text-primary-600"></i>Contact Details
            </h2>
        </div>
        <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">

            {{-- Email (required, must be unique) --}}
            <div>
                <label class="form-label">Email Address <span class="text-red-500">*</span></label>
                <input type="email" name="email"
                       value="{{ old('email', $application->applicant_email ?? '') }}"
                       class="form-input @error('email') border-red-500 @enderror" required>
                @error('email')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Phone --}}
            <div>
                <label class="form-label">Phone Number</label>
                <input type="tel" name="phone_number"
                       value="{{ old('phone_number', $application->phone ?? '') }}"
                       class="form-input" placeholder="+234 XXX XXX XXXX">
            </div>

            {{-- Residential Address --}}
            <div class="md:col-span-2">
                <label class="form-label">Residential Address</label>
                <textarea name="residential_address" rows="2" class="form-input">{{ old('residential_address') }}</textarea>
            </div>

            {{-- Emergency Contact --}}
            <div>
                <label class="form-label">Emergency Contact Name</label>
                <input type="text" name="emergency_contact_name"
                       value="{{ old('emergency_contact_name') }}" class="form-input">
            </div>
            <div>
                <label class="form-label">Emergency Contact Phone</label>
                <input type="tel" name="emergency_contact_phone"
                       value="{{ old('emergency_contact_phone') }}" class="form-input">
            </div>
            <div>
                <label class="form-label">Relationship to Employee</label>
                <input type="text" name="emergency_contact_relationship"
                       value="{{ old('emergency_contact_relationship') }}"
                       class="form-input" placeholder="e.g. Spouse, Parent, Sibling">
            </div>

        </div>
    </div>

    {{-- ===================================================
        SECTION 3: EMPLOYMENT DETAILS
    =================================================== --}}
    <div class="bg-white rounded-lg shadow mb-6">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
            <h2 class="text-lg font-semibold text-gray-900">
                <i class="fas fa-briefcase mr-2 text-primary-600"></i>Employment Details
            </h2>
        </div>
        <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">

            <div>
                <label class="form-label">Job Title</label>
                <input type="text" name="job_title"
                       value="{{ old('job_title', $application->keywordSet->job_title ?? '') }}"
                       class="form-input">
            </div>
            <div>
                <label class="form-label">Department</label>
                <input type="text" name="department" value="{{ old('department') }}"
                       class="form-input" placeholder="e.g. Finance, IT, Operations">
            </div>
            <div>
                <label class="form-label">Location / Branch</label>
                <input type="text" name="location" value="{{ old('location') }}" class="form-input">
            </div>
            <div>
                <label class="form-label">Employment Type</label>
                <select name="employment_type" class="form-select">
                    <option value="full_time" {{ old('employment_type', 'full_time') == 'full_time' ? 'selected' : '' }}>Full Time</option>
                    <option value="part_time" {{ old('employment_type') == 'part_time' ? 'selected' : '' }}>Part Time</option>
                    <option value="contract"  {{ old('employment_type') == 'contract'  ? 'selected' : '' }}>Contract</option>
                    <option value="intern"    {{ old('employment_type') == 'intern'    ? 'selected' : '' }}>Intern</option>
                </select>
            </div>
            <div>
                <label class="form-label">Date of Hire</label>
                <input type="date" name="date_of_hire"
                       value="{{ old('date_of_hire', now()->toDateString()) }}" class="form-input">
            </div>
            <div>
                <label class="form-label">Employment Status</label>
                <select name="status" class="form-select">
                    <option value="active"     {{ old('status', 'active') == 'active'     ? 'selected' : '' }}>Active</option>
                    <option value="inactive"   {{ old('status') == 'inactive'   ? 'selected' : '' }}>Inactive</option>
                    <option value="suspended"  {{ old('status') == 'suspended'  ? 'selected' : '' }}>Suspended</option>
                    <option value="terminated" {{ old('status') == 'terminated' ? 'selected' : '' }}>Terminated</option>
                </select>
            </div>
            <div>
                <label class="form-label">Line Manager</label>
                <input type="text" name="line_manager" value="{{ old('line_manager') }}" class="form-input">
            </div>
            <div>
                <label class="form-label">Department Head</label>
                <input type="text" name="department_head" value="{{ old('department_head') }}" class="form-input">
            </div>

        </div>
    </div>

    {{-- ===================================================
        SECTION 4: IDENTIFICATION & COMPENSATION
    =================================================== --}}
    <div class="bg-white rounded-lg shadow mb-6">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
            <h2 class="text-lg font-semibold text-gray-900">
                <i class="fas fa-id-card mr-2 text-primary-600"></i>Identification & Compensation
            </h2>
        </div>
        <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">

            <div>
                <label class="form-label">National ID / Passport No.</label>
                <input type="text" name="national_id" value="{{ old('national_id') }}" class="form-input">
            </div>
            <div>
                <label class="form-label">Tax ID (TIN)</label>
                <input type="text" name="tax_id" value="{{ old('tax_id') }}" class="form-input">
            </div>
            <div>
                <label class="form-label">Pension Details</label>
                <input type="text" name="pension_details" value="{{ old('pension_details') }}"
                       class="form-input" placeholder="PFA name / PEN number">
            </div>
            <div>
                <label class="form-label">Salary (₦)</label>
                <input type="number" name="salary" value="{{ old('salary') }}"
                       class="form-input" placeholder="0.00" step="0.01" min="0">
            </div>
            <div>
                <label class="form-label">Bank Name</label>
                <input type="text" name="bank_name" value="{{ old('bank_name') }}" class="form-input">
            </div>
            <div>
                <label class="form-label">Bank Account Number</label>
                <input type="text" name="bank_account_number" value="{{ old('bank_account_number') }}" class="form-input">
            </div>

        </div>
    </div>

    {{-- ===================================================
        SECTION 5: EDUCATION & WORK HISTORY
    =================================================== --}}
    <div class="bg-white rounded-lg shadow mb-6">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
            <h2 class="text-lg font-semibold text-gray-900">
                <i class="fas fa-graduation-cap mr-2 text-primary-600"></i>Education & Work History
            </h2>
        </div>
        <div class="p-6 grid grid-cols-1 gap-6">

            <div>
                <label class="form-label">Academic Background</label>
                <textarea name="academic_background" rows="3" class="form-input"
                          placeholder="e.g. B.Sc. Accounting, University of Lagos, 2018">{{ old('academic_background') }}</textarea>
            </div>
            <div>
                <label class="form-label">Certifications</label>
                <textarea name="certifications" rows="2" class="form-input"
                          placeholder="e.g. ACCA, PMP, CIPM">{{ old('certifications') }}</textarea>
            </div>
            <div>
                <label class="form-label">Professional Memberships</label>
                <textarea name="professional_memberships" rows="2" class="form-input"
                          placeholder="e.g. ICAN, NIM">{{ old('professional_memberships') }}</textarea>
            </div>
            <div>
                <label class="form-label">Previous Roles</label>
                <textarea name="previous_roles" rows="3" class="form-input"
                          placeholder="List previous job roles and companies">{{ old('previous_roles') }}</textarea>
            </div>
            <div>
                <label class="form-label">Promotion History</label>
                <textarea name="promotion_history" rows="2" class="form-input"
                          placeholder="Record any promotions within the company">{{ old('promotion_history') }}</textarea>
            </div>

        </div>
    </div>

    {{-- Form Action Buttons --}}
    <div class="flex justify-end gap-4 mt-6">
        <a href="{{ route('admin.staff.index') }}" class="btn btn-outline">Cancel</a>
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-save mr-2"></i> Save Staff Profile
        </button>
    </div>

</form>
@endsection