<?php

namespace App\Filament\Common\Pages\Auth;

use App\Data\RegisterTokenData;
use App\Models\RegisterToken;
use App\Models\User;
use DanHarrin\LivewireRateLimiting\Exceptions\TooManyRequestsException;
use Filament\Facades\Filament;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\TextInput;
use Filament\Http\Responses\Auth\Contracts\RegistrationResponse;
use Filament\Notifications\Notification;
use Filament\Pages\Auth\Register as BaseRegister;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Url;

class Register extends BaseRegister
{
    #[Url(as: 't')]
    public ?string $registerToken;

    public RegisterTokenData $registerTokenData;

    public function mount(): void
    {
        if (Filament::auth()->check()) {
            redirect()->intended(Filament::getUrl());
        }

        abort_if(!isset($this->registerToken), 404);

        $registerTokenModel = RegisterToken::whereToken($this->registerToken)->firstOrFail();

        $this->registerTokenData = RegisterTokenData::from($registerTokenModel);

        abort_if(isset($registerTokenModel->used_at), 404);

        $this->form->fill([
            'name' => $this->registerTokenData->name,
            'email' => $this->registerTokenData->email,
        ]);
    }

    public function register(): ?RegistrationResponse
    {
        try {
            $this->rateLimit(2);
        } catch (TooManyRequestsException $exception) {
            Notification::make()
                ->title(__('filament-panels::pages/auth/register.notifications.throttled.title', [
                    'seconds' => $exception->secondsUntilAvailable,
                    'minutes' => ceil($exception->secondsUntilAvailable / 60),
                ]))
                ->body(array_key_exists('body', __('filament-panels::pages/auth/register.notifications.throttled') ?: []) ? __('filament-panels::pages/auth/register.notifications.throttled.body', [
                    'seconds' => $exception->secondsUntilAvailable,
                    'minutes' => ceil($exception->secondsUntilAvailable / 60),
                ]) : null)
                ->danger()
                ->send();

            return null;
        }

        $data = $this->form->getState();

        $data['name'] = $this->registerTokenData->name;
        $data['email'] = $this->registerTokenData->email;

        /** @var ?User $user */
        $user = null;

        DB::transaction(function () use ($data, &$user) {
            $user = $this->getUserModel()::create($data);

            RegisterToken::whereToken($this->registerToken)->update([
                'used_at' => now(),
            ]);
        });

        $this->sendEmailVerificationNotification($user);

        Filament::auth()->login($user);

        session()->regenerate();

        return app(RegistrationResponse::class);
    }

    protected function getNameFormComponent(): Component
    {
        return TextInput::make('name')
            ->label('Név')
            ->disabled()
            ->dehydrated()
            ->required()
            ->maxLength(255)
            ->autofocus();
    }

    protected function getEmailFormComponent(): Component
    {
        return TextInput::make('email')
            ->label('E-mail cím')
            ->disabled()
            ->dehydrated()
            ->email()
            ->required()
            ->maxLength(255)
            ->unique($this->getUserModel());
    }
}
