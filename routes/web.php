<?php

use App\Livewire\Listings\Create as CreateListing;
use App\Livewire\Listings\Edit as EditListing;
use App\Livewire\Listings\Index as ListingsIndex;
use App\Livewire\Listings\Show as ShowListing;
use App\Livewire\Marketplace\Index as MarketplaceIndex;
use App\Livewire\Marketplace\Show as MarketplaceShow;
use Illuminate\Support\Facades\Route;

Route::middleware('throttle:public-marketplace')->group(function () {
    Route::livewire('/', MarketplaceIndex::class)->name('home');
});

Route::middleware('throttle:public-listing-show')->group(function () {
    Route::livewire('marketplace/listings/{marketplaceListing}', MarketplaceShow::class)
        ->name('marketplace.listings.show');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');
    Route::livewire('listings', ListingsIndex::class)->name('listings.index');
    Route::livewire('listings/create', CreateListing::class)->name('listings.create');
    Route::livewire('listings/{listing:slug}', ShowListing::class)->name('listings.show');
    Route::livewire('listings/{listing}/edit', EditListing::class)->name('listings.edit');
});

require __DIR__.'/settings.php';
