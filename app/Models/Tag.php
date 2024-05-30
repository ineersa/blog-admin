<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
    ];

    public function posts()
    {
        return $this
            ->belongsToMany(Post::class, 'posts_tags')
            ->withTimestamps();
    }

    public function postsPublished()
    {
        return $this
            ->posts()
            ->where('posts.published', '=', true);
    }

    public function postsUnpublished()
    {
        return $this
            ->posts()
            ->where('posts.published', '!=', true);
    }
}
