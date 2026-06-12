<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Models\User;

class BlogMigrationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sqlPath = base_path('posts.sql');

        if (!file_exists($sqlPath)) {
            $this->command->error("posts.sql file not found at root!");
            return;
        }

        $this->command->info("Reading posts.sql...");
        $sql = file_get_contents($sqlPath);

        // Sanitize and rename the posts table to old_posts to avoid conflicts with existing posts table
        $sql = str_replace('`posts`', '`old_posts`', $sql);

        // Drop old_posts table if it already exists
        Schema::dropIfExists('old_posts');

        $this->command->info("Importing old posts into temporary table 'old_posts'...");
        
        // Execute the SQL unprepared
        DB::unprepared($sql);

        if (!Schema::hasTable('old_posts')) {
            $this->command->error("Failed to import old_posts table!");
            return;
        }

        $oldPosts = DB::table('old_posts')->get();
        $this->command->info("Found " . $oldPosts->count() . " posts to migrate.");

        // Get or create a default user for authorship
        $defaultUserId = User::where('id', 1)->first()?->id 
            ?? User::first()?->id 
            ?? User::factory()->create([
                'name' => 'Admin User',
                'email' => 'admin@example.com',
                'password' => bcrypt('password'),
            ])->id;

        // Get models from sky config/plugin
        $postModelClass = config('zeus-sky.models.Post', \LaraZeus\Sky\Models\Post::class);
        $tagModelClass = config('zeus-sky.models.Tag', \LaraZeus\Sky\Models\Tag::class);

        // First, ensure the "Skincare" category exists in Spatie Tags
        $skincareCategory = $tagModelClass::firstOrCreate(
            ['slug->en' => 'skincare', 'type' => 'category'],
            [
                'name' => ['en' => 'Skincare'],
                'slug' => ['en' => 'skincare'],
            ]
        );

        $migratedCount = 0;

        foreach ($oldPosts as $oldPost) {
            $this->command->info("Migrating: {$oldPost->title}");

            // Map status
            $status = 'draft';
            if (strtoupper($oldPost->status) === 'PUBLISHED') {
                $status = 'publish';
            }

            // Create new post model
            $post = new $postModelClass();
            $post->setTranslation('title', 'en', $oldPost->title);
            $post->setTranslation('description', 'en', $oldPost->excerpt ?? '');
            $post->setTranslation('content', 'en', $oldPost->body);

            $post->slug = $oldPost->slug;
            $post->post_type = 'post';
            $post->user_id = $oldPost->author_id == 1 ? $defaultUserId : ($oldPost->author_id ?: $defaultUserId);
            $post->featured_image = $oldPost->image;
            $post->status = $status;
            
            // Map featured status using sticky_until
            if ($oldPost->featured) {
                $post->sticky_until = now()->addYears(10);
            }

            // Map timestamps
            $post->published_at = $oldPost->created_at ?: now();
            $post->created_at = $oldPost->created_at ?: now();
            $post->updated_at = $oldPost->updated_at ?: now();

            $post->save();

            // Sync the category using the Spatie Tags method on the model
            $post->syncTagsWithType([$skincareCategory->name], 'category');

            // Parse and attach keywords as tags (type => 'tag')
            if (!empty($oldPost->keywords)) {
                // Remove carriage returns and handle various spacing
                $cleanKeywords = str_replace("\r\n", ",", $oldPost->keywords);
                $cleanKeywords = str_replace("\n", ",", $cleanKeywords);
                $keywords = array_filter(array_map('trim', explode(',', $cleanKeywords)));
                
                foreach ($keywords as $keyword) {
                    if (!empty($keyword)) {
                        $post->attachTag($keyword, 'tag');
                    }
                }
            }

            $migratedCount++;
        }

        // Drop the temporary old_posts table
        Schema::dropIfExists('old_posts');

        $this->command->info("Successfully migrated {$migratedCount} posts!");
    }
}
