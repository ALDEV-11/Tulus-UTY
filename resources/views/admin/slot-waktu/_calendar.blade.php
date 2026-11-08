@php
    $weekdayLabels = ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'];
    $today = now();
    $prevBulan = $month->copy()->subMonthNoOverflow()->format('Y-m');
    $nextBulan = $month->copy()->addMonthNoOverflow()->format('Y-m');
@endphp

<div class="card slot-calendar-card mb-6">
    <x-ui.card-header
        :title="$month->translatedFormat('F Y')"
        subtitle="Klik tanggal untuk melihat dan mengelola slot."
        icon="bx bx-calendar"
        color="warning">
        <x-slot:actions>
            <a
                href="{{ route('admin.slot-waktu.index', ['bulan' => $prevBulan]) }}"
                class="btn btn-sm btn-icon btn-outline-secondary"
                aria-label="Bulan sebelumnya"
                title="Bulan sebelumnya">
                <i class="icon-base bx bx-chevron-left" aria-hidden="true"></i>
            </a>
            <a
                href="{{ route('admin.slot-waktu.index', ['bulan' => $today->format('Y-m'), 'tanggal' => $today->toDateString()]) }}#slot-tanggal"
                class="btn btn-sm btn-outline-primary">
                <i class="icon-base bx bx-calendar-check me-1" aria-hidden="true"></i>Hari ini
            </a>
            <a
                href="{{ route('admin.slot-waktu.index', ['bulan' => $nextBulan]) }}"
                class="btn btn-sm btn-icon btn-outline-secondary"
                aria-label="Bulan berikutnya"
                title="Bulan berikutnya">
                <i class="icon-base bx bx-chevron-right" aria-hidden="true"></i>
            </a>
        </x-slot:actions>
    </x-ui.card-header>

    <div class="card-body">
        <div class="d-flex flex-wrap gap-2 mb-4">
            <span class="badge bg-label-primary">{{ $monthSummary['total'] }} slot</span>
            <span class="badge bg-label-success">{{ $monthSummary['tersedia'] }} tersedia</span>
            <span class="badge bg-label-warning">{{ $monthSummary['sebagian'] }} sebagian</span>
            <span class="badge bg-label-danger">{{ $monthSummary['penuh'] }} penuh</span>
            <span class="badge bg-label-secondary">{{ $monthSummary['nonaktif'] }} nonaktif</span>
        </div>

        <div class="table-responsive">
            <table class="slot-calendar table mb-0">
                <caption class="visually-hidden">Kalender slot waktu {{ $month->translatedFormat('F Y') }}</caption>
                <thead>
                    <tr>
                        @foreach ($weekdayLabels as $label)
                            <th scope="col">{{ $label }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @for ($date = $calendarStart->copy(); $date->lte($calendarEnd); $date->addDay())
                        @if ($date->isMonday())
                            <tr>
                        @endif

                        @php
                            $dateKey = $date->toDateString();
                            $slotsHari = $calendarSlots->get($dateKey, collect());
                            $jumlahSlot = $slotsHari->count();
                            $isOutside = $date->format('Y-m') !== $bulan;
                            $isToday = $date->isToday();
                            $isSelected = $selectedDateKey === $dateKey;
                            $statusHari = ['tersedia' => 0, 'sebagian' => 0, 'penuh' => 0, 'nonaktif' => 0];
                            $dots = [];

                            foreach ($slotsHari as $slotHari) {
                                [$statusColor, $statusKey] = $slotStatus($slotHari);
                                $dots[] = $statusColor;
                                $statusHari[$statusKey]++;
                            }

                            $ariaLabel = $date->translatedFormat('l, d F Y').', '.$jumlahSlot.' slot';

                            if ($jumlahSlot > 0) {
                                $ariaLabel .= ': '.$statusHari['tersedia'].' tersedia, '.$statusHari['sebagian'].' sebagian, '.$statusHari['penuh'].' penuh, '.$statusHari['nonaktif'].' nonaktif';
                            }
                        @endphp

                        <td class="{{ $isOutside ? 'slot-calendar-outside' : '' }}">
                            <a
                                href="{{ route('admin.slot-waktu.index', ['bulan' => $date->format('Y-m'), 'tanggal' => $dateKey]) }}#slot-tanggal"
                                class="slot-calendar-day{{ $isToday ? ' is-today' : '' }}{{ $isSelected ? ' is-selected' : '' }}"
                                aria-label="{{ $ariaLabel }}"
                                @if ($isSelected) aria-current="true" @elseif ($isToday) aria-current="date" @endif>
                                <span class="slot-calendar-date">{{ $date->day }}</span>

                                @if ($jumlahSlot > 0)
                                    <span class="slot-calendar-dots" aria-hidden="true">
                                        @foreach (array_slice($dots, 0, 4) as $dot)
                                            <span class="slot-calendar-dot bg-{{ $dot }}"></span>
                                        @endforeach
                                        @if (count($dots) > 4)
                                            <span class="slot-calendar-more">+{{ count($dots) - 4 }}</span>
                                        @endif
                                    </span>
                                    <span class="slot-calendar-count">{{ $jumlahSlot }} slot</span>
                                @else
                                    <span class="slot-calendar-empty" aria-hidden="true">—</span>
                                @endif
                            </a>
                        </td>

                        @if ($date->isSunday())
                            </tr>
                        @endif
                    @endfor
                </tbody>
            </table>
        </div>

        <div class="slot-calendar-legend d-flex flex-wrap gap-3 mt-3 small text-body-secondary">
            <span class="d-inline-flex align-items-center gap-1">
                <span class="slot-calendar-dot slot-calendar-legend-dot bg-success" aria-hidden="true"></span>Tersedia
            </span>
            <span class="d-inline-flex align-items-center gap-1">
                <span class="slot-calendar-dot slot-calendar-legend-dot bg-warning" aria-hidden="true"></span>Sebagian terisi
            </span>
            <span class="d-inline-flex align-items-center gap-1">
                <span class="slot-calendar-dot slot-calendar-legend-dot bg-danger" aria-hidden="true"></span>Penuh
            </span>
            <span class="d-inline-flex align-items-center gap-1">
                <span class="slot-calendar-dot slot-calendar-legend-dot bg-secondary" aria-hidden="true"></span>Nonaktif
            </span>
        </div>
    </div>
</div>
