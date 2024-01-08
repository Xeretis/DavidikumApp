@php use Filament\Support\Facades\FilamentView; @endphp
<x-filament-panels::page.simple>
    {{ FilamentView::renderHook('panels::auth.login.form.before') }}

    <x-filament-panels::form wire:submit="authenticate">
        {{ $this->form }}

        <x-filament-panels::form.actions
            :actions="$this->getCachedFormActions()"
            :full-width="$this->hasFullWidthFormActions()"
        />
    </x-filament-panels::form>

    {{ FilamentView::renderHook('panels::auth.login.form.after') }}
</x-filament-panels::page.simple>
