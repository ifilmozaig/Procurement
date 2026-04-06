<x-filament-widgets::widget>
    <style>
        @keyframes fadeSlideDown {
            0% { opacity: 0; transform: translateY(-20px); }
            100% { opacity: 1; transform: translateY(0); }
        }
        .dark .profile-buttons .fi-btn-icon {
            color: #ffffff !important;
        }

        .dark .profile-buttons svg {
            color: #ffffff !important;
            stroke: #ffffff !important;
        }
        .profile-widget .fi-avatar {
            animation: fadeSlideDown 0.5s ease-out both;
            animation-delay: 0.1s;
        }
        .profile-widget .profile-greeting {
            animation: fadeSlideDown 0.5s ease-out both;
            animation-delay: 0.2s;
        }
        .profile-widget .profile-buttons {
            animation: fadeSlideDown 0.5s ease-out both;
            animation-delay: 0.3s;
        }
        .profile-form-floating {
            position: absolute;
            top: calc(100% + 8px);
            left: 0;
            right: 0;
            z-index: 50;
            background: light-dark(#ffffff, #1f2937);
            border: 1px solid light-dark(#e5e7eb, #374151);
            border-radius: 12px;
            padding: 16px;
            box-shadow: 0 10px 25px -5px rgba(0,0,0,0.1), 0 4px 10px -5px rgba(0,0,0,0.05);
        }
        .profile-form-floating.entering {
            animation: fadeSlideDown 0.4s ease-out both;
        }
        .profile-form-floating.leaving {
            animation: fadeSlideUp 0.3s ease-in both;
        }
        .profile-form-floating .fi-fo-field-wrp {
            animation: fadeSlideDown 0.4s ease-out both;
        }
        .profile-form-floating .fi-fo-field-wrp:nth-child(1) { animation-delay: 0.05s; }
        .profile-form-floating .fi-fo-field-wrp:nth-child(2) { animation-delay: 0.1s; }
        .profile-form-floating .fi-fo-field-wrp:nth-child(3) { animation-delay: 0.15s; }
        .profile-form-floating .fi-fo-field-wrp:nth-child(4) { animation-delay: 0.2s; }
        .profile-form-floating .form-actions {
            animation: fadeSlideDown 0.4s ease-out both;
            animation-delay: 0.25s;
        }
    </style>

    <x-filament::section>
        <div class="profile-widget" style="position: relative;">

            <div style="display: flex; align-items: flex-start; gap: 12px;">

                {{-- Avatar --}}
                <div class="fi-avatar" style="display: flex; height: 40px; width: 40px; align-items: center; justify-content: center; border-radius: 9999px; background-color: light-dark(#1f2937, #ffffff); color: light-dark(#ffffff, #1f2937); font-weight: 700; font-size: 14px; flex-shrink: 0; margin-top: 2px;">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </div>

                <div style="display: flex; flex-direction: column; justify-content: center;">

                    {{-- Greeting --}}
                    <div class="profile-greeting">
                        <p style="font-size: 15px; font-weight: 600; color: light-dark(#374151, #f3f4f6); margin: 0; line-height: 1.2;">Selamat Datang</p>
                        <p style="font-size: 13px; color: light-dark(#6b7280, #f3f4f6); margin: 0 0 6px 0; line-height: 1.2;">{{ auth()->user()->name }}</p>
                    </div>

                    {{-- Buttons --}}
                    <div class="profile-buttons" style="display: flex; align-items: center; gap: 8px; margin-left: -2px;">
                        @if (!$showForm)
                            <x-filament::button
                                wire:click="$set('showForm', true)"
                                color="gray"
                                size="sm"
                                icon="heroicon-o-pencil-square"
                            >
                                Edit Profil
                            </x-filament::button>
                        @endif

                        <x-filament::button
                            tag="a"
                            href="{{ filament()->getLogoutUrl() }}"
                            color="gray"
                            size="sm"
                            icon="heroicon-o-arrow-right-on-rectangle"
                        >
                            Keluar
                        </x-filament::button>
                    </div>
                </div>
            </div>

            {{-- Form Floating --}}
            <div
                x-data="{
                    show: @entangle('showForm'),
                    animClass: '',
                    toggle(val) {
                        if (val) {
                            this.animClass = 'entering';
                        } else {
                            this.animClass = 'leaving';
                        }
                    }
                }"
                x-init="$watch('show', val => toggle(val))"
                x-show="show"
                :class="animClass"
                class="profile-form-floating"
            >
                <form wire:submit="save">
                    {{ $this->form }}

                    <div class="form-actions" style="margin-top: 16px; display: flex; align-items: center; gap: 12px;">
                        <x-filament::button type="submit" color="primary" size="sm">
                            Simpan
                        </x-filament::button>
                        <x-filament::button
                            wire:click="$set('showForm', false)"
                            color="gray"
                            size="sm"
                        >
                            Batal
                        </x-filament::button>
                    </div>
                </form>
            </div>

        </div>
    </x-filament::section>
</x-filament-widgets::widget>