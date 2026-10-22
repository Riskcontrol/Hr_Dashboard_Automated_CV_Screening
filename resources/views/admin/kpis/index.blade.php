{{-- resources/views/admin/kpis/index.blade.php --}}
@extends('layouts.app')
@section('content')

<div class="mb-6 flex justify-between items-center">
    <div>
        <h1 class="text-3xl font-bold text-gray-900">KPI Tracking</h1>
        <p class="text-gray-500 mt-1">Monitor staff performance targets and scores</p>
    </div>
    <a href="{{ route('admin.kpis.create') }}" class="btn btn-primary">
        <i class="fas fa-plus mr-2"></i> Add KPI
    </a>
</div>

{{-- Stat Cards --}}
<div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-6">
    @php $cards = [['total','Total KPIs','chart-line','blue'],['excellent','Excellent','trophy','green'],['good','Good','thumbs-up','blue'],['poor','Poor/Critical','exclamation-triangle','red'],['avg_score','Avg Score %','percentage','purple']]; @endphp
    @foreach($cards as [$key, $label, $icon, $color])
    <div class="bg-white p-4 rounded-lg shadow flex items-center gap-3">
        <div class="p-2 rounded-full bg-{{ $color }}-100 text-{{ $color }}-600">
            <i class="fas fa-{{ $icon }}"></i>
        </div>
        <div>
            <p class="text-xl font-bold text-gray-900">{{ $stats[$key] }}{{ $key === 'avg_score' ? '%' : '' }}</p>
            <p class="text-gray-500 text-xs">{{ $label }}</p>
        </div>
    </div>
    @endforeach
</div>

{{-- Filters --}}
<div class="bg-white rounded-lg shadow mb-6 p-4">
    <form method="GET" action="{{ route('admin.kpis.index') }}" class="flex flex-wrap gap-3 items-end">
        <div class="flex-1 min-w-40">
            <label class="form-label">Search</label>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="KPI title or staff..." class="form-input">
        </div>
        <div class="min-w-36">
            <label class="form-label">Rating</label>
            <select name="rating" class="form-select">
                <option value="">All</option>
                @foreach(['excellent'=>'Excellent','good'=>'Good','average'=>'Average','poor'=>'Poor','critical'=>'Critical'] as $v=>$l)
                    <option value="{{ $v }}" {{ request('rating')==$v?'selected':'' }}>{{ $l }}</option>
                @endforeach
            </select>
        </div>
        <div class="min-w-36">
            <label class="form-label">Period</label>
            <select name="period_type" class="form-select">
                <option value="">All</option>
                <option value="monthly" {{ request('period_type')=='monthly'?'selected':'' }}>Monthly</option>
                <option value="quarterly" {{ request('period_type')=='quarterly'?'selected':'' }}>Quarterly</option>
                <option value="annually" {{ request('period_type')=='annually'?'selected':'' }}>Annual</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary"><i class="fas fa-search mr-2"></i> Filter</button>
        @if(request()->hasAny(['search','rating','period_type','status']))
            <a href="{{ route('admin.kpis.index') }}" class="btn btn-outline">Clear</a>
        @endif
    </form>
</div>

{{-- KPI Cards --}}
<div class="space-y-4">
@if($kpis->count())
    @foreach($kpis as $kpi)
    <a href="{{ route('admin.kpis.show', $kpi) }}"
       class="block bg-white rounded-lg shadow hover:shadow-md border border-transparent hover:border-primary-300 transition-all cursor-pointer">
        <div class="p-5">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div class="flex items-center gap-3 min-w-48">
                    <div class="w-10 h-10 rounded-full bg-primary-100 text-primary-700 flex items-center justify-center font-bold text-sm flex-shrink-0">
                        {{ $kpi->staffProfile->initials ?? '?' }}
                    </div>
                    <div>
                        <p class="font-semibold text-gray-900 text-sm">{{ $kpi->staffProfile->full_name ?? 'Unknown' }}</p>
                        <p class="text-xs text-gray-500">{{ $kpi->department ?? '' }}</p>
                    </div>
                </div>
                <div class="min-w-48">
                    <p class="text-xs text-gray-400 uppercase mb-1">KPI</p>
                    <p class="text-sm font-medium text-gray-900">{{ $kpi->title }}</p>
                    @if($kpi->category) <p class="text-xs text-gray-500">{{ $kpi->category }}</p> @endif
                </div>
                <div class="min-w-32">
                    <p class="text-xs text-gray-400 uppercase mb-1">Period</p>
                    <span class="badge badge-secondary">{{ $kpi->period_type_label }}</span>
                </div>
                <div class="min-w-32">
                    <p class="text-xs text-gray-400 uppercase mb-1">Target / Actual</p>
                    <p class="text-sm text-gray-900">{{ number_format($kpi->target_value,0) }} / {{ $kpi->actual_value !== null ? number_format($kpi->actual_value,0) : '—' }} {{ $kpi->unit }}</p>
                </div>
                <div class="min-w-40">
                    @if($kpi->score_percentage !== null)
                        <div class="flex items-center gap-2">
                            <div class="w-20 bg-gray-200 rounded-full h-2">
                                <div class="{{ $kpi->progress_color }} h-2 rounded-full" style="width:{{ $kpi->score_percentage }}%"></div>
                            </div>
                            <span class="text-sm font-bold text-gray-900">{{ $kpi->score_percentage }}%</span>
                        </div>
                    @else
                        <span class="text-gray-400 italic text-sm">Pending</span>
                    @endif
                </div>
                <div class="flex items-center gap-3">
                    @if($kpi->rating)
                        <span class="badge {{ $kpi->rating_color }}">{{ ucfirst($kpi->rating) }}</span>
                    @endif
                    <i class="fas fa-chevron-right text-gray-400 text-sm"></i>
                </div>
            </div>
        </div>
    </a>
    @endforeach
    <div class="mt-4">{{ $kpis->links() }}</div>
@else
    <div class="bg-white rounded-lg shadow text-center py-16">
        <i class="fas fa-chart-line text-gray-300 text-5xl mb-4"></i>
        <h3 class="text-lg font-medium text-gray-900 mb-2">No KPIs found</h3>
        <a href="{{ route('admin.kpis.create') }}" class="btn btn-primary"><i class="fas fa-plus mr-2"></i> Add KPI</a>
    </div>
@endif
</div>

@endsection