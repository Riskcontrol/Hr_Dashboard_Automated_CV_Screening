@extends('layouts.app')

@section('content')
<div class="mb-8">
    <div class="flex justify-between items-center">
        <h1 class="text-3xl font-bold text-gray-900">Dashboard</h1>
        <div class="flex space-x-4">
            <a href="{{ route('admin.keyword-sets.create') }}" class="btn btn-primary">
                <i class="fas fa-plus mr-2"></i>
                Add Position
            </a>
        </div>
    </div>
</div>

<!-- Stats Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6 mb-8">
    <div class="bg-white p-6 rounded-lg shadow">
        <div class="flex items-center">
            <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                <i class="fas fa-file-alt text-2xl"></i>
            </div>
            <div class="ml-4">
                <h3 class="text-2xl font-bold text-gray-900">{{ $stats['total_applications'] }}</h3>
                <p class="text-gray-600">Total Applications</p>
            </div>
        </div>
    </div>

    <div class="bg-white p-6 rounded-lg shadow">
        <div class="flex items-center">
            <div class="p-3 rounded-full bg-green-100 text-green-600">
                <i class="fas fa-check-circle text-2xl"></i>
            </div>
            <div class="ml-4">
                <h3 class="text-2xl font-bold text-gray-900">{{ $stats['qualified_applications'] }}</h3>
                <p class="text-gray-600">Qualified</p>
            </div>
        </div>
    </div>

    <div class="bg-white p-6 rounded-lg shadow">
        <div class="flex items-center">
            <div class="p-3 rounded-full bg-sky-100 text-sky-600">
                <i class="fas fa-star-half-alt text-2xl"></i>
            </div>
            <div class="ml-4">
                <h3 class="text-2xl font-bold text-gray-900">{{ $stats['fairly_qualified_applications'] }}</h3>
                <p class="text-gray-600">Fairly Qualified</p>
            </div>
        </div>
    </div>

    <div class="bg-white p-6 rounded-lg shadow">
        <div class="flex items-center">
            <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
                <i class="fas fa-clock text-2xl"></i>
            </div>
            <div class="ml-4">
                <h3 class="text-2xl font-bold text-gray-900">{{ $stats['pending_applications'] }}</h3>
                <p class="text-gray-600">Pending</p>
            </div>
        </div>
    </div>

    <div class="bg-white p-6 rounded-lg shadow">
        <div class="flex items-center">
            <div class="p-3 rounded-full bg-purple-100 text-purple-600">
                <i class="fas fa-chart-line text-2xl"></i>
            </div>
            <div class="ml-4">
                <h3 class="text-2xl font-bold text-gray-900">{{ $stats['qualification_rate'] }}%</h3>
                <p class="text-gray-600">Qualification Rate</p>
            </div>
        </div>
    </div>
</div>

<!-- Recent Applications -->
@if($recentApplications->count() > 0)
<div class="bg-white rounded-lg shadow mb-8">
    <div class="p-6 border-b border-gray-200">
        <h2 class="text-xl font-semibold text-gray-900">Recent Applications</h2>
    </div>
    <div class="overflow-y-auto h-96">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Position</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Qualification Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Processing Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">CV</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Submitted</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($recentApplications as $app)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                        {{ $app->applicant_name }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ $app->applicant_email }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ $app->keywordSet->job_title ?? 'N/A' }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($app->qualification_status === 'Qualified')
                            <span class="badge badge-success">Qualified</span>
                        @elseif($app->qualification_status === 'Fairly Qualified')
                            <span class="badge badge-info">Fairly Qualified</span>
                        @elseif($app->qualification_status === 'Not Qualified')
                            <span class="badge badge-danger">Not Qualified</span>
                        @elseif($app->qualification_status === 'pending')
                            <span class="badge badge-warning">Pending</span>
                        @else
                            <span class="badge badge-secondary">{{ ucfirst($app->qualification_status) }}</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($app->processing_status === 'completed')
                            <span class="badge badge-success">Completed</span>
                        @elseif($app->processing_status === 'failed')
                            <span class="badge badge-danger">Failed</span>
                        @elseif($app->processing_status === 'processing')
                            <span class="badge badge-info">Processing</span>
                        @else
                            <span class="badge badge-warning">Pending</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        <a href="{{ route('admin.applications.cv.download', $app->id) }}" class="text-blue-600 hover:text-blue-800">
                            Download
                        </a>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ $app->created_at->diffForHumans() }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif

<!-- Available Positions -->
<div class="bg-white rounded-lg shadow">
    <div class="p-6 border-b border-gray-200">
        <div class="flex justify-between items-center">
            <h2 class="text-xl font-semibold text-gray-900">Available Positions</h2>
            <a href="{{ route('admin.keyword-sets.index') }}" class="text-sm text-blue-600 hover:text-blue-800">
                View All →
            </a>
        </div>
    </div>
    <div class="p-6">
        @php
            try {
                $keywordSets = \App\Models\KeywordSet::where('is_active', true)->take(6)->get();
            } catch (\Exception $e) {
                $keywordSets = collect();
            }
        @endphp

        @if($keywordSets->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($keywordSets as $set)
                    <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                        <h3 class="font-semibold text-lg text-gray-900 mb-2">{{ $set->job_title }}</h3>
                        @if($set->description)
                            <p class="text-gray-600 text-sm mb-3">{{ Str::limit($set->description, 80) }}</p>
                        @endif
                        <div class="flex flex-wrap gap-1">
                            @foreach(array_slice($set->keywords ?? [], 0, 3) as $keyword)
                                <span class="badge badge-secondary">{{ $keyword }}</span>
                            @endforeach
                            @if(count($set->keywords ?? []) > 3)
                                <span class="text-gray-500 text-xs self-center">+{{ count($set->keywords) - 3 }} more</span>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-8">
                <i class="fas fa-briefcase text-gray-400 text-4xl mb-4"></i>
                <h3 class="text-lg font-medium text-gray-900 mb-2">No positions available</h3>
                <p class="text-gray-500 mb-4">Create keyword sets to start screening applications.</p>
                <a href="{{ route('admin.keyword-sets.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus mr-2"></i>
                    Create First Position
                </a>
            </div>
        @endif
    </div>
</div>

<!-- Quick Actions -->
<div class="mt-8">
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-6">
        <div class="flex items-start">
            <div class="flex-shrink-0">
                <i class="fas fa-info-circle text-blue-600 text-xl"></i>
            </div>
            <div class="ml-4">
                <h3 class="text-lg font-medium text-blue-900 mb-2">Getting Started</h3>
                <div class="text-blue-700 space-y-2">
                    <p>• <strong>Create keyword sets</strong> for different job positions</p>
                    <p>• <strong>Share the application URL</strong>: <code class="bg-blue-100 px-2 py-1 rounded">{{ url('/') }}</code></p>
                    <p>• <strong>Monitor applications</strong> and review qualified candidates</p>
                </div>
                <div class="mt-4 flex space-x-4">
                    <a href="{{ route('admin.keyword-sets.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus mr-2"></i>
                        Create Position
                    </a>
                    <a href="{{ url('/') }}" target="_blank" class="btn btn-outline">
                        <i class="fas fa-external-link-alt mr-2"></i>
                        View Application Form
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
