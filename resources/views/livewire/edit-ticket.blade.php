<x-filament::section class="max-w-7xl mx-auto items-center justify-center">
    <x-slot name="heading">
        Edit Ticket
    </x-slot>

    <form wire:submit="update">
        {{$this->form}}

        <x-filament::button type="submit">
            Save Ticket
        </x-filament::button>
    </form
</x-filament::section>
