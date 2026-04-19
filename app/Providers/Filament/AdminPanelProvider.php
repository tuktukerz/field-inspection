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
            ->brandName('SIMTEL')
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

                    .fi-app-footer {
                        font-family: 'Plus Jakarta Sans', ui-sans-serif, system-ui, sans-serif;
                        font-size: 0.75rem;
                        color: #64748b;
                        text-align: center;
                        padding: 1.25rem 1.5rem 1.5rem;
                        line-height: 1.6;
                    }
                    .fi-app-footer strong {
                        color: #334155;
                        font-weight: 600;
                    }
                    .dark .fi-app-footer {
                        color: #a1a1aa;
                    }
                    .dark .fi-app-footer strong {
                        color: #e4e4e7;
                    }

                    .fi-lightbox-overlay {
                        position: fixed;
                        inset: 0;
                        background: rgba(0, 0, 0, 0.85);
                        backdrop-filter: blur(4px);
                        -webkit-backdrop-filter: blur(4px);
                        z-index: 9999;
                        display: none;
                        align-items: center;
                        justify-content: center;
                        padding: 2.5rem;
                        cursor: zoom-out;
                    }
                    .fi-lightbox-overlay.is-open {
                        display: flex;
                    }
                    .fi-lightbox-overlay img {
                        max-width: 100%;
                        max-height: 100%;
                        border-radius: 0.5rem;
                        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
                        object-fit: contain;
                    }
                    .fi-lightbox-close {
                        position: absolute;
                        top: 1.25rem;
                        right: 1.25rem;
                        color: #ffffff;
                        cursor: pointer;
                        background: rgba(255, 255, 255, 0.12);
                        border: 1px solid rgba(255, 255, 255, 0.2);
                        padding: 0;
                        width: 2.5rem;
                        height: 2.5rem;
                        border-radius: 9999px;
                        display: inline-flex;
                        align-items: center;
                        justify-content: center;
                        transition: background 0.15s, border-color 0.15s;
                    }
                    .fi-lightbox-close:hover {
                        background: rgba(255, 255, 255, 0.22);
                        border-color: rgba(255, 255, 255, 0.35);
                    }
                    .fi-lightbox-close svg {
                        width: 1.25rem;
                        height: 1.25rem;
                        stroke: currentColor;
                        stroke-width: 2.25;
                        stroke-linecap: round;
                        fill: none;
                    }
                    .fi-fo-file-upload img[src] {
                        cursor: zoom-in;
                    }

                    /* Ensure widgets in the same row match height */
                    .fi-dashboard-page .fi-wi,
                    .fi-dashboard-page .fi-wi > div,
                    .fi-dashboard-page .fi-wi-table-ctn {
                        height: 100%;
                    }
                    .fi-dashboard-page .fi-ta-ctn {
                        height: 100%;
                        display: flex;
                        flex-direction: column;
                    }

                    /* Pretty table styling */
                    .fi-ta thead tr {
                        background: linear-gradient(180deg, #f8fafc, #f1f5f9) !important;
                    }
                    .dark .fi-ta thead tr {
                        background: linear-gradient(180deg, #2c2c31, #26262b) !important;
                    }
                    .fi-ta thead th {
                        font-size: 0.6875rem !important;
                        font-weight: 700 !important;
                        text-transform: uppercase !important;
                        letter-spacing: 0.06em !important;
                        color: #475569 !important;
                    }
                    .dark .fi-ta thead th {
                        color: #a1a1aa !important;
                    }
                    .fi-ta tbody tr {
                        transition: background 0.15s ease, transform 0.15s ease;
                    }
                    .fi-ta tbody tr:hover {
                        background: linear-gradient(90deg, rgba(14,165,233,0.04), transparent) !important;
                    }
                    .dark .fi-ta tbody tr:hover {
                        background: linear-gradient(90deg, rgba(14,165,233,0.08), transparent) !important;
                    }
                    .fi-ta tbody td {
                        padding-top: 0.875rem !important;
                        padding-bottom: 0.875rem !important;
                    }
                    .fi-ta-text-item-icon {
                        flex-shrink: 0;
                    }
                    .fi-badge {
                        font-weight: 600 !important;
                        letter-spacing: 0.01em !important;
                    }
                </style>
                HTML),
            )
            ->renderHook(
                PanelsRenderHook::BODY_END,
                fn (): HtmlString => new HtmlString(<<<'HTML'
                <div id="fi-lightbox" class="fi-lightbox-overlay">
                    <button type="button" class="fi-lightbox-close" aria-label="Tutup">
                        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M6 6l12 12M18 6L6 18"/></svg>
                    </button>
                    <img id="fi-lightbox-img" src="" alt="Preview">
                </div>

                <script>
                    (function () {
                        if (window.__fiLightboxInit) return;
                        window.__fiLightboxInit = true;

                        const IMG_RE = /\.(jpe?g|png|gif|webp|bmp|svg|avif)(\?|#|$)/i;

                        const openLightbox = (src) => {
                            const box = document.getElementById('fi-lightbox');
                            const img = document.getElementById('fi-lightbox-img');
                            if (!box || !img) return;
                            img.src = src;
                            box.classList.add('is-open');
                            document.body.style.overflow = 'hidden';
                        };
                        const closeLightbox = () => {
                            const box = document.getElementById('fi-lightbox');
                            if (!box) return;
                            box.classList.remove('is-open');
                            document.body.style.overflow = '';
                        };

                        const findImageUrl = (upload) => {
                            // 1. Look for any anchor (open/download) pointing to image
                            const links = upload.querySelectorAll('a[href]');
                            for (const a of links) {
                                if (IMG_RE.test(a.href)) return a.href;
                            }
                            // 2. FilePond stores server id / URL on items
                            const img = upload.querySelector('img[src]');
                            if (img && img.src) return img.src;
                            return null;
                        };

                        document.addEventListener('click', (e) => {
                            const box = document.getElementById('fi-lightbox');
                            if (box && (e.target === box || e.target.closest('.fi-lightbox-close'))) {
                                e.preventDefault();
                                closeLightbox();
                                return;
                            }
                            const upload = e.target.closest('.fi-fo-file-upload');
                            if (!upload) return;
                            // Skip clicks on delete/remove controls or input elements
                            if (e.target.closest('.filepond--action-remove-item, [data-action="remove"], input, label.filepond--drop-label')) return;

                            const src = findImageUrl(upload);
                            if (!src) return;

                            e.preventDefault();
                            e.stopPropagation();
                            openLightbox(src);
                        }, true);

                        document.addEventListener('keydown', (e) => {
                            if (e.key === 'Escape') closeLightbox();
                        });
                    })();
                </script>
                HTML),
            )
            ->renderHook(
                PanelsRenderHook::PAGE_END,
                fn (): HtmlString => new HtmlString(<<<'HTML'
                <footer class="fi-app-footer">
                    &copy; 2026 Dinas Cipta Karya, Tata Ruang dan Pertanahan Provinsi DKI Jakarta<br>
                    <strong>SIMTEL</strong> &mdash; Sistem Informasi Menara Telekomunikasi
                </footer>
                HTML),
            );
    }
}
