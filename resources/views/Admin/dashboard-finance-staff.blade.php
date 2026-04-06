<x-filament-panels::page>
    {{-- Hanya render widgets sekali --}}
    
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
        [x-data] > div {
            animation: fadeSlideDown 0.6s ease-out both;
        }

        [x-data] > div:nth-child(1) {
            animation-delay: 0.1s;
        }

        [x-data] > div:nth-child(2) {
            animation-delay: 0.2s;
        }

        [x-data] > div:nth-child(3) {
            animation-delay: 0.3s;
        }

        [x-data] > div:nth-child(4) {
            animation-delay: 0.4s;
        }

        [x-data] > div:nth-child(5) {
            animation-delay: 0.5s;
        }

        [x-data] > div:nth-child(6) {
            animation-delay: 0.6s;
        }
        .fi-ta-table tbody tr {
            animation: fadeSlideDown 0.5s ease-out both;
        }

        .fi-ta-table tbody tr:nth-child(1) { animation-delay: 0.7s; }
        .fi-ta-table tbody tr:nth-child(2) { animation-delay: 0.75s; }
        .fi-ta-table tbody tr:nth-child(3) { animation-delay: 0.8s; }
        .fi-ta-table tbody tr:nth-child(4) { animation-delay: 0.85s; }
        .fi-ta-table tbody tr:nth-child(5) { animation-delay: 0.9s; }
        .fi-ta-table tbody tr:nth-child(6) { animation-delay: 0.95s; }
        .fi-ta-table tbody tr:nth-child(7) { animation-delay: 1s; }
        .fi-ta-table tbody tr:nth-child(8) { animation-delay: 1.05s; }
        .fi-ta-table tbody tr:nth-child(9) { animation-delay: 1.1s; }
        .fi-ta-table tbody tr:nth-child(10) { animation-delay: 1.15s; }
        .fi-ta-table thead {
            animation: fadeSlideDown 0.6s ease-out both;
            animation-delay: 0.65s;
        }
        .fi-ta {
            animation: fadeSlideDown 0.6s ease-out both;
            animation-delay: 0.6s;
        }
    </style>
</x-filament-panels::page>