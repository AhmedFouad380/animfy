<?php

namespace App\Filament\Widgets;

use App\Models\User;
use App\Models\Enrollment;
use App\Models\Payment;
use App\Models\Addon;
use App\Models\ThreeDObject;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    // Enable polling every 10 seconds for real-time dynamic feel
    protected static ?string $pollingInterval = '10s';

    protected function getStats(): array
    {
        // 1. Calculate Total Revenue
        $totalRevenue = Payment::where('status', 'success')->sum('amount');
        
        // 2. Count Registered Students
        $studentsCount = User::count();

        // 3. Count Active Enrollments
        $activeEnrollmentsCount = Enrollment::where('status', 'active')->count();

        // 4. Count Digital Assets (Addons + 3D Objects)
        $assetsCount = Addon::count() + ThreeDObject::count();

        return [
            Stat::make('Total Revenue', 'EGP ' . number_format($totalRevenue, 2))
                ->description('Total earnings from successful checkouts')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->chart([200, 400, 800, 1200, 1500, $totalRevenue])
                ->color('success'),

            Stat::make('Registered Students', number_format($studentsCount))
                ->description('Total registered student accounts')
                ->descriptionIcon('heroicon-m-user-group')
                ->chart([3, 7, 12, 18, 22, $studentsCount])
                ->color('primary'),

            Stat::make('Active Enrollments', number_format($activeEnrollmentsCount))
                ->description('Current active course subscriptions')
                ->descriptionIcon('heroicon-m-academic-cap')
                ->chart([1, 4, 8, 10, 15, $activeEnrollmentsCount])
                ->color('warning'),

            Stat::make('Digital Assets', number_format($assetsCount))
                ->description('Total addons & 3D models available')
                ->descriptionIcon('heroicon-m-cube')
                ->chart([5, 8, 12, 15, 18, $assetsCount])
                ->color('info'),
        ];
    }
}
