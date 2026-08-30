<?php

use App\Livewire\Listings\Create as CreateListing;
use App\Livewire\Listings\Edit as EditListing;
use App\Livewire\Listings\Index as ListingsIndex;
use App\Livewire\Listings\Show as ShowListing;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');
    Route::livewire('listings', ListingsIndex::class)->name('listings.index');
    Route::livewire('listings/create', CreateListing::class)->name('listings.create');
    Route::livewire('listings/{listing:slug}', ShowListing::class)->name('listings.show');
    Route::livewire('listings/{listing}/edit', EditListing::class)->name('listings.edit');
});

require __DIR__.'/settings.php';
