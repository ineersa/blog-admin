<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TagResource\Pages;
use App\Filament\Resources\TagResource\RelationManagers;
use App\Models\Tag;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TagResource extends Resource
{
    protected static ?string $model = Tag::class;

    protected static ?string $navigationIcon = 'heroicon-o-hashtag';

    protected static ?string $navigationLabel = 'Tags';

    protected static ?string $navigationGroup = 'Blog';

    public static function form(Form $form): Form
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

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('slug')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('posts_count')
                    ->counts(['posts'])
                    ->label('Posts Count')
                    ->sortable(),
                TextColumn::make('posts_published_count')
                    ->counts(['postsPublished'])
                    ->label('Published?')
                    ->sortable(),
                TextColumn::make('posts_unpublished_count')
                    ->counts(['postsUnpublished'])
                    ->label('Unpublished?')
                    ->sortable(),
                TextColumn::make('created_at')->label('Created')->date()
                    ->sortable()->searchable()->toggleable(),

            ])
            ->filters([
                //
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

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTags::route('/'),
            'create' => Pages\CreateTag::route('/create'),
            'edit' => Pages\EditTag::route('/{record}/edit'),
        ];
    }
}
