<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $posts = DB::table('posts')->get();

        foreach ($posts as $post) {
            $oldSlug = $post->slug;
            $newSlug = $oldSlug;

            // Remove leading/trailing slashes and "/blog/" prefix if present
            if (str_starts_with($newSlug, '/blog/')) {
                $newSlug = substr($newSlug, 6);
            }
            if (str_starts_with($newSlug, '/')) {
                $newSlug = ltrim($newSlug, '/');
            }
            if (str_ends_with($newSlug, '/')) {
                $newSlug = rtrim($newSlug, '/');
            }

            // Replace underscores with hyphens
            $newSlug = str_replace('_', '-', $newSlug);
            
            // Clean up any double/multiple hyphens
            $newSlug = preg_replace('/-+/', '-', $newSlug);

            // Lowercase the slug
            $newSlug = Str::lower($newSlug);

            if ($oldSlug !== $newSlug) {
                DB::table('posts')
                    ->where('id', $post->id)
                    ->update(['slug' => $newSlug]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Slugs conversion to lowercase and hyphens is not cleanly reversible 
        // to their exact original camelCase/underscored form, so we do nothing here.
    }
};
