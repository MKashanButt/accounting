<?php

namespace App\Filament\Resources\ExpenseResource\Pages;

use App\Filament\Resources\ExpenseResource;
use App\Models\Expense;
use App\Models\User;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;
use Filament\Resources\Pages\ListRecords;

class ListExpenses extends ListRecords
{
    protected static string $resource = ExpenseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        $managers = User::whereHas('role', fn($q) => $q->where('name', 'manager'))
            ->get();

        // Start with the "All" tab
        $tabs = [
            'all' => Tab::make('All Expenses')
                ->badge(Expense::count()),
        ];

        // Add a tab for each manager
        foreach ($managers as $manager) {
            $tabs[$manager->name] = Tab::make($manager->name)
                ->modifyQueryUsing(fn(Builder $query) => $query->where('user_id', $manager->id))
                ->badge($manager->budget);
        }

        return $tabs;;
    }
}
