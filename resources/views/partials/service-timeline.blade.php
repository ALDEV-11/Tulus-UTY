@php
    $serviceSteps = \App\Enums\StatusServis::cases();
    $serviceStatus = $servis->status;
    $serviceStep = $serviceStatus->urutan();
    $serviceTotal = count($serviceSteps);
    $servicePercent = $serviceTotal > 1 ? (($serviceStep - 1) / ($serviceTotal - 1)) * 100 : 0;
    $compact = $compact ?? false;
@endphp

@if ($compact)
    <div>
        <div
            class="progress"
            style="height: .35rem;"
            role="progressbar"
            aria-label="Progres servis {{ $serviceStatus->label() }}"
            aria-valuenow="{{ $serviceStep }}"
            aria-valuemin="1"
            aria-valuemax="{{ $serviceTotal }}">
            <div class="progress-bar bg-{{ $serviceStatus->badge() }}" style="width: {{ $servicePercent }}%"></div>
        </div>
        <small class="text-body-secondary d-block mt-1">
            {{ $serviceStatus->label() }} &middot; langkah {{ $serviceStep }} dari {{ $serviceTotal }}
        </small>
    </div>
@else
    <div>
        <div
            class="progress"
            style="height: .5rem;"
            role="progressbar"
            aria-label="Progres servis {{ $serviceStatus->label() }}"
            aria-valuenow="{{ $serviceStep }}"
            aria-valuemin="1"
            aria-valuemax="{{ $serviceTotal }}">
            <div class="progress-bar bg-{{ $serviceStatus->badge() }}" style="width: {{ $servicePercent }}%"></div>
        </div>

        <ol class="list-unstyled d-flex justify-content-between gap-2 mt-2 mb-0">
            @foreach ($serviceSteps as $step)
                @php
                    $stepSelesai = $step->urutan() < $serviceStep;
                    $stepAktif = $step === $serviceStatus;
                @endphp
                <li class="flex-fill text-center small">
                    <span
                        class="d-inline-flex align-items-center justify-content-center rounded-circle bg-label-{{ $stepSelesai ? 'success' : ($stepAktif ? $step->badge() : 'secondary') }} mb-1"
                        style="width: 1.5rem; height: 1.5rem;"
                        aria-hidden="true">
                        @if ($stepSelesai)
                            <i class="icon-base bx bx-check"></i>
                        @else
                            {{ $step->urutan() }}
                        @endif
                    </span>
                    <span class="d-block {{ $stepAktif ? 'fw-semibold' : 'text-body-secondary' }}">
                        {{ $step->label() }}
                    </span>
                </li>
            @endforeach
        </ol>

        <span class="visually-hidden">Status servis saat ini: {{ $serviceStatus->label() }}.</span>
    </div>
@endif
