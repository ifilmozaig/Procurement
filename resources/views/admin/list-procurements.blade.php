@php
    $class = 'dashboard-procurement';
@endphp

<x-filament-panels::page>
    <style>
        @keyframes fadeSlideDown {
            0% { opacity: 0; transform: translateY(-40px); }
            100% { opacity: 1; transform: translateY(0); }
        }

        .dashboard-procurement .fi-wi-stats-overview {
            animation: fadeSlideDown 0.6s ease-out both;
            animation-delay: 0.1s;
        }

        .dashboard-procurement .fi-ta {
            animation: fadeSlideDown 0.6s ease-out both;
            animation-delay: 0.7s;
        }

        .dashboard-procurement .fi-ta-table thead {
            animation: fadeSlideDown 0.6s ease-out both;
            animation-delay: 0.75s;
        }

        .dashboard-procurement .fi-ta-table tbody tr {
            animation: fadeSlideDown 0.5s ease-out both;
        }

        .dashboard-procurement .fi-ta-table tbody tr:nth-child(1)  { animation-delay: 0.8s; }
        .dashboard-procurement .fi-ta-table tbody tr:nth-child(2)  { animation-delay: 0.85s; }
        .dashboard-procurement .fi-ta-table tbody tr:nth-child(3)  { animation-delay: 0.9s; }
        .dashboard-procurement .fi-ta-table tbody tr:nth-child(4)  { animation-delay: 0.95s; }
        .dashboard-procurement .fi-ta-table tbody tr:nth-child(5)  { animation-delay: 1.0s; }
        .dashboard-procurement .fi-ta-table tbody tr:nth-child(6)  { animation-delay: 1.05s; }
        .dashboard-procurement .fi-ta-table tbody tr:nth-child(7)  { animation-delay: 1.1s; }
        .dashboard-procurement .fi-ta-table tbody tr:nth-child(8)  { animation-delay: 1.15s; }
        .dashboard-procurement .fi-ta-table tbody tr:nth-child(9)  { animation-delay: 1.2s; }
        .dashboard-procurement .fi-ta-table tbody tr:nth-child(10) { animation-delay: 1.25s; }
        .dashboard-procurement,
        .dashboard-procurement > * {
            transform: none !important;
            will-change: auto !important;
        }
        .procurement-stats-wrapper {
            margin-bottom: 1.25rem;
        }
    </style>

    <div class="{{ $class }}">

        @if(auth()->user()->hasRole(['hrga', 'super_admin']))
            <div class="procurement-stats-wrapper">
                @livewire(\App\Filament\Resources\Widgets\DashboardHRGAStats::class)
            </div>
        @endif

        {{ $this->table }}
    </div>
</x-filament-panels::page>