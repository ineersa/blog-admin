<?php

namespace App\Filament\Resources\PostResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;

class TagsRelationManager extends RelationManager
{
    protected static string $relationship = 'tags';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->minLength(3)
                    ->maxLength(255)
                    ->live(onBlur: true)
                    ->afterStateUpdated(function (string $operation, string $state, Forms\Set $set, Forms\Get $get) {
                        // operation - create, edit
                        // state - current value
                        if ($operation === 'edit') {
                            return;
                        }
                        $slug = Str::slug($state);
                        $set('slug', $slug);
                    }),
                Forms\Components\TextInput::make('slug')
                    ->required()
                    ->readOnlyOn('edit')
                    ->unique(ignoreRecord: true)
                    ->regex('/^[a-z0-9]+(?:[_-][a-z0-9]+)*$/')
                    ->minLength(3)
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('slug')
                    ->searchable()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
