<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Builder;

class RevenueChartService
{
    /**
     * Build daily revenue data for the current month vs. the previous month.
     *
     * @param  Builder  $query  Servis query, optionally scoped to a customer.
     */
    public function build(Builder $query, ?int $year = null): array
    {
        $now = now();
        $currentMonthStart = $now->copy()->startOfMonth();
        $currentMonthEnd = $now->copy()->endOfMonth();
        $previousMonthStart = $now->copy()->subMonthNoOverflow()->startOfMonth();
        $previousMonthEnd = $now->copy()->subMonthNoOverflow()->endOfMonth();

        $currentMonthLabel = $currentMonthStart->translatedFormat('F Y');
        $previousMonthLabel = $previousMonthStart->translatedFormat('F Y');

        $daysInCurrentMonth = (int) $currentMonthStart->daysInMonth;
        $daysInPreviousMonth = (int) $previousMonthStart->daysInMonth;
        $maxDays = max($daysInCurrentMonth, $daysInPreviousMonth);

        // Fetch daily totals for both months in a single query
        $totals = (clone $query)
            ->whereBetween('tanggal_masuk', [$previousMonthStart->toDateString(), $currentMonthEnd->toDateString()])
            ->selectRaw("DATE(tanggal_masuk) as tanggal, SUM(total_biaya) as total")
            ->groupBy('tanggal')
            ->pluck('total', 'tanggal');

        $labels = [];
        $currentData = [];
        $previousData = [];

        for ($day = 1; $day <= $maxDays; $day++) {
            $labels[] = (string) $day;

            if ($day <= $daysInCurrentMonth) {
                $currentKey = $currentMonthStart->copy()->addDays($day - 1)->toDateString();
                $currentData[] = (float) ($totals[$currentKey] ?? 0);
            } else {
                $currentData[] = 0;
            }

            if ($day <= $daysInPreviousMonth) {
                $previousKey = $previousMonthStart->copy()->addDays($day - 1)->toDateString();
                $previousData[] = (float) ($totals[$previousKey] ?? 0);
            } else {
                $previousData[] = 0;
            }
        }

        $currentTotal = array_sum($currentData);
        $previousTotal = array_sum($previousData);

        if ($previousTotal > 0) {
            $growth = (($currentTotal - $previousTotal) / $previousTotal) * 100;
            $growthLabel = ($growth > 0 ? '+' : '') . number_format($growth, 1, ',', '.') . '%';
            $growthArc = min(100, max(0, abs($growth)));
            if ($growth > 0) {
                $growthTone = 'success';
                $growthNote = 'Pertumbuhan vs bulan lalu';
            } elseif ($growth < 0) {
                $growthTone = 'danger';
                $growthNote = 'Penurunan vs bulan lalu';
            } else {
                $growthTone = 'info';
                $growthNote = 'Stabil vs bulan lalu';
            }
        } elseif ($currentTotal > 0) {
            $growth = 100.0;
            $growthLabel = '+100%';
            $growthArc = 100;
            $growthTone = 'success';
            $growthNote = 'Pendapatan baru bulan ini';
        } else {
            $growth = 0.0;
            $growthLabel = '0%';
            $growthArc = 0;
            $growthTone = 'secondary';
            $growthNote = 'Belum ada data pendapatan';
        }

        return [
            'current_month_label' => $currentMonthLabel,
            'previous_month_label' => $previousMonthLabel,
            'labels' => $labels,
            'series' => [
                ['name' => $currentMonthLabel, 'data' => $currentData],
                ['name' => $previousMonthLabel, 'data' => array_map(fn ($value) => $value > 0 ? -$value : 0, $previousData)],
            ],
            'current_total' => $currentTotal,
            'previous_total' => $previousTotal,
            'current_total_label' => $this->formatRupiah($currentTotal),
            'previous_total_label' => $this->formatRupiah($previousTotal),
            'growth' => $growth,
            'growth_label' => $growthLabel,
            'growth_arc' => $growthArc,
            'growth_tone' => $growthTone,
            'growth_note' => $growthNote,
            'has_data' => array_sum($currentData) > 0 || array_sum($previousData) > 0,
        ];
    }

    private function formatRupiah(float $value): string
    {
        if ($value >= 1_000_000_000) {
            return 'Rp ' . number_format($value / 1_000_000_000, 1, ',', '.') . ' M';
        }

        if ($value >= 1_000_000) {
            return 'Rp ' . number_format($value / 1_000_000, 1, ',', '.') . ' jt';
        }

        if ($value >= 1_000) {
            return 'Rp ' . number_format($value / 1_000, 0, ',', '.') . ' rb';
        }

        return 'Rp ' . number_format($value, 0, ',', '.');
    }
}
