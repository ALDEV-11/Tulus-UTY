@props([
    'items' => [],
    'title' => 'Ringkasan Status',
    'subtitle' => null,
    'ariaLabel' => 'Filter status',
    'total' => null,
    'totalLabel' => 'Total',
])

<div {{ $attributes->class(['card', 'app-status-summary-card']) }}>
    <div class="app-status-summary-head">
        <div>
            <h6 class="mb-0">{{ $title }}</h6>
            @if ($subtitle)
                <small class="text-body-secondary">{{ $subtitle }}</small>
            @endif
        </div>

        @if ($total !== null)
            <span class="badge bg-label-primary">
                {{ $totalLabel }} {{ number_format($total, 0, ',', '.') }}
            </span>
        @endif
    </div>

    <nav class="app-status-summary" aria-label="{{ $ariaLabel }}">
        @foreach ($items as $item)
            <a href="{{ $item['url'] }}"
               class="app-status-summary-item {{ ! empty($item['active']) ? 'is-active' : '' }}"
               @if (! empty($item['active'])) aria-current="true" @endif>
                <span class="app-status-summary-label">
                    <span class="app-status-dot bg-{{ $item['color'] ?? 'secondary' }}" aria-hidden="true"></span>
                    {{ $item['label'] }}
                </span>
                <span class="app-status-summary-value">{{ number_format($item['count'] ?? 0, 0, ',', '.') }}</span>
            </a>
        @endforeach
    </nav>
</div>
