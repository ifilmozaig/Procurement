<?php

namespace App\Filament\Resources\Procurements\Pages;

use Filament\Auth\Pages\Register as BaseRegister;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\EmbeddedSchema;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Text;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\HtmlString;

class Register extends BaseRegister
{
    public function getSubheading(): string | Htmlable | null
    {
        return null;
    }

    public function getFormContentComponent(): Component
    {
        $footerItems = [
            Actions::make($this->getFormActions())
                ->alignment($this->getFormActionsAlignment())
                ->fullWidth($this->hasFullWidthFormActions())
                ->key('form-actions'),
        ];

        if (filament()->hasLogin()) {
            $footerItems[] = Text::make(new HtmlString(
                '<style>
                    .fi-sc-text:has(> .register-links) {
                        display: block !important;
                        width: 100% !important;
                    }
                </style>' .
                '<div class="register-links" style="display: flex; justify-content: center; align-items: center; margin-top: 16px; width: 100%;">' .
                '<a href="' . filament()->getLoginUrl() . '" style="color: #d97706; text-decoration: none; font-weight: 500; font-size: 14px; white-space: nowrap;">Sudah punya akun? Masuk</a>' .
                '</div>'
            ));
        }

        return Form::make([EmbeddedSchema::make('form')])
            ->id('form')
            ->livewireSubmitHandler('register')
            ->footer($footerItems);
    }
}
