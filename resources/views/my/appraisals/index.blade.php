{{--
    resources/views/my/appraisals/index.blade.php
    Staff self-service — shows ONLY the logged in staff member's own appraisals.
--}}
@extends('layouts.app')

@section('content')

<div class="mb-6">
    <h1 class="text-3xl font-bold text-gray-900">My Appraisals</h1>
    <p class="text-gray-500 mt-1">Your probation reviews and performance appraisals</p>
</div>

<div class="bg-white rounded-lg shadow overflow-hidden">
    <table class="w-full">
        <thead class="bg-gray-50">
            <tr>
                <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Type</th>
                <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Due Date</th>
                <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Status</th>
                <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Rating</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($appraisals as $appraisal)
                <tr class="hover:bg-gray-50 cursor-pointer" onclick="window.location='{{ route('my.appraisals.show', $appraisal) }}'">
                    <td class="px-6 py-4">{{ ucfirst(str_replace('_', ' ', $appraisal->appraisal_type)) }}</td>
                    <td class="px-6 py-4">{{ $appraisal->due_date?->format('M d, Y') }}</td>
                    <td class="px-6 py-4">
                        <span class="badge {{ $appraisal->status === 'completed' ? 'badge-success' : 'badge-warning' }}">
                            {{ ucfirst(str_replace('_', ' ', $appraisal->status)) }}
                        </span>
                    </td>
                    <td class="px-6 py-4">{{ $appraisal->overall_rating ? ucfirst(str_replace('_', ' ', $appraisal->overall_rating)) : '—' }}</td>
                </tr>
            @empty
                <tr><td colspan="4" class="px-6 py-8 text-center text-gray-500">No appraisals on file yet.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">{{ $appraisals->links() }}</div>

@endsection