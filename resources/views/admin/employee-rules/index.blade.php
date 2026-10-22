@extends('layouts.app')

@section('content')

<div class="mb-6 flex justify-between items-center">
    <div>
        <h1 class="text-3xl font-bold text-gray-900">Employee Rules PDF</h1>
        <p class="text-gray-500 mt-1">Upload and manage the Employee Handbook and Code of Conduct</p>
    </div>
    <button onclick="document.getElementById('upload-modal').classList.remove('hidden')"
            class="btn btn-primary">
        <i class="fas fa-upload mr-2"></i> Upload Document
    </button>
</div>

<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
    <div class="bg-white p-5 rounded-lg shadow flex items-center gap-4 stat-card-square">
        <div class="p-3 rounded-full bg-blue-100 text-blue-600"><i class="fas fa-book text-xl"></i></div>
        <div><p class="text-2xl font-bold text-gray-900">{{ $handbook->count() }}</p><p class="text-gray-500 text-sm">Handbooks</p></div>
    </div>
    <div class="bg-white p-5 rounded-lg shadow flex items-center gap-4 stat-card-square">
        <div class="p-3 rounded-full bg-green-100 text-green-600"><i class="fas fa-gavel text-xl"></i></div>
        <div><p class="text-2xl font-bold text-gray-900">{{ $codeOfConduct->count() }}</p><p class="text-gray-500 text-sm">Code of Conduct</p></div>
    </div>
    <div class="bg-white p-5 rounded-lg shadow flex items-center gap-4 stat-card-square">
        <div class="p-3 rounded-full bg-purple-100 text-purple-600"><i class="fas fa-users text-xl"></i></div>
        <div><p class="text-2xl font-bold text-gray-900">{{ $staffCount }}</p><p class="text-gray-500 text-sm">Active Staff</p></div>
    </div>
    <div class="bg-white p-5 rounded-lg shadow flex items-center gap-4 stat-card-square">
        <div class="p-3 rounded-full bg-yellow-100 text-yellow-600"><i class="fas fa-bell text-xl"></i></div>
        <div><p class="text-2xl font-bold text-gray-900">{{ $linkedUserCount }}</p><p class="text-gray-500 text-sm">Staff Notifiable</p></div>
    </div>
</div>

{{-- Handbook section --}}
<div class="bg-white rounded-lg shadow mb-6 overflow-hidden">
    <div class="px-6 py-4 border-b bg-gray-50">
        <h2 class="font-bold text-gray-800"><i class="fas fa-book mr-2 text-blue-500"></i>Employee Handbook</h2>
    </div>
    @if($handbook->isEmpty())
        <div class="p-8 text-center text-gray-500">
            <i class="fas fa-book text-4xl mb-3 text-gray-300"></i>
            <p>No handbook uploaded yet.</p>
        </div>
    @else
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Title</th>
                    <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase">File</th>
                    <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Uploaded By</th>
                    <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Date</th>
                    <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Notified</th>
                    <th class="px-6 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach($handbook as $doc)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 font-medium">{{ $doc->title }}</td>
                    <td class="px-6 py-4 text-sm text-gray-500">{{ $doc->file_name }}</td>
                    <td class="px-6 py-4 text-sm">{{ $doc->uploaded_by }}</td>
                    <td class="px-6 py-4 text-sm">{{ $doc->created_at->format('M d, Y') }}</td>
                    <td class="px-6 py-4">
                        @if($doc->notified_at)
                            <span class="badge badge-success">Sent {{ $doc->notified_at->format('M d') }}</span>
                        @else
                            <span class="badge badge-secondary">Not sent</span>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex gap-2">
                            <a href="{{ route('admin.employee-rules.download', $doc) }}"
                               class="text-blue-500 hover:text-blue-700 text-sm" target="_blank">
                                <i class="fas fa-download"></i>
                            </a>
                            <form method="POST" action="{{ route('admin.employee-rules.notify', $doc) }}">
                                @csrf
                                <button type="submit" class="text-green-500 hover:text-green-700 text-sm"
                                        title="Re-send notification to all staff">
                                    <i class="fas fa-bell"></i>
                                </button>
                            </form>
                            <form method="POST" action="{{ route('admin.employee-rules.destroy', $doc) }}"
                                  onsubmit="return confirm('Delete this document?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-red-400 hover:text-red-600 text-sm">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>

{{-- Code of Conduct section --}}
<div class="bg-white rounded-lg shadow mb-6 overflow-hidden">
    <div class="px-6 py-4 border-b bg-gray-50">
        <h2 class="font-bold text-gray-800"><i class="fas fa-gavel mr-2 text-green-500"></i>Code of Conduct</h2>
    </div>
    @if($codeOfConduct->isEmpty())
        <div class="p-8 text-center text-gray-500">
            <i class="fas fa-gavel text-4xl mb-3 text-gray-300"></i>
            <p>No Code of Conduct uploaded yet.</p>
        </div>
    @else
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Title</th>
                    <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase">File</th>
                    <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Uploaded By</th>
                    <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Date</th>
                    <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Notified</th>
                    <th class="px-6 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach($codeOfConduct as $doc)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 font-medium">{{ $doc->title }}</td>
                    <td class="px-6 py-4 text-sm text-gray-500">{{ $doc->file_name }}</td>
                    <td class="px-6 py-4 text-sm">{{ $doc->uploaded_by }}</td>
                    <td class="px-6 py-4 text-sm">{{ $doc->created_at->format('M d, Y') }}</td>
                    <td class="px-6 py-4">
                        @if($doc->notified_at)
                            <span class="badge badge-success">Sent {{ $doc->notified_at->format('M d') }}</span>
                        @else
                            <span class="badge badge-secondary">Not sent</span>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex gap-2">
                            <a href="{{ route('admin.employee-rules.download', $doc) }}"
                               class="text-blue-500 hover:text-blue-700 text-sm" target="_blank">
                                <i class="fas fa-download"></i>
                            </a>
                            <form method="POST" action="{{ route('admin.employee-rules.notify', $doc) }}">
                                @csrf
                                <button type="submit" class="text-green-500 hover:text-green-700 text-sm"
                                        title="Re-send notification to all staff">
                                    <i class="fas fa-bell"></i>
                                </button>
                            </form>
                            <form method="POST" action="{{ route('admin.employee-rules.destroy', $doc) }}"
                                  onsubmit="return confirm('Delete this document?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-red-400 hover:text-red-600 text-sm">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>

{{-- UPLOAD MODAL --}}
<div id="upload-modal" class="hidden fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-lg mx-4">
        <div class="flex justify-between items-center p-6 border-b">
            <h2 class="text-xl font-bold text-gray-900">Upload Document</h2>
            <button onclick="document.getElementById('upload-modal').classList.add('hidden')"
                    class="text-gray-400 hover:text-gray-600 text-2xl">&times;</button>
        </div>
        <form method="POST" action="{{ route('admin.employee-rules.store') }}"
              enctype="multipart/form-data" class="p-6 space-y-4">
            @csrf
            <div>
                <label class="form-label">Document Type</label>
                <select name="type" class="form-select" required>
                    <option value="handbook">Employee Handbook</option>
                    <option value="code_of_conduct">Code of Conduct</option>
                </select>
            </div>
            <div>
                <label class="form-label">Document Title</label>
                <input type="text" name="title" class="form-input"
                       placeholder="e.g. Employee Handbook 2026" required>
            </div>
            <div>
                <label class="form-label">PDF File (max 20MB)</label>
                <input type="file" name="document" accept=".pdf" class="form-input" required>
            </div>
            <div class="flex items-center gap-2">
                <input type="checkbox" name="notify_staff" id="notify_staff" value="1" checked>
                <label for="notify_staff" class="text-sm text-gray-700">
                    Notify all staff by email when uploaded
                </label>
            </div>
            <div class="flex justify-end gap-3 pt-2 border-t">
                <button type="button"
                        onclick="document.getElementById('upload-modal').classList.add('hidden')"
                        class="btn btn-outline">Cancel</button>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-upload mr-2"></i> Upload
                </button>
            </div>
        </form>
    </div>
</div>

@endsection