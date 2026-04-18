<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use App\Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Support\Facades\FilamentView;
use Filament\View\PanelsRenderHook;
use Filament\Widgets\AccountWidget;
use Filament\Widgets\FilamentInfoWidget;
use Illuminate\Support\HtmlString;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login(\App\Filament\Pages\Auth\Login::class)
            ->brandName('Inspeksi Lapangan')
            ->brandLogo(fn () => view('filament.brand'))
            ->brandLogoHeight('2.5rem')
            ->favicon(asset('images/favicon.svg'))
            ->font('Plus Jakarta Sans')
            ->defaultAvatarProvider(\App\AvatarProviders\GradientAvatarProvider::class)
            ->colors([
                'primary' => Color::Sky,
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([
                // Custom widgets discovered in app/Filament/Widgets
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                PreventRequestForgery::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ])
            ->renderHook(
                PanelsRenderHook::HEAD_END,
                fn (): HtmlString => new HtmlString(<<<'HTML'
                <style>
                    .fi-btn.fi-color:not(.fi-outlined):not(.fi-link) {
                        --bg: var(--color-700) !important;
                        --text: #ffffff !important;
                    }
                    .fi-btn.fi-color:not(.fi-outlined):not(.fi-link):hover {
                        --bg: var(--color-800) !important;
                    }
                    .fi-btn.fi-color:not(.fi-outlined):not(.fi-link),
                    .fi-btn.fi-color:not(.fi-outlined):not(.fi-link) .fi-icon,
                    .fi-btn.fi-color:not(.fi-outlined):not(.fi-link) svg {
                        color: #ffffff !important;
                    }

                    .fi-section {
                        border-left: 3px solid rgb(var(--primary-500)) !important;
                    }

                    .fi-topbar {
                        background: #f5f5f7 !important;
                    }
                    .dark .fi-topbar {
                        background: #1c1c20 !important;
                        border-bottom: none !important;
                    }

                    .fi-fo-field-label-required-mark {
                        display: inline-block;
                        margin-left: 0.375rem;
                        font-size: 0;
                        line-height: 1;
                        vertical-align: middle;
                        top: 0;
                    }
                    .fi-fo-field-label-required-mark::after {
                        content: 'Wajib';
                        display: inline-block;
                        padding: 0.0625rem 0.4375rem;
                        font-size: 0.625rem;
                        font-weight: 600;
                        line-height: 1.25;
                        color: rgb(var(--danger-700));
                        background-color: rgb(var(--danger-50));
                        border: 1px solid rgb(var(--danger-200));
                        border-radius: 0.3125rem;
                        letter-spacing: 0.02em;
                    }
                    .dark .fi-fo-field-label-required-mark::after {
                        color: rgb(var(--danger-300));
                        background-color: rgba(239, 68, 68, 0.1);
                        border-color: rgba(239, 68, 68, 0.3);
                    }

                    .fi-modal-close-overlay {
                        background-color: rgba(15, 23, 42, 0.35) !important;
                        backdrop-filter: blur(10px) saturate(1.2);
                        -webkit-backdrop-filter: blur(10px) saturate(1.2);
                    }
                    .dark .fi-modal-close-overlay {
                        background-color: rgba(0, 0, 0, 0.45) !important;
                    }

                    .fi-body {
                        background: #f5f5f7 !important;
                    }
                    .dark .fi-body {
                        background: #1c1c20 !important;
                    }

                    .fi-section,
                    .fi-wi {
                        background-color: #fcfcfd !important;
                    }
                    .dark .fi-section,
                    .dark .fi-wi {
                        background-color: #26262b !important;
                        border: none !important;
                        border-left: none !important;
                    }

                    .dark .fi-sidebar {
                        background-color: #1c1c20 !important;
                        border-right: none !important;
                    }
                    .dark .fi-sidebar-item-active .fi-sidebar-item-button {
                        background-color: rgba(14, 165, 233, 0.15) !important;
                    }
                    .dark .fi-ta-table,
                    .dark .fi-ta-row {
                        background-color: #26262b !important;
                    }
                    .dark .fi-ta-row:hover {
                        background-color: #2c2c32 !important;
                    }
                    .dark .fi-ta-striped tr:nth-child(even) {
                        background-color: #2a2a30 !important;
                    }

                    .fi-custom-brand-text {
                        color: #0f172a;
                    }
                    .dark .fi-custom-brand-text {
                        color: #f4f4f5;
                    }

                    .fi-topbar .fi-avatar,
                    .fi-topbar .fi-user-menu-trigger {
                        outline: 2px solid rgb(var(--primary-500));
                        outline-offset: 2px;
                        border-radius: 9999px;
                    }
                </style>
                HTML),
            );
    }
}
