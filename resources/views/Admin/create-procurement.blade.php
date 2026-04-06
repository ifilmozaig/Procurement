@php
    $class = 'procurement-create-page';
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
        .procurement-create-page .fi-header-actions {
            animation: fadeSlideDown 0.5s ease-out both;
            animation-delay: 0.1s;
        }

        .procurement-create-page .fi-fo-section {
            animation: fadeSlideDown 0.6s ease-out both;
        }

        .procurement-create-page .fi-fo-section:nth-child(1) { animation-delay: 0.15s; }
        .procurement-create-page .fi-fo-section:nth-child(2) { animation-delay: 0.2s; }
        .procurement-create-page .fi-fo-field-wrp {
            animation: fadeSlideDown 0.5s ease-out both;
        }

        .procurement-create-page .fi-fo-field-wrp:nth-child(1) { animation-delay: 0.25s; }
        .procurement-create-page .fi-fo-field-wrp:nth-child(2) { animation-delay: 0.3s; }
        .procurement-create-page .fi-fo-field-wrp:nth-child(3) { animation-delay: 0.35s; }
        .procurement-create-page .fi-fo-field-wrp:nth-child(4) { animation-delay: 0.4s; }
        .procurement-create-page .fi-fo-field-wrp:nth-child(5) { animation-delay: 0.45s; }
        .procurement-create-page .fi-fo-field-wrp:nth-child(6) { animation-delay: 0.5s; }
        .procurement-create-page .fi-form-actions {
            animation: fadeSlideDown 0.6s ease-out both;
            animation-delay: 0.6s;
        }
    </style>

    <div class="{{ $class }}">
        <form wire:submit="create">
            {{ $this->form }}
            
            <div class="fi-form-actions">
                <x-filament::button type="submit">
                    Create Procurement
                </x-filament::button>
            </div>
        </form>
    </div>
</x-filament-panels::page>