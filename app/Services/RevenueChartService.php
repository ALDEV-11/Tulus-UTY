<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Builder;

class RevenueChartService
{
    private const MONTHS = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];

    /**
     * Build monthly revenue data for a year and its previous year.
     *
     * @param  Builder  $query  Servis query, optionally scoped to a customer.
     */
    public function build(Builder $query, ?int $year = null): array
    {
        $currentYear = (int) now()->year;
        $availableYears = (clone $query)
            ->selectRaw('YEAR(tanggal_masuk) as tahun')
            ->whereNotNull('tanggal_masuk')
            ->distinct()
            ->orderByDesc('tahun')
            ->pluck('tahun')
            ->map(fn ($value) => (int) $value)
            ->all();

        if (! in_array($currentYear, $availableYears, true)) {
            $availableYears[] = $currentYear;
        }

        rsort($availableYears);

        $selectedYear = $year ?: $currentYear;
        if (! in_array($selectedYear, $availableYears, true)) {
            $selectedYear = $currentYear;
        }

        $previousYear = $selectedYear - 1;
        $totals = (clone $query)
            ->whereBetween('tanggal_masuk', ["{$previousYear}-01-01", "{$selectedYear}-12-31"])
            ->selectRaw("DATE_FORMAT(tanggal_masuk, '%Y-%m') as bulan, SUM(total_biaya) as total")
            ->groupBy('bulan')
            ->pluck('total', 'bulan');

        $currentData = [];
        $previousData = [];
        foreach (array_keys(self::MONTHS) as $monthIndex) {
            $month = $monthIndex + 1;
            $currentKey = sprintf('%04d-%02d', $selectedYear, $month);
            $previousKey = sprintf('%04d-%02d', $previousYear, $month);

            $currentData[] = (float) ($totals[$currentKey] ?? 0);
            $previousData[] = (float) ($totals[$previousKey] ?? 0);
        }

        $lastMonth = $selectedYear === $currentYear ? (int) now()->month : 12;
        $currentTotal = array_sum(array_slice($currentData, 0, $lastMonth));
        $previousTotal = array_sum(array_slice($previousData, 0, $lastMonth));

        $growth = $previousTotal > 0
            ? (($currentTotal - $previousTotal) / $previousTotal) * 100
            : null;

        $periodLabel = $lastMonth === 12
            ? 'Jan–Des'
            : 'Jan–'.self::MONTHS[$lastMonth - 1];

        return [
            'year' => $selectedYear,
            'previous_year' => $previousYear,
            'labels' => self::MONTHS,
            'series' => [
                ['name' => (string) $selectedYear, 'data' => $currentData],
                ['name' => (string) $previousYear, 'data' => array_map(fn ($value) => $value > 0 ? -$value : 0, $previousData)],
            ],
            'current_total' => $currentTotal,
            'previous_total' => $previousTotal,
            'current_total_label' => $this->formatRupiah($currentTotal),
            'previous_total_label' => $this->formatRupiah($previousTotal),
            'growth' => $growth,
            'growth_label' => $growth === null ? '—' : number_format($growth, 1, ',', '.').'%',
            'growth_arc' => $growth === null ? 0 : min(100, abs($growth)),
            'growth_tone' => $growth === null ? 'secondary' : ($growth >= 0 ? 'success' : 'danger'),
            'growth_note' => $growth === null
                ? 'Belum ada data pembanding'
                : 'Pertumbuhan pendapatan '.$periodLabel,
            'period_label' => $periodLabel,
            'available_years' => $availableYears,
            'has_data' => array_sum($currentData) > 0 || array_sum($previousData) > 0,
        ];
    }

    private function formatRupiah(float $value): string
    {
        if ($value >= 1_000_000_000) {
            return 'Rp '.number_format($value / 1_000_000_000, 1, ',', '.').' M';
        }

        if ($value >= 1_000_000) {
            return 'Rp '.number_format($value / 1_000_000, 1, ',', '.').' jt';
        }

        if ($value >= 1_000) {
            return 'Rp '.number_format($value / 1_000, 0, ',', '.').' rb';
        }

        return 'Rp '.number_format($value, 0, ',', '.');
    }
}
