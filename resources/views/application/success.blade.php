@extends('layouts.app')

@section('content')
<div class="container max-w-2xl mx-auto">
    <div class="text-center">
        <!-- Success Icon -->
        <div class="mb-6">
            <div class="mx-auto flex items-center justify-center h-20 w-20 rounded-full bg-green-100">
                <i class="fas fa-check-circle text-green-600 text-4xl"></i>
            </div>
        </div>
        
        <h1 class="text-3xl font-bold text-gray-900 mb-4">Application Submitted Successfully!</h1>
        <p class="text-lg text-gray-600 mb-8">Thank you for applying to Risk Control Services Nigeria</p>
        
        <!-- Application Details Card -->
        <div class="bg-white rounded-lg shadow-lg p-6 mb-8">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">Application Details</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-left">
                <div>
                    <label class="block text-sm font-medium text-gray-500">Name</label>
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
                    <label class="block text-sm font-medium text-gray-500">Application ID</label>
                    <p class="text-lg font-mono text-gray-900">#{{ str_pad($application->id, 6, '0', STR_PAD_LEFT) }}</p>
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

        <!-- Status Information -->
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-6 mb-8">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <i class="fas fa-info-circle text-blue-600 text-xl"></i>
                </div>
                <div class="ml-4 text-left">
                    <h3 class="text-lg font-medium text-blue-900 mb-2">What happens next?</h3>
                    <div class="text-blue-700 space-y-2">
                        <p>• Your CV is being processed and screened against job requirements</p>
                        <p>• You'll receive an email notification once processing is complete</p>
                        <p>• You can check your application status anytime using the link below</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="{{ route('application.status', $application->id) }}" 
               class="inline-flex items-center justify-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                <i class="fas fa-eye mr-2"></i>
                Check Application Status
            </a>
            
            <a href="{{ route('application.form') }}" 
               class="inline-flex items-center justify-center px-6 py-3 border border-gray-300 text-base font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                <i class="fas fa-plus mr-2"></i>
                Submit Another Application
            </a>
        </div>

        <!-- Contact Information -->
        <div class="mt-8 text-center">
            <p class="text-sm text-gray-500">
                Questions? Contact us at 
                <a href="mailto:hr@riskcontolnigeria.com" class="text-blue-600 hover:text-blue-800">hr@riskcontolnigeria.com</a>
            </p>
        </div>
    </div>
</div>

@endsection