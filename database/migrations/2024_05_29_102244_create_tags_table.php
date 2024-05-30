<?php

use App\Models\Post;
use App\Models\Tag;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tags', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->timestamps();
        });
        Schema::create('posts_tags', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Post::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(Tag::class)->constrained()->cascadeOnDelete();
            $table->timestamps();
        });
        Schema::dropColumns('posts', 'tags');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('posts_tags');
        Schema::dropIfExists('tags');
        Schema::table('posts', function (Blueprint $table) {
            $table->json('tags')->nullable();
        });
    }
};
