@extends('layouts.app')

@section('content')
<div class="mb-8">
    <div class="flex justify-between items-center">
        <h1 class="text-3xl font-bold text-gray-900">Edit Keyword Set</h1>
        <a href="{{ route('admin.keyword-sets.show', $keywordSet) }}" class="btn btn-outline">
            <i class="fas fa-arrow-left mr-2"></i>
            Back to Details
        </a>
    </div>
</div>

<div class="bg-white rounded-lg shadow">
    <div class="p-6">
        <form method="POST" action="{{ route('admin.keyword-sets.update', $keywordSet) }}">
            @csrf
            @method('PUT')
            
            <div class="mb-6">
                <label for="job_title" class="form-label">
                    Job Title <span class="text-red-500">*</span>
                </label>
                <input type="text" 
                       class="form-input @error('job_title') border-red-500 @enderror" 
                       id="job_title" 
                       name="job_title" 
                       value="{{ old('job_title', $keywordSet->job_title) }}" 
                       placeholder="e.g., Software Developer, Marketing Manager"
                       required>
                @error('job_title')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-6">
                <label for="keywords" class="form-label">
                    Keywords <span class="text-red-500">*</span>
                </label>
                <textarea class="form-input @error('keywords') border-red-500 @enderror" 
                          id="keywords" 
                          name="keywords" 
                          rows="4" 
                          placeholder="Enter keywords separated by commas. e.g., PHP, Laravel, MySQL, JavaScript"
                          required>{{ old('keywords', implode(', ', $keywordSet->keywords ?? [])) }}</textarea>
                <p class="text-gray-500 text-sm mt-1">
                    Separate each keyword with a comma. These keywords will be searched in uploaded CVs.
                </p>
                @error('keywords')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-6">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-input @error('description') border-red-500 @enderror" 
                          id="description" 
                          name="description" 
                          rows="3" 
                          placeholder="Optional description for this keyword set">{{ old('description', $keywordSet->description) }}</textarea>
                @error('description')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-6">
                <div class="flex items-center">
                    <input type="checkbox" 
                           id="is_active" 
                           name="is_active" 
                           value="1"
                           {{ old('is_active', $keywordSet->is_active) ? 'checked' : '' }}
                           class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                    <label for="is_active" class="ml-2 text-sm text-gray-700">
                        Active (available for CV screening)
                    </label>
                </div>
            </div>

            <div class="flex justify-end space-x-4">
                <a href="{{ route('admin.keyword-sets.show', $keywordSet) }}" class="btn btn-outline">Cancel</a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save mr-2"></i>
                    Update Keyword Set
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
