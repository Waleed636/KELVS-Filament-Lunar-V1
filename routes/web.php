<?php

use Illuminate\Support\Facades\Route;

Route::get('/', \App\Livewire\Storefront\Home::class);
Route::get('/shop', \App\Livewire\Storefront\Shop::class);
Route::get('/about', \App\Livewire\Storefront\About::class);
Route::get('/products/{slug}', \App\Livewire\Storefront\ProductShow::class);
Route::get('/cart', \App\Livewire\Storefront\CartPage::class);
Route::get('/checkout', \App\Livewire\Storefront\Checkout::class);
Route::get('/checkout/thankyou/{id}', \App\Livewire\Storefront\ThankYou::class)->name('checkout.thankyou');

Route::redirect('/login', '/admin/login')->name('login');

// ── Policy & Legacy Content 1-Hop Rescue Redirects ───────────────────────────
Route::redirect('/about-us', '/about', 301);
Route::redirect('/refund-policy', '/return-policy', 301);

$policyList = [
    'privacy-policy',
    'return-policy',
    'terms-and-conditions',
    'shipping-policy',
    'refund-policy',
];

foreach ($policyList as $pSlug) {
    $target = $pSlug === 'refund-policy' ? '/return-policy' : '/' . $pSlug;
    Route::redirect('/blog/post/' . $pSlug, $target, 301);
    Route::redirect('/blog/page/' . $pSlug, $target, 301);
}

// Redirect legacy /blog/{slug} URLs to new /blog/post/{slug} structure (converting underscores to hyphens and lowercasing)
Route::get('/blog/{slug}', function ($slug) {
    $cleanSlug = str_replace('_', '-', strtolower(trim($slug)));
    
    $policyMap = [
        'privacy-policy'       => '/privacy-policy',
        'return-policy'        => '/return-policy',
        'terms-and-conditions' => '/terms-and-conditions',
        'shipping-policy'      => '/shipping-policy',
        'refund-policy'        => '/return-policy',
    ];

    if (array_key_exists($cleanSlug, $policyMap)) {
        return redirect()->to($policyMap[$cleanSlug], 301);
    }

    return redirect()->to('/blog/post/' . $cleanSlug, 301);
});

// Redirect legacy /shop/{slug} URLs directly to new /products/{new_slug} structure in 1 single hop
Route::get('/shop/{slug}', function ($slug) {
    $normalizedSlug = str_replace(['_', ' '], '-', strtolower(trim($slug)));

    $slugMap = [
        'kelvs-whitening-serum'             => 'kelvs-whitening-serum-alpha-arbutin-hyaluronic-acid-fades-dark-spots',
        'kelvs-vitamin-c-serum'             => 'kelvs-vitamin-c-serum-sodium-ascorbyl-phosphate-brightens-skin-fights-acne',
        'anti-aging-serum'                  => 'kelvs-anti-aging-serum-niacinamide-hyaluronic-acid-minimizes-pores-controls-oil',
        'kelvs-anti-aging-serum'            => 'kelvs-anti-aging-serum-niacinamide-hyaluronic-acid-minimizes-pores-controls-oil',
        'kelvs-hydrating-serum'             => 'kelvs-hydration-serum-vitamin-b5-hyaluronic-acid-deep-hydration-skin-barrier-repair',
        'kelvs-hydration-serum'             => 'kelvs-hydration-serum-vitamin-b5-hyaluronic-acid-deep-hydration-skin-barrier-repair',
        'kelvs-vitamin-e-serum-(oil-blend)' => 'kelvs-vitamin-e-serum-jojoba-rosehip-argan-grapeseed-deep-hydration-skin-restoration',
        'kelvs-vitamin-e-serum'             => 'kelvs-vitamin-e-serum-jojoba-rosehip-argan-grapeseed-deep-hydration-skin-restoration',
        'kelvs-aha-serum'                   => 'kelvs-lactic-acid-5-serum-lactic-acid-hyaluronic-acid-smooth-skin-texture-even-tone',
        'kelvs-lactic-acid-serum'           => 'kelvs-lactic-acid-5-serum-lactic-acid-hyaluronic-acid-smooth-skin-texture-even-tone',
        'kelvs-bha-serum'                   => 'kelvs-bha-salicylic-acid-2-serum-salicylic-acid-hyaluronic-acid-clear-pores-fight-acne',
        'kelvs-gentle-cleanser'             => 'kelvs-gentle-cleanser-sodium-lauroyl-sarcosinate-coco-glucoside-sulfate-free-daily-cleanse',
        'kelvs-toning-solution'             => 'kelvs-toning-solution-glycolic-acid-6-aha-brighten-skin-smooth-texture',
        'kelvs-calming-toner'               => 'kelvs-calming-toner-cucumber-rose-aloe-vera-menthol-soothe-hydrate-minimize-pores',
        'kelvs-micellar-water'              => 'kelvs-micellar-water-no-rinse-makeup-remover-gentle-cleanser-all-skin-types',
        'kelvs-rose-water-mist'             => 'kelvs-rose-water-mist-rose-hydrosol-hydrate-soothe-protect-skin-all-day',
        'aloe-vera-mist'                    => 'kelvs-aloe-vera-mist-pure-aloe-vera-extract-calm-hydrate-refresh-skin',
        'kelvs-aloe-vera-mist'              => 'kelvs-aloe-vera-mist-pure-aloe-vera-extract-calm-hydrate-refresh-skin',
        'detangle-shampoo'                  => 'kelvs-detangle-shampoo-cetrimonium-chloride-reduce-breakage-tangles-buildup',
        'kelvs-detangle-shampoo'            => 'kelvs-detangle-shampoo-cetrimonium-chloride-reduce-breakage-tangles-buildup',
        'keratin-hair-masque'               => 'kelvs-keratin-hair-masque-hydrolyzed-keratin-coconut-castor-oil-repair-frizz-control-breakage',
        'kelvs-keratin-hair-masque'         => 'kelvs-keratin-hair-masque-hydrolyzed-keratin-coconut-castor-oil-repair-frizz-control-breakage',
        'kelvs-anti-dandruff-shampoo'       => 'kelvs-anti-dandruff-shampoo-zinc-pyrithione-cetrimonium-chloride-eliminate-flakes-soothe-scalp',
        'anti-dandruff-shampoo'             => 'kelvs-anti-dandruff-shampoo-zinc-pyrithione-cetrimonium-chloride-eliminate-flakes-soothe-scalp',
        'keratin-detangle-combo'            => 'kelvs-hair-repair-combo-detangle-shampoo-keratin-hair-masque-repair-detangle-eliminate-frizz',
        'hair-repair-combo'                 => 'kelvs-hair-repair-combo-detangle-shampoo-keratin-hair-masque-repair-detangle-eliminate-frizz',
        'scalp-hair-repair-combo'           => 'kelvs-scalp-hair-repair-combo-anti-dandruff-shampoo-keratin-hair-masque-clear-scalp-restore-hair',
        'clear-skin-starter-kit'            => 'kelvs-clear-skin-starter-kit-cleanser-bha-vitamin-c-hydrating-serum-complete-4-step-routine',
    ];

    if (array_key_exists($normalizedSlug, $slugMap)) {
        return redirect()->to('/products/' . $slugMap[$normalizedSlug], 301);
    }

    return redirect()->to('/products/' . $normalizedSlug, 301);
});

// Dynamic CMS Page Route (Policy pages & backend created pages)
Route::get('/{slug}', \App\Livewire\Storefront\PageShow::class)
    ->where('slug', '^(?!admin|blog|shop|products|cart|checkout|about|sitemap\.xml)[a-z0-9\-]+$');

// Dynamic Meta / Facebook Product Catalog Feeds
Route::get('/feeds/meta-catalog.csv', [\App\Http\Controllers\MetaCatalogFeedController::class, 'csv'])->name('feeds.meta.csv');
Route::get('/feeds/meta-catalog.xml', [\App\Http\Controllers\MetaCatalogFeedController::class, 'xml'])->name('feeds.meta.xml');
Route::get('/feeds/facebook-catalog.csv', [\App\Http\Controllers\MetaCatalogFeedController::class, 'csv']);
Route::get('/feeds/facebook-catalog.xml', [\App\Http\Controllers\MetaCatalogFeedController::class, 'xml']);

// Dynamic XML Sitemap for SEO (Consolidated & Canonical)
Route::get('/sitemap.xml', function () {
    $urls = [];
    $baseUrl = 'https://kelvsint.com';

    // 1. Static Pages & Standalone Policy Pages
    $staticPages = [
        ['path' => '', 'changefreq' => 'daily', 'priority' => '1.0'],
        ['path' => '/shop', 'changefreq' => 'daily', 'priority' => '0.8'],
        ['path' => '/about', 'changefreq' => 'weekly', 'priority' => '0.5'],
        ['path' => '/blog', 'changefreq' => 'daily', 'priority' => '0.7'],
        ['path' => '/privacy-policy', 'changefreq' => 'monthly', 'priority' => '0.5'],
        ['path' => '/return-policy', 'changefreq' => 'monthly', 'priority' => '0.5'],
        ['path' => '/terms-and-conditions', 'changefreq' => 'monthly', 'priority' => '0.5'],
        ['path' => '/shipping-policy', 'changefreq' => 'monthly', 'priority' => '0.5'],
    ];

    foreach ($staticPages as $page) {
        $urls[] = [
            'loc' => $baseUrl . $page['path'],
            'lastmod' => now()->startOfDay()->toAtomString(),
            'changefreq' => $page['changefreq'],
            'priority' => $page['priority'],
        ];
    }

    // 2. Dynamic Active Products (Excludes demo & soft-deleted items)
    try {
        if (class_exists(\Lunar\Models\Product::class)) {
            $products = \Lunar\Models\Product::whereNull('deleted_at')
                ->where('status', 'published')
                ->where('id', '>=', 8)
                ->with('urls')
                ->get();

            foreach ($products as $product) {
                $slug = $product->urls->firstWhere('default', true)?->slug 
                    ?? $product->urls->first()?->slug;

                if ($slug) {
                    $urls[] = [
                        'loc' => $baseUrl . '/products/' . $slug,
                        'lastmod' => ($product->updated_at ?? now())->toAtomString(),
                        'changefreq' => 'weekly',
                        'priority' => '0.9',
                    ];
                }
            }
        }
    } catch (\Throwable $e) {
        logger()->error('Sitemap product generation failed: ' . $e->getMessage());
    }

    // 3. Dynamic Blog Posts (Excludes policy pages)
    try {
        if (class_exists(\LaraZeus\Sky\Models\Post::class)) {
            $postClass = config('zeus-sky.models.Post', \App\Models\Post::class);
            if (class_exists($postClass)) {
                $posts = $postClass::published()
                    ->where(function($q) {
                        $q->where('post_type', 'post')
                          ->orWhereNull('post_type');
                    })
                    ->whereNotIn('slug', [
                        'privacy-policy',
                        'return-policy',
                        'terms-and-conditions',
                        'shipping-policy',
                        'refund-policy'
                    ])
                    ->get();

                foreach ($posts as $post) {
                    $urls[] = [
                        'loc' => $baseUrl . '/blog/post/' . $post->slug,
                        'lastmod' => ($post->updated_at ?? now())->toAtomString(),
                        'changefreq' => 'weekly',
                        'priority' => '0.7',
                    ];
                }
            }
        }
    } catch (\Throwable $e) {
        logger()->error('Sitemap post generation failed: ' . $e->getMessage());
    }

    // 4. Render XML view or run inline fallback
    try {
        $xml = view('sitemap', compact('urls'))->render();
        return response($xml, 200)
            ->header('Content-Type', 'text/xml');
    } catch (\Throwable $e) {
        logger()->error('Sitemap rendering failed: ' . $e->getMessage());
        
        $fallbackXml = '<?xml version="1.0" encoding="UTF-8"?>';
        $fallbackXml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
        foreach ($urls as $url) {
            $fallbackXml .= '<url>';
            $fallbackXml .= '<loc>' . htmlspecialchars($url['loc']) . '</loc>';
            $fallbackXml .= '<lastmod>' . $url['lastmod'] . '</lastmod>';
            $fallbackXml .= '<changefreq>' . $url['changefreq'] . '</changefreq>';
            $fallbackXml .= '<priority>' . $url['priority'] . '</priority>';
            $fallbackXml .= '</url>';
        }
        $fallbackXml .= '</urlset>';

        return response($fallbackXml, 200)
            ->header('Content-Type', 'text/xml');
    }
});


