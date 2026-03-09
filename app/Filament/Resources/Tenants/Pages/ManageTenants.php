<?php

namespace App\Filament\Resources\Tenants\Pages;

use App\Filament\Resources\Tenants\TenantResource;
use App\Models\Tenant;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;
use Illuminate\Support\Facades\Artisan;

class ManageTenants extends ManageRecords
{
    protected static string $resource = TenantResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->using(function (array $data) {
                    $tenant = Tenant::create([
                        'id' => $data['id'],
                    ]);

                    $tenant->domains()->create([
                        'domain' => $data['domain'],
                    ]);

                    Artisan::call('tenants:migrate', [
                        '--tenants' => [$tenant->id],
                    ]);

                    return $tenant;
                }),
        ];
    }
}
