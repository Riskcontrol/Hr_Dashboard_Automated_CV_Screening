@extends('layouts.app')

@section('content')

{{-- Header --}}
<div class="mb-6 flex justify-between items-center">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">
            Good {{ now()->hour < 12 ? 'Morning' : (now()->hour < 17 ? 'Afternoon' : 'Evening') }},
            {{ auth()->user()->name }} 👋
        </h1>
        <p class="text-gray-500 text-sm mt-1">
            {{ now()->format('l, F j, Y') }} &mdash; Here's what's happening today
        </p>
    </div>
    <div class="flex gap-3">
        <a href="{{ route('admin.staff.create') }}" class="btn btn-outline text-sm">
            <i class="fas fa-user-plus mr-2"></i> Add Staff
        </a>
        <a href="{{ route('admin.keyword-sets.create') }}" class="btn btn-primary text-sm">
            <i class="fas fa-plus mr-2"></i> New Position
        </a>
    </div>
</div>

{{-- ROW 1: HR Metrics --}}
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">

    <a href="{{ route('admin.staff.index') }}"
       class="bg-white rounded-lg shadow p-5 flex items-center gap-4 hover:shadow-md transition">
        <div class="p-3 rounded-lg bg-blue-100 text-blue-600 flex-shrink-0">
            <i class="fas fa-users text-xl"></i>
        </div>
        <div>
            <p class="text-2xl font-bold text-gray-900">{{ $hrStats['active_staff'] }}</p>
            <p class="text-sm text-gray-500">Active Staff</p>
        </div>
    </a>

    <a href="{{ route('admin.leave.index') }}?status=pending"
       class="bg-white rounded-lg shadow p-5 flex items-center gap-4 hover:shadow-md transition">
        <div class="p-3 rounded-lg bg-yellow-100 text-yellow-600 flex-shrink-0">
            <i class="fas fa-calendar-alt text-xl"></i>
        </div>
        <div>
            <p class="text-2xl font-bold text-gray-900">{{ $hrStats['pending_leave'] }}</p>
            <p class="text-sm text-gray-500">Pending Leave</p>
        </div>
    </a>

    <a href="{{ route('admin.appraisals.index') }}"
       class="bg-white rounded-lg shadow p-5 flex items-center gap-4 hover:shadow-md transition
              {{ $hrStats['overdue_appraisals'] > 0 ? 'border-l-4 border-red-400' : '' }}">
        <div class="p-3 rounded-lg bg-purple-100 text-purple-600 flex-shrink-0">
            <i class="fas fa-clipboard-list text-xl"></i>
        </div>
        <div>
            <p class="text-2xl font-bold text-gray-900">{{ $hrStats['overdue_appraisals'] }}</p>
            <p class="text-sm text-gray-500">Overdue Appraisals</p>
        </div>
    </a>

    <a href="{{ route('admin.compliance.index') }}"
       class="bg-white rounded-lg shadow p-5 flex items-center gap-4 hover:shadow-md transition
              {{ $hrStats['expiring_compliance'] > 0 ? 'border-l-4 border-orange-400' : '' }}">
        <div class="p-3 rounded-lg bg-orange-100 text-orange-600 flex-shrink-0">
            <i class="fas fa-exclamation-triangle text-xl"></i>
        </div>
        <div>
            <p class="text-2xl font-bold text-gray-900">{{ $hrStats['expiring_compliance'] }}</p>
            <p class="text-sm text-gray-500">Expiring Documents</p>
        </div>
    </a>

</div>

{{-- ROW 2: CV Screening Metrics --}}
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">

    <a href="{{ route('admin.applications.index') }}"
       class="bg-white rounded-lg shadow p-5 flex items-center gap-4 hover:shadow-md transition">
        <div class="p-3 rounded-lg bg-indigo-100 text-indigo-600 flex-shrink-0">
            <i class="fas fa-file-alt text-xl"></i>
        </div>
        <div>
            <p class="text-2xl font-bold text-gray-900">{{ $stats['total_applications'] }}</p>
            <p class="text-sm text-gray-500">Total Applications</p>
        </div>
    </a>

    <a href="{{ route('admin.applications.index') }}"
       class="bg-white rounded-lg shadow p-5 flex items-center gap-4 hover:shadow-md transition">
        <div class="p-3 rounded-lg bg-green-100 text-green-600 flex-shrink-0">
            <i class="fas fa-check-circle text-xl"></i>
        </div>
        <div>
            <p class="text-2xl font-bold text-gray-900">{{ $stats['qualified_applications'] }}</p>
            <p class="text-sm text-gray-500">Qualified</p>
        </div>
    </a>

    <a href="{{ route('admin.applications.index') }}"
       class="bg-white rounded-lg shadow p-5 flex items-center gap-4 hover:shadow-md transition">
        <div class="p-3 rounded-lg bg-yellow-100 text-yellow-600 flex-shrink-0">
            <i class="fas fa-clock text-xl"></i>
        </div>
        <div>
            <p class="text-2xl font-bold text-gray-900">{{ $stats['pending_applications'] }}</p>
            <p class="text-sm text-gray-500">Pending Review</p>
        </div>
    </a>

    <div class="bg-white rounded-lg shadow p-5 flex items-center gap-4">
        <div class="p-3 rounded-lg bg-teal-100 text-teal-600 flex-shrink-0">
            <i class="fas fa-chart-line text-xl"></i>
        </div>
        <div>
            <p class="text-2xl font-bold text-gray-900">{{ $stats['qualification_rate'] }}%</p>
            <p class="text-sm text-gray-500">Qualification Rate</p>
        </div>
    </div>

</div>

{{-- ROW 3: Birthdays + Quick Actions --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">

    {{-- Upcoming Birthdays --}}
    <div class="lg:col-span-2 bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b flex items-center gap-2">
            <span class="text-xl">🎂</span>
            <h2 class="font-semibold text-gray-900">Upcoming Birthdays</h2>
            <span class="text-xs text-gray-400 ml-1">Next 30 days</span>
        </div>
        <div class="p-4">
            @if($upcomingBirthdays->count() > 0)
                <div class="space-y-3">
                    @foreach($upcomingBirthdays->take(5) as $staff)
                    @php
                        $birthdayThisYear = \Carbon\Carbon::createFromDate(
                            now()->year,
                            $staff->date_of_birth->month,
                            $staff->date_of_birth->day
                        );
                        if ($birthdayThisYear->isPast() && !$birthdayThisYear->isToday()) {
                            $birthdayThisYear->addYear();
                        }
                        $daysUntil = (int) now()->startOfDay()->diffInDays($birthdayThisYear->startOfDay());
                        $isToday   = $daysUntil === 0;
                    @endphp
                    <a href="{{ route('admin.staff.show', $staff) }}"
                       class="flex items-center gap-3 p-3 rounded-lg hover:bg-gray-50 transition
                              {{ $isToday ? 'bg-pink-50 border border-pink-200' : '' }}">
                        @if($staff->profile_photo)
                            <img src="{{ Storage::url($staff->profile_photo) }}"
                                 class="w-10 h-10 rounded-full object-cover flex-shrink-0">
                        @else
                            <div class="w-10 h-10 rounded-full flex items-center justify-center
                                        font-bold text-sm flex-shrink-0
                                        {{ $isToday ? 'bg-pink-200 text-pink-700' : 'bg-primary-100 text-primary-700' }}">
                                {{ $staff->initials }}
                            </div>
                        @endif
                        <div class="flex-1 min-w-0">
                            <p class="font-medium text-gray-900 text-sm truncate">{{ $staff->full_name }}</p>
                            <p class="text-xs text-gray-500 truncate">
                                {{ $staff->job_title ?? $staff->department ?? '—' }}
                            </p>
                        </div>
                        <div class="text-right flex-shrink-0">
                            <p class="text-xs font-medium {{ $isToday ? 'text-pink-600' : 'text-gray-600' }}">
                                @if($isToday) 🎉 Today!
                                @elseif($daysUntil === 1) Tomorrow
                                @else In {{ $daysUntil }} days
                                @endif
                            </p>
                            <p class="text-xs text-gray-400">
                                {{ $staff->date_of_birth->format('M d') }}
                            </p>
                        </div>
                    </a>
                    @endforeach
                </div>
                @if($upcomingBirthdays->count() > 5)
                    <p class="text-xs text-gray-400 text-center mt-3">
                        +{{ $upcomingBirthdays->count() - 5 }} more upcoming
                    </p>
                @endif
            @else
                <div class="text-center py-8 text-gray-400">
                    <span class="text-3xl">🎂</span>
                    <p class="text-sm mt-2">No birthdays in the next 30 days</p>
                </div>
            @endif
        </div>
    </div>

    {{-- Quick Actions --}}
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b">
            <h2 class="font-semibold text-gray-900">Quick Actions</h2>
        </div>
        <div class="p-4 space-y-2">
            <a href="{{ route('admin.staff.create') }}"
               class="flex items-center gap-3 p-3 rounded-lg hover:bg-gray-50 transition text-sm">
                <div class="p-2 bg-blue-100 text-blue-600 rounded">
                    <i class="fas fa-user-plus text-sm"></i>
                </div>
                <span class="text-gray-700">Add New Staff Member</span>
            </a>
            <a href="{{ route('admin.leave.index') }}?status=pending"
               class="flex items-center gap-3 p-3 rounded-lg hover:bg-gray-50 transition text-sm">
                <div class="p-2 bg-yellow-100 text-yellow-600 rounded">
                    <i class="fas fa-calendar-check text-sm"></i>
                </div>
                <span class="text-gray-700">Review Pending Leave</span>
            </a>
            <a href="{{ route('admin.appraisals.index') }}"
               class="flex items-center gap-3 p-3 rounded-lg hover:bg-gray-50 transition text-sm">
                <div class="p-2 bg-purple-100 text-purple-600 rounded">
                    <i class="fas fa-clipboard-list text-sm"></i>
                </div>
                <span class="text-gray-700">View Appraisals</span>
            </a>
            <a href="{{ route('admin.appraisal-schedule.index') }}"
               class="flex items-center gap-3 p-3 rounded-lg hover:bg-gray-50 transition text-sm">
                <div class="p-2 bg-indigo-100 text-indigo-600 rounded">
                    <i class="fas fa-calendar-alt text-sm"></i>
                </div>
                <span class="text-gray-700">Appraisal Schedule</span>
            </a>
            <a href="{{ route('admin.employee-rules.index') }}"
               class="flex items-center gap-3 p-3 rounded-lg hover:bg-gray-50 transition text-sm">
                <div class="p-2 bg-red-100 text-red-600 rounded">
                    <i class="fas fa-file-pdf text-sm"></i>
                </div>
                <span class="text-gray-700">Employee Rules PDF</span>
            </a>
            <a href="{{ route('admin.pension.index') }}"
               class="flex items-center gap-3 p-3 rounded-lg hover:bg-gray-50 transition text-sm">
                <div class="p-2 bg-green-100 text-green-600 rounded">
                    <i class="fas fa-shield-alt text-sm"></i>
                </div>
                <span class="text-gray-700">Pension Records</span>
            </a>
            <a href="{{ route('admin.compliance.index') }}"
               class="flex items-center gap-3 p-3 rounded-lg hover:bg-gray-50 transition text-sm">
                <div class="p-2 bg-orange-100 text-orange-600 rounded">
                    <i class="fas fa-shield-alt text-sm"></i>
                </div>
                <span class="text-gray-700">Compliance Documents</span>
            </a>
            <a href="{{ url('/') }}" target="_blank"
               class="flex items-center gap-3 p-3 rounded-lg hover:bg-gray-50 transition text-sm">
                <div class="p-2 bg-gray-100 text-gray-600 rounded">
                    <i class="fas fa-external-link-alt text-sm"></i>
                </div>
                <span class="text-gray-700">View Application Form</span>
            </a>
        </div>
    </div>

</div>

{{-- ROW 4: Recent Applications --}}
@if($recentApplications->count() > 0)
<div class="bg-white rounded-lg shadow">
    <div class="px-6 py-4 border-b flex items-center justify-between">
        <h2 class="font-semibold text-gray-900">Recent Applications</h2>
        <a href="{{ route('admin.applications.index') }}"
           class="text-sm text-blue-600 hover:text-blue-800">View all →</a>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Applicant</th>
                    <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Position</th>
                    <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Status</th>
                    <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Submitted</th>
                    <th class="px-6 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach($recentApplications->take(6) as $app)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-3">
                        <p class="font-medium text-gray-900 text-sm">{{ $app->applicant_name }}</p>
                        <p class="text-xs text-gray-500">{{ $app->applicant_email }}</p>
                    </td>
                    <td class="px-6 py-3 text-sm text-gray-600">
                        {{ $app->keywordSet->job_title ?? 'N/A' }}
                    </td>
                    <td class="px-6 py-3">
                        @if($app->qualification_status === 'qualified')
                            <span class="badge badge-success">Qualified</span>
                        @elseif($app->qualification_status === 'Fairly Qualified')
                            <span class="badge badge-warning">Fairly Qualified</span>
                        @elseif($app->qualification_status === 'Not Qualified')
                            <span class="badge badge-danger">Not Qualified</span>
                        @else
                            <span class="badge badge-secondary">Pending</span>
                        @endif
                    </td>
                    <td class="px-6 py-3 text-sm text-gray-500">
                        {{ $app->created_at->diffForHumans() }}
                    </td>
                    <td class="px-6 py-3">
                        <a href="{{ route('admin.applications.show', $app->id) }}"
                           class="text-blue-500 hover:text-blue-700 text-sm">
                            <i class="fas fa-eye"></i>
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif

@endsection