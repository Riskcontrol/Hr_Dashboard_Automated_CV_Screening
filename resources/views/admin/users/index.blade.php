@extends('layouts.app')

@section('content')

<div class="mb-6 flex justify-between items-center">
    <div>
        <h1 class="text-3xl font-bold text-gray-900">User Accounts</h1>
        <p class="text-gray-500 mt-1">Approve registrations and manage staff login accounts</p>
    </div>
    <a href="{{ route('admin.users.own-password') }}" class="btn btn-outline">
        <i class="fas fa-key mr-2"></i> Change My Password
    </a>
</div>

<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
    <div class="bg-white p-5 rounded-lg shadow flex items-center gap-4">
        <div class="p-3 rounded-full bg-blue-100 text-blue-600"><i class="fas fa-users text-xl"></i></div>
        <div><p class="text-2xl font-bold">{{ $stats['total'] }}</p><p class="text-gray-500 text-sm">Total</p></div>
    </div>
    <div class="bg-white p-5 rounded-lg shadow flex items-center gap-4 {{ $stats['pending'] > 0 ? 'border-l-4 border-yellow-400' : '' }}">
        <div class="p-3 rounded-full bg-yellow-100 text-yellow-600"><i class="fas fa-clock text-xl"></i></div>
        <div><p class="text-2xl font-bold">{{ $stats['pending'] }}</p><p class="text-gray-500 text-sm">Pending</p></div>
    </div>
    <div class="bg-white p-5 rounded-lg shadow flex items-center gap-4">
        <div class="p-3 rounded-full bg-green-100 text-green-600"><i class="fas fa-check-circle text-xl"></i></div>
        <div><p class="text-2xl font-bold">{{ $stats['active'] }}</p><p class="text-gray-500 text-sm">Active</p></div>
    </div>
    <div class="bg-white p-5 rounded-lg shadow flex items-center gap-4">
        <div class="p-3 rounded-full bg-red-100 text-red-600"><i class="fas fa-ban text-xl"></i></div>
        <div><p class="text-2xl font-bold">{{ $stats['suspended'] }}</p><p class="text-gray-500 text-sm">Suspended</p></div>
    </div>
</div>

<div class="bg-white rounded-lg shadow p-4 mb-6">
    <form method="GET" class="flex gap-3 items-end flex-wrap">
        <div class="flex-1 min-w-48">
            <label class="form-label">Search</label>
            <input type="text" name="search" class="form-input"
                   placeholder="Name or email..." value="{{ request('search') }}">
        </div>
        <div class="w-40">
            <label class="form-label">Status</label>
            <select name="status" class="form-select">
                <option value="">All</option>
                <option value="pending"   {{ request('status') == 'pending'   ? 'selected' : '' }}>Pending</option>
                <option value="active"    {{ request('status') == 'active'    ? 'selected' : '' }}>Active</option>
                <option value="suspended" {{ request('status') == 'suspended' ? 'selected' : '' }}>Suspended</option>
            </select>
        </div>
        <div class="flex gap-2">
            <button type="submit" class="btn btn-primary"><i class="fas fa-search mr-1"></i> Filter</button>
            @if(request()->hasAny(['search','status']))
                <a href="{{ route('admin.users.index') }}" class="btn btn-outline">Clear</a>
            @endif
        </div>
    </form>
</div>

@if($stats['pending'] > 0)
<div class="bg-yellow-50 border border-yellow-300 rounded-lg p-4 mb-6 flex items-center gap-3">
    <i class="fas fa-exclamation-triangle text-yellow-600 text-xl"></i>
    <div>
        <p class="font-semibold text-yellow-800">{{ $stats['pending'] }} account(s) waiting for approval</p>
        <p class="text-sm text-yellow-700">These staff members registered but cannot log in until you approve them.</p>
    </div>
</div>
@endif

@if(session('success'))
    <div class="alert alert-success mb-4"><i class="fas fa-check-circle mr-2"></i>{{ session('success') }}</div>
@endif

<div class="bg-white rounded-lg shadow overflow-hidden">
    <table class="w-full">
        <thead class="bg-gray-50 border-b">
            <tr>
                <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Name</th>
                <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Email</th>
                <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Role</th>
                <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Status</th>
                <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Registered</th>
                <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Approved By</th>
                <th class="px-6 py-3"></th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($users as $user)
            <tr class="hover:bg-gray-50 {{ $user->status === 'pending' ? 'bg-yellow-50' : '' }}">
                <td class="px-6 py-4">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-primary-100 text-primary-700 flex items-center justify-center font-bold text-xs flex-shrink-0">
                            {{ strtoupper(substr($user->name, 0, 2)) }}
                        </div>
                        <p class="font-medium text-gray-900">{{ $user->name }}</p>
                    </div>
                </td>
                <td class="px-6 py-4 text-sm text-gray-600">{{ $user->email }}</td>
                <td class="px-6 py-4">
                    <span class="badge {{ $user->role === 'admin' ? 'badge-danger' : ($user->role === 'hr_manager' ? 'badge-warning' : 'badge-secondary') }}">
                        {{ ucfirst(str_replace('_', ' ', $user->role)) }}
                    </span>
                </td>
                <td class="px-6 py-4">
                    <span class="badge {{ $user->status === 'active' ? 'badge-success' : ($user->status === 'pending' ? 'badge-warning' : 'badge-danger') }}">
                        {{ ucfirst($user->status) }}
                    </span>
                </td>
                <td class="px-6 py-4 text-sm text-gray-500">{{ $user->created_at->format('M d, Y') }}</td>
                <td class="px-6 py-4 text-sm text-gray-500">
                    @if($user->approved_at)
                        {{ $user->approved_by }}
                        <p class="text-xs text-gray-400">{{ $user->approved_at->format('M d, Y') }}</p>
                    @else
                        <span class="text-gray-300">—</span>
                    @endif
                </td>
                <td class="px-6 py-4">
                    <div class="flex items-center gap-3">
                        @if($user->status === 'pending')
                        <form method="POST" action="{{ route('admin.users.approve', $user) }}">
                            @csrf
                            <button type="submit" class="text-green-600 hover:text-green-800 text-sm font-medium"
                                    onclick="return confirm('Approve {{ $user->name }}?')">
                                <i class="fas fa-check mr-1"></i> Approve
                            </button>
                        </form>
                        @endif

                        @if($user->status === 'active' && $user->id !== auth()->id())
                        <form method="POST" action="{{ route('admin.users.suspend', $user) }}">
                            @csrf
                            <button type="submit" class="text-red-400 hover:text-red-600"
                                    onclick="return confirm('Suspend {{ $user->name }}?')" title="Suspend">
                                <i class="fas fa-ban"></i>
                            </button>
                        </form>
                        @endif

                        @if($user->status === 'suspended')
                        <form method="POST" action="{{ route('admin.users.reactivate', $user) }}">
                            @csrf
                            <button type="submit" class="text-green-500 hover:text-green-700" title="Reactivate">
                                <i class="fas fa-undo"></i>
                            </button>
                        </form>
                        @endif

                        <a href="{{ route('admin.users.edit-password', $user) }}"
                           class="text-blue-500 hover:text-blue-700" title="Change Password">
                            <i class="fas fa-key"></i>
                        </a>
                    </div>
                </td>
            </tr>
            @empty
            <tr><td colspan="7" class="px-6 py-12 text-center text-gray-500">No accounts found.</td></tr>
            @endforelse
        </tbody>
    </table>
    <div class="px-6 py-4 border-t">{{ $users->withQueryString()->links() }}</div>
</div>

@endsection