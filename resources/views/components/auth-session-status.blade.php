@props(['status'])

@if ($status)
    <div {{ $attributes->merge(['class' => 'rounded-lg border border-blue-200 bg-blue-50 p-4 mb-4']) }}>
        <div class="flex items-start gap-3">
            <i class="fas fa-info-circle text-blue-500 mt-0.5"></i>
            <p class="text-sm text-blue-800 font-medium">{{ $status }}</p>
        </div>
    </div>
@endif