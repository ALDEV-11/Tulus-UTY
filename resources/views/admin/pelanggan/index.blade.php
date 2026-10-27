@php
    $pageTitle = 'Pelanggan';
@endphp

@push('styles')
<style>
    #pelanggan-results {
        transition: opacity .15s ease-in-out;
    }

    #pelanggan-results[aria-busy="true"] {
        opacity: .55;
        pointer-events: none;
    }

    #pelanggan-search-group.is-loading #search-icon .bx {
        display: none;
    }

    #pelanggan-search-group.is-loading #search-icon::after {
        content: '';
        display: inline-block;
        width: 1rem;
        height: 1rem;
        border: 2px solid currentColor;
        border-right-color: transparent;
        border-radius: 50%;
        animation: pelanggan-search-spin .65s linear infinite;
    }

    @keyframes pelanggan-search-spin {
        to {
            transform: rotate(360deg);
        }
    }

    @media (prefers-reduced-motion: reduce) {
        #pelanggan-results {
            transition: none;
        }

        #pelanggan-search-group.is-loading #search-icon::after {
            animation: none;
        }
    }
</style>
@endpush

@include('admin.partials.page-head')

@if (session('new_password'))
    <div class="alert alert-warning alert-dismissible fade show" role="alert">
        <div class="d-flex align-items-center gap-3">
            <div class="flex-shrink-0">
                <span class="avatar avatar-sm">
                    <span class="avatar-initial rounded-circle bg-warning text-white">
                        <i class="icon-base bx bx-key" aria-hidden="true"></i>
                    </span>
                </span>
            </div>
            <div>
                <h6 class="alert-heading mb-1">Password baru untuk {{ session('new_password_name') }}</h6>
                <p class="mb-2 small">
                    Catat password sementara ini dan sampaikan kepada pelanggan melalui jalur aman.
                    Password tidak ditampilkan lagi setelah halaman ditinggalkan.
                </p>
                <code class="fs-6 d-inline-block bg-body p-2 rounded border">{{ session('new_password') }}</code>
            </div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
    </div>
@endif

@include('admin.partials.page-header', [
    'title' => 'Pelanggan',
    'subtitle' => $subtitle,
    'subtitleId' => 'pelanggan-subtitle',
    'icon' => 'bx bx-group',
    'actionModal' => 'modalTambahPelanggan',
    'actionLabel' => 'Tambah Pelanggan',
    'actionIcon' => 'bx bx-user-plus',
])

<div class="card admin-table">
    <div class="card-header py-4">
        <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3">
            <div class="d-flex align-items-center gap-3">
                <div class="avatar flex-shrink-0">
                    <span class="avatar-initial rounded-circle bg-label-success">
                        <i class="icon-base bx bx-group" aria-hidden="true"></i>
                    </span>
                </div>
                <div>
                    <h5 class="mb-0">Daftar Pelanggan</h5>
                    <small class="text-body-secondary">Kelola akun dan data pelanggan</small>
                </div>
            </div>

            <form method="GET" action="{{ route('admin.pelanggan.index') }}"
                  class="d-flex flex-column flex-sm-row gap-2 align-items-sm-center"
                  role="search" id="pelanggan-search-form">
                <div class="input-group input-group-merge" id="pelanggan-search-group">
                    <span class="input-group-text" id="search-icon"><i class="icon-base bx bx-search" aria-hidden="true"></i></span>
                    <label class="visually-hidden" for="q">Cari pelanggan</label>
                    <input
                        type="search"
                        class="form-control"
                        id="q"
                        name="q"
                        value="{{ $search }}"
                        placeholder="Nama, email, atau nomor HP"
                        maxlength="100"
                        autocomplete="off"
                        aria-describedby="search-icon"
                        aria-controls="pelanggan-results">
                </div>
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary flex-fill flex-sm-grow-0" id="btn-cari-pelanggan">
                        <i class="icon-base bx bx-search me-1" aria-hidden="true"></i>Cari
                    </button>
                    <a href="{{ route('admin.pelanggan.index') }}"
                       class="btn btn-outline-secondary {{ $search === '' ? 'd-none' : '' }}"
                       id="btn-reset-cari" data-reset-search>Reset</a>
                </div>
            </form>
        </div>
    </div>

    <div id="pelanggan-search-error" class="alert alert-danger d-none mx-3 mt-3 mb-0" role="alert">
        <i class="icon-base bx bx-error-circle me-1" aria-hidden="true"></i>
        Pencarian gagal. Periksa koneksi Anda lalu coba lagi.
    </div>

    <div id="pelanggan-results" role="region" aria-label="Hasil pencarian pelanggan">
        @include('admin.pelanggan._results')
    </div>
</div>

<p id="pelanggan-search-status" class="visually-hidden" role="status" aria-live="polite"></p>

<div id="pelanggan-edit-modals">
    @include('admin.pelanggan._edit-modals')
</div>

<div class="modal fade" id="modalTambahPelanggan" tabindex="-1" aria-labelledby="modalTambahPelangganLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <form method="POST" action="{{ route('admin.pelanggan.store') }}">
                <input type="hidden" name="_modal" value="modalTambahPelanggan">
                <input type="hidden" name="_record" value="">

                <div class="modal-header">
                    <div>
                        <h5 class="modal-title mb-1" id="modalTambahPelangganLabel">Tambah Pelanggan</h5>
                        <small class="text-body-secondary">Buat akun pelanggan baru.</small>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>

                <div class="modal-body">
                    @include('admin.pelanggan._form', [
                        'pelanggan' => $pelangganBaru,
                        'prefix' => 'tambah-pelanggan-',
                        'useOld' => old('_modal') === 'modalTambahPelanggan',
                        'inModal' => true,
                    ])
                </div>
            </form>
        </div>
    </div>
</div>

@include('admin.partials.modal-auto-open')

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.getElementById('pelanggan-search-form');
        const input = document.getElementById('q');
        const results = document.getElementById('pelanggan-results');
        const modals = document.getElementById('pelanggan-edit-modals');
        const subtitle = document.getElementById('pelanggan-subtitle');
        const reset = document.getElementById('btn-reset-cari');
        const group = document.getElementById('pelanggan-search-group');
        const status = document.getElementById('pelanggan-search-status');
        const errorBox = document.getElementById('pelanggan-search-error');

        if (!form || !input || !results || !modals) {
            return;
        }

        const endpoint = @json(route('admin.pelanggan.index'));
        let timer = null;
        let request = null;
        let lastRequested = input.value.trim();

        const setLoading = function (loading) {
            if (group) {
                group.classList.toggle('is-loading', loading);
            }

            results.setAttribute('aria-busy', loading ? 'true' : 'false');
        };

        const setError = function (visible) {
            if (errorBox) {
                errorBox.classList.toggle('d-none', !visible);
            }
        };

        const syncReset = function () {
            if (reset) {
                reset.classList.toggle('d-none', input.value.trim() === '');
            }
        };

        const syncUrl = function (query) {
            const url = new URL(window.location.href);

            if (query === '') {
                url.searchParams.delete('q');
            } else {
                url.searchParams.set('q', query);
            }

            url.searchParams.delete('page');
            window.history.replaceState({}, '', url);
        };

        const search = async function (value) {
            const term = value.trim();

            if (term === lastRequested) {
                return;
            }

            lastRequested = term;

            if (request) {
                request.abort();
            }

            const current = new AbortController();
            request = current;
            setError(false);
            setLoading(true);

            if (status) {
                status.textContent = 'Mencari pelanggan...';
            }

            const url = new URL(endpoint);
            if (term !== '') {
                url.searchParams.set('q', term);
            }

            try {
                const response = await fetch(url, {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    signal: current.signal,
                });

                if (!response.ok) {
                    throw new Error('Pencarian gagal.');
                }

                const data = await response.json();

                results.innerHTML = data.results;
                modals.innerHTML = data.modals;

                if (subtitle) {
                    subtitle.textContent = data.subtitle;
                }

                if (status) {
                    status.textContent = data.total + ' pelanggan ditemukan.';
                }

                setError(false);
                syncUrl(term);
            } catch (error) {
                if (error.name !== 'AbortError') {
                    lastRequested = null;
                    setError(true);

                    if (status) {
                        status.textContent = 'Pencarian gagal. Periksa koneksi Anda lalu coba lagi.';
                    }

                    console.error(error);
                }
            } finally {
                if (request === current) {
                    request = null;
                    setLoading(false);
                }
            }
        };

        input.addEventListener('input', function () {
            syncReset();
            clearTimeout(timer);
            timer = setTimeout(function () {
                search(input.value);
            }, 300);
        });

        form.addEventListener('submit', function (event) {
            event.preventDefault();
            clearTimeout(timer);
            search(input.value);
        });

        document.addEventListener('click', function (event) {
            const trigger = event.target.closest('[data-reset-search]');

            if (!trigger) {
                return;
            }

            event.preventDefault();
            clearTimeout(timer);
            input.value = '';
            syncReset();
            input.focus();
            search('');
        });

        syncReset();
    });
</script>
@endpush

@include('admin.partials.page-foot')
