<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Filament\Support\Facades\FilamentView;
use Filament\View\PanelsRenderHook;
use Illuminate\Support\Facades\Blade;

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
        FilamentView::registerRenderHook(
            PanelsRenderHook::HEAD_START,
            fn (): string => Blade::render('
                <link rel="manifest" href="/manifest.json">
                <meta name="theme-color" content="#0284c7">
                <link rel="apple-touch-icon" href="/icons/icon-192x192.png">
                <script>
                    if ("serviceWorker" in navigator) {
                        window.addEventListener("load", () => {
                            navigator.serviceWorker.register("/sw.js");
                        });
                    }
                </script>
            '),
        );
    }
}
