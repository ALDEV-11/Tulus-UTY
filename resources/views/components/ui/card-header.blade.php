@props([
    'title',
    'subtitle' => null,
    'subtitleId' => null,
    'icon' => 'bx bx-grid-alt',
    'color' => 'primary',
])

<div {{ $attributes->class(['card-header', 'app-card-header']) }}>
    <div class="app-card-header-main">
        <div class="app-card-heading">
            <span class="avatar flex-shrink-0">
                <span class="avatar-initial rounded-circle bg-label-{{ $color }}">
                    <i class="icon-base {{ $icon }}" aria-hidden="true"></i>
                </span>
            </span>
            <div>
                <h5>{{ $title }}</h5>
                @if ($subtitle)
                    <small class="text-body-secondary" @if ($subtitleId) id="{{ $subtitleId }}" @endif>{{ $subtitle }}</small>
                @endif
            </div>
        </div>

        @isset($actions)
            <div class="app-card-actions">{{ $actions }}</div>
        @endisset
    </div>

    @isset($toolbar)
        <div class="app-card-toolbar">{{ $toolbar }}</div>
    @endisset
</div>
