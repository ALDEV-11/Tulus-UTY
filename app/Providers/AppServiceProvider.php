<?php

namespace App\Providers;

use App\Enums\UserRole;
use App\Models\Kendaraan;
use App\Policies\KendaraanPolicy;
use App\Services\ReservationNotificationService;
use Carbon\Carbon;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useBootstrapFive();
        Carbon::setLocale(config('app.locale'));

        Gate::policy(Kendaraan::class, KendaraanPolicy::class);

        View::composer(['admin.partials.navbar', 'admin.partials.sidebar'], function ($view) {
            $user = auth()->user();

            if (! $user || $user->role !== UserRole::Admin) {
                $view->with([
                    'pendingReservasiCount' => 0,
                    'recentPendingReservasi' => collect(),
                ]);

                return;
            }

            $service = app(ReservationNotificationService::class);

            $view->with([
                'pendingReservasiCount' => $service->pendingCount(),
                'recentPendingReservasi' => $service->recentPending(),
            ]);
        });
    }
}
