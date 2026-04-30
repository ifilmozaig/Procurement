@php
    $class = 'procurement-edit-page';
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
        .procurement-edit-page .fi-header-actions {
            animation: fadeSlideDown 0.5s ease-out both;
            animation-delay: 0.1s;
        }
        .procurement-edit-page .fi-fo-section {
            animation: fadeSlideDown 0.6s ease-out both;
        }

        .procurement-edit-page .fi-fo-section:nth-child(1) { animation-delay: 0.15s; }
        .procurement-edit-page .fi-fo-section:nth-child(2) { animation-delay: 0.2s; }
        .procurement-edit-page .fi-fo-field-wrp {
            animation: fadeSlideDown 0.5s ease-out both;
        }

        .procurement-edit-page .fi-fo-field-wrp:nth-child(1) { animation-delay: 0.25s; }
        .procurement-edit-page .fi-fo-field-wrp:nth-child(2) { animation-delay: 0.3s; }
        .procurement-edit-page .fi-fo-field-wrp:nth-child(3) { animation-delay: 0.35s; }
        .procurement-edit-page .fi-fo-field-wrp:nth-child(4) { animation-delay: 0.4s; }
        .procurement-edit-page .fi-fo-field-wrp:nth-child(5) { animation-delay: 0.45s; }
        .procurement-edit-page .fi-fo-field-wrp:nth-child(6) { animation-delay: 0.5s; }
        .procurement-edit-page .fi-fo-field-wrp:nth-child(7) { animation-delay: 0.55s; }
        .procurement-edit-page .fi-fo-field-wrp:nth-child(8) { animation-delay: 0.6s; }
        .procurement-edit-page .fi-form-actions {
            animation: fadeSlideDown 0.6s ease-out both;
            animation-delay: 0.65s;
        }
    </style>

    <div class="{{ $class }}">
        <form wire:submit="save">
            {{ $this->form }}
            
            <div class="fi-form-actions">
                <x-filament::button type="submit">
                    Save Changes
                </x-filament::button>
            </div>
        </form>
    </div>
</x-filament-panels::page>