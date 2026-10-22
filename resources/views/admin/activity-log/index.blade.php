@extends('layouts.app')

@section('content')

<div class="mb-6 flex justify-between items-center">
    <div>
        <h1 class="text-3xl font-bold text-gray-900">Activity Log</h1>
        <p class="text-gray-500 mt-1">Every action taken in the system — cannot be edited or deleted</p>
    </div>
    <div class="text-xs text-gray-400 bg-gray-100 px-3 py-2 rounded-lg">
        <i class="fas fa-lock mr-1"></i> Read-only audit trail
    </div>
</div>

<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
    <div class="bg-white p-5 rounded-lg shadow flex items-center gap-4">
        <div class="p-3 rounded-full bg-blue-100 text-blue-600"><i class="fas fa-list text-xl"></i></div>
        <div><p class="text-2xl font-bold">{{ $stats['total'] }}</p><p class="text-gray-500 text-sm">Total Entries</p></div>
    </div>
    <div class="bg-white p-5 rounded-lg shadow flex items-center gap-4">
        <div class="p-3 rounded-full bg-green-100 text-green-600"><i class="fas fa-calendar-day text-xl"></i></div>
        <div><p class="text-2xl font-bold">{{ $stats['today'] }}</p><p class="text-gray-500 text-sm">Today</p></div>
    </div>
    <div class="bg-white p-5 rounded-lg shadow flex items-center gap-4">
        <div class="p-3 rounded-full bg-purple-100 text-purple-600"><i class="fas fa-users text-xl"></i></div>
        <div><p class="text-2xl font-bold">{{ $stats['unique_users'] }}</p><p class="text-gray-500 text-sm">Active Today</p></div>
    </div>
    <div class="bg-white p-5 rounded-lg shadow flex items-center gap-4">
        <div class="p-3 rounded-full bg-orange-100 text-orange-600"><i class="fas fa-clock text-xl"></i></div>
        <div>
            <p class="text-xs font-bold text-gray-900 truncate max-w-24">{{ $stats['last_action'] ?? '—' }}</p>
            <p class="text-gray-500 text-sm">Last Action</p>
        </div>
    </div>
</div>

<div class="bg-white rounded-lg shadow p-4 mb-6">
    <form method="GET" class="grid grid-cols-2 md:grid-cols-5 gap-3 items-end">
        <div>
            <label class="form-label">Search</label>
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Name, description..." class="form-input">
        </div>
        <div>
            <label class="form-label">Module</label>
            <select name="module" class="form-select">
                <option value="">All Modules</option>
                @foreach($modules as $mod)
                    <option value="{{ $mod }}" {{ request('module') == $mod ? 'selected' : '' }}>{{ $mod }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="form-label">Action</label>
            <select name="action" class="form-select">
                <option value="">All Actions</option>
                @foreach(['created','updated','deleted','approved','rejected','sent','uploaded','login','logout','password'] as $act)
                    <option value="{{ $act }}" {{ request('action') == $act ? 'selected' : '' }}>{{ ucfirst($act) }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="form-label">Date</label>
            <input type="date" name="date" value="{{ request('date') }}" class="form-input">
        </div>
        <div class="flex gap-2">
            <button type="submit" class="btn btn-primary flex-1">
                <i class="fas fa-search mr-1"></i> Filter
            </button>
            @if(request()->hasAny(['search','module','action','date']))
                <a href="{{ route('admin.activity-log.index') }}" class="btn btn-outline">Clear</a>
            @endif
        </div>
    </form>
</div>

<div class="bg-white rounded-lg shadow overflow-hidden">
    @if($logs->count())
        <table class="w-full">
            <thead class="bg-gray-50 border-b">
                <tr>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Timestamp</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase">User</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Action</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Module</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Description</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase">IP</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach($logs as $log)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 whitespace-nowrap">
                        <p class="text-xs font-medium text-gray-700">{{ $log->created_at->format('M d, Y') }}</p>
                        <p class="text-xs text-gray-400">{{ $log->created_at->format('H:i:s') }}</p>
                    </td>
                    <td class="px-4 py-3">
                        <p class="text-sm font-medium text-gray-900">{{ $log->user_name }}</p>
                        <p class="text-xs text-gray-400">{{ ucfirst(str_replace('_', ' ', $log->user_role)) }}</p>
                    </td>
                    <td class="px-4 py-3">
                        <span class="badge {{ $log->action_color }}">
                            <i class="fas {{ $log->action_icon }} mr-1"></i>{{ ucfirst($log->action) }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-sm text-gray-600">{{ $log->module }}</td>
                    <td class="px-4 py-3 text-sm text-gray-700 max-w-xs">{{ $log->description }}</td>
                    <td class="px-4 py-3 text-xs text-gray-400">{{ $log->ip_address }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div class="px-6 py-4 border-t">{{ $logs->withQueryString()->links() }}</div>
    @else
        <div class="text-center py-16">
            <i class="fas fa-list text-gray-300 text-5xl mb-4 block"></i>
            <p class="text-gray-500">No activity logged yet.</p>
        </div>
    @endif
</div>

@endsection