@php
    $class = 'users-edit-page';
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
        .users-edit-page .fi-header-actions {
            animation: fadeSlideDown 0.5s ease-out both;
            animation-delay: 0.1s;
        }
        .users-edit-page .fi-fo-section {
            animation: fadeSlideDown 0.6s ease-out both;
            animation-delay: 0.15s;
        }
        .users-edit-page .fi-fo-field-wrp {
            animation: fadeSlideDown 0.5s ease-out both;
        }

        .users-edit-page .fi-fo-field-wrp:nth-child(1) { animation-delay: 0.25s; }
        .users-edit-page .fi-fo-field-wrp:nth-child(2) { animation-delay: 0.35s; }
        .users-edit-page .fi-fo-field-wrp:nth-child(3) { animation-delay: 0.45s; }
        .users-edit-page .fi-fo-field-wrp:nth-child(4) { animation-delay: 0.55s; }
        .users-edit-page .fi-form-actions {
            animation: fadeSlideDown 0.6s ease-out both;
            animation-delay: 0.65s;
        }
    </style>

    <div class="{{ $class }}">
        <form wire:submit="save">
            {{ $this->form }}
            
            <div class="fi-form-actions">
                <x-filament::button type="submit">
                    Simpan
                </x-filament::button>
            </div>
        </form>
    </div>
</x-filament-panels::page>