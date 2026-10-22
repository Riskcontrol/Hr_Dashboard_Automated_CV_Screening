{{-- resources/views/admin/kpis/show.blade.php --}}
@extends('layouts.app')
@section('content')

<div class="mb-6 flex items-center justify-between">
    <div class="flex items-center gap-4">
        <a href="{{ route('admin.kpis.index') }}" class="text-gray-500 hover:text-gray-700"><i class="fas fa-arrow-left"></i></a>
        <h1 class="text-3xl font-bold text-gray-900">KPI Details</h1>
    </div>
    <div class="flex gap-3">
        <a href="{{ route('admin.kpis.edit', $kpi) }}" class="btn btn-primary"><i class="fas fa-edit mr-2"></i> Edit</a>
        <form method="POST" action="{{ route('admin.kpis.destroy', $kpi) }}" onsubmit="return confirm('Delete this KPI?')">
            @csrf @method('DELETE')
            <button type="submit" class="btn btn-danger"><i class="fas fa-trash mr-2"></i> Delete</button>
        </form>
    </div>
</div>

{{-- Scorecard --}}
<div class="bg-white rounded-lg shadow mb-6 p-6">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 items-center">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 rounded-full bg-primary-100 text-primary-700 flex items-center justify-center text-xl font-bold">
                {{ $kpi->staffProfile->initials ?? '?' }}
            </div>
            <div>
                <p class="font-bold text-gray-900">{{ $kpi->staffProfile->full_name }}</p>
                <p class="text-gray-500 text-sm">{{ $kpi->staffProfile->job_title ?? '' }}</p>
                <a href="{{ route('admin.staff.show', $kpi->staffProfile) }}" class="text-blue-600 hover:underline text-xs">View Profile →</a>
            </div>
        </div>
        <div class="text-center">
            <p class="text-xl font-bold text-gray-900">{{ $kpi->title }}</p>
            <p class="text-gray-500 text-sm">{{ $kpi->period_type_label }} · {{ $kpi->period_start->format('M Y') }} — {{ $kpi->period_end->format('M Y') }}</p>
            @if($kpi->category) <span class="badge badge-secondary mt-1">{{ $kpi->category }}</span> @endif
        </div>
        <div class="text-center">
            @if($kpi->score_percentage !== null)
                <div class="text-5xl font-bold {{ $kpi->score_percentage >= 90 ? 'text-green-600' : ($kpi->score_percentage >= 70 ? 'text-blue-600' : ($kpi->score_percentage >= 50 ? 'text-yellow-600' : 'text-red-600')) }}">
                    {{ $kpi->score_percentage }}%
                </div>
                <span class="badge {{ $kpi->rating_color }} mt-2 text-sm px-4 py-1">{{ ucfirst($kpi->rating) }}</span>
            @else
                <div class="text-4xl font-bold text-gray-300">—</div>
                <p class="text-gray-400 text-sm mt-1">Actual value not entered yet</p>
            @endif
        </div>
    </div>
    @if($kpi->score_percentage !== null)
    <div class="mt-6">
        <div class="w-full bg-gray-200 rounded-full h-4">
            <div class="{{ $kpi->progress_color }} h-4 rounded-full" style="width:{{ $kpi->score_percentage }}%"></div>
        </div>
        <div class="flex justify-between text-sm mt-1">
            <span class="text-gray-500">Actual: {{ number_format($kpi->actual_value,0) }} {{ $kpi->unit }}</span>
            <span class="text-gray-500">Target: {{ number_format($kpi->target_value,0) }} {{ $kpi->unit }}</span>
        </div>
    </div>
    @endif
</div>

{{-- Details --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
            <h3 class="font-semibold text-gray-900"><i class="fas fa-info-circle mr-2 text-primary-600"></i>KPI Information</h3>
        </div>
        <div class="p-6 space-y-3">
            @foreach(['Department'=>$kpi->department,'Period'=>$kpi->period_type_label,'Start'=>$kpi->period_start->format('M d, Y'),'End'=>$kpi->period_end->format('M d, Y'),'Status'=>ucfirst($kpi->status),'Reviewed By'=>$kpi->reviewed_by] as $l=>$v)
            <div class="flex justify-between py-1 border-b border-gray-100 last:border-0">
                <span class="text-gray-500 text-sm">{{ $l }}</span>
                <span class="text-gray-900 text-sm font-medium">{{ $v ?? '—' }}</span>
            </div>
            @endforeach
            @if($kpi->notes)<div class="pt-2"><p class="text-gray-500 text-xs uppercase mb-1">Notes</p><p class="text-sm text-gray-900">{{ $kpi->notes }}</p></div>@endif
        </div>
    </div>
    @if($staffKpis->count())
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
            <h3 class="font-semibold text-gray-900"><i class="fas fa-history mr-2 text-primary-600"></i>Other KPIs for {{ $kpi->staffProfile->full_name }}</h3>
        </div>
        <div class="divide-y divide-gray-100">
            @foreach($staffKpis as $other)
            <a href="{{ route('admin.kpis.show', $other) }}" class="px-6 py-3 flex items-center justify-between hover:bg-gray-50 block">
                <div>
                    <p class="text-sm font-medium text-gray-900">{{ $other->title }}</p>
                    <p class="text-xs text-gray-500">{{ $other->period_type_label }} · {{ $other->period_start->format('M Y') }}</p>
                </div>
                <div class="flex items-center gap-2">
                    @if($other->score_percentage !== null)<span class="text-sm font-bold text-gray-900">{{ $other->score_percentage }}%</span>@endif
                    @if($other->rating)<span class="badge {{ $other->rating_color }}">{{ ucfirst($other->rating) }}</span>@endif
                </div>
            </a>
            @endforeach
        </div>
    </div>
    @endif
</div>

@endsection