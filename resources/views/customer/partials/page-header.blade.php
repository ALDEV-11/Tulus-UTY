@php
    $actionIcon = $actionIcon ?? 'bx bx-plus';
    $backIcon = $backIcon ?? 'bx bx-arrow-back';
@endphp

<div class="app-page-header mb-6">
    <div>
        <h4 class="fw-bold mb-1">{{ $title }}</h4>
        @isset($subtitle)
            <p class="text-body-secondary mb-0">{{ $subtitle }}</p>
        @endisset
    </div>

    @if (isset($actionRoute))
        <a href="{{ $actionRoute }}" class="btn btn-primary">
            <i class="icon-base {{ $actionIcon }} me-1" aria-hidden="true"></i>{{ $actionLabel }}
        </a>
    @elseif (isset($backRoute))
        <a href="{{ $backRoute }}" class="btn btn-outline-secondary">
            <i class="icon-base {{ $backIcon }} me-1" aria-hidden="true"></i>{{ $backLabel ?? 'Kembali' }}
        </a>
    @endif
</div>
