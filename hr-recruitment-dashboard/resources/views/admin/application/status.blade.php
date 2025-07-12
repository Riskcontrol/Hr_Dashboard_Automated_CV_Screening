@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="text-center mb-4">
                <h1 class="h3">Application Status</h1>
                <p class="text-muted">Application ID: #{{ str_pad($application->id, 6, '0', STR_PAD_LEFT) }}</p>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">{{ $application->applicant_name }}</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>Email:</strong> {{ $application->applicant_email }}<br>
                            <strong>Phone:</strong> {{ $application->phone ?? 'Not provided' }}<br>
                            <strong>Position:</strong> {{ $application->keywordSet->job_title ?? 'Not specified' }}
                        </div>
                        <div class="col-md-6">
                            <strong>Submitted:</strong> {{ $application->created_at->format('M d, Y \a\t H:i') }}<br>
                            <strong>CV File:</strong> {{ $application->cv_original_name }}<br>
                            <strong>File Size:</strong> {{ $application->file_size_formatted }}
                        </div>
                    </div>

                    <hr>

                    <div class="text-center application-status" data-status="{{ $application->qualification_status }}">
                        @if($application->qualification_status === 'qualified')
                            <div class="alert alert-success">
                                <i class="fas fa-check-circle fa-2x mb-2"></i>
                                <h4>Congratulations! You're Qualified</h4>
                                <p class="mb-0">Your CV matches our requirements with {{ $application->match_percentage }}% compatibility.</p>
                            </div>

                            @if($application->found_keywords)
                                <div class="row mt-4">
                                    <div class="col-md-6">
                                        <h6 class="text-success">✓ Found Keywords</h6>
                                        <div class="keywords-list">
                                            @foreach($application->found_keywords as $keyword)
                                                <span class="badge bg-success me-1 mb-1">{{ $keyword }}</span>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            @endif

                        @elseif($application->qualification_status === 'not_qualified')
                            <div class="alert alert-danger">
                                <i class="fas fa-times-circle fa-2x mb-2"></i>
                                <h4>Application Not Qualified</h4>
                                <p class="mb-0">Your CV matches {{ $application->match_percentage }}% of our requirements.</p>
                            </div>

                            <div class="row mt-4">
                                @if($application->found_keywords)
                                    <div class="col-md-6">
                                        <h6 class="text-success">✓ Found Keywords</h6>
                                        <div class="keywords-list">
                                            @foreach($application->found_keywords as $keyword)
                                                <span class="badge bg-success me-1 mb-1">{{ $keyword }}</span>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif

                                @if($application->missing_keywords)
                                    <div class="col-md-6">
                                        <h6 class="text-danger">✗ Missing Keywords</h6>
                                        <div class="keywords-list">
                                            @foreach($application->missing_keywords as $keyword)
                                                <span class="badge bg-danger me-1 mb-1">{{ $keyword }}</span>
                                            @endforeach
                                        </div>
                                        <small class="text-muted">Consider updating your CV to include these skills.</small>
                                    </div>
                                @endif
                            </div>

                        @elseif($application->qualification_status === 'pending')
                            <div class="alert alert-warning">
                                <i class="fas fa-clock fa-2x mb-2"></i>
                                <h4>Processing Your Application</h4>
                                <p class="mb-0">Please wait while we analyze your CV. This usually takes 1-2 minutes.</p>
                                <div class="mt-3">
                                    <div class="spinner-border text-warning" role="status">
                                        <span class="visually-hidden">Processing...</span>
                                    </div>
                                </div>
                            </div>

                        @else
                            <div class="alert alert-secondary">
                                <i class="fas fa-exclamation-triangle fa-2x mb-2"></i>
                                <h4>Processing Failed</h4>
                                <p class="mb-0">There was an issue processing your CV. Please try submitting again or contact support.</p>
                            </div>
                        @endif
                    </div>

                    @if($application->processed_at)
                        <div class="text-center mt-3">
                            <small class="text-muted">
                                Processed on {{ $application->processed_at->format('M d, Y \a\t H:i') }}
                            </small>
                        </div>
                    @endif
                </div>
            </div>

            <div class="text-center mt-4">
                <a href="{{ route('application.form') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>
                    Submit New Application
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
