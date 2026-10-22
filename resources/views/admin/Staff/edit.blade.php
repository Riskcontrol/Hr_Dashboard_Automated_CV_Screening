{{-- 
    resources/views/admin/staff/edit.blade.php
    
    Staff Profile — Edit Page
    Same form as create.blade.php but pre-filled with the 
    existing staff member's data using old() and $staff->field.
    Submits via PUT to admin.staff.update.
--}}
@extends('layouts.app')

@section('content')

{{-- Page Header --}}
<div class="mb-6 flex items-center gap-4">
    <a href="{{ route('admin.staff.show', $staff) }}" class="text-gray-500 hover:text-gray-700">
        <i class="fas fa-arrow-left"></i>
    </a>
    <div>
        <h1 class="text-3xl font-bold text-gray-900">Edit — {{ $staff->full_name }}</h1>
        <p class="text-gray-500 mt-1">{{ $staff->employee_id }}</p>
    </div>
</div>

{{-- 
    PUT method — HTML forms only support GET and POST.
    @method('PUT') adds a hidden _method field that Laravel reads
    to treat this as a PUT (update) request.
--}}
<form method="POST" action="{{ route('admin.staff.update', $staff) }}" enctype="multipart/form-data">
    @csrf
    @method('PUT')

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

            <div>
                <label class="form-label">Full Name <span class="text-red-500">*</span></label>
                {{-- old() checks for a previously submitted value (if validation failed), 
                     otherwise falls back to the current $staff value --}}
                <input type="text" name="full_name"
                       value="{{ old('full_name', $staff->full_name) }}"
                       class="form-input @error('full_name') border-red-500 @enderror" required>
                @error('full_name')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="form-label">Employee ID <span class="text-red-500">*</span></label>
                <input type="text" name="employee_id"
                       value="{{ old('employee_id', $staff->employee_id) }}"
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
                        <option value="{{ $user->id }}" {{ old('user_id', $staff->user_id) == $user->id ? 'selected' : '' }}>
                            {{ $user->name }} ({{ $user->email }})
                        </option>
                    @endforeach
                </select>
                <p class="text-gray-500 text-xs mt-1">Link this staff record to a login account so they can view their own Leave and Appraisals.</p>
                @error('user_id')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="form-label">Gender</label>
                <select name="gender" class="form-select">
                    <option value="">Select</option>
                    @foreach(['male', 'female', 'other'] as $g)
                        <option value="{{ $g }}" {{ old('gender', $staff->gender) == $g ? 'selected' : '' }}>
                            {{ ucfirst($g) }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="form-label">Date of Birth</label>
                {{-- ?-> safely accesses the date even if null, avoiding errors --}}
                <input type="date" name="date_of_birth"
                       value="{{ old('date_of_birth', $staff->date_of_birth?->toDateString()) }}"
                       class="form-input">
            </div>

            <div>
                <label class="form-label">Marital Status</label>
                <select name="marital_status" class="form-select">
                    <option value="">Select</option>
                    @foreach(['single', 'married', 'divorced', 'widowed'] as $ms)
                        <option value="{{ $ms }}" {{ old('marital_status', $staff->marital_status) == $ms ? 'selected' : '' }}>
                            {{ ucfirst($ms) }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="form-label">Nationality</label>
                <input type="text" name="nationality"
                       value="{{ old('nationality', $staff->nationality) }}" class="form-input">
            </div>

            {{-- Profile Photo — shows current photo with option to replace --}}
            <div class="md:col-span-2">
                <label class="form-label">Profile Photo</label>
                @if($staff->profile_photo)
                    <div class="flex items-center gap-4 mb-2">
                        <img src="{{ Storage::url($staff->profile_photo) }}"
                             class="w-16 h-16 rounded-full object-cover border">
                        <span class="text-sm text-gray-500">Current photo. Upload a new one to replace it.</span>
                    </div>
                @endif
                <input type="file" name="profile_photo" accept="image/*" class="form-input">
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

            <div>
                <label class="form-label">Email <span class="text-red-500">*</span></label>
                <input type="email" name="email"
                       value="{{ old('email', $staff->email) }}"
                       class="form-input @error('email') border-red-500 @enderror" required>
                @error('email')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="form-label">Phone Number</label>
                <input type="tel" name="phone_number"
                       value="{{ old('phone_number', $staff->phone_number) }}" class="form-input">
            </div>

            <div class="md:col-span-2">
                <label class="form-label">Residential Address</label>
                <textarea name="residential_address" rows="2" class="form-input">{{ old('residential_address', $staff->residential_address) }}</textarea>
            </div>

            <div>
                <label class="form-label">Emergency Contact Name</label>
                <input type="text" name="emergency_contact_name"
                       value="{{ old('emergency_contact_name', $staff->emergency_contact_name) }}" class="form-input">
            </div>
            <div>
                <label class="form-label">Emergency Contact Phone</label>
                <input type="tel" name="emergency_contact_phone"
                       value="{{ old('emergency_contact_phone', $staff->emergency_contact_phone) }}" class="form-input">
            </div>
            <div>
                <label class="form-label">Relationship</label>
                <input type="text" name="emergency_contact_relationship"
                       value="{{ old('emergency_contact_relationship', $staff->emergency_contact_relationship) }}" class="form-input">
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
                       value="{{ old('job_title', $staff->job_title) }}" class="form-input">
            </div>
            <div>
                <label class="form-label">Department</label>
                <input type="text" name="department"
                       value="{{ old('department', $staff->department) }}" class="form-input">
            </div>
            <div>
                <label class="form-label">Location</label>
                <input type="text" name="location"
                       value="{{ old('location', $staff->location) }}" class="form-input">
            </div>
            <div>
                <label class="form-label">Employment Type</label>
                <select name="employment_type" class="form-select">
                    @foreach(['full_time' => 'Full Time', 'part_time' => 'Part Time', 'contract' => 'Contract', 'intern' => 'Intern'] as $val => $label)
                        <option value="{{ $val }}" {{ old('employment_type', $staff->employment_type) == $val ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="form-label">Date of Hire</label>
                <input type="date" name="date_of_hire"
                       value="{{ old('date_of_hire', $staff->date_of_hire?->toDateString()) }}" class="form-input">
            </div>
            <div>
                <label class="form-label">Status</label>
                <select name="status" class="form-select">
                    @foreach(['active' => 'Active', 'inactive' => 'Inactive', 'suspended' => 'Suspended', 'terminated' => 'Terminated'] as $val => $label)
                        <option value="{{ $val }}" {{ old('status', $staff->status) == $val ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="form-label">Line Manager</label>
                <input type="text" name="line_manager"
                       value="{{ old('line_manager', $staff->line_manager) }}" class="form-input">
            </div>
            <div>
                <label class="form-label">Department Head</label>
                <input type="text" name="department_head"
                       value="{{ old('department_head', $staff->department_head) }}" class="form-input">
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
                <label class="form-label">National ID / Passport</label>
                <input type="text" name="national_id" value="{{ old('national_id', $staff->national_id) }}" class="form-input">
            </div>
            <div>
                <label class="form-label">Tax ID (TIN)</label>
                <input type="text" name="tax_id" value="{{ old('tax_id', $staff->tax_id) }}" class="form-input">
            </div>
            <div>
                <label class="form-label">Pension Details</label>
                <input type="text" name="pension_details" value="{{ old('pension_details', $staff->pension_details) }}" class="form-input">
            </div>
            <div>
                <label class="form-label">Salary (₦)</label>
                <input type="number" name="salary" value="{{ old('salary', $staff->salary) }}"
                       class="form-input" step="0.01" min="0">
            </div>
            <div>
                <label class="form-label">Bank Name</label>
                <input type="text" name="bank_name" value="{{ old('bank_name', $staff->bank_name) }}" class="form-input">
            </div>
            <div>
                <label class="form-label">Bank Account Number</label>
                <input type="text" name="bank_account_number" value="{{ old('bank_account_number', $staff->bank_account_number) }}" class="form-input">
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
                <textarea name="academic_background" rows="3" class="form-input">{{ old('academic_background', $staff->academic_background) }}</textarea>
            </div>
            <div>
                <label class="form-label">Certifications</label>
                <textarea name="certifications" rows="2" class="form-input">{{ old('certifications', $staff->certifications) }}</textarea>
            </div>
            <div>
                <label class="form-label">Professional Memberships</label>
                <textarea name="professional_memberships" rows="2" class="form-input">{{ old('professional_memberships', $staff->professional_memberships) }}</textarea>
            </div>
            <div>
                <label class="form-label">Previous Roles</label>
                <textarea name="previous_roles" rows="3" class="form-input">{{ old('previous_roles', $staff->previous_roles) }}</textarea>
            </div>
            <div>
                <label class="form-label">Promotion History</label>
                <textarea name="promotion_history" rows="2" class="form-input">{{ old('promotion_history', $staff->promotion_history) }}</textarea>
            </div>

        </div>
    </div>

    {{-- Form Action Buttons --}}
    <div class="flex justify-end gap-4 mt-6">
        <a href="{{ route('admin.staff.show', $staff) }}" class="btn btn-outline">Cancel</a>
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-save mr-2"></i> Update Profile
        </button>
    </div>

</form>
@endsection