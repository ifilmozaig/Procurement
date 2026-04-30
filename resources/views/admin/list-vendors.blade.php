<x-filament-panels::page>

    <style>
        @keyframes fadeSlideDown {
            0%   { opacity: 0; transform: translateY(-40px); }
            100% { opacity: 1; transform: translateY(0); }
        }
        .fi-ta {
            animation: fadeSlideDown 0.6s ease-out both;
            animation-delay: 0.1s;
        }
        .fi-ta-table thead {
            animation: fadeSlideDown 0.6s ease-out both;
            animation-delay: 0.2s;
        }
        .fi-ta-table tbody tr:nth-child(1)  { animation: fadeSlideDown 0.5s ease-out both; animation-delay: 0.3s; }
        .fi-ta-table tbody tr:nth-child(2)  { animation: fadeSlideDown 0.5s ease-out both; animation-delay: 0.35s; }
        .fi-ta-table tbody tr:nth-child(3)  { animation: fadeSlideDown 0.5s ease-out both; animation-delay: 0.4s; }
        .fi-ta-table tbody tr:nth-child(4)  { animation: fadeSlideDown 0.5s ease-out both; animation-delay: 0.45s; }
        .fi-ta-table tbody tr:nth-child(5)  { animation: fadeSlideDown 0.5s ease-out both; animation-delay: 0.5s; }
        .fi-ta-table tbody tr:nth-child(6)  { animation: fadeSlideDown 0.5s ease-out both; animation-delay: 0.55s; }
        .fi-ta-table tbody tr:nth-child(7)  { animation: fadeSlideDown 0.5s ease-out both; animation-delay: 0.6s; }
        .fi-ta-table tbody tr:nth-child(8)  { animation: fadeSlideDown 0.5s ease-out both; animation-delay: 0.65s; }
        .fi-ta-table tbody tr:nth-child(9)  { animation: fadeSlideDown 0.5s ease-out both; animation-delay: 0.7s; }
        .fi-ta-table tbody tr:nth-child(10) { animation: fadeSlideDown 0.5s ease-out both; animation-delay: 0.75s; }
        .fi-ta-table tbody tr:nth-child(11) { animation: fadeSlideDown 0.5s ease-out both; animation-delay: 0.8s; }
        .fi-ta-table tbody tr:nth-child(12) { animation: fadeSlideDown 0.5s ease-out both; animation-delay: 0.85s; }
        .fi-ta-table tbody tr:nth-child(13) { animation: fadeSlideDown 0.5s ease-out both; animation-delay: 0.9s; }
        .fi-ta-table tbody tr:nth-child(14) { animation: fadeSlideDown 0.5s ease-out both; animation-delay: 0.95s; }
        .fi-ta-table tbody tr:nth-child(15) { animation: fadeSlideDown 0.5s ease-out both; animation-delay: 1s; }
    </style>

    {{ $this->table }}

</x-filament-panels::page>