<nav aria-label="breadcrumb" class="mb-4">
    <ol class="breadcrumb breadcrumb-custom-icon mb-0">
        @unless (request()->routeIs('admin'))
            <li class="breadcrumb-item">
                <a href="{{ route('admin') }}">Dashboard</a>
                <i class="breadcrumb-icon icon-base bx bx-chevron-right align-middle" aria-hidden="true"></i>
            </li>
        @endunless
        <li class="breadcrumb-item active" aria-current="page">
            @isset($icon)
                <i class="icon-base {{ $icon }} me-1" aria-hidden="true"></i>
            @endisset
            {{ $title }}
        </li>
    </ol>
</nav>

<div class="admin-page-header d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-6">
    <div>
        <h4 class="fw-bold mb-1">{{ $title }}</h4>
        <p class="text-body-secondary mb-0"@isset($subtitleId) id="{{ $subtitleId }}"@endisset>{{ $subtitle }}</p>
    </div>

    @if (isset($actionModal))
        <button
            type="button"
            class="btn btn-primary"
            id="btn-action-{{ Str::slug($actionLabel) }}"
            data-bs-toggle="modal"
            data-bs-target="#{{ $actionModal }}">
            <i class="icon-base {{ $actionIcon ?? 'bx bx-plus' }} me-2" aria-hidden="true"></i>{{ $actionLabel }}
        </button>
    @elseif (isset($actionRoute))
        <a href="{{ $actionRoute }}" class="btn btn-primary" id="btn-action-{{ Str::slug($actionLabel) }}">
            <i class="icon-base {{ $actionIcon ?? 'bx bx-plus' }} me-2" aria-hidden="true"></i>{{ $actionLabel }}
        </a>
    @endif
</div>
