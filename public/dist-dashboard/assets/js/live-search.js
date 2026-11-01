document.addEventListener('DOMContentLoaded', function () {
    const liveStatus = document.querySelector('[data-live-search-status]');

    const announce = function (message) {
        if (liveStatus) {
            liveStatus.textContent = message;
        }
    };

    document.querySelectorAll('[data-live-search-form]').forEach(function (form) {
        const input = form.querySelector('[data-live-search-input]');
        const group = form.querySelector('[data-live-search-group]');
        const endpoint = form.getAttribute('action') || window.location.href;
        const subtitleSelector = form.dataset.liveSearchSubtitle || '';
        const subtitle = subtitleSelector ? document.querySelector(subtitleSelector) : null;
        const targets = (form.dataset.liveSearchTargets || '')
            .split(',')
            .map(function (selector) {
                return selector.trim();
            })
            .filter(Boolean)
            .map(function (selector) {
                return document.querySelector(selector);
            })
            .filter(Boolean);

        if (!input || targets.length === 0) {
            return;
        }

        const buildUrl = function () {
            const url = new URL(endpoint, window.location.origin);
            const params = new URLSearchParams();

            new FormData(form).forEach(function (value, key) {
                if (typeof value !== 'string' || value.trim() === '') {
                    return;
                }

                params.append(key, value);
            });

            params.delete('page');
            url.search = params.toString();

            return url;
        };

        let timer = null;
        let controller = null;
        let lastRequest = buildUrl().search;

        const setLoading = function (loading) {
            if (group) {
                group.classList.toggle('is-loading', loading);
            }

            input.setAttribute('aria-busy', loading ? 'true' : 'false');

            targets.forEach(function (target) {
                if (target.classList.contains('live-search-results')) {
                    target.setAttribute('aria-busy', loading ? 'true' : 'false');
                }
            });
        };

        const search = async function () {
            const url = buildUrl();
            const requestKey = url.search;

            if (requestKey === lastRequest) {
                return;
            }

            lastRequest = requestKey;

            if (controller) {
                controller.abort();
            }

            const current = new AbortController();
            controller = current;

            input.classList.remove('is-invalid');
            input.removeAttribute('aria-invalid');
            setLoading(true);
            announce('Memuat hasil pencarian...');

            try {
                const response = await fetch(url, {
                    credentials: 'same-origin',
                    signal: current.signal,
                });

                if (!response.ok) {
                    throw new Error('Pencarian gagal.');
                }

                const html = await response.text();
                const documentBaru = new DOMParser().parseFromString(html, 'text/html');
                let replaced = 0;

                targets.forEach(function (target) {
                    if (!target.id) {
                        return;
                    }

                    const fresh = documentBaru.getElementById(target.id);

                    if (fresh) {
                        target.innerHTML = fresh.innerHTML;
                        replaced += 1;
                    }
                });

                if (replaced === 0) {
                    throw new Error('Hasil pencarian tidak ditemukan.');
                }

                if (subtitle && subtitleSelector) {
                    const freshSubtitle = documentBaru.querySelector(subtitleSelector);

                    if (freshSubtitle) {
                        subtitle.textContent = freshSubtitle.textContent;
                    }
                }

                const resetButton = document.querySelector('[data-live-search-reset]');
                const freshResetButton = documentBaru.querySelector('[data-live-search-reset]');

                if (resetButton && freshResetButton) {
                    resetButton.className = freshResetButton.className;
                    resetButton.setAttribute('href', freshResetButton.getAttribute('href'));
                }

                window.history.replaceState({}, '', url);
                announce(subtitle ? subtitle.textContent : 'Hasil pencarian diperbarui.');
            } catch (error) {
                if (error.name === 'AbortError') {
                    return;
                }

                lastRequest = null;
                input.classList.add('is-invalid');
                input.setAttribute('aria-invalid', 'true');
                announce('Pencarian gagal. Periksa koneksi Anda lalu coba lagi.');
                console.error(error);
            } finally {
                if (controller === current) {
                    controller = null;
                    setLoading(false);
                }
            }
        };

        input.addEventListener('input', function () {
            input.classList.remove('is-invalid');
            input.removeAttribute('aria-invalid');
            window.clearTimeout(timer);
            timer = window.setTimeout(search, 300);
        });

        form.addEventListener('submit', function (event) {
            event.preventDefault();
            window.clearTimeout(timer);
            search();
        });
    });
});
