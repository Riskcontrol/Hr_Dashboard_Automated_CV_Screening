@extends('layouts.app')

@section('content')
<div class="container max-w-4xl mx-auto">
    <div class="text-center mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Application Status</h1>
        <p class="text-gray-600 mt-2">Application ID: #{{ str_pad($application->id, 6, '0', STR_PAD_LEFT) }}</p>
    </div>

    <!-- Application Details -->
    <div class="bg-white rounded-lg shadow-lg p-6 mb-8">
        <h2 class="text-xl font-semibold text-gray-900 mb-4">Application Information</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-500">Applicant Name</label>
                <p class="text-lg text-gray-900">{{ $application->applicant_name }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-500">Email</label>
                <p class="text-lg text-gray-900">{{ $application->applicant_email }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-500">Phone</label>
                <p class="text-lg text-gray-900">{{ $application->phone ?? 'Not provided' }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-500">Position Applied</label>
                <p class="text-lg text-gray-900">{{ $application->keywordSet->job_title ?? 'Not specified' }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-500">CV File</label>
                <p class="text-lg text-gray-900">{{ $application->cv_original_name }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-500">Submitted</label>
                <p class="text-lg text-gray-900">{{ $application->created_at->format('M d, Y \a\t H:i') }}</p>
            </div>
        </div>
    </div>

    <!-- Status Display -->
    <div class="text-center application-status mb-8" data-status="{{ $application->processing_status }}">
        @if($application->processing_status === 'completed')
            @if($application->qualification_status === 'qualified')
            <div class="bg-green-50 border border-green-200 rounded-lg p-8">
                <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-green-100 mb-4">
                    <i class="fas fa-check-circle text-green-600 text-3xl"></i>
                </div>
                <h3 class="text-2xl font-bold text-green-900 mb-2">Congratulations! You're Qualified</h3>
                <p class="text-green-700 mb-4">Your CV matches our requirements with {{ $application->match_percentage }}% compatibility.</p>
                <div class="bg-white rounded-lg p-4 border border-green-200">
                    <p class="text-green-800 font-medium">Our HR team will contact you within 2-3 business days.</p>
                </div>
            </div>
            @elseif($application->qualification_status === 'not_qualified')
            <div class="bg-red-50 border border-red-200 rounded-lg p-8">
                <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-red-100 mb-4">
                    <i class="fas fa-times-circle text-red-600 text-3xl"></i>
                </div>
                <h3 class="text-2xl font-bold text-red-900 mb-2">Application Not Qualified</h3>
                <p class="text-red-700 mb-4">Your CV matches {{ $application->match_percentage }}% of our requirements.</p>
                
                @if($application->missing_keywords)
                    <div class="bg-white rounded-lg p-4 border border-red-200">
                        <h4 class="font-semibold text-red-900 mb-2">Skills we're looking for:</h4>
                        <div class="flex flex-wrap gap-2 justify-center">
                            @foreach($application->missing_keywords as $keyword)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                    {{ $keyword }}
                                </span>
                            @endforeach
                        </div>
                        <p class="text-red-700 text-sm mt-3">Consider developing these skills and applying for future opportunities.</p>
                    </div>
                @endif
            </div>
            @endif
        @elseif($application->processing_status === 'pending' || $application->processing_status === 'processing')
            <div class="bg-green-50 border border-green-200 rounded-lg p-8">
                <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-green-100 mb-4">
                    <i class="fas fa-check-circle text-green-600 text-3xl"></i>
                </div>
                <h3 class="text-2xl font-bold text-green-900 mb-2">Application Received</h3>
                <p class="text-green-700 mb-4">Thank you for your application. We have received your CV and will get back to you shortly.</p>
            </div>
        @elseif($application->processing_status === 'failed')
            <div class="bg-gray-50 border border-gray-200 rounded-lg p-8">
                <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-gray-100 mb-4">
                    <i class="fas fa-exclamation-triangle text-gray-600 text-3xl"></i>
                </div>
                <h3 class="text-2xl font-bold text-gray-900 mb-2">Processing Failed</h3>
                <p class="text-gray-700 mb-4">There was an issue processing your CV. Please try submitting again or contact support.</p>
            </div>
        @endif
    </div>

    <!-- Keywords Found (if available) -->
    @if($application->found_keywords && count($application->found_keywords) > 0)
    <div class="bg-white rounded-lg shadow-lg p-6 mb-8">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">✓ Skills Found in Your CV</h3>
        <div class="flex flex-wrap gap-2">
            @foreach($application->found_keywords as $keyword)
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                    <i class="fas fa-check mr-1"></i>
                    {{ $keyword }}
                </span>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Action Buttons -->
    <div class="text-center">
        <a href="{{ route('application.form') }}" 
           class="inline-flex items-center justify-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
            <i class="fas fa-plus mr-2"></i>
            Submit New Application
        </a>
    </div>
</div>

@endsection
