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

        $newPath = $path;

        // 1. Replace underscores with hyphens
        if (str_contains($newPath, '_')) {
            $newPath = str_replace('_', '-', $newPath);
        }

        // 2. Normalize case to lowercase for SEO purposes
        $lowercasePath = mb_strtolower($newPath, 'UTF-8');
        if ($lowercasePath !== $newPath) {
            $newPath = $lowercasePath;
        }

        // 3. Remove consecutive hyphens (e.g. "foo--bar" -> "foo-bar")
        $newPath = preg_replace('/-+/', '-', $newPath);

        // Canonical Domain: Strip 'www.' prefix to consolidate domain authority
        $host = $request->getHost();
        $targetHost = $host;
        if (str_starts_with(strtolower($host), 'www.')) {
            $targetHost = substr($host, 4);
        }

        // If the path changed OR host changed, issue a single 301 Permanent Redirect
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
