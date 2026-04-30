@php
    $class = 'dashboard-manager';
@endphp

<x-filament-panels::page>
    <style>
        @keyframes fadeSlideDown {
            0% {
                opacity: 0;
                transform: translateY(-40px);
            }
            100% {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .dashboard-manager [x-data] > div {
            animation: fadeSlideDown 0.6s ease-out both;
        }

        .dashboard-manager [x-data] > div:nth-child(1) { animation-delay: 0.1s; }
        .dashboard-manager [x-data] > div:nth-child(2) { animation-delay: 0.2s; }
        .dashboard-manager [x-data] > div:nth-child(3) { animation-delay: 0.3s; }
        .dashboard-manager [x-data] > div:nth-child(4) { animation-delay: 0.4s; }
        .dashboard-manager [x-data] > div:nth-child(5) { animation-delay: 0.5s; }
        .dashboard-manager [x-data] > div:nth-child(6) { animation-delay: 0.6s; }

        .dashboard-manager .fi-ta {
            animation: fadeSlideDown 0.6s ease-out both;
            animation-delay: 0.6s;
        }

        .dashboard-manager .fi-ta-table thead {
            animation: fadeSlideDown 0.6s ease-out both;
            animation-delay: 0.65s;
        }

        .dashboard-manager .fi-ta-table tbody tr {
            animation: fadeSlideDown 0.5s ease-out both;
        }

        .dashboard-manager .fi-ta-table tbody tr:nth-child(1) { animation-delay: 0.7s; }
        .dashboard-manager .fi-ta-table tbody tr:nth-child(2) { animation-delay: 0.75s; }
        .dashboard-manager .fi-ta-table tbody tr:nth-child(3) { animation-delay: 0.8s; }
        .dashboard-manager .fi-ta-table tbody tr:nth-child(4) { animation-delay: 0.85s; }
        .dashboard-manager .fi-ta-table tbody tr:nth-child(5) { animation-delay: 0.9s; }
        .dashboard-manager .fi-ta-table tbody tr:nth-child(6) { animation-delay: 0.95s; }
        .dashboard-manager .fi-ta-table tbody tr:nth-child(7) { animation-delay: 1s; }
        .dashboard-manager .fi-ta-table tbody tr:nth-child(8) { animation-delay: 1.05s; }
        .dashboard-manager .fi-ta-table tbody tr:nth-child(9) { animation-delay: 1.1s; }
        .dashboard-manager .fi-ta-table tbody tr:nth-child(10) { animation-delay: 1.15s; }
    </style>

    <div class="{{ $class }}">
        <x-filament-widgets::widgets
            :widgets="$this->getWidgets()"
            :columns="3"
        />
    </div>
</x-filament-panels::page>