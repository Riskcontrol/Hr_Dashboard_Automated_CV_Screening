@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Create Keyword Set</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="{{ route('admin.keyword-sets.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>
            Back to List
        </a>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Keyword Set Details</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.keyword-sets.store') }}">
                    @csrf
                    
                    <div class="mb-3">
                        <label for="job_title" class="form-label">Job Title <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('job_title') is-invalid @enderror" 
                               id="job_title" name="job_title" value="{{ old('job_title') }}" 
                               placeholder="e.g., Software Developer, Marketing Manager">
                        @error('job_title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="keywords" class="form-label">Keywords <span class="text-danger">*</span></label>
                        <textarea class="form-control @error('keywords') is-invalid @enderror" 
                                  id="keywords" name="keywords" rows="4" 
                                  placeholder="Enter keywords separated by commas. e.g., PHP, Laravel, MySQL, JavaScript">{{ old('keywords') }}</textarea>
                        <div class="form-text">
                            Separate each keyword with a comma. These keywords will be searched in uploaded CVs.
                        </div>
                        @error('keywords')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" 
                                  id="description" name="description" rows="3" 
                                  placeholder="Optional description for this keyword set">{{ old('description') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="1" id="is_active" name="is_active" 
                                   {{ old('is_active', true) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">
                                Active
                            </label>
                            <div class="form-text">
                                Only active keyword sets will be available for CV screening.
                            </div>
                        </div>
                    </div>

                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <a href="{{ route('admin.keyword-sets.index') }}" class="btn btn-secondary me-md-2">Cancel</a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>
                            Create Keyword Set
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Tips</h5>
            </div>
            <div class="card-body">
                <ul class="list-unstyled">
                    <li class="mb-2">
                        <i class="fas fa-lightbulb text-warning me-2"></i>
                        <strong>Be Specific:</strong> Use specific skills and technologies relevant to the job.
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-search text-info me-2"></i>
                        <strong>Case Insensitive:</strong> Keywords will match regardless of case.
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-check-circle text-success me-2"></i>
                        <strong>All Required:</strong> ALL keywords must be found for qualification.
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-tags text-primary me-2"></i>
                        <strong>Examples:</strong> PHP, Laravel, MySQL, React, Project Management
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Add keyword preview functionality
    document.getElementById('keywords').addEventListener('input', function() {
        const keywords = this.value.split(',').map(k => k.trim()).filter(k => k.length > 0);
        const preview = document.getElementById('keyword-preview');
        
        if (preview) {
            preview.innerHTML = '';
            keywords.forEach(keyword => {
                const badge = document.createElement('span');
                badge.className = 'badge bg-secondary me-1 mb-1';
                badge.textContent = keyword;
                preview.appendChild(badge);
            });
        }
    });
</script>
@endpush
@endsection