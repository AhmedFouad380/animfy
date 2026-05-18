<?php

namespace App\Filament\Widgets;

use App\Models\Payment;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class RevenueChart extends ChartWidget
{
    protected static ?string $heading = 'Monthly Revenue Trend';

    protected static ?string $pollingInterval = '15s';

    // Show a premium dark-mode friendly line chart
    protected function getData(): array
    {
        $revenueData = [];
        $monthLabels = [];

        // Loop through the last 6 months to construct dynamic database analytics
        for ($i = 5; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $monthLabels[] = $month->format('M Y');

            // Sum successful payments for this month
            $monthlyRevenue = Payment::where('status', 'success')
                ->whereYear('created_at', $month->year)
                ->whereMonth('created_at', $month->month)
                ->sum('amount');

            $revenueData[] = (float) $monthlyRevenue;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Revenue (EGP)',
                    'data' => $revenueData,
                    'fill' => 'start',
                    'backgroundColor' => 'rgba(245, 158, 11, 0.1)', // Subtle Amber fill
                    'borderColor' => '#f59e0b', // Sleek Amber border
                    'tension' => 0.4, // Smooth curve
                ],
            ],
            'labels' => $monthLabels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
