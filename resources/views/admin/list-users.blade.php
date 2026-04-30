@php
    $class = 'users-list-page';
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

        .users-list-page .fi-header-actions {
            animation: fadeSlideDown 0.5s ease-out both;
            animation-delay: 0.1s;
        }

        .users-list-page .fi-ta-header {
            animation: fadeSlideDown 0.6s ease-out both;
            animation-delay: 0.2s;
        }

        .users-list-page .fi-ta {
            animation: fadeSlideDown 0.6s ease-out both;
            animation-delay: 0.3s;
        }

        .users-list-page .fi-ta-table thead {
            animation: fadeSlideDown 0.6s ease-out both;
            animation-delay: 0.4s;
        }

        .users-list-page .fi-ta-table tbody tr {
            animation: fadeSlideDown 0.5s ease-out both;
        }

        .users-list-page .fi-ta-table tbody tr:nth-child(1) { animation-delay: 0.5s; }
        .users-list-page .fi-ta-table tbody tr:nth-child(2) { animation-delay: 0.55s; }
        .users-list-page .fi-ta-table tbody tr:nth-child(3) { animation-delay: 0.6s; }
        .users-list-page .fi-ta-table tbody tr:nth-child(4) { animation-delay: 0.65s; }
        .users-list-page .fi-ta-table tbody tr:nth-child(5) { animation-delay: 0.7s; }
        .users-list-page .fi-ta-table tbody tr:nth-child(6) { animation-delay: 0.75s; }
        .users-list-page .fi-ta-table tbody tr:nth-child(7) { animation-delay: 0.8s; }
        .users-list-page .fi-ta-table tbody tr:nth-child(8) { animation-delay: 0.85s; }
        .users-list-page .fi-ta-table tbody tr:nth-child(9) { animation-delay: 0.9s; }
        .users-list-page .fi-ta-table tbody tr:nth-child(10) { animation-delay: 0.95s; }
        .users-list-page .fi-ta-pagination {
            animation: fadeSlideDown 0.6s ease-out both;
             animation-delay: 1s;
        } 
    </style>

    <div class="{{ $class }}">
        {{ $this->table }}
    </div>
</x-filament-panels::page>