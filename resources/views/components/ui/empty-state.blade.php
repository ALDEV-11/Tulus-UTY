@props([
    'title',
    'description' => null,
    'icon' => null,
    'color' => 'secondary',
])

<div {{ $attributes->class(['app-empty-state', 'text-center']) }}>
    @if ($icon)
        <div class="avatar avatar-lg mb-3 mx-auto">
            <span class="avatar-initial rounded-circle bg-label-{{ $color }}">
                <i class="icon-base {{ $icon }} icon-lg" aria-hidden="true"></i>
            </span>
        </div>
    @endif

    <h6 class="mb-1">{{ $title }}</h6>

    @if ($description)
        <p class="text-body-secondary mb-0">{{ $description }}</p>
    @endif

    @isset($action)
        <div class="mt-4">{{ $action }}</div>
    @endisset
</div>
