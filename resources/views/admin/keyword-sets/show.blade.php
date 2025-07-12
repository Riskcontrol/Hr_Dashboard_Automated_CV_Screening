{{-- @extends('layouts.app')

@section('content')
<div class="mb-8">
    <div class="flex justify-between items-center">
        <h1 class="text-3xl font-bold text-gray-900">{{ $keywordSet->job_title }}</h1>
        <div class="flex space-x-4">
            <a href="{{ route('admin.keyword-sets.edit', $keywordSet) }}" class="btn btn-primary">
                <i class="fas fa-edit mr-2"></i>
                Edit
            </a>
            <a href="{{ route('admin.keyword-sets.index') }}" class="btn btn-outline">
                <i class="fas fa-arrow-left mr-2"></i>
                Back to List
            </a>
        </div>
    </div>
</div>

<!-- Keyword Set Details -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Main Details -->
    <div class="lg:col-span-2">
        <div class="bg-white rounded-lg shadow">
            <div class="p-6 border-b border-gray-200">
                <h2 class="text-xl font-semibold text-gray-900">Job Details</h2>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Job Title</label>
                        <p class="text-lg font-semibold text-gray-900">{{ $keywordSet->job_title }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                        @if($keywordSet->is_active)
                            <span class="badge badge-success">Active</span>
                        @else
                            <span class="badge badge-secondary">Inactive</span>
                        @endif
                    </div>
                </div>

                @if($keywordSet->description)
                <div class="mt-6">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                    <p class="text-gray-900">{{ $keywordSet->description }}</p>
                </div>
                @endif

                <div class="mt-6">
                    <label class="block text-sm font-medium text-gray-700 mb-3">Required Keywords</label>
                    <div class="flex flex-wrap gap-2">
                        @foreach($keywordSet->keywords ?? [] as $keyword)
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                                {{ $keyword }}
                            </span>
                        @endforeach
                    </div>
                    <p class="text-sm text-gray-500 mt-2">
                        All {{ count($keywordSet->keywords ?? []) }} keywords must be found in a CV for qualification
                    </p>
                </div>

                <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-gray-500">
                    <div>
                        <strong>Created:</strong> {{ $keywordSet->created_at ? $keywordSet->created_at->format('M d, Y \a\t H:i') : 'N/A' }}
                    </div>
                    <div>
                        <strong>Last Updated:</strong> {{ $keywordSet->updated_at ? $keywordSet->updated_at->format('M d, Y \a\t H:i') : 'N/A' }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Panel -->
    <div class="space-y-6">
        <!-- Application Stats -->
        <div class="bg-white rounded-lg shadow">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Application Stats</h3>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <span class="text-gray-600">Total Applications</span>
                        <span class="text-2xl font-bold text-gray-900">{{ $stats['total_applications'] }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-green-600">Qualified</span>
                        <span class="text-xl font-semibold text-green-600">{{ $stats['qualified'] }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-red-600">Not Qualified</span>
                        <span class="text-xl font-semibold text-red-600">{{ $stats['not_qualified'] }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-yellow-600">Pending</span>
                        <span class="text-xl font-semibold text-yellow-600">{{ $stats['pending'] }}</span>
                    </div>
                </div>

                @if($stats['total_applications'] > 0)
                <div class="mt-4 pt-4 border-t border-gray-200">
                    <div class="flex items-center justify-between">
                        <span class="text-gray-600">Success Rate</span>
                        <span class="text-lg font-semibold text-blue-600">
                            {{ round(($stats['qualified'] / $stats['total_applications']) * 100, 1) }}%
                        </span>
                    </div>
                </div>
                @endif
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="bg-white rounded-lg shadow">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Quick Actions</h3>
            </div>
            <div class="p-6 space-y-3">
                <a href="{{ route('admin.keyword-sets.edit', $keywordSet) }}" 
                   class="w-full btn btn-primary flex items-center justify-center">
                    <i class="fas fa-edit mr-2"></i>
                    Edit Keywords
                </a>
                
                @if($keywordSet->is_active)
                    <button onclick="toggleStatus()" class="w-full btn btn-outline flex items-center justify-center">
                        <i class="fas fa-pause mr-2"></i>
                        Deactivate
                    </button>
                @else
                    <button onclick="toggleStatus()" class="w-full btn btn-success flex items-center justify-center">
                        <i class="fas fa-play mr-2"></i>
                        Activate
                    </button>
                @endif

                <button onclick="showDeleteModal()" class="w-full btn btn-danger flex items-center justify-center">
                    <i class="fas fa-trash mr-2"></i>
                    Delete Set
                </button>
            </div>
        </div>

        <!-- Information Panel -->
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-info-circle text-blue-600"></i>
                </div>
                <div class="ml-3">
                    <h4 class="text-sm font-medium text-blue-900">How it works</h4>
                    <div class="mt-2 text-sm text-blue-700">
                        <p>When applicants submit CVs for this position, their resume text will be scanned for all {{ count($keywordSet->keywords ?? []) }} keywords listed above.</p>
                        <p class="mt-2">Only applications containing ALL keywords will be marked as "Qualified".</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3 text-center">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                <i class="fas fa-exclamation-triangle text-red-600"></i>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mt-2">Delete Keyword Set</h3>
            <div class="mt-2 px-7 py-3">
                <p class="text-sm text-gray-500">
                    Are you sure you want to delete this keyword set? This action cannot be undone.
                </p>
            </div>
            <div class="items-center px-4 py-3">
                <form method="POST" action="{{ route('admin.keyword-sets.destroy', $keywordSet) }}" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="px-4 py-2 bg-red-500 text-white text-base font-medium rounded-md shadow-sm hover:bg-red-700">
                        Delete
                    </button>
                </form>
                <button onclick="hideDeleteModal()" class="ml-3 px-4 py-2 bg-gray-300 text-gray-800 text-base font-medium rounded-md shadow-sm hover:bg-gray-400">
                    Cancel
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function showDeleteModal() {
    document.getElementById('deleteModal').classList.remove('hidden');
}

function hideDeleteModal() {
    document.getElementById('deleteModal').classList.add('hidden');
}

function toggleStatus() {
    // This would be implemented with a form or AJAX call
    alert('Status toggle functionality can be implemented here');
}
</script>
@endpush
@endsection




 --}}

@extends('layouts.app')

@section('content')
<div class="mb-8">
    <div class="flex justify-content-between items-center">
        <h1 class="text-3xl font-bold text-gray-900">{{ $keywordSet->job_title }}</h1>
        <div class="flex space-x-4">
            <a href="{{ route('admin.keyword-sets.edit', $keywordSet) }}" class="btn btn-primary">
                <i class="fas fa-edit mr-2"></i>
                Edit
            </a>
            <a href="{{ route('admin.keyword-sets.index') }}" class="btn btn-outline">
                <i class="fas fa-arrow-left mr-2"></i>
                Back to List
            </a>
        </div>
    </div>
</div>

<!-- Keyword Set Details -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Main Details -->
    <div class="lg:col-span-2">
        <div class="bg-white rounded-lg shadow">
            <div class="p-6 border-b border-gray-200">
                <h2 class="text-xl font-semibold text-gray-900">Job Details</h2>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Job Title</label>
                        <p class="text-lg font-semibold text-gray-900">{{ $keywordSet->job_title }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                        @if($keywordSet->is_active)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                <i class="fas fa-check-circle mr-1"></i>
                                Active
                            </span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                <i class="fas fa-pause-circle mr-1"></i>
                                Inactive
                            </span>
                        @endif
                    </div>
                </div>

                @if($keywordSet->description)
                <div class="mt-6">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                    <p class="text-gray-900">{{ $keywordSet->description }}</p>
                </div>
                @endif

                <div class="mt-6">
                    <label class="block text-sm font-medium text-gray-700 mb-3">Required Keywords</label>
                    <div class="flex flex-wrap gap-2">
                        @foreach($keywordSet->keywords ?? [] as $keyword)
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                                {{ $keyword }}
                            </span>
                        @endforeach
                    </div>
                    <p class="text-sm text-gray-500 mt-2">
                        All {{ count($keywordSet->keywords ?? []) }} keywords must be found in a CV for qualification
                    </p>
                </div>

                <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-gray-500">
                    <div>
                        <strong>Created:</strong> {{ $keywordSet->created_at ? $keywordSet->created_at->format('M d, Y \a\t H:i') : 'N/A' }}
                    </div>
                    <div>
                        <strong>Last Updated:</strong> {{ $keywordSet->updated_at ? $keywordSet->updated_at->format('M d, Y \a\t H:i') : 'N/A' }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Panel -->
    <div class="space-y-6">
        <!-- Application Stats -->
        <div class="bg-white rounded-lg shadow">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Application Stats</h3>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <span class="text-gray-600">Total Applications</span>
                        <span class="text-2xl font-bold text-gray-900">{{ $stats['total_applications'] }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-green-600">Qualified</span>
                        <span class="text-xl font-semibold text-green-600">{{ $stats['qualified'] }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-red-600">Not Qualified</span>
                        <span class="text-xl font-semibold text-red-600">{{ $stats['not_qualified'] }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-yellow-600">Pending</span>
                        <span class="text-xl font-semibold text-yellow-600">{{ $stats['pending'] }}</span>
                    </div>
                </div>

                @if($stats['total_applications'] > 0)
                <div class="mt-4 pt-4 border-t border-gray-200">
                    <div class="flex items-center justify-between">
                        <span class="text-gray-600">Success Rate</span>
                        <span class="text-lg font-semibold text-blue-600">
                            {{ round(($stats['qualified'] / $stats['total_applications']) * 100, 1) }}%
                        </span>
                    </div>
                </div>
                @endif
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="bg-white rounded-lg shadow">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Quick Actions</h3>
            </div>
            <div class="p-6 space-y-3">
                <a href="{{ route('admin.keyword-sets.edit', $keywordSet) }}" 
                   class="w-full inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <i class="fas fa-edit mr-2"></i>
                    Edit Keywords
                </a>
                
                <!-- Toggle Status Form -->
                <form method="POST" action="{{ route('admin.keyword-sets.toggle-status', $keywordSet) }}" class="w-full">
                    @csrf
                    @method('PATCH')
                    @if($keywordSet->is_active)
                        <button type="submit" class="w-full inline-flex items-center justify-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <i class="fas fa-pause mr-2"></i>
                            Deactivate
                        </button>
                    @else
                        <button type="submit" class="w-full inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                            <i class="fas fa-play mr-2"></i>
                            Activate
                        </button>
                    @endif
                </form>

                <button onclick="showDeleteModal()" class="w-full inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                    <i class="fas fa-trash mr-2"></i>
                    Delete Set
                </button>
            </div>
        </div>

        <!-- Information Panel -->
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-info-circle text-blue-600"></i>
                </div>
                <div class="ml-3">
                    <h4 class="text-sm font-medium text-blue-900">How it works</h4>
                    <div class="mt-2 text-sm text-blue-700">
                        <p>When applicants submit CVs for this position, their resume text will be scanned for all {{ count($keywordSet->keywords ?? []) }} keywords listed above.</p>
                        <p class="mt-2">Only applications containing ALL keywords will be marked as "Qualified".</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Improved Delete Confirmation Modal -->
<div id="deleteModal" class="hidden fixed inset-0 bg-black bg-opacity-50 overflow-y-auto h-full w-full z-50 flex items-center justify-center">
    <div class="relative bg-white rounded-lg shadow-xl max-w-md w-full mx-4">
        <!-- Modal Header -->
        <div class="flex items-center justify-between p-6 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Delete Keyword Set</h3>
            <button onclick="hideDeleteModal()" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <!-- Modal Body -->
        <div class="p-6">
            <div class="flex items-center mb-4">
                <div class="flex-shrink-0 w-12 h-12 rounded-full bg-red-100 flex items-center justify-center">
                    <i class="fas fa-exclamation-triangle text-red-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-600">
                        Are you sure you want to delete "<strong>{{ $keywordSet->job_title }}</strong>"? This action cannot be undone.
                    </p>
                </div>
            </div>
            
            @if($stats['total_applications'] > 0)
            <div class="bg-yellow-50 border border-yellow-200 rounded-md p-3 mb-4">
                <div class="flex">
                    <i class="fas fa-exclamation-triangle text-yellow-600 mt-0.5 mr-2"></i>
                    <div class="text-sm text-yellow-700">
                        <strong>Warning:</strong> This keyword set has {{ $stats['total_applications'] }} associated applications that will also be affected.
                    </div>
                </div>
            </div>
            @endif
        </div>
        
        <!-- Modal Footer -->
        <div class="flex items-center justify-end space-x-3 p-6 border-t border-gray-200">
            <button onclick="hideDeleteModal()" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                Cancel
            </button>
            <form method="POST" action="{{ route('admin.keyword-sets.destroy', $keywordSet) }}" class="inline">
                @csrf
                @method('DELETE')
                <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-red-600 border border-transparent rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                    <i class="fas fa-trash mr-1"></i>
                    Delete Keyword Set
                </button>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
function showDeleteModal() {
    document.getElementById('deleteModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden'; // Prevent background scrolling
}

function hideDeleteModal() {
    document.getElementById('deleteModal').classList.add('hidden');
    document.body.style.overflow = 'auto'; // Restore scrolling
}

// Close modal when clicking outside
document.getElementById('deleteModal').addEventListener('click', function(e) {
    if (e.target === this) {
        hideDeleteModal();
    }
});

// Close modal with Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        hideDeleteModal();
    }
});
</script>
@endpush
@endsection