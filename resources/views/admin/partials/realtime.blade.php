@php
    $pusherEnabled = config('broadcasting.default') === 'pusher'
        && filled(config('broadcasting.connections.pusher.key'))
        && filled(config('broadcasting.connections.pusher.secret'))
        && filled(config('broadcasting.connections.pusher.app_id'));
@endphp

<style>
    .reservation-bell {
        position: relative;
    }

    .reservation-bell .badge {
        position: absolute;
        top: -0.15rem;
        right: -0.35rem;
        padding: 0.25em 0.45em;
        font-size: 0.625rem;
        line-height: 1;
    }

    .reservation-notification-menu {
        width: min(24rem, calc(100vw - 2rem));
    }

    .reservation-notification-list {
        max-height: 22rem;
        overflow-y: auto;
    }

    @media (prefers-reduced-motion: reduce) {
        .toast {
            transition: none;
        }
    }
</style>

<div class="toast-container position-fixed bottom-0 end-0 p-3" id="reservation-toast-container" aria-live="polite" aria-atomic="true"></div>

@if ($pusherEnabled)
    <script src="https://js.pusher.com/8.4.0/pusher.min.js"></script>
@endif

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const list = document.getElementById('reservation-notification-list');
        const badges = document.querySelectorAll('[data-reservation-badge]');
        const countLabels = document.querySelectorAll('[data-reservation-count-label]');
        const toastContainer = document.getElementById('reservation-toast-container');
        const notificationsUrl = @json(route('admin.reservasi.notifications'));
        const pusherKey = @json(config('broadcasting.connections.pusher.key'));
        const pusherCluster = @json(config('broadcasting.connections.pusher.options.cluster', 'mt1'));
        const pusherEnabled = @json($pusherEnabled);

        if (!list) {
            return;
        }

        const setCount = function (count) {
            badges.forEach(function (badge) {
                badge.textContent = count;
                badge.classList.toggle('d-none', count < 1);
            });

            countLabels.forEach(function (label) {
                label.textContent = count;
            });
        };

        const createItem = function (item) {
            const li = document.createElement('li');
            li.className = 'notification-item';
            li.dataset.reservationId = item.id;

            const link = document.createElement('a');
            link.className = 'dropdown-item py-3';
            link.href = item.url || '#';

            const wrapper = document.createElement('div');
            wrapper.className = 'd-flex align-items-start gap-3';

            const avatar = document.createElement('span');
            avatar.className = 'avatar avatar-sm flex-shrink-0';

            const avatarInner = document.createElement('span');
            avatarInner.className = 'avatar-initial rounded-circle bg-label-warning';

            const icon = document.createElement('i');
            icon.className = 'icon-base bx bx-calendar-plus';
            icon.setAttribute('aria-hidden', 'true');

            avatarInner.appendChild(icon);
            avatar.appendChild(avatarInner);

            const body = document.createElement('div');
            body.className = 'flex-grow-1';

            const name = document.createElement('div');
            name.className = 'fw-semibold small';
            name.textContent = item.pelanggan || 'Pelanggan';

            const schedule = document.createElement('div');
            schedule.className = 'text-body-secondary small';
            schedule.textContent = [item.kode, item.tanggal, item.jam].filter(Boolean).join(' · ');

            const vehicle = document.createElement('div');
            vehicle.className = 'text-body-secondary small';
            vehicle.textContent = item.kendaraan || '-';

            body.append(name, schedule, vehicle);
            wrapper.append(avatar, body);
            link.appendChild(wrapper);
            li.appendChild(link);

            return li;
        };

        const renderItems = function (items) {
            list.replaceChildren();

            if (!items.length) {
                const empty = document.createElement('li');
                empty.id = 'reservation-notification-empty';

                const text = document.createElement('div');
                text.className = 'dropdown-item-text text-center text-body-secondary small py-4';
                text.textContent = 'Belum ada reservasi yang menunggu konfirmasi.';

                empty.appendChild(text);
                list.appendChild(empty);

                return;
            }

            items.forEach(function (item) {
                list.appendChild(createItem(item));
            });
        };

        const prependItem = function (item) {
            if (list.querySelector('[data-reservation-id="' + item.id + '"]')) {
                return;
            }

            const empty = document.getElementById('reservation-notification-empty');

            if (empty) {
                empty.remove();
            }

            list.prepend(createItem(item));

            while (list.children.length > 5) {
                list.lastElementChild.remove();
            }
        };

        const showToast = function (item) {
            if (!toastContainer || !window.bootstrap) {
                return;
            }

            const toastEl = document.createElement('div');
            toastEl.className = 'toast';
            toastEl.setAttribute('role', 'status');
            toastEl.setAttribute('aria-live', 'polite');
            toastEl.setAttribute('aria-atomic', 'true');

            const header = document.createElement('div');
            header.className = 'toast-header';

            const title = document.createElement('strong');
            title.className = 'me-auto';
            title.textContent = 'Reservasi baru';

            const time = document.createElement('small');
            time.className = 'text-body-secondary me-2';
            time.textContent = 'Baru saja';

            const close = document.createElement('button');
            close.type = 'button';
            close.className = 'btn-close';
            close.setAttribute('data-bs-dismiss', 'toast');
            close.setAttribute('aria-label', 'Tutup');

            header.append(title, time, close);

            const body = document.createElement('div');
            body.className = 'toast-body';

            const customer = document.createElement('div');
            customer.className = 'fw-semibold';
            customer.textContent = (item.pelanggan || 'Pelanggan') + ' · ' + (item.kode || '');

            const schedule = document.createElement('div');
            schedule.className = 'small text-body-secondary mt-1';
            schedule.textContent = [item.tanggal, item.jam, item.kendaraan].filter(Boolean).join(' · ');

            const detail = document.createElement('a');
            detail.className = 'btn btn-sm btn-primary mt-3';
            detail.href = item.url || '#';
            detail.textContent = 'Lihat reservasi';

            body.append(customer, schedule, detail);
            toastEl.append(header, body);
            toastContainer.appendChild(toastEl);

            toastEl.addEventListener('hidden.bs.toast', function () {
                toastEl.remove();
            });

            new window.bootstrap.Toast(toastEl, { delay: 8000 }).show();
        };

        const handlePayload = function (item) {
            if (typeof item.pending_count !== 'undefined') {
                setCount(item.pending_count);
            }

            prependItem(item);
            showToast(item);
        };

        const poll = async function () {
            if (document.visibilityState === 'hidden') {
                return;
            }

            try {
                const response = await fetch(notificationsUrl, {
                    headers: { Accept: 'application/json' },
                    credentials: 'same-origin',
                    cache: 'no-store',
                });

                if (!response.ok) {
                    return;
                }

                const data = await response.json();
                setCount(data.count || 0);
                renderItems(Array.isArray(data.items) ? data.items : []);
            } catch (error) {
                console.warn('Gagal memuat notifikasi reservasi.', error);
            }
        };

        let pusherReady = false;

        if (pusherEnabled && window.Pusher) {
            try {
                const pusher = new window.Pusher(pusherKey, {
                    cluster: pusherCluster,
                    forceTLS: true,
                    authEndpoint: @json(url('broadcasting/auth')),
                    auth: {
                        headers: {
                            'X-CSRF-TOKEN': @json(csrf_token()),
                        },
                    },
                });

                const channel = pusher.subscribe('private-admin.reservations');

                channel.bind('reservation.created', handlePayload);
                channel.bind('pusher:subscription_error', function (status) {
                    console.warn('Langganan channel notifikasi reservasi gagal.', status);
                });

                pusher.connection.bind('error', function (error) {
                    console.warn('Koneksi Pusher bermasalah, notifikasi akan memakai polling.', error);
                });

                pusherReady = true;
            } catch (error) {
                console.warn('Pusher tidak dapat diinisialisasi, memakai polling.', error);
            }
        }

        setInterval(poll, pusherReady ? 60000 : 30000);
    });
</script>
