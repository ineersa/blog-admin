<?php

namespace App\Filament\Widgets;

use App\Models\Post;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class PostsCount extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Posts Count', function () {
                $str = 'Total - '.Post::count().'<br>';
                $str .= 'Published - '.Post::wherePublished(true)->count().'<br>';
                $str .= 'Not Published - '.Post::wherePublished(false)->count();

                return new \Illuminate\Support\HtmlString($str);
            }),
        ];
    }
}
