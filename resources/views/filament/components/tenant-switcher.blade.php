<x-filament::dropdown placement="bottom-end" width="xs">
    <x-slot name="trigger">
        <x-filament::button color="gray" icon="heroicon-m-building-office-2">
            {{ $selectedTenant->id }}
        </x-filament::button>
    </x-slot>

    <x-filament::dropdown.list>

        <x-filament::dropdown.list.item disabled>
            Switch tenant
        </x-filament::dropdown.list.item>

        @foreach ($this->getTenants() as $tenant)
            <x-filament::dropdown.list.item
                wire:click="switchTenant('{{ $tenant['id'] }}')"
                icon="{{ $selectedTenant->id === $tenant['id'] ? 'heroicon-m-check' : null }}"
                color="{{ $selectedTenant->id === $tenant['id'] ? 'primary' : 'gray' }}"
            >
                {{ $tenant['name'] }}
            </x-filament::dropdown.list.item>
        @endforeach
    </x-filament::dropdown.list>
</x-filament::dropdown>
