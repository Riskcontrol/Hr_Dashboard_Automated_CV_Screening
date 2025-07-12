@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Header -->
    <div class="text-center mb-12">
        <h1 class="text-4xl font-bold text-primary-600 mb-4">Risk Control Services Nigeria</h1>
        <h2 class="text-2xl text-gray-600 mb-2">Job Application Portal</h2>
        <p class="text-lg text-gray-500">Submit your CV and get instant qualification results</p>
    </div>

    <!-- Application Form -->
    <div class="card shadow-lg">
        <div class="card-header bg-primary-600 text-white">
            <h3 class="text-lg font-semibold flex items-center">
                <i class="fas fa-file-upload mr-3"></i>
                Submit Your Application
            </h3>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('application.store') }}" enctype="multipart/form-data" id="application-form">
                @csrf
                
                <!-- Personal Information -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label for="applicant_name" class="form-label">
                            Full Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               class="form-input @error('applicant_name') border-red-500 @enderror" 
                               id="applicant_name" 
                               name="applicant_name" 
                               value="{{ old('applicant_name') }}" 
                               placeholder="Enter your full name" 
                               required>
                        @error('applicant_name')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="applicant_email" class="form-label">
                            Email Address <span class="text-red-500">*</span>
                        </label>
                        <input type="email" 
                               class="form-input @error('applicant_email') border-red-500 @enderror" 
                               id="applicant_email" 
                               name="applicant_email" 
                               value="{{ old('applicant_email') }}" 
                               placeholder="your.email@gmail.com" 
                               required>
                        @error('applicant_email')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Contact & Position -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label for="phone" class="form-label">Phone Number</label>
                        <input type="tel" 
                               class="form-input @error('phone') border-red-500 @enderror" 
                               id="phone" 
                               name="phone" 
                               value="{{ old('phone') }}" 
                               placeholder="+234 XXX XXX XXXX">
                        @error('phone')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="job_position" class="form-label">
                            Position Applied For <span class="text-red-500">*</span>
                        </label>
                        <select class="form-select @error('job_position') border-red-500 @enderror" 
                                id="job_position" 
                                name="job_position" 
                                required>
                            <option value="">Select a position</option>
                            @if(isset($keywordSets))
                                @forelse($keywordSets as $set)
                                    <option value="{{ $set->id }}" {{ old('job_position') == $set->id ? 'selected' : '' }}>
                                        {{ $set->job_title }}
                                    </option>
                                @empty
                                    <option value="" disabled>No positions available</option>
                                @endforelse
                            @endif
                        </select>
                        @error('job_position')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- File Upload -->
                <div class="mb-8">
                    <label for="cv_file" class="form-label">
                        Upload CV/Resume <span class="text-red-500">*</span>
                    </label>
                    
                    <div class="file-upload-area @error('cv_file') border-red-300 @enderror" 
                         onclick="document.getElementById('cv_file').click()">
                        <div class="flex flex-col items-center">
                            <i class="fas fa-cloud-upload-alt text-6xl text-gray-400 mb-4"></i>
                            <h4 class="text-xl text-gray-600 mb-2">Drag and drop your CV here</h4>
                            <p class="text-gray-500 mb-4">or click to browse files</p>
                            <div class="file-name-display text-primary-600 font-semibold"></div>
                            <p class="text-sm text-gray-400 mt-2">
                                Supported formats: PDF, DOC, DOCX (Max size: 10MB)
                            </p>
                        </div>
                    </div>
                    
                    <input type="file" 
                           class="hidden" 
                           id="cv_file" 
                           name="cv_file" 
                           accept=".pdf,.doc,.docx" 
                           required>
                    
                    @error('cv_file')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Information Alert -->
                <div class="alert alert-info mb-6">
                    <div class="flex">
                        <i class="fas fa-info-circle mr-3 mt-0.5"></i>
                        <div>
                            <p class="font-semibold">How it works:</p>
                            <p>Your CV will be automatically screened against job requirements. You'll receive instant feedback on your qualification status.</p>
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="text-center">
                    <button type="submit" 
                            class="btn btn-primary px-8 py-3 text-lg" 
                            id="submit-btn">
                        <i class="fas fa-paper-plane mr-2"></i>
                        Submit Application
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Footer -->
    <div class="text-center mt-8">
        <p class="text-gray-500 text-sm">
            Your data is processed securely and will be used only for recruitment purposes.
        </p>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('application-form');
        const fileInput = document.getElementById('cv_file');
        const fileUploadArea = document.querySelector('.file-upload-area');
        const fileNameDisplay = document.querySelector('.file-name-display');
        const submitBtn = document.getElementById('submit-btn');

        // Form submission
        if (form) {
            form.addEventListener('submit', function() {
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Processing...';
                submitBtn.disabled = true;
                submitBtn.classList.add('opacity-75', 'cursor-not-allowed');
            });
        }

        // File input change
        if (fileInput) {
            fileInput.addEventListener('change', function() {
                const file = this.files[0];
                if (file) {
                    validateAndDisplayFile(file);
                }
            });
        }

        // Drag and drop functionality
        if (fileUploadArea) {
            fileUploadArea.addEventListener('dragover', function(e) {
                e.preventDefault();
                this.classList.add('dragover');
            });

            fileUploadArea.addEventListener('dragleave', function(e) {
                e.preventDefault();
                this.classList.remove('dragover');
            });

            fileUploadArea.addEventListener('drop', function(e) {
                e.preventDefault();
                this.classList.remove('dragover');
                
                const files = e.dataTransfer.files;
                if (files.length > 0) {
                    fileInput.files = files;
                    validateAndDisplayFile(files[0]);
                }
            });
        }

        function validateAndDisplayFile(file) {
            // File size validation (10MB)
            const maxSize = 10 * 1024 * 1024;
            if (file.size > maxSize) {
                alert('File size must not exceed 10MB');
                fileInput.value = '';
                fileNameDisplay.textContent = '';
                return;
            }
            
            // File type validation
            const allowedTypes = ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
            if (!allowedTypes.includes(file.type)) {
                alert('Only PDF, DOC, and DOCX files are allowed');
                fileInput.value = '';
                fileNameDisplay.textContent = '';
                return;
            }

            // Display file name
            if (fileNameDisplay) {
                fileNameDisplay.textContent = file.name;
            }
        }
    });
</script>
@endpush
@endsection
