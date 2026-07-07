<?php

namespace App\Providers;

use App\Services\SettingsRepository;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(SettingsRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Use the design-system pagination view app-wide.
        Paginator::defaultView('pagination.app');
        Paginator::defaultSimpleView('pagination.app');

        // Apply operator-adjustable settings (registration toggle, send chunk
        // size, etc.) on top of the config defaults. Wrapped so a missing table
        // during install/migrate never breaks booting.
        try {
            $this->app->make(SettingsRepository::class)->applyToConfig();
        } catch (\Throwable $e) {
            // Settings table not migrated yet; fall back to config defaults.
        }
    }
}
