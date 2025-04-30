<?php

namespace App\Filament\Resources\PayroleResource\Pages;

use App\Filament\Resources\PayroleResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPayroles extends ListRecords
{
    protected static string $resource = PayroleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
