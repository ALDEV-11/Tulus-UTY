@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const modalId = @json(old('_modal'));

        if (!modalId) {
            return;
        }

        const modalElement = document.getElementById(modalId);

        if (!modalElement || !window.bootstrap) {
            return;
        }

        modalElement.addEventListener('shown.bs.modal', function () {
            const invalidField = modalElement.querySelector('.is-invalid');

            if (invalidField) {
                invalidField.focus();
            }
        });

        window.bootstrap.Modal.getOrCreateInstance(modalElement).show();
    });
</script>
@endpush
