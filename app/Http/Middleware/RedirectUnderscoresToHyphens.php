<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RedirectUnderscoresToHyphens
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Only redirect GET requests to avoid disrupting forms, APIs, etc.
        if (!$request->isMethod('GET')) {
            return $next($request);
        }

        $path = $request->getPathInfo();

        // Check if the path is a system route or static asset that should be skipped
        if ($this->shouldSkip($path)) {
            return $next($request);
        }

        $canonicalDomain = 'https://kelvsint.com';
        $normalizedPath = rtrim(mb_strtolower($path, 'UTF-8'), '/');
        if ($normalizedPath === '') {
            $normalizedPath = '/';
        }

        // ── 1. POLICY PAGES 1-HOP RESOLUTION ─────────────────────────────────
        $policyMap = [
            '/blog/post/privacy-policy'        => '/privacy-policy',
            '/blog/post/return-policy'         => '/return-policy',
            '/blog/post/terms-and-conditions'  => '/terms-and-conditions',
            '/blog/post/shipping-policy'       => '/shipping-policy',
            '/blog/post/refund-policy'         => '/return-policy',
            '/blog/page/privacy-policy'        => '/privacy-policy',
            '/blog/page/return-policy'         => '/return-policy',
            '/blog/page/terms-and-conditions'  => '/terms-and-conditions',
            '/blog/page/shipping-policy'       => '/shipping-policy',
            '/blog/page/refund-policy'         => '/return-policy',
            '/refund-policy'                   => '/return-policy',
            '/about-us'                        => '/about',
        ];

        if (array_key_exists($normalizedPath, $policyMap)) {
            return redirect()->to($canonicalDomain . $policyMap[$normalizedPath], 301);
        }


        // ── 2. LEGACY /shop/{slug} 1-HOP RESOLUTION ──────────────────────────
        $legacyShopMap = [
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

        if (str_starts_with($normalizedPath, '/shop/') && strlen($normalizedPath) > 6) {
            $shopSlug = substr($normalizedPath, 6);
            $cleanShopSlug = str_replace('_', '-', $shopSlug);
            $targetSlug = $legacyShopMap[$cleanShopSlug] ?? $cleanShopSlug;
            return redirect()->to($canonicalDomain . '/products/' . $targetSlug, 301);
        }

        // ── 3. LEGACY /blog/{slug} 1-HOP RESOLUTION ──────────────────────────
        if (str_starts_with($normalizedPath, '/blog/') && !str_starts_with($normalizedPath, '/blog/post/')) {
            $blogSlug = substr($normalizedPath, 6);
            $cleanBlogSlug = str_replace('_', '-', $blogSlug);
            return redirect()->to($canonicalDomain . '/blog/post/' . $cleanBlogSlug, 301);
        }

        // ── 4. GENERAL NORMALIZATION (Underscores, Casing, WWW removal) ───────
        $newPath = $path;

        if (str_contains($newPath, '_')) {
            $newPath = str_replace('_', '-', $newPath);
        }

        $lowercasePath = mb_strtolower($newPath, 'UTF-8');
        if ($lowercasePath !== $newPath) {
            $newPath = $lowercasePath;
        }

        $newPath = preg_replace('/-+/', '-', $newPath);

        $host = $request->getHost();
        $targetHost = $host;
        if (str_starts_with(strtolower($host), 'www.')) {
            $targetHost = substr($host, 4);
        }

        if ($newPath !== $path || $targetHost !== $host) {
            $queryString = $request->getQueryString();
            $scheme = ($request->isSecure() || $request->header('X-Forwarded-Proto') === 'https' || app()->isProduction())
                ? 'https'
                : $request->getScheme();

            $port = $request->getPort();
            $portString = ($port && !in_array($port, [80, 443])) ? ':' . $port : '';

            $newUrl = $scheme . '://' . $targetHost . $portString . $newPath . ($queryString ? '?' . $queryString : '');

            return redirect()->to($newUrl, 301);
        }

        return $next($request);
    }

    /**
     * Determine if the path should skip redirection.
     */
    private function shouldSkip(string $path): bool
    {
        $excludePrefixes = [
            '/admin',
            '/lunar',
            '/livewire',
            '/_debugbar',
            '/storage',
            '/filament',
            '/feeds',
        ];

        foreach ($excludePrefixes as $prefix) {
            if (str_starts_with($path, $prefix) || str_contains($path, '/' . ltrim($prefix, '/'))) {
                return true;
            }
        }

        // Skip static files/assets
        if (preg_match('/\.(css|js|png|jpg|jpeg|gif|svg|webp|ico|woff|woff2|ttf|otf|map|json|txt|xml|csv|mp4|webm)$/i', $path)) {
            return true;
        }

        return false;
    }
}
