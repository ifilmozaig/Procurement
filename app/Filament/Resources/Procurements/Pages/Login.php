<?php

namespace App\Filament\Resources\Procurements\Pages;

use Filament\Auth\Pages\Login as BaseLogin;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\EmbeddedSchema;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Text;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\HtmlString;

class Login extends BaseLogin
{
    public function getSubheading(): string | Htmlable | null
    {
        return null;
    }

    protected function getPasswordFormComponent(): \Filament\Forms\Components\TextInput
    {
        return parent::getPasswordFormComponent()
            ->hint('');
    }

    protected function getRedirectUrl(): string
    {
        return '/admin';
    }

    public function getFormContentComponent(): Component
    {
        $footerItems = [
            Actions::make($this->getFormActions())
                ->alignment($this->getFormActionsAlignment())
                ->fullWidth($this->hasFullWidthFormActions())
                ->key('form-actions'),
        ];

        if (filament()->hasRegistration() || filament()->hasPasswordReset()) {
            $footerItems[] = Text::make(new HtmlString(
                '<style>
                    .fi-sc-text:has(> .login-links) {
                        display: block !important;
                        width: 100% !important;
                    }
                </style>' .
                '<div class="login-links" style="display: flex; justify-content: space-between; align-items: center; margin-top: 16px; width: 100%;">' .
                (filament()->hasRegistration()
                    ? '<a href="' . filament()->getRegistrationUrl() . '" style="color: #d97706; text-decoration: none; font-weight: 500; font-size: 14px; white-space: nowrap;">Buat akun baru</a>'
                    : '<span></span>') .
                (filament()->hasPasswordReset()
                    ? '<a href="' . filament()->getRequestPasswordResetUrl() . '" style="color: #d97706; text-decoration: none; font-weight: 500; font-size: 14px; white-space: nowrap;">Lupa kata sandi?</a>'
                    : '') .
                '</div>'
            ));
        }

        return Form::make([EmbeddedSchema::make('form')])
            ->id('form')
            ->livewireSubmitHandler('authenticate')
            ->footer($footerItems)
            ->visible(fn (): bool => blank($this->userUndertakingMultiFactorAuthentication));
    }
}