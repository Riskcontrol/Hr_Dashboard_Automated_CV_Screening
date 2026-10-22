@extends('layouts.app')

@section('content')

<div class="mb-6 flex justify-between items-center">
    <div>
        <h1 class="text-3xl font-bold text-gray-900">Applications</h1>
        <p class="text-gray-500 mt-1">CV screening results for all open positions</p>
    </div>
    <a href="{{ route('admin.applications.export-qualified') }}" class="btn btn-success">
        <i class="fas fa-download mr-2"></i> Export Qualified
    </a>
</div>

{{-- Filters --}}
<div class="bg-white rounded-lg shadow p-4 mb-6">
    <form method="GET" class="grid grid-cols-2 md:grid-cols-4 gap-3 items-end">
        <div>
            <label class="form-label">Status</label>
            <select name="status" class="form-select">
                <option value="">All Statuses</option>
                <option value="pending"       {{ request('status') == 'pending'       ? 'selected' : '' }}>Pending</option>
                <option value="processing"    {{ request('status') == 'processing'    ? 'selected' : '' }}>Processing</option>
                <option value="qualified"     {{ request('status') == 'qualified'     ? 'selected' : '' }}>Qualified</option>
                <option value="not_qualified" {{ request('status') == 'not_qualified' ? 'selected' : '' }}>Not Qualified</option>
                <option value="failed"        {{ request('status') == 'failed'        ? 'selected' : '' }}>Failed</option>
            </select>
        </div>
        <div>
            <label class="form-label">Position</label>
            <select name="keyword_set_id" class="form-select">
                <option value="">All Positions</option>
                @foreach($keywordSets as $keywordSet)
                    <option value="{{ $keywordSet->id }}"
                        {{ request('keyword_set_id') == $keywordSet->id ? 'selected' : '' }}>
                        {{ $keywordSet->job_title }}
                    </option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="form-label">Search</label>
            <input type="text" name="search" class="form-input"
                   placeholder="Name or email" value="{{ request('search') }}">
        </div>
        <div class="flex gap-2">
            <button type="submit" class="btn btn-primary flex-1">
                <i class="fas fa-search mr-1"></i> Filter
            </button>
            @if(request()->hasAny(['status','keyword_set_id','search']))
                <a href="{{ route('admin.applications.index') }}" class="btn btn-outline">Clear</a>
            @endif
        </div>
    </form>
</div>

@if(session('success'))
    <div class="alert alert-success mb-4"><i class="fas fa-check-circle mr-2"></i>{{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="alert alert-danger mb-4"><i class="fas fa-exclamation-circle mr-2"></i>{{ session('error') }}</div>
@endif

<div class="bg-white rounded-lg shadow overflow-hidden">
    <form id="bulkForm" method="POST" action="{{ route('admin.applications.bulk-action') }}">
        @csrf

        <div class="px-6 py-3 border-b bg-gray-50 flex items-center gap-3">
            <select name="action" class="form-select w-48" id="bulkAction">
                <option value="">Bulk Action</option>
                <option value="delete">Delete Selected</option>
                <option value="reprocess">Reprocess Selected</option>
            </select>
            <div id="keywordSetSelect" class="hidden">
                <select name="keyword_set_id" class="form-select w-48">
                    <option value="">Select Position</option>
                    @foreach($keywordSets as $keywordSet)
                        <option value="{{ $keywordSet->id }}">{{ $keywordSet->job_title }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="btn btn-outline text-sm"
                    onclick="return confirm('Apply this action to selected?')">
                Apply
            </button>
        </div>

        <table class="w-full">
            <thead class="bg-gray-50 border-b">
                <tr>
                    <th class="px-4 py-3 text-left w-8">
                        <input type="checkbox" id="selectAll" class="rounded">
                    </th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Applicant</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Position</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Status</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Match %</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Submitted</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($applications as $application)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3">
                        <input type="checkbox" name="applications[]"
                               value="{{ $application->id }}" class="rounded">
                    </td>
                    <td class="px-4 py-4">
                        <p class="font-medium text-gray-900 text-sm">{{ $application->applicant_name }}</p>
                        <p class="text-xs text-gray-500">{{ $application->applicant_email }}</p>
                        @if($application->phone)
                            <p class="text-xs text-gray-400">{{ $application->phone }}</p>
                        @endif
                    </td>
                    <td class="px-4 py-4 text-sm text-gray-700">
                        {{ $application->keywordSet->job_title ?? 'N/A' }}
                    </td>
                    <td class="px-4 py-4">
                        @switch($application->qualification_status)
                            @case('qualified')
                                <span class="badge badge-success">Qualified</span>
                                @break
                            @case('not_qualified')
                                <span class="badge badge-danger">Not Qualified</span>
                                @break
                            @case('processing')
                                <span class="badge badge-secondary">Processing</span>
                                @break
                            @case('failed')
                                <span class="badge badge-danger">Failed</span>
                                @break
                            @default
                                <span class="badge badge-warning">Pending</span>
                        @endswitch
                    </td>
                    <td class="px-4 py-4">
                        @if($application->match_percentage)
                            <span class="font-bold text-gray-900">{{ $application->match_percentage }}%</span>
                        @else
                            <span class="text-gray-400 text-sm">—</span>
                        @endif
                    </td>
                    <td class="px-4 py-4 text-sm text-gray-500">
                        {{ $application->created_at->format('M d, Y') }}
                        <p class="text-xs text-gray-400">{{ $application->created_at->format('H:i') }}</p>
                    </td>
                    <td class="px-4 py-4">
                        <div class="flex items-center gap-3">
                            <a href="{{ route('admin.applications.show', $application->id) }}"
                               class="text-blue-500 hover:text-blue-700" title="View">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('admin.applications.cv.download', $application->id) }}"
                               class="text-green-500 hover:text-green-700" title="Download CV">
                                <i class="fas fa-download"></i>
                            </a>
                            <form method="POST"
                                  action="{{ route('admin.applications.reprocess', $application->id) }}"
                                  class="inline">
                                @csrf
                                <button type="submit"
                                        class="text-yellow-500 hover:text-yellow-700"
                                        title="Reprocess"
                                        onclick="return confirm('Reprocess this CV?')">
                                    <i class="fas fa-redo"></i>
                                </button>
                            </form>
                            <form method="POST"
                                  action="{{ route('admin.applications.destroy', $application->id) }}"
                                  class="inline">
                                @csrf @method('DELETE')
                                <button type="submit"
                                        class="text-red-400 hover:text-red-600"
                                        title="Delete"
                                        onclick="return confirm('Delete this application?')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                        <i class="fas fa-file-alt text-4xl text-gray-300 mb-3 block"></i>
                        No applications found.
                        @if(request()->hasAny(['status','keyword_set_id','search']))
                            <a href="{{ route('admin.applications.index') }}" class="text-blue-600 ml-1">Clear filters</a>
                        @endif
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </form>

    <div class="px-6 py-4 border-t">
        {{ $applications->links() }}
    </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    document.getElementById('selectAll').addEventListener('change', function () {
        document.querySelectorAll('input[name="applications[]"]')
            .forEach(cb => cb.checked = this.checked);
    });

    document.getElementById('bulkAction').addEventListener('change', function () {
        const kss = document.getElementById('keywordSetSelect');
        if (this.value === 'reprocess') {
            kss.classList.remove('hidden');
            kss.querySelector('select').required = true;
        } else {
            kss.classList.add('hidden');
            kss.querySelector('select').required = false;
        }
    });
});
</script>
@endpush