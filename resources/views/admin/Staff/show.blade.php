{{-- 
    resources/views/admin/staff/show.blade.php
    
    Staff Profile — View Page
    Shows the full details of a single employee.
    Organised into sections: Personal, Contact, Employment, ID & Pay, Education.
--}}
@extends('layouts.app')

@section('content')

{{-- Page Header --}}
<div class="mb-6 flex items-center justify-between">
    <div class="flex items-center gap-4">
        {{-- Back button --}}
        <a href="{{ route('admin.staff.index') }}" class="text-gray-500 hover:text-gray-700">
            <i class="fas fa-arrow-left"></i>
        </a>
        <h1 class="text-3xl font-bold text-gray-900">Staff Profile</h1>
    </div>
    <div class="flex gap-3">
        <a href="{{ route('admin.staff.edit', $staff) }}" class="btn btn-primary">
            <i class="fas fa-edit mr-2"></i> Edit Profile
        </a>
        {{-- Delete button — uses a form because HTML links can't send DELETE --}}
        <form method="POST" action="{{ route('admin.staff.destroy', $staff) }}"
              onsubmit="return confirm('Delete {{ $staff->full_name }}? This cannot be undone.')">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger">
                <i class="fas fa-trash mr-2"></i> Delete
            </button>
        </form>
    </div>
</div>

{{-- =====================================================
    PROFILE HEADER CARD
    Shows avatar, name, title, department, status badges
===================================================== --}}
<div class="bg-white rounded-lg shadow mb-6 p-6">
    <div class="flex items-center gap-6">

        {{-- Avatar — photo if uploaded, otherwise initials --}}
        @if($staff->profile_photo)
            <img src="{{ Storage::url($staff->profile_photo) }}"
                 class="w-24 h-24 rounded-full object-cover border-4 border-primary-100">
        @else
            <div class="w-24 h-24 rounded-full bg-primary-100 text-primary-700
                        flex items-center justify-center text-3xl font-bold">
                {{ $staff->initials }}
            </div>
        @endif

        <div class="flex-1">
            <h2 class="text-2xl font-bold text-gray-900">{{ $staff->full_name }}</h2>
            <p class="text-gray-500">
                {{ $staff->job_title ?? 'No title set' }}
                @if($staff->department) · {{ $staff->department }} @endif
            </p>
            <div class="flex items-center gap-3 mt-2">
                {{-- Status badge --}}
                <span class="badge {{ $staff->status === 'active' ? 'badge-success' :
                    ($staff->status === 'terminated' ? 'badge-danger' : 'badge-warning') }}">
                    {{ ucfirst($staff->status) }}
                </span>
                {{-- Employment type badge --}}
                <span class="badge badge-secondary">{{ $staff->employment_type_label }}</span>
                {{-- Employee ID --}}
                <span class="font-mono text-sm text-gray-500">{{ $staff->employee_id }}</span>
            </div>
        </div>

        {{-- Link to original CV application if this person was hired through the system --}}
        @if($staff->application)
        <div class="text-right">
            <p class="text-xs text-gray-400 mb-1">Hired from application</p>
            <a href="{{ route('admin.applications.show', $staff->application) }}"
               class="text-sm text-blue-600 hover:underline">
                <i class="fas fa-file-alt mr-1"></i> View CV Application
            </a>
        </div>
        @endif

    </div>
</div>

{{-- =====================================================
    DETAIL SECTIONS — Two column grid on large screens
===================================================== --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

    {{-- PERSONAL INFORMATION --}}
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
            <h3 class="font-semibold text-gray-900">
                <i class="fas fa-user mr-2 text-primary-600"></i>Personal Information
            </h3>
        </div>
        <div class="p-6 space-y-4">
            @php
            // Build key-value pairs — null values show as "—"
            $personal = [
                'Gender'         => $staff->gender ? ucfirst($staff->gender) : null,
                'Date of Birth'  => $staff->date_of_birth
                    ? $staff->date_of_birth->format('M d, Y') . ' (Age ' . $staff->age . ')'
                    : null,
                'Marital Status' => $staff->marital_status ? ucfirst($staff->marital_status) : null,
                'Nationality'    => $staff->nationality,
            ];
            @endphp
            @foreach($personal as $label => $value)
            <div class="flex justify-between py-2 border-b border-gray-100 last:border-0">
                <span class="text-gray-500 text-sm">{{ $label }}</span>
                <span class="text-gray-900 text-sm font-medium">{{ $value ?? '—' }}</span>
            </div>
            @endforeach
        </div>
    </div>

    {{-- CONTACT DETAILS --}}
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
            <h3 class="font-semibold text-gray-900">
                <i class="fas fa-address-book mr-2 text-primary-600"></i>Contact Details
            </h3>
        </div>
        <div class="p-6 space-y-4">
            @php
            $contact = [
                'Email'   => $staff->email,
                'Phone'   => $staff->phone_number,
                'Address' => $staff->residential_address,
            ];
            @endphp
            @foreach($contact as $label => $value)
            <div class="flex justify-between py-2 border-b border-gray-100 last:border-0">
                <span class="text-gray-500 text-sm">{{ $label }}</span>
                <span class="text-gray-900 text-sm font-medium text-right max-w-xs">{{ $value ?? '—' }}</span>
            </div>
            @endforeach

            {{-- Emergency contact shown separately with its own sub-heading --}}
            @if($staff->emergency_contact_name)
            <div class="pt-2 mt-2 border-t border-gray-200">
                <p class="text-xs text-gray-400 uppercase mb-2">Emergency Contact</p>
                <p class="text-sm font-medium text-gray-900">{{ $staff->emergency_contact_name }}</p>
                <p class="text-sm text-gray-500">
                    {{ $staff->emergency_contact_phone }}
                    @if($staff->emergency_contact_relationship)
                        · {{ $staff->emergency_contact_relationship }}
                    @endif
                </p>
            </div>
            @endif
        </div>
    </div>

    {{-- EMPLOYMENT DETAILS --}}
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
            <h3 class="font-semibold text-gray-900">
                <i class="fas fa-briefcase mr-2 text-primary-600"></i>Employment Details
            </h3>
        </div>
        <div class="p-6 space-y-4">
            @php
            $employment = [
                'Employee ID'  => $staff->employee_id,
                'Job Title'    => $staff->job_title,
                'Department'   => $staff->department,
                'Location'     => $staff->location,
                'Type'         => $staff->employment_type_label,
                'Date of Hire' => $staff->date_of_hire ? $staff->date_of_hire->format('M d, Y') : null,
                'Line Manager' => $staff->line_manager,
                'Dept. Head'   => $staff->department_head,
            ];
            @endphp
            @foreach($employment as $label => $value)
            <div class="flex justify-between py-2 border-b border-gray-100 last:border-0">
                <span class="text-gray-500 text-sm">{{ $label }}</span>
                <span class="text-gray-900 text-sm font-medium">{{ $value ?? '—' }}</span>
            </div>
            @endforeach
        </div>
    </div>

    {{-- IDENTIFICATION & COMPENSATION --}}
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
            <h3 class="font-semibold text-gray-900">
                <i class="fas fa-id-card mr-2 text-primary-600"></i>Identification & Compensation
            </h3>
        </div>
        <div class="p-6 space-y-4">
            @php
            $ident = [
                'National ID'     => $staff->national_id,
                'Tax ID (TIN)'    => $staff->tax_id,
                'Pension Details' => $staff->pension_details,
                'Salary'          => $staff->salary ? '₦' . number_format($staff->salary, 2) : null,
                'Bank'            => $staff->bank_name,
                'Account Number'  => $staff->bank_account_number,
            ];
            @endphp
            @foreach($ident as $label => $value)
            <div class="flex justify-between py-2 border-b border-gray-100 last:border-0">
                <span class="text-gray-500 text-sm">{{ $label }}</span>
                <span class="text-gray-900 text-sm font-medium">{{ $value ?? '—' }}</span>
            </div>
            @endforeach
        </div>
    </div>

    {{-- EDUCATION & WORK HISTORY — spans full width --}}
    <div class="bg-white rounded-lg shadow lg:col-span-2">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
            <h3 class="font-semibold text-gray-900">
                <i class="fas fa-graduation-cap mr-2 text-primary-600"></i>Education & Work History
            </h3>
        </div>
        <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
            @php
            $edu = [
                'Academic Background'      => $staff->academic_background,
                'Certifications'           => $staff->certifications,
                'Professional Memberships' => $staff->professional_memberships,
                'Previous Roles'           => $staff->previous_roles,
                'Promotion History'        => $staff->promotion_history,
            ];
            @endphp
            @foreach($edu as $label => $value)
            <div>
                <p class="text-xs text-gray-400 uppercase mb-1">{{ $label }}</p>
                {{-- whitespace-pre-line preserves line breaks in textarea content --}}
                <p class="text-sm text-gray-900 whitespace-pre-line">{{ $value ?? '—' }}</p>
            </div>
            @endforeach
        </div>
    </div>

</div>

{{-- Last updated timestamp --}}
<div class="mt-6 text-sm text-gray-400 text-right">
    Last updated: {{ $staff->updated_at->format('M d, Y \a\t g:i A') }}
</div>

@endsection