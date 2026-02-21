@php
    $tenants = $this->getTenants();
    $selectedTenantName = session('selected_tenant_name') ?? 'Select tenant';
@endphp

<x-filament::dropdown placement="bottom-end" width="xs">
    <x-slot name="trigger">
        <x-filament::button color="gray" icon="heroicon-m-building-office-2">
            {{ $selectedTenantName }}
        </x-filament::button>
    </x-slot>

    <x-filament::dropdown.list>
        <x-filament::dropdown.list.item
            wire:click="switchTenant(null)"
            icon="heroicon-m-x-mark"
            color="gray"
        >
            No tenant
        </x-filament::dropdown.list.item>

        <x-filament::dropdown.list.item disabled>
            Switch tenant
        </x-filament::dropdown.list.item>

        @foreach ($tenants as $tenant)
            <x-filament::dropdown.list.item
                wire:click="switchTenant('{{ $tenant['id'] }}')"
                icon="{{ $selectedTenant === $tenant['id'] ? 'heroicon-m-check' : null }}"
                color="{{ $selectedTenant === $tenant['id'] ? 'primary' : 'gray' }}"
            >
                {{ $tenant['name'] }}
            </x-filament::dropdown.list.item>
        @endforeach
    </x-filament::dropdown.list>
</x-filament::dropdown>
