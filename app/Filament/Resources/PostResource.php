<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PostResource\Pages;
use App\Filament\Resources\PostResource\RelationManagers;
use App\Models\Post;
use Filament\Forms;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\ColorColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class PostResource extends Resource
{
    protected static ?string $model = Post::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-duplicate';

    protected static ?string $navigationLabel = 'Blog posts';

    protected static ?string $navigationGroup = 'Blog';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Content')->schema([
                    TextInput::make('title')
                        ->minLength(10)
                        ->maxLength(120)
                        ->live(onBlur: true)
                        ->afterStateUpdated(function (string $operation, string $state, Forms\Set $set, Forms\Get $get) {
                            $firstSentence = explode('.', $state);
                            $set('short_description', $firstSentence[0] ?? '');
                            // operation - create, edit
                            // state - current value
                            if ($operation === 'edit') {
                                return;
                            }
                            $slug = Str::slug($state);
                            $set('slug', $slug);
                        })
                        ->required(),
                    TextInput::make('slug')
                        ->minLength(3)
                        ->maxLength(255)
                        ->unique(ignoreRecord: true)
                        ->regex('/^[a-z0-9]+(?:[_-][a-z0-9]+)*$/')
                        ->readOnlyOn('edit')
                        ->required(),
                    Select::make('category_id')
                        ->relationship('category', 'name')
                        ->required()
                        ->label('Category'),
                    ColorPicker::make('color')
                        ->hexColor()
                        ->required(),
                    MarkdownEditor::make('content')
                        ->required()
                        ->live(onBlur: true)
                        ->afterStateUpdated(function (string $operation, string $state, Forms\Set $set, Forms\Get $get) {
                            // operation - create, edit
                            // state - current value
                            if ($operation === 'edit') {
                                return;
                            }
                            $firstSentence = explode('.', $state);
                            $set('short_description', $firstSentence[0] ?? '');
                        })
                        ->fileAttachmentsDisk('shared')
                        ->fileAttachmentsDirectory('attachments')
                        ->columnSpanFull(),
                    Forms\Components\Textarea::make('short_description')
                        ->required()
                        ->minLength('30')
                        ->maxLength(255)
                        ->columnSpanFull(),
                ])->columnSpan(2)
                    ->columns(2),
                Group::make()->schema([
                    Section::make('Image')->schema([
                        FileUpload::make('thumbnail')
                            ->disk('shared')
                            ->directory('thumbnails')
                            ->required(),
                    ])
                        ->collapsible()
                        ->columnSpan(1)
                        ->columns(1),
                    Section::make('Meta')->schema([
                        Select::make('tags')
                            ->relationship('tags', 'name')
                            ->multiple()
                            ->preload()
                            ->rules([
                                fn (string $operation): \Closure => function (string $attribute, $value, \Closure $fail) use ($operation) {
                                    if ($operation === 'edit') {
                                        if (empty($value)) {
                                            $fail('Tags are required on edit.');
                                        }
                                    }
                                },
                            ]),
                        TagsInput::make('keywords')
                            ->required()
                            ->placeholder('Keyword'),
                        Toggle::make('published'),
                    ])->columnSpan(1)
                        ->columns(1),
                ]),
            ])->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('thumbnail')
                    ->disk('shared')
                    ->toggleable(),
                ColorColumn::make('color')->toggleable(),
                TextColumn::make('title')->sortable()->searchable()->toggleable(),
                TextColumn::make('slug')->sortable()->searchable()->toggleable(),
                TextColumn::make('category.name')
                    ->sortable()->searchable()->toggleable(),
                TextColumn::make('tags.name')->toggleable(),
                ToggleColumn::make('published')->sortable(),
                TextColumn::make('created_at')->label('Created')->date()
                    ->sortable()->searchable()->toggleable(),
            ])
            ->filters([
                /*Filter::make('Published Posts')->query(
                    function (Builder $query) {
                        $query->where('published', true);
                    }
                ),
                Tables\Filters\TernaryFilter::make('published')->label('Is Published'),*/
                Tables\Filters\SelectFilter::make('category_id')
                    ->relationship('category', 'name')
                    ->searchable()
                    ->preload()
                    ->label('Category'),
                Tables\Filters\SelectFilter::make('tags')
                    ->relationship('tags', 'name')
                    ->searchable()
                    ->multiple()
                    ->preload()
                    ->label('Tags'),
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
            RelationManagers\TagsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPosts::route('/'),
            'create' => Pages\CreatePost::route('/create'),
            'edit' => Pages\EditPost::route('/{record}/edit'),
        ];
    }
}
