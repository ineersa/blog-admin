<?php

namespace App\Filament\Resources\PostResource\Pages;

use App\Filament\Resources\PostResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use \Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListPosts extends ListRecords
{
    protected static string $resource = PostResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        return [
            Tab::make('All'),
            Tab::make('Published')->modifyQueryUsing(function (Builder $query) {
                $query->where('published', true);
            }),
            Tab::make('Un Published')->modifyQueryUsing(function (Builder $query) {
                $query->where('published', false);
            }),
        ];
    }
}
