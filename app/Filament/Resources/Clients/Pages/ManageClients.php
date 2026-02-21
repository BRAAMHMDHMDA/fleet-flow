<?php

namespace App\Filament\Resources\Clients\Pages;

use App\Filament\Resources\Clients\ClientResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageClients extends ManageRecords
{
    protected static string $resource = ClientResource::class;

    protected $listeners = ['tenant-switched' => '$refresh'];

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->disabled(fn () => blank(session('selected_tenant_id')))
                ->tooltip(fn () => blank(session('selected_tenant_id'))
                    ? 'Select a tenant first.'
                    : null),
            ];
    }
}
