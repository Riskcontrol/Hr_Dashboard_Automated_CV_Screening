@extends('layouts.app')

@section('content')

<div class="mb-6 flex justify-between items-center">
    <div>
        <h1 class="text-3xl font-bold text-gray-900">Job Positions</h1>
        <p class="text-gray-500 mt-1">Manage keyword sets used for CV screening</p>
    </div>
    <a href="{{ route('admin.keyword-sets.create') }}" class="btn btn-primary">
        <i class="fas fa-plus mr-2"></i> Create New Position
    </a>
</div>

@if(session('success'))
    <div class="alert alert-success mb-4"><i class="fas fa-check-circle mr-2"></i>{{ session('success') }}</div>
@endif

@if($keywordSets->count() > 0)

<div class="bg-white rounded-lg shadow overflow-hidden">
    <table class="w-full">
        <thead class="bg-gray-50 border-b">
            <tr>
                <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Job Title</th>
                <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Keywords</th>
                <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Status</th>
                <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Applications</th>
                <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Created By</th>
                <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Created</th>
                <th class="px-6 py-3"></th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @foreach($keywordSets as $set)
            <tr class="hover:bg-gray-50">
                <td class="px-6 py-4">
                    <p class="font-medium text-gray-900">{{ $set->job_title }}</p>
                    @if($set->description)
                        <p class="text-xs text-gray-500 mt-1">{{ Str::limit($set->description, 60) }}</p>
                    @endif
                </td>
                <td class="px-6 py-4">
                    <div class="flex flex-wrap gap-1">
                        @foreach(array_slice($set->keywords, 0, 3) as $keyword)
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-700">
                                {{ $keyword }}
                            </span>
                        @endforeach
                        @if(count($set->keywords) > 3)
                            <span class="text-xs text-gray-400">+{{ count($set->keywords) - 3 }} more</span>
                        @endif
                    </div>
                </td>
                <td class="px-6 py-4">
                    @if($set->is_active)
                        <span class="badge badge-success">Active</span>
                    @else
                        <span class="badge badge-secondary">Inactive</span>
                    @endif
                </td>
                <td class="px-6 py-4">
                    <span class="font-bold text-gray-900">{{ $set->applications_count ?? 0 }}</span>
                </td>
                <td class="px-6 py-4 text-sm text-gray-600">{{ $set->creator->name }}</td>
                <td class="px-6 py-4 text-sm text-gray-500">{{ $set->created_at->format('M d, Y') }}</td>
                <td class="px-6 py-4">
                    <div class="flex items-center gap-3">
                        <a href="{{ route('admin.keyword-sets.show', $set) }}"
                           class="text-blue-500 hover:text-blue-700" title="View">
                            <i class="fas fa-eye"></i>
                        </a>
                        <a href="{{ route('admin.keyword-sets.edit', $set) }}"
                           class="text-yellow-500 hover:text-yellow-700" title="Edit">
                            <i class="fas fa-edit"></i>
                        </a>
                        <form method="POST" action="{{ route('admin.keyword-sets.destroy', $set) }}"
                              class="inline" onsubmit="return confirm('Delete this keyword set?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-red-400 hover:text-red-600" title="Delete">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <div class="px-6 py-4 border-t">
        {{ $keywordSets->links() }}
    </div>
</div>

@else
<div class="bg-white rounded-lg shadow p-16 text-center">
    <i class="fas fa-tags text-5xl text-gray-300 mb-4 block"></i>
    <h3 class="text-lg font-medium text-gray-900 mb-2">No job positions yet</h3>
    <p class="text-gray-500 mb-6">Create your first keyword set to start screening CVs.</p>
    <a href="{{ route('admin.keyword-sets.create') }}" class="btn btn-primary">
        <i class="fas fa-plus mr-2"></i> Create First Position
    </a>
</div>
@endif

@endsection