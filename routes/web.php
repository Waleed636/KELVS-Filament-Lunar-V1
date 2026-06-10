<?php

use Illuminate\Support\Facades\Route;

Route::get('/', \App\Livewire\Storefront\Home::class);
Route::get('/products/{slug}', \App\Livewire\Storefront\ProductShow::class);
Route::get('/cart', \App\Livewire\Storefront\CartPage::class);
Route::get('/checkout', \App\Livewire\Storefront\Checkout::class);

Route::redirect('/login', '/admin/login')->name('login');

