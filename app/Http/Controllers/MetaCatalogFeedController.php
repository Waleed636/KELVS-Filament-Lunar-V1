<?php

namespace App\Http\Controllers;

use App\Services\MetaCatalogFeedService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;

class MetaCatalogFeedController extends Controller
{
    public function __construct(
        protected MetaCatalogFeedService $feedService
    ) {}

    /**
     * Return dynamic Meta Product Catalog in CSV format.
     */
    public function csv(Request $request): Response
    {
        $shouldCache = !$request->has('nocache');
        $cacheKey = 'meta_catalog_feed_csv';

        if ($shouldCache) {
            $csvContent = Cache::remember($cacheKey, now()->addHours(2), function () {
                return $this->feedService->generateCsv();
            });
        } else {
            $csvContent = $this->feedService->generateCsv();
            Cache::put($cacheKey, $csvContent, now()->addHours(2));
        }

        return response($csvContent, 200, [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'inline; filename="meta-catalog.csv"',
            'Cache-Control'       => 'public, max-age=3600',
        ]);
    }

    /**
     * Return dynamic Meta Product Catalog in standard RSS 2.0 XML format.
     */
    public function xml(Request $request): Response
    {
        $shouldCache = !$request->has('nocache');
        $cacheKey = 'meta_catalog_feed_xml';

        if ($shouldCache) {
            $xmlContent = Cache::remember($cacheKey, now()->addHours(2), function () {
                return $this->feedService->generateXml();
            });
        } else {
            $xmlContent = $this->feedService->generateXml();
            Cache::put($cacheKey, $xmlContent, now()->addHours(2));
        }

        return response($xmlContent, 200, [
            'Content-Type'        => 'application/xml; charset=UTF-8',
            'Content-Disposition' => 'inline; filename="meta-catalog.xml"',
            'Cache-Control'       => 'public, max-age=3600',
        ]);
    }
}
