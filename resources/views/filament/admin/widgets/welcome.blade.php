<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">Helló {{ auth()->user()->name }}!</x-slot>
        <p>Jó újra látni! Kezeld a kollégisták étkezés lemondásait, a lemondás beállításait, vagy a felhasználókat.
            Amennyiben valami kérdésed van, vedd fel velem a kapcsolatot az
            <x-filament::link href="mailto:ocskon@gmail.com">ocskon@gmail.com</x-filament::link>
            email címen!
        </p>
    </x-filament::section>
</x-filament-widgets::widget>
