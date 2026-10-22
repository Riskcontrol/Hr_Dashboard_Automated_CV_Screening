@extends('layouts.app')

@section('content')

<div class="mb-6 flex justify-between items-center">
    <div>
        <h1 class="text-3xl font-bold text-gray-900">Pension Records</h1>
        <p class="text-gray-500 mt-1">Manage staff pension contributions and providers</p>
    </div>
    <button onclick="document.getElementById('add-modal').classList.remove('hidden')"
            class="btn btn-primary">
        <i class="fas fa-plus mr-2"></i> Add Record
    </button>
</div>

<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
    <div class="bg-white p-5 rounded-lg shadow flex items-center gap-4 stat-card-square">
        <div class="p-3 rounded-full bg-blue-100 text-blue-600"><i class="fas fa-shield-alt text-xl"></i></div>
        <div><p class="text-2xl font-bold">{{ $stats['total'] }}</p><p class="text-gray-500 text-sm">Total Records</p></div>
    </div>
    <div class="bg-white p-5 rounded-lg shadow flex items-center gap-4 stat-card-square">
        <div class="p-3 rounded-full bg-green-100 text-green-600"><i class="fas fa-check-circle text-xl"></i></div>
        <div><p class="text-2xl font-bold">{{ $stats['active'] }}</p><p class="text-gray-500 text-sm">Active</p></div>
    </div>
    <div class="bg-white p-5 rounded-lg shadow flex items-center gap-4 stat-card-square">
        <div class="p-3 rounded-full bg-yellow-100 text-yellow-600"><i class="fas fa-pause-circle text-xl"></i></div>
        <div><p class="text-2xl font-bold">{{ $stats['suspended'] }}</p><p class="text-gray-500 text-sm">Suspended</p></div>
    </div>
    <div class="bg-white p-5 rounded-lg shadow flex items-center gap-4 stat-card-square">
        <div class="p-3 rounded-full bg-red-100 text-red-600"><i class="fas fa-exclamation-circle text-xl"></i></div>
        <div><p class="text-2xl font-bold">{{ $stats['no_record'] }}</p><p class="text-gray-500 text-sm">No Record</p></div>
    </div>
</div>

<div class="bg-white rounded-lg shadow overflow-hidden">
    <table class="w-full">
        <thead class="bg-gray-50">
            <tr>
                <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Staff Member</th>
                <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Provider</th>
                <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase">RSA Number</th>
                <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Contributions</th>
                <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Status</th>
                <th class="px-6 py-3"></th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($records as $record)
            <tr class="hover:bg-gray-50 cursor-pointer"
                onclick="window.location='{{ route('admin.pension.show', $record) }}'">
                <td class="px-6 py-4 font-medium">{{ $record->staffProfile->full_name }}</td>
                <td class="px-6 py-4 text-sm">{{ $record->pension_provider ?: '—' }}</td>
                <td class="px-6 py-4 text-sm text-gray-500">{{ $record->rsa_number ?: '—' }}</td>
                <td class="px-6 py-4 text-sm">{{ $record->contribution_summary }}</td>
                <td class="px-6 py-4">
                    <span class="badge {{
                        $record->status === 'active' ? 'badge-success' :
                        ($record->status === 'suspended' ? 'badge-warning' : 'badge-secondary')
                    }}">{{ ucfirst($record->status) }}</span>
                </td>
                <td class="px-6 py-4">
                    <a href="{{ route('admin.pension.edit', $record) }}"
                       class="text-blue-500 hover:text-blue-700 text-sm" onclick="event.stopPropagation()">
                        <i class="fas fa-edit"></i>
                    </a>
                </td>
            </tr>
            @empty
            <tr><td colspan="6" class="px-6 py-8 text-center text-gray-500">No pension records yet.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">{{ $records->links() }}</div>

@if($staffWithoutPension->isNotEmpty())
<div class="mt-6 bg-yellow-50 border border-yellow-200 rounded-lg p-4">
    <p class="text-sm font-medium text-yellow-800 mb-2">
        <i class="fas fa-exclamation-triangle mr-2"></i>
        {{ $staffWithoutPension->count() }} active staff member(s) have no pension record:
    </p>
    <p class="text-sm text-yellow-700">{{ $staffWithoutPension->pluck('full_name')->join(', ') }}</p>
</div>
@endif

{{-- ADD RECORD MODAL --}}
<div id="add-modal" class="hidden fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-2xl mx-4 overflow-y-auto" style="max-height:90vh">
        <div class="flex justify-between items-center p-6 border-b">
            <h2 class="text-xl font-bold">Add Pension Record</h2>
            <button onclick="document.getElementById('add-modal').classList.add('hidden')"
                    class="text-gray-400 hover:text-gray-600 text-2xl">&times;</button>
        </div>
        <form method="POST" action="{{ route('admin.pension.store') }}" class="p-6 space-y-4">
            @csrf
            <div>
                <label class="form-label">Staff Member</label>
                <select name="staff_profile_id" class="form-select" required>
                    <option value="">Select staff member</option>
                    @foreach($staffWithoutPension as $s)
                        <option value="{{ $s->id }}">{{ $s->full_name }} ({{ $s->employee_id }})</option>
                    @endforeach
                </select>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="form-label">Pension Provider</label>
                    <input type="text" name="pension_provider" class="form-input" placeholder="e.g. ARM Pensions">
                </div>
                <div>
                    <label class="form-label">Pension PIN</label>
                    <input type="text" name="pension_pin" class="form-input">
                </div>
                <div>
                    <label class="form-label">RSA Number</label>
                    <input type="text" name="rsa_number" class="form-input">
                </div>
                <div>
                    <label class="form-label">Enrollment Date</label>
                    <input type="date" name="enrollment_date" class="form-input">
                </div>
                <div>
                    <label class="form-label">Employee Contribution</label>
                    <input type="number" step="0.01" name="employee_contribution" class="form-input" placeholder="8">
                </div>
                <div>
                    <label class="form-label">Employer Contribution</label>
                    <input type="number" step="0.01" name="employer_contribution" class="form-input" placeholder="10">
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="form-label">Contribution Type</label>
                    <select name="contribution_type" class="form-select" required>
                        <option value="percentage">Percentage (%)</option>
                        <option value="fixed">Fixed Amount (₦)</option>
                    </select>
                </div>
                <div>
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select" required>
                        <option value="active">Active</option>
                        <option value="suspended">Suspended</option>
                        <option value="exited">Exited</option>
                    </select>
                </div>
            </div>
            <div>
                <label class="form-label">Notes</label>
                <textarea name="notes" rows="2" class="form-textarea w-full"></textarea>
            </div>
            <div class="flex justify-end gap-3 pt-2 border-t">
                <button type="button"
                        onclick="document.getElementById('add-modal').classList.add('hidden')"
                        class="btn btn-outline">Cancel</button>
                <button type="submit" class="btn btn-primary">Save Record</button>
            </div>
        </form>
    </div>
</div>

@endsection