<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationItem;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use App\Filament\Resources\Widgets\AccountProfileWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
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
            ->login(\App\Filament\Resources\Procurements\Pages\Login::class)
            // ->registration(\App\Filament\Resources\Procurements\Pages\Register::class)
            // ->passwordReset()
            ->globalSearch(false)
            ->sidebarCollapsibleOnDesktop()
            ->maxContentWidth('full')
            ->brandLogo(fn () => new \Illuminate\Support\HtmlString('
                <div style="display: flex; align-items: center; gap: 10px;">
                    <img src="' . asset('images/logo.png') . '" alt="Logo" style="height: 40px; width: 40px; object-fit: contain;">
                    <div style="display: flex; flex-direction: column; line-height: 1.25;">
                        <span style="font-size: 17px; font-weight: 700; color: light-dark(#1a1a1a, #ffffff); white-space: nowrap; letter-spacing: 0.2px;">Konnco Studio</span>
                        <span style="font-size: 9px; font-weight: 800; color: light-dark(#6b7280, #ffffff); white-space: nowrap; letter-spacing: 3.5px;">Execute Better</span>
                    </div>
                </div>
            '))
            ->brandLogoHeight('40px')
            ->favicon(asset('images/logo.png'))
            ->brandName('Konnco Studio')
            ->renderHook(
                'panels::head.start',
                fn (): \Illuminate\Support\HtmlString => new \Illuminate\Support\HtmlString('
                    <script>
                        document.addEventListener("DOMContentLoaded", function() {
                            const parts = document.title.split(" - ");
                            if (parts.length === 2) {
                                document.title = "Konnco Studio | " + parts[0];
                            }
                        });
                    </script>
                ')
            )
            ->colors([
                'primary' => Color::hex('#ec6c1c'),
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                \App\Filament\Resources\Procurements\Pages\CustomDashboard::class,
                \App\Filament\Resources\Procurements\Pages\DashboardFinanceStaff::class,
                \App\Filament\Resources\Procurements\Pages\DashboardManager::class,
                \App\Filament\Resources\Procurements\Pages\ManagerApproval::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([
                AccountProfileWidget::class,
            ])

            ->navigationItems([
                NavigationItem::make('Realisasi Pengadaan')
                    ->url(fn (): string => \App\Filament\Resources\MasterData\ExpenseMasterResource::getUrl('realisasi'))
                    ->icon('heroicon-o-document-chart-bar')
                    ->isActiveWhen(fn (): bool =>
                        str_ends_with(
                            rtrim(parse_url(request()->url(), PHP_URL_PATH), '/'),
                            '/realisasi'
                        )
                    )
                    // finance_manager tetap bisa lihat menu & akses halaman Realisasi
                    ->visible(fn (): bool =>
                        auth()->check() &&
                        auth()->user()->hasRole(['hrga', 'finance', 'finance_manager', 'super_admin'])
                    )
                    ->sort(6),
            ])

            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ])
            ->renderHook(
                'panels::head.end',
                fn () => new \Illuminate\Support\HtmlString('
                    <style>
                        .dark .fi-user-avatar { filter: invert(1) !important; }
                        .dark .fi-text-color-950,
                        .dark .dark\:fi-text-color-950 { color: #ffffff !important; }
                        .fi-modal-window-ctn,
                        .fi-modal-window-ctn::before,
                        .fi-modal-window-ctn::after,
                        [data-headlessui-state] .fi-modal-window-ctn,
                        .fi-modal-backdrop,
                        .fi-modal-overlay,
                        [class*="fi-modal"] > div:first-child,
                        .fixed.inset-0.z-40,
                        .fixed.inset-0.bg-black,
                        .fixed.inset-0.bg-gray,
                        .fixed.inset-0 {
                            background: transparent !important;
                            background-color: transparent !important;
                            backdrop-filter: none !important;
                            -webkit-backdrop-filter: none !important;
                            opacity: 1 !important;
                        }
                    </style>
                ')
            )
            ->renderHook(
                'panels::head.end',
                fn () => new \Illuminate\Support\HtmlString('
                    <style>
                        .fi-modal-window-ctn {
                            background: transparent !important;
                            backdrop-filter: none !important;
                            -webkit-backdrop-filter: none !important;
                        }
                        .fi-user-menu-dropdown,
                        [x-placement],
                        .fi-dropdown-panel {
                            animation: dropdownSlideDown 0.25s ease-out both !important;
                            transform-origin: top right;
                        }
                        @keyframes dropdownSlideDown {
                            0%   { opacity: 0; transform: translateY(-12px) scaleY(0.95); }
                            100% { opacity: 1; transform: translateY(0) scaleY(1); }
                        }
                    </style>

                    <script>
                    // ── Fix: matikan highlight "Master Data Beban" saat di halaman realisasi ──
                    (function fixNavActive() {
                        function fix() {
                            var isRealisasi = window.location.pathname.replace(/\/$/, "").endsWith("/realisasi");
                            if (!isRealisasi) return;

                            var navItems = document.querySelectorAll(
                                ".fi-sidebar-nav a, nav a, [class*=\'fi-sidebar\'] a"
                            );

                            navItems.forEach(function(a) {
                                var href = (a.getAttribute("href") || "").replace(/\/$/, "");
                                var isRealisasiLink = href.endsWith("/realisasi");
                                var isMasterDataLink = href.endsWith("/expense-masters") || href.includes("/expense-masters");

                                if (isMasterDataLink && !isRealisasiLink) {
                                    a.removeAttribute("aria-current");
                                    a.classList.forEach(function(cls) {
                                        if (cls.includes("active") || cls.includes("current")) {
                                            a.classList.remove(cls);
                                        }
                                    });
                                    a.style.setProperty("color", "rgb(107 114 128)", "important");
                                    a.style.setProperty("background-color", "transparent", "important");
                                    a.querySelectorAll("*").forEach(function(el) {
                                        el.style.setProperty("color", "inherit", "important");
                                        el.style.setProperty("background-color", "transparent", "important");
                                    });
                                }

                                if (isRealisasiLink) {
                                    a.setAttribute("aria-current", "page");
                                }
                            });
                        }

                        if (document.readyState === "loading") {
                            document.addEventListener("DOMContentLoaded", fix);
                        } else {
                            fix();
                        }
                        document.addEventListener("livewire:navigated", fix);
                        document.addEventListener("livewire:load", fix);
                        setTimeout(fix, 100);
                        setTimeout(fix, 500);
                    })();
                    </script>
                ')
            );
    }
}