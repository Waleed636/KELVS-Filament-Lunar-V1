<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;
use Lunar\Models\Product;
use App\Models\Post;

class SitemapController extends Controller
{
    /**
     * Generate dynamic XML sitemap.
     */
    public function index(): Response
    {
        $urls = [];
        $baseUrl = url('/');

        // 1. Static Pages
        $staticPages = [
            ['path' => '', 'changefreq' => 'daily', 'priority' => '1.0'],
            ['path' => '/shop', 'changefreq' => 'daily', 'priority' => '0.8'],
            ['path' => '/about', 'changefreq' => 'weekly', 'priority' => '0.5'],
            ['path' => '/blog', 'changefreq' => 'daily', 'priority' => '0.7'],
        ];

        foreach ($staticPages as $page) {
            $urls[] = [
                'loc' => $baseUrl . $page['path'],
                'lastmod' => now()->startOfDay()->toAtomString(),
                'changefreq' => $page['changefreq'],
                'priority' => $page['priority'],
            ];
        }

        // 2. Dynamic Products
        try {
            $products = Product::with('urls')->get();
            foreach ($products as $product) {
                $slug = $product->urls->first()?->slug;
                if ($slug) {
                    $urls[] = [
                        'loc' => $baseUrl . '/products/' . $slug,
                        'lastmod' => ($product->updated_at ?? now())->toAtomString(),
                        'changefreq' => 'weekly',
                        'priority' => '0.9',
                    ];
                }
            }
        } catch (\Exception $e) {
            // Log or handle error gracefully
        }

        // 3. Dynamic Blog Posts
        try {
            if (class_exists(Post::class)) {
                $posts = Post::published()->get();
                foreach ($posts as $post) {
                    $urls[] = [
                        'loc' => $baseUrl . '/blog/post/' . $post->slug,
                        'lastmod' => ($post->updated_at ?? now())->toAtomString(),
                        'changefreq' => 'weekly',
                        'priority' => '0.7',
                    ];
                }
            }
        } catch (\Exception $e) {
            // Log or handle error gracefully
        }

        $xml = view('sitemap', compact('urls'))->render();

        return response($xml, 200)
            ->header('Content-Type', 'text/xml');
    }
}
