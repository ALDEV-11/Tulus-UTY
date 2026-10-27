@php
    $icon = $icon ?? match ($parentTitle ?? null) {
        'Layanan' => 'bx bx-wrench',
        'Merk Motor' => 'bx bx-car',
        'Slot Waktu' => 'bx bx-calendar',
        'Pelanggan' => 'bx bx-group',
        default => null,
    };
@endphp

<nav aria-label="breadcrumb" class="mb-4">
    <ol class="breadcrumb breadcrumb-custom-icon mb-0">
        <li class="breadcrumb-item">
            <a href="{{ route('admin') }}">Dashboard</a>
            <i class="breadcrumb-icon icon-base bx bx-chevron-right align-middle" aria-hidden="true"></i>
        </li>
        <li class="breadcrumb-item">
            @if ($icon)
                <i class="icon-base {{ $icon }} me-1" aria-hidden="true"></i>
            @endif
            <a href="{{ $backRoute }}">{{ $parentTitle ?? $title }}</a>
            <i class="breadcrumb-icon icon-base bx bx-chevron-right align-middle" aria-hidden="true"></i>
        </li>
        <li class="breadcrumb-item active" aria-current="page">{{ $currentTitle ?? $title }}</li>
    </ol>
</nav>

<div class="admin-page-header d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-6">
    <div>
        <h4 class="fw-bold mb-1">{{ $title }}</h4>
        <p class="text-body-secondary mb-0">{{ $subtitle }}</p>
    </div>

    <a href="{{ $backRoute }}" class="btn btn-outline-secondary" id="btn-back-{{ Str::slug($title) }}">
        <i class="icon-base bx bx-arrow-back me-2" aria-hidden="true"></i>Kembali
    </a>
</div>
