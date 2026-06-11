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

