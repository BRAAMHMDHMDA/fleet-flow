<?php

namespace App\Filament\Resources\Tenants;

use App\Filament\Resources\Tenants\Pages\ManageTenants;
use App\Models\Tenant;
use App\Rules\UniqueDomain;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Stancl\Tenancy\Database\Models\Domain;

class TenantResource extends Resource
{
    protected static ?string $model = Tenant::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'id';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('id')
                ->required()
                ->unique()
                ->minLength(3)
                ->maxLength(30)
                ->label('Tenant ID'),

                TextInput::make('domain')
                    ->required()
                    ->minLength(3)
                    ->maxLength(30)
                    ->label('Domain')
                    ->suffix(config('tenancy.central_domain_suffix'))
                    ->rules(fn ($record) => [new UniqueDomain($record?->id)])
                    ->dehydrateStateUsing(function (?string $state) {
                        return $state . config('tenancy.central_domain_suffix');
                    })
            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('id')
                    ->label('ID'),

                TextEntry::make('domains.domain')
                    ->label('Domain')
                    ->formatStateUsing(fn ($state) => $state),

                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->searchable(),
                TextColumn::make('domains.domain')
                    ->label('Domain')
                    ->searchable()
                    ->formatStateUsing(fn ($state) => $state),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make()
                    ->mutateRecordDataUsing(function (array $data): array {
                        $suffix = config('tenancy.central_domain_suffix') ?? '';

                        $domain = DB::table('domains')
                            ->where('tenant_id', $data['id'])
                            ->value('domain');

                        $data['domain'] = $domain
                            ? Str::of($domain)->replaceEnd($suffix, '')->value()
                            : null;

                        return $data;
                    })
                    ->using(function (array $data, Tenant $record): void {
                        $record->update([
                            'id' => $data['id'],
                        ]);

                        $record->domains()->update([
                            'domain' => $data['domain']
                        ]);
                    }),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageTenants::route('/'),
        ];
    }
}
