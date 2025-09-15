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
