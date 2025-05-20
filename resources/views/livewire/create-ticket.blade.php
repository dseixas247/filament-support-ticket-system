<x-filament::section class="max-w-7xl mx-auto items-center justify-center">
    <x-slot name="heading">
        Create Ticket
    </x-slot>

    <form wire:submit="create">
        {{$this->form}}

        <x-filament::button type="submit">
            Create Ticket
        </x-filament::button>
    </form
</x-filament::section>