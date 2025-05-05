<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ExpenseResource\Pages;
use App\Filament\Resources\ExpenseResource\RelationManagers;
use App\Models\Expense;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Resources\Components\Tab;
use Illuminate\Support\Facades\Storage;

class ExpenseResource extends Resource
{
    protected static ?string $model = Expense::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\DatePicker::make('date')
                    ->required(),
                Forms\Components\Textarea::make('description')
                    ->required()
                    ->columnSpanFull(),
                Forms\Components\FileUpload::make('file')
                    ->columnSpanFull(),
                Forms\Components\Select::make('type')
                    ->options([
                        'Debit' => 'Debit',
                        'Credit' => 'Credit',
                    ])
                    ->required(),
                Forms\Components\TextInput::make('amount')
                    ->required()
                    ->numeric(),
                Forms\Components\Select::make('user_id')
                    ->label('Users')
                    ->options(
                        User::whereHas('role', function ($q) {
                            $q->where('name', 'manager'); // Adjust this condition as needed
                        })
                            ->pluck('name', 'id') // This assumes you want name as option text and id as value
                            ->toArray()
                    )
                    ->default(null),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('date')
                    ->label('Date')
                    ->date()
                    ->sortable(),

                Tables\Columns\TextColumn::make('description')
                    ->label('Description')
                    ->limit(40)
                    ->wrap(),

                Tables\Columns\TextColumn::make('file')
                    ->label('Attachment')
                    ->url(fn($record) => Storage::url($record->file))
                    ->openUrlInNewTab()
                    ->formatStateUsing(fn() => 'View'),

                Tables\Columns\TextColumn::make('user.name')
                    ->label('User')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('debit_summary')
                    ->label('Debit')
                    ->state(fn($record) => $record->type === 'Debit' ? number_format($record->amount, 2) : null)
                    ->color(fn($record) => $record->type === 'Debit' ? 'success' : null),

                Tables\Columns\TextColumn::make('credit')
                    ->label('Credit')
                    ->getStateUsing(fn($record) => $record->type === 'Credit' ? number_format($record->amount, 2) : null)
                    ->color(fn($record) => $record->type === 'Credit' ? 'danger' : null),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Logged On')
                    ->dateTime()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultGroup('date')
            ->groups([
                Group::make('date')
                    ->label('Month')
                    ->getTitleFromRecordUsing(function (Expense $record) {
                        return $record->date->format('F Y'); // e.g. "March 2024"
                    })
                    ->collapsible()
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListExpenses::route('/'),
            'create' => Pages\CreateExpense::route('/create'),
            'edit' => Pages\EditExpense::route('/{record}/edit'),
        ];
    }
}
