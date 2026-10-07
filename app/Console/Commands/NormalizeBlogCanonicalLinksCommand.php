<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\Post;

class NormalizeBlogCanonicalLinksCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'seo:normalize-blog-links {--dry-run : Run without making database changes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Normalize all in-body blog links to canonical https://kelvsint.com/blog/post/{slug} structure and export SQL migration';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $isDryRun = $this->option('dry-run');
        $this->info($isDryRun ? 'Starting DRY RUN for blog link normalization...' : 'Starting Blog Link Normalization...');

        $posts = Post::all();
        $this->line("Found {$posts->count()} total posts in database.");

        $policyMap = [
            'privacy-policy'       => 'https://kelvsint.com/privacy-policy',
            'return-policy'        => 'https://kelvsint.com/return-policy',
            'terms-and-conditions' => 'https://kelvsint.com/terms-and-conditions',
            'shipping-policy'      => 'https://kelvsint.com/shipping-policy',
            'refund-policy'        => 'https://kelvsint.com/return-policy',
        ];

        // Backup current posts
        $backupData = [];
        foreach ($posts as $p) {
            $backupData[$p->id] = [
                'id'      => $p->id,
                'slug'    => $p->slug,
                'title'   => $p->title,
                'content' => $p->content,
            ];
        }

        $backupPath = database_path('sql/posts_backup_links.json');
        @file_put_contents($backupPath, json_encode($backupData, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        $this->info("Backup saved to: {$backupPath}");

        $sqlStatements = [];
        $totalReplacements = 0;
        $affectedPosts = 0;

        DB::beginTransaction();

        try {
            foreach ($posts as $post) {
                $content = $post->content;
                $originalContent = $content;
                $postReplacements = 0;

                // 1. Matches: href="https://kelvsint.com/blog/{slug}" or href="/blog/{slug}"
                $pattern = '#(?<prefix>href=[\'"])(?:https?://(?:www\.)?kelvsint\.com)?/blog/(?<slug>[a-zA-Z0-9_\-]+)(?<suffix>[\'"])#i';
                $content = preg_replace_callback($pattern, function ($matches) use ($policyMap, &$postReplacements) {
                    $slug = $matches['slug'];
                    $cleanSlug = str_replace('_', '-', strtolower(trim($slug)));

                    // Do not replace Zeus Sky reserved keywords
                    if (in_array($cleanSlug, ['post', 'category', 'tag', 'faq', 'library', 'page'])) {
                        return $matches[0];
                    }

                    $postReplacements++;

                    if (isset($policyMap[$cleanSlug])) {
                        return $matches['prefix'] . $policyMap[$cleanSlug] . $matches['suffix'];
                    }

                    return $matches['prefix'] . "https://kelvsint.com/blog/post/{$cleanSlug}" . $matches['suffix'];
                }, $content);

                // 2. Matches: href="https://kelvsint.com/blog/{slug}/" (with trailing slash)
                $patternTrailing = '#(?<prefix>href=[\'"])(?:https?://(?:www\.)?kelvsint\.com)?/blog/(?<slug>[a-zA-Z0-9_\-]+)/(?<suffix>[\'"])#i';
                $content = preg_replace_callback($patternTrailing, function ($matches) use ($policyMap, &$postReplacements) {
                    $slug = $matches['slug'];
                    $cleanSlug = str_replace('_', '-', strtolower(trim($slug)));

                    if (in_array($cleanSlug, ['post', 'category', 'tag', 'faq', 'library', 'page'])) {
                        return $matches[0];
                    }

                    $postReplacements++;

                    if (isset($policyMap[$cleanSlug])) {
                        return $matches['prefix'] . $policyMap[$cleanSlug] . $matches['suffix'];
                    }

                    return $matches['prefix'] . "https://kelvsint.com/blog/post/{$cleanSlug}" . $matches['suffix'];
                }, $content);

                // 3. Matches: href="https://www.kelvsint.com/blog/post/{slug}" (removes www from /blog/post/)
                $patternWwwPost = '#(?<prefix>href=[\'"])https?://www\.kelvsint\.com/blog/post/(?<slug>[a-zA-Z0-9_\-]+)(?<suffix>[\'"])#i';
                $content = preg_replace_callback($patternWwwPost, function ($matches) use (&$postReplacements) {
                    $slug = $matches['slug'];
                    $cleanSlug = str_replace('_', '-', strtolower(trim($slug)));
                    $postReplacements++;
                    return $matches['prefix'] . "https://kelvsint.com/blog/post/{$cleanSlug}" . $matches['suffix'];
                }, $content);

                if ($content !== $originalContent) {
                    $affectedPosts++;
                    $totalReplacements += $postReplacements;
                    $this->line(" - Post #{$post->id} [{$post->slug}]: {$postReplacements} links normalized.");

                    if (!$isDryRun) {
                        DB::table('posts')->where('id', $post->id)->update([
                            'content' => $content,
                        ]);
                    }

                    $escapedContent = addcslashes($content, "'\\");
                    $sqlStatements[] = "UPDATE `posts` SET `content` = '{$escapedContent}' WHERE `id` = {$post->id};";
                }
            }

            if (!$isDryRun) {
                DB::commit();
                $this->info("Database successfully updated ({$totalReplacements} links in {$affectedPosts} posts).");
            } else {
                DB::rollBack();
                $this->warn("Dry run completed. No database changes were committed.");
            }

            // Export SQL file for production deployment
            if (!empty($sqlStatements)) {
                $sqlContent = "-- KELVS Blog In-Body Links Canonical Normalization\n";
                $sqlContent .= "-- Generated: " . date('Y-m-d H:i:s') . "\n";
                $sqlContent .= "SET NAMES utf8mb4;\n";
                $sqlContent .= "START TRANSACTION;\n\n";
                $sqlContent .= implode("\n\n", $sqlStatements) . "\n\n";
                $sqlContent .= "COMMIT;\n";

                $sqlFilePath = database_path('sql/fix_blog_canonical_links_prod.sql');
                @file_put_contents($sqlFilePath, $sqlContent);
                $this->info("Production SQL migration file saved to: {$sqlFilePath}");
            }

            $this->info("Normalization complete! Summary: {$totalReplacements} links updated across {$affectedPosts} posts.");
            return 0;

        } catch (\Throwable $e) {
            DB::rollBack();
            $this->error("Failed to normalize links: " . $e->getMessage());
            return 1;
        }
    }
}
