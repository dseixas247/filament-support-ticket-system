<x-filament::section class="max-w-7xl mx-auto items-center justify-center">
    <x-slot name="heading">
        Login
    </x-slot>

    <form wire:submit="authenticate">
        {{$this->form}}

        <x-filament::button type="submit">
            Login
        </x-filament::button>
    </form
</x-filament::section>