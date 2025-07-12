@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Keyword Sets</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="{{ route('admin.keyword-sets.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>
            Create New Set
        </a>
    </div>
</div>

<div class="card">
    <div class="card-body">
        @if($keywordSets->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Job Title</th>
                            <th>Keywords</th>
                            <th>Status</th>
                            <th>Applications</th>
                            <th>Created By</th>
                            <th>Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($keywordSets as $set)
                        <tr>
                            <td>
                                <strong>{{ $set->job_title }}</strong>
                                @if($set->description)
                                    <br><small class="text-muted">{{ Str::limit($set->description, 50) }}</small>
                                @endif
                            </td>
                            <td>
                                <div class="keywords-preview">
                                    @foreach(array_slice($set->keywords, 0, 3) as $keyword)
                                        <span class="badge bg-secondary me-1">{{ $keyword }}</span>
                                    @endforeach
                                    @if(count($set->keywords) > 3)
                                        <span class="text-muted">+{{ count($set->keywords) - 3 }} more</span>
                                    @endif
                                </div>
                            </td>
                            <td>
                                @if($set->is_active)
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-secondary">Inactive</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-primary">{{ $set->applications_count ?? 0 }}</span>
                            </td>
                            <td>{{ $set->creator->name }}</td>
                            <td>{{ $set->created_at->format('M d, Y') }}</td>
                            <td>
                                <div class="btn-group btn-group-sm" role="group">
                                    <a href="{{ route('admin.keyword-sets.show', $set) }}" class="btn btn-outline-info">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.keyword-sets.edit', $set) }}" class="btn btn-outline-warning">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form method="POST" action="{{ route('admin.keyword-sets.destroy', $set) }}" class="d-inline" 
                                          onsubmit="return confirm('Are you sure you want to delete this keyword set?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{ $keywordSets->links() }}
        @else
            <div class="text-center py-5">
                <i class="fas fa-tags fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">No keyword sets created yet</h5>
                <p class="text-muted">Create your first keyword set to start screening CVs.</p>
                <a href="{{ route('admin.keyword-sets.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>
                    Create Keyword Set
                </a>
            </div>
        @endif
    </div>
</div>
@endsection