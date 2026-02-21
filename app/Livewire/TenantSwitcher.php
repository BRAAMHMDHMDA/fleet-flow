<?php

namespace App\Livewire;

use App\Models\Tenant;
use Illuminate\View\View;
use Livewire\Component;
use Stancl\Tenancy\Database\Models\Domain;

class TenantSwitcher extends Component
{
    public ?string $selectedTenant = null;

    public function mount(): void
    {
        $this->selectedTenant = session('selected_tenant_id');
    }

    public function switchTenant(?string $tenantId): void
    {
        if (! $tenantId) {
            session()->forget(['selected_tenant_id', 'selected_tenant_name']);
            $this->selectedTenant = null;
            $this->dispatch('tenant-switched');
            return;
        }

        $tenant = Tenant::find($tenantId);

        if ($tenant) {
            session([
                'selected_tenant_id' => $tenant->id,
                'selected_tenant_name' => $tenant->id,
            ]);

            $this->selectedTenant = $tenant->id;
            $this->dispatch('tenant-switched');
        }
    }

    public function getTenants(): array
    {
        return Tenant::all()->map(function ($tenant) {
            $domain = Domain::where('tenant_id', $tenant->id)->first();

            return [
                'id' => $tenant->id,
                'name' => $domain?->domain ?? $tenant->id,
            ];
        })->toArray();
    }

    public function render(): View
    {
        return view('filament.components.tenant-switcher');
    }
}
