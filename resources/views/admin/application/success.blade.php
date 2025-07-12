@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="text-center">
                <div class="mb-4">
                    <i class="fas fa-check-circle text-success" style="font-size: 4rem;"></i>
                </div>
                
                <h1 class="h3 mb-3">Application Submitted Successfully!</h1>
                
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Application Details</h5>
                        <hr>
                        <div class="row">
                            <div class="col-sm-6 text-start">
                                <strong>Name:</strong><br>
                                <strong>Email:</strong><br>
                                <strong>Submitted:</strong><br>
                                <strong>Application ID:</strong>
                            </div>
                            <div class="col-sm-6 text-start">
                                {{ $application->applicant_name }}<br>
                                {{ $application->applicant_email }}<br>
                                {{ $application->created_at->format('M d, Y \a\t H:i') }}<br>
                                #{{ str_pad($application->id, 6, '0', STR_PAD_LEFT) }}
                            </div>
                        </div>
                    </div>
                </div>

                <div class="alert alert-info mt-4">
                    <i class="fas fa-info-circle me-2"></i>
                    Your CV is being processed. This usually takes 1-2 minutes.
                </div>

                <div class="d-grid gap-2 d-md-block">
                    <a href="{{ route('application.status', $application->id) }}" class="btn btn-primary">
                        <i class="fas fa-eye me-2"></i>
                        Check Status
                    </a>
                    <a href="{{ route('application.form') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-plus me-2"></i>
                        Submit Another
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection