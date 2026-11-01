<script src="{{ asset('dist-dashboard/assets/vendor/libs/sweetalert2/sweetalert2.all.min.js') }}"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const swalBootstrap = Swal.mixin({
            customClass: {
                confirmButton: 'btn btn-primary px-4',
                cancelButton: 'btn btn-outline-secondary px-4 me-2'
            },
            buttonsStyling: false
        });

        @if (session('status') || session('success'))
            swalBootstrap.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: {{ Js::from(session('status') ?? session('success')) }},
                timer: 3500,
                timerProgressBar: true,
                showConfirmButton: true
            });
        @endif

        @if (session('error'))
            swalBootstrap.fire({
                icon: 'error',
                title: 'Gagal!',
                text: {{ Js::from(session('error')) }},
                showConfirmButton: true
            });
        @endif

        @if (session('warning'))
            swalBootstrap.fire({
                icon: 'warning',
                title: 'Peringatan',
                text: {{ Js::from(session('warning')) }},
                showConfirmButton: true
            });
        @endif

        @if (session('info'))
            swalBootstrap.fire({
                icon: 'info',
                title: 'Informasi',
                text: {{ Js::from(session('info')) }},
                showConfirmButton: true
            });
        @endif

        @if ($errors->any())
            swalBootstrap.fire({
                icon: 'error',
                title: 'Terjadi Kesalahan',
                html: `{!! '<ul class="text-start mb-0 ps-3">' . implode('', array_map(fn($e) => '<li>' . e($e) . '</li>', $errors->all())) . '</ul>' !!}`,
                showConfirmButton: true
            });
        @endif
    });
</script>
