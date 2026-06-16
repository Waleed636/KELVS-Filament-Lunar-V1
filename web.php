<?php
use TCG\Voyager\Events\Routing;
use TCG\Voyager\Events\RoutingAdmin;
use TCG\Voyager\Events\RoutingAdminAfter;
use TCG\Voyager\Events\RoutingAfter;
use TCG\Voyager\Facades\Voyager;

Route::group(['middleware' => ['web']], function () {
    // Fast image variants (WebP/JPEG) for improved LCP/FCP.
    Route::get('/img-cache/{size}/{encodedPath}.{format}', 'ImageCacheController@show')
        ->where([
            'size' => '\d+x\d+',
            'encodedPath' => '[A-Za-z0-9\-_]+',
            'format' => 'webp|jpg|jpeg|png',
        ])
        ->name('img-cache');

    // Fast image variants for /public/img assets (landing page hero, etc).
    Route::get('/asset-img-cache/{size}/{encodedPath}.{format}', 'AssetImageCacheController@show')
        ->where([
            'size' => '\d+x\d+',
            'encodedPath' => '[A-Za-z0-9\-_]+',
            'format' => 'webp|jpg|jpeg|png',
        ])
        ->name('asset-img-cache');

    Route::get('/', 'PagesController@index')->name('landing-page');
    // Route::get('/skin-analyzer-tool', 'PagesController@skinAnalyzerTool')->name('skin-analyzer-tool');

    Route::get('/shop', 'ShopController@index')->name('shop.index');
    Route::get('/blog', 'BlogController@index')->name('blog.index');
    Route::get('/blog/{slug}', 'BlogController@show')->name('blog.show');
    Route::get('/shop/{product}', 'ShopController@show')->name('shop.show');

    Route::get('/terms-and-conditions', 'PagesController@termsAndConditions')->name('termsAndConditions');
    Route::get('/contact-us', 'PagesController@contactUs')->name('contactUs');
    Route::post('/post-contact-us', 'PagesController@contactUsPost')->name('contactUsPost');
    Route::get('/about-us', 'PagesController@aboutUs')->name('aboutUs');
    Route::get('/return-policy', 'PagesController@returnPolicy')->name('returnPolicy');
    Route::get('/privacy-policy', 'PagesController@privacyPolicy')->name('privacyPolicy');
    Route::get('/refund-policy', 'PagesController@refundPolicy')->name('refundPolicy');
    Route::get('/shipping-policy', 'PagesController@shippingPolicy')->name('shippingPolicy');

    Route::get('/cart', 'CartController@index')->name('cart.index');
    Route::post('/cart/{product}', 'CartController@store')->name('cart.store');
    Route::patch('/cart/{product}', 'CartController@update')->name('cart.update');
    Route::delete('/cart/{product}', 'CartController@destroy')->name('cart.destroy');
    Route::post('/cart/switchToSaveForLater/{product}', 'CartController@switchToSaveForLater')->name('cart.switchToSaveForLater');

    Route::delete('/saveForLater/{product}', 'SaveForLaterController@destroy')->name('saveForLater.destroy');
    Route::post('/saveForLater/switchToCart/{product}', 'SaveForLaterController@switchToCart')->name('saveForLater.switchToCart');

    Route::post('/coupon', 'CouponsController@store')->name('coupon.store');
    Route::delete('/coupon', 'CouponsController@destroy')->name('coupon.destroy');

    Route::post('/subscribe-popup', 'EmailSubscriberController@store')->name('popup.subscribe');

    Route::get('/checkout', 'CheckoutController@index')->name('checkout.index');
    // Route::get('/checkout', 'CheckoutController@index')->name('checkout.index')->middleware('auth');
    Route::post('/checkout', 'CheckoutController@store')->name('checkout.store');
    Route::post('/checkout/save-progress', 'CheckoutController@saveProgress')->name('checkout.saveProgress');

    Route::post('/checkout/buynow', 'CheckoutController@buyNow')->name('checkout.buynow');

    Route::post('/paypal-checkout', 'CheckoutController@paypalCheckout')->name('checkout.paypal');

    Route::get('/guestCheckout', 'CheckoutController@index')->name('guestCheckout.index');


    Route::get('/thankyou', 'ConfirmationController@index')->name('confirmation.index');

    Route::post('/review', 'ReviewsController@store')->name('review.store');

    Route::group(['prefix' => 'admin'], function () {
        Voyager::routes();
        
        Route::delete('orders/0', 'Voyager\OrdersController@massDestroy')->name('voyager.orders.mass_delete')->middleware('admin.user');

        Route::get('orders/{id}/push-postex', 'Voyager\OrdersController@pushPostEx')->name('voyager.orders.push-postex');

        // Your overwrites here

        Route::post('upload', ['uses' => 'VoyagerController@upload', 'as' => 'voyager.upload']);
    });

    Auth::routes();

    // Route::get('/home', 'HomeController@index')->name('home');

    Route::get('/search', 'ShopController@search')->name('search');

    Route::get('/search-algolia', 'ShopController@searchAlgolia')->name('search-algolia');

    Route::middleware('auth')->group(function () {
        Route::get('/my-profile', 'UsersController@edit')->name('users.edit');
        Route::patch('/my-profile', 'UsersController@update')->name('users.update');

        Route::get('/my-orders', 'OrdersController@index')->name('orders.index');
        Route::get('/my-orders/{order}', 'OrdersController@show')->name('orders.show');
    });

    Route::prefix('admin')->middleware('admin.user')->group(function () {
        Route::get('/sales-reports', 'ReportsController@salesReports')->name('admin.salesReports');
        Route::get('/coupon-code-reports', 'ReportsController@couponCodeReport')->name('admin.couponCodeReport');
        
        Route::prefix('/orders')->group(function () {
            Route::post('/bulk-ship', 'OrdersController@bulkShip')->name('admin.orders.bulkShip');
            Route::post('/bulk-mark-as-paid', 'OrdersController@bulkMarkAsPaid')->name('admin.orders.bulkMarkAsPaid');
            Route::post('/update-field-status', 'OrdersController@updateFieldStatus')->name('admin.orders.updateFieldStatus');
        });
    });

    Route::group(['as' => 'self-voyager.'], function () {
        event(new Routing());

        Route::group(['middleware' => 'admin.user'], function () {
            event(new RoutingAdmin());

            // Admin Media
            Route::group([
                'as'     => 'media.',
                'prefix' => 'media',
            ], function () {
                Route::post('delete_file_folder', ['uses' => 'VoyagerMediaController@delete', 'as' => 'delete']);
            });

            event(new RoutingAdminAfter());
        });

    Route::get('/sitemap.xml', function() {
        $urls = [];
        $baseUrl = 'https://www.kelvsint.com';

        // Static routes
        $staticRoutes = ['/', '/shop', '/blog', '/about-us', '/terms-and-conditions', '/contact-us', '/return-policy', '/privacy-policy', '/refund-policy', '/shipping-policy'];

        foreach ($staticRoutes as $route) {
            $urls[] = [
                'loc' => rtrim($baseUrl, '/') . $route,
                'lastmod' => \Carbon\Carbon::now()->toAtomString(),
                'priority' => $route === '/' ? '1.0' : '0.8',
            ];
        }

        // Products
        $products = \App\Product::all();
        foreach ($products as $product) {
            $urls[] = [
                'loc' => rtrim($baseUrl, '/') . '/shop/' . $product->slug,
                'lastmod' => $product->updated_at ? $product->updated_at->toAtomString() : \Carbon\Carbon::now()->toAtomString(),
                'priority' => '0.6',
            ];
        }

        // Blog Posts - include all
        $posts = \App\Post::all();
        foreach ($posts as $post) {
            $urls[] = [
                'loc' => rtrim($baseUrl, '/') . '/blog/' . $post->slug,
                'lastmod' => $post->updated_at ? $post->updated_at->toAtomString() : \Carbon\Carbon::now()->toAtomString(),
                'priority' => '0.6',
            ];
        }

        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

        foreach ($urls as $url) {
            $loc = htmlspecialchars($url['loc'], ENT_XML1, 'UTF-8');
            $xml .= "  <url>\n";
            $xml .= "    <loc>" . $loc . "</loc>\n";
            $xml .= "    <lastmod>" . $url['lastmod'] . "</lastmod>\n";
            $xml .= "    <priority>" . $url['priority'] . "</priority>\n";
            $xml .= "  </url>\n";
        }
        $xml .= '</urlset>';

        return response($xml, 200, ['Content-Type' => 'application/xml']);
    });

});

});