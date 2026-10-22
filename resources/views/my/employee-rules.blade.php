@extends('layouts.app')

@section('content')

<div class="mb-6">
    <h1 class="text-3xl font-bold text-gray-900">Employee Rules PDF</h1>
    <p class="text-gray-500 mt-1">Your Employee Handbook and Code of Conduct documents</p>
</div>

{{-- Employee Handbook --}}
<div class="bg-white rounded-lg shadow mb-6 overflow-hidden">
    <div class="px-6 py-4 border-b bg-gray-50 flex items-center gap-2">
        <i class="fas fa-book text-blue-500"></i>
        <h2 class="font-bold text-gray-800">Employee Handbook</h2>
    </div>

    @if($handbook->isEmpty())
        <div class="p-8 text-center text-gray-500">
            <i class="fas fa-book text-4xl mb-3 text-gray-300"></i>
            <p>No handbook has been uploaded by HR yet.</p>
        </div>
    @else
        <div class="divide-y divide-gray-100">
            @foreach($handbook as $doc)
            <div class="px-6 py-4 flex items-center justify-between hover:bg-gray-50">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-red-100 text-red-600 rounded">
                        <i class="fas fa-file-pdf text-xl"></i>
                    </div>
                    <div>
                        <p class="font-medium text-gray-900">{{ $doc->title }}</p>
                        <p class="text-sm text-gray-500">
                            Uploaded by {{ $doc->uploaded_by }}
                            on {{ $doc->created_at->format('M d, Y') }}
                        </p>
                    </div>
                </div>
                <a href="{{ route('my.employee-rules.download', $doc) }}"
                   target="_blank"
                   class="btn btn-outline text-sm">
                    <i class="fas fa-download mr-2"></i> View / Download
                </a>
            </div>
            @endforeach
        </div>
    @endif
</div>

{{-- Code of Conduct --}}
<div class="bg-white rounded-lg shadow overflow-hidden">
    <div class="px-6 py-4 border-b bg-gray-50 flex items-center gap-2">
        <i class="fas fa-gavel text-green-500"></i>
        <h2 class="font-bold text-gray-800">Code of Conduct</h2>
    </div>

    @if($codeOfConduct->isEmpty())
        <div class="p-8 text-center text-gray-500">
            <i class="fas fa-gavel text-4xl mb-3 text-gray-300"></i>
            <p>No Code of Conduct has been uploaded by HR yet.</p>
        </div>
    @else
        <div class="divide-y divide-gray-100">
            @foreach($codeOfConduct as $doc)
            <div class="px-6 py-4 flex items-center justify-between hover:bg-gray-50">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-red-100 text-red-600 rounded">
                        <i class="fas fa-file-pdf text-xl"></i>
                    </div>
                    <div>
                        <p class="font-medium text-gray-900">{{ $doc->title }}</p>
                        <p class="text-sm text-gray-500">
                            Uploaded by {{ $doc->uploaded_by }}
                            on {{ $doc->created_at->format('M d, Y') }}
                        </p>
                    </div>
                </div>
                <a href="{{ route('my.employee-rules.download', $doc) }}"
                   target="_blank"
                   class="btn btn-outline text-sm">
                    <i class="fas fa-download mr-2"></i> View / Download
                </a>
            </div>
            @endforeach
        </div>
    @endif
</div>

@endsection