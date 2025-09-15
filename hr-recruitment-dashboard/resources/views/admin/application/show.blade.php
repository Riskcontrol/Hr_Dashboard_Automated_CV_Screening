@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4>Application Details</h4>
                    <a href="{{ route('admin.applications.index') }}" class="btn btn-secondary">Back to List</a>
                </div>

                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif

                    <div class="row">
                        <!-- Application Information -->
                        <div class="col-md-6">
                            <h5>Personal Information</h5>
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Name:</strong></td>
                                    <td>{{ $application->applicant_name }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Email:</strong></td>
                                    <td>{{ $application->applicant_email }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Phone:</strong></td>
                                    <td>{{ $application->phone ?? 'Not provided' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Position:</strong></td>
                                    <td>{{ $application->keywordSet->job_title ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Submitted:</strong></td>
                                    <td>{{ $application->created_at->format('Y-m-d H:i:s') }}</td>
                                </tr>
                            </table>
                        </div>

                        <!-- Processing Information -->
                        <div class="col-md-6">
                            <h5>Processing Status</h5>
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Status:</strong></td>
                                    <td>
                                        @switch($application->qualification_status)
                                            @case('qualified')
                                                <span class="badge badge-success">Qualified</span>
                                                @break
                                            @case('not_qualified')
                                                <span class="badge badge-danger">Not Qualified</span>
                                                @break
                                            @case('processing')
                                                <span class="badge badge-info">Processing</span>
                                                @break
                                            @case('failed')
                                                <span class="badge badge-danger">Failed</span>
                                                @break
                                            @default
                                                <span class="badge badge-warning">Pending</span>
                                        @endswitch
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Match Percentage:</strong></td>
                                    <td>{{ $application->match_percentage ? $application->match_percentage . '%' : 'Not calculated' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Processing Started:</strong></td>
                                    <td>
                                        @if($application->processing_started_at)
                                            @if(is_string($application->processing_started_at))
                                                {{ $application->processing_started_at }}
                                            @else
                                                {{ $application->processing_started_at->format('Y-m-d H:i:s') }}
                                            @endif
                                        @else
                                            Not started
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Processed At:</strong></td>
                                    <td>
                                        @if($application->processed_at)
                                            @if(is_string($application->processed_at))
                                                {{ $application->processed_at }}
                                            @else
                                                {{ $application->processed_at->format('Y-m-d H:i:s') }}
                                            @endif
                                        @else
                                            Not completed
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <!-- CV File Information -->
                    <div class="row mt-4">
                        <div class="col-md-12">
                            <h5>CV File</h5>
                            <div class="card">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <p><strong>Original Name:</strong> {{ $application->cv_original_name }}</p>
                                            <p><strong>File Size:</strong> {{ number_format($application->cv_file_size / 1024, 2) }} KB</p>
                                            <p><strong>Stored Path:</strong> {{ $application->cv_stored_path }}</p>
                                        </div>
                                        <div class="col-md-6 text-right">
                                            <a href="{{ route('admin.applications.cv.download', $application->id) }}" class="btn btn-primary">
                                                <i class="fas fa-download"></i> Download CV
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Found Keywords (if processed) -->
                    @if($application->found_keywords)
                        <div class="row mt-4">
                            <div class="col-md-6">
                                <h5>Found Keywords</h5>
                                <div class="list-group">
                                    @foreach(json_decode($application->found_keywords, true) as $keyword)
                                        <div class="list-group-item list-group-item-success">{{ $keyword }}</div>
                                    @endforeach
                                </div>
                            </div>

                            @if($application->missing_keywords)
                                <div class="col-md-6">
                                    <h5>Missing Keywords</h5>
                                    <div class="list-group">
                                        @foreach(json_decode($application->missing_keywords, true) as $keyword)
                                            <div class="list-group-item list-group-item-danger">{{ $keyword }}</div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endif

                    <!-- Extracted Text (if available) -->
                    @if($application->extracted_text)
                        <div class="row mt-4">
                            <div class="col-md-12">
                                <h5>Extracted Text</h5>
                                <div class="card">
                                    <div class="card-body">
                                        <pre style="white-space: pre-wrap; max-height: 300px; overflow-y: auto;">{{ Str::limit($application->extracted_text, 2000) }}</pre>
                                        @if(strlen($application->extracted_text) > 2000)
                                            <p><em>... (text truncated)</em></p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- AI Evaluation Section -->
                    @if($application->ai_evaluation)
                        <div class="row mt-4">
                            <div class="col-md-12">
                                <h5><i class="fas fa-robot text-primary"></i> AI Evaluation</h5>
                                <div class="card border-primary">
                                    <div class="card-header bg-primary text-white">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span><i class="fas fa-brain"></i> Gemini AI Assessment</span>
                                            <div>
                                                <span class="badge badge-light">Score: {{ $application->ai_score }}/100</span>
                                                @if($application->ai_recommendation == 'RECOMMEND')
                                                    <span class="badge badge-success">{{ $application->ai_recommendation }}</span>
                                                @elseif($application->ai_recommendation == 'CONSIDER')
                                                    <span class="badge badge-warning">{{ $application->ai_recommendation }}</span>
                                                @else
                                                    <span class="badge badge-danger">{{ $application->ai_recommendation }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <!-- Overall Evaluation -->
                                        <div class="mb-4">
                                            <h6><i class="fas fa-file-alt text-info"></i> Overall Assessment</h6>
                                            <p class="text-muted">{{ $application->ai_evaluation }}</p>
                                        </div>

                                        <div class="row">
                                            <!-- Strengths -->
                                            <div class="col-md-6">
                                                <h6><i class="fas fa-thumbs-up text-success"></i> Strengths</h6>
                                                @if($application->ai_strengths && count($application->ai_strengths) > 0)
                                                    <ul class="list-group list-group-flush">
                                                        @foreach($application->ai_strengths as $strength)
                                                            <li class="list-group-item border-0 px-0 py-1">
                                                                <i class="fas fa-check-circle text-success mr-2"></i>{{ $strength }}
                                                            </li>
                                                        @endforeach
                                                    </ul>
                                                @else
                                                    <p class="text-muted">No specific strengths identified.</p>
                                                @endif
                                            </div>

                                            <!-- Weaknesses -->
                                            <div class="col-md-6">
                                                <h6><i class="fas fa-exclamation-triangle text-warning"></i> Areas for Improvement</h6>
                                                @if($application->ai_weaknesses && count($application->ai_weaknesses) > 0)
                                                    <ul class="list-group list-group-flush">
                                                        @foreach($application->ai_weaknesses as $weakness)
                                                            <li class="list-group-item border-0 px-0 py-1">
                                                                <i class="fas fa-exclamation-circle text-warning mr-2"></i>{{ $weakness }}
                                                            </li>
                                                        @endforeach
                                                    </ul>
                                                @else
                                                    <p class="text-muted">No specific concerns identified.</p>
                                                @endif
                                            </div>
                                        </div>

                                        <!-- Evaluation Timestamp -->
                                        <div class="mt-3 pt-3 border-top">
                                            <small class="text-muted">
                                                <i class="fas fa-clock"></i> AI evaluation completed on: 
                                                {{ $application->ai_evaluated_at ? $application->ai_evaluated_at->format('Y-m-d H:i:s') : 'Not available' }}
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Actions -->
                    <div class="row mt-4">
                        <div class="col-md-12">
                            <div class="btn-group" role="group">
                                <form method="POST" action="{{ route('admin.applications.reprocess', $application->id) }}" style="display: inline;">
                                    @csrf
                                    <button type="submit" class="btn btn-warning" onclick="return confirm('Reprocess this CV?')">
                                        <i class="fas fa-sync"></i> Reprocess CV
                                    </button>
                                </form>

                                <form method="POST" action="{{ route('admin.applications.destroy', $application->id) }}" style="display: inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger" onclick="return confirm('Delete this application and CV file permanently?')">
                                        <i class="fas fa-trash"></i> Delete Application
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
