@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4>Manage Applications</h4>
                    <a href="{{ route('admin.applications.export-qualified') }}" class="btn btn-success">
                        Export Qualified
                    </a>
                </div>

                <div class="card-body">
                    <!-- Filters -->
                    <form method="GET" class="mb-4">
                        <div class="row">
                            <div class="col-md-3">
                                <select name="status" class="form-control">
                                    <option value="">All Statuses</option>
                                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="processing" {{ request('status') == 'processing' ? 'selected' : '' }}>Processing</option>
                                    <option value="qualified" {{ request('status') == 'qualified' ? 'selected' : '' }}>Qualified</option>
                                    <option value="not_qualified" {{ request('status') == 'not_qualified' ? 'selected' : '' }}>Not Qualified</option>
                                    <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>Failed</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <select name="keyword_set_id" class="form-control">
                                    <option value="">All Positions</option>
                                    @foreach($keywordSets as $keywordSet)
                                        <option value="{{ $keywordSet->id }}" {{ request('keyword_set_id') == $keywordSet->id ? 'selected' : '' }}>
                                            {{ $keywordSet->job_title }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <input type="text" name="search" class="form-control" placeholder="Search by name or email" value="{{ request('search') }}">
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-primary">Filter</button>
                            </div>
                        </div>
                    </form>

                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif

                    <!-- Bulk Actions -->
                    <form id="bulkForm" method="POST" action="{{ route('admin.applications.bulk-action') }}">
                        @csrf
                        <div class="mb-3">
                            <div class="row">
                                <div class="col-md-4">
                                    <select name="action" class="form-control" required>
                                        <option value="">Select Action</option>
                                        <option value="delete">Delete Selected</option>
                                        <option value="reprocess">Reprocess Selected</option>
                                    </select>
                                </div>
                                <div class="col-md-4" id="keywordSetSelect" style="display: none;">
                                    <select name="keyword_set_id" class="form-control">
                                        <option value="">Select Position</option>
                                        @foreach($keywordSets as $keywordSet)
                                            <option value="{{ $keywordSet->id }}">{{ $keywordSet->job_title }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <button type="submit" class="btn btn-warning" onclick="return confirm('Are you sure?')">Apply</button>
                                </div>
                            </div>
                        </div>

                        <!-- Applications Table -->
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th><input type="checkbox" id="selectAll"></th>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Phone</th>
                                        <th>Position</th>
                                        <th>Status</th>
                                        <th>Match %</th>
                                        <th>CV</th>
                                        <th>Submitted</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($applications as $application)
                                        <tr>
                                            <td><input type="checkbox" name="applications[]" value="{{ $application->id }}"></td>
                                            <td>{{ $application->applicant_name }}</td>
                                            <td>{{ $application->applicant_email }}</td>
                                            <td>{{ $application->phone ?? 'N/A' }}</td>
                                            <td>{{ $application->keywordSet->job_title ?? 'N/A' }}</td>
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
                                            <td>{{ $application->match_percentage ? $application->match_percentage . '%' : 'N/A' }}</td>
                                            <td>
                                                <a href="{{ route('admin.applications.cv.download', $application->id) }}" class="btn btn-sm btn-primary">
                                                    Download
                                                </a>
                                            </td>
                                            <td>{{ $application->created_at->format('Y-m-d H:i') }}</td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('admin.applications.show', $application->id) }}" class="btn btn-sm btn-info">View</a>
                                                    <form method="POST" action="{{ route('admin.applications.reprocess', $application->id) }}" style="display: inline;">
                                                        @csrf
                                                        <button type="submit" class="btn btn-sm btn-warning" onclick="return confirm('Reprocess this CV?')">Reprocess</button>
                                                    </form>
                                                    <form method="POST" action="{{ route('admin.applications.destroy', $application->id) }}" style="display: inline;">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Delete this application and CV file?')">Delete</button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="10" class="text-center">No applications found.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </form>

                    <!-- Pagination -->
                    {{ $applications->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Select all checkbox functionality
    document.getElementById('selectAll').addEventListener('change', function() {
        const checkboxes = document.querySelectorAll('input[name="applications[]"]');
        checkboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
    });

    // Show keyword set select when reprocess is selected
    document.querySelector('select[name="action"]').addEventListener('change', function() {
        const keywordSetSelect = document.getElementById('keywordSetSelect');
        if (this.value === 'reprocess') {
            keywordSetSelect.style.display = 'block';
            keywordSetSelect.querySelector('select').required = true;
        } else {
            keywordSetSelect.style.display = 'none';
            keywordSetSelect.querySelector('select').required = false;
        }
    });
});
</script>
@endsection
