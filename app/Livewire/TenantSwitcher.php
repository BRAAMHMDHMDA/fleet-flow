<?php

namespace App\Livewire;

use App\Models\Tenant;
use Illuminate\View\View;
use Livewire\Component;
use Stancl\Tenancy\Database\Models\Domain;

class TenantSwitcher extends Component
{
    public $selectedTenant;

    public function mount(): void
    {
        $this->selectedTenant = Tenant::find(session('selected_tenant_id'));
    }

    public function switchTenant(?string $tenantId): void
    {
        $tenant = Tenant::find($tenantId);

        if ($tenant) {
            session([
                'selected_tenant_id' => $tenant->id,
                'selected_tenant_name' => $tenant->id,
            ]);
            $this->selectedTenant = $tenant;
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
