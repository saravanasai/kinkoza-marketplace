<?php

use App\Actions\Listings\UpdateListingAction;
use App\Enums\ListingStatus;
use App\Models\Listing;
use App\Models\User;

beforeEach(function (): void {
    config()->set('scout.driver', 'collection');
});

test('keeps the existing slug when a listing title is unchanged', function () {
    $user = User::factory()->create();
    $company = $user->company;
    $listing = Listing::factory()->for($company)->create([
        'title' => 'CNC Lathe',
        'slug' => 'cnc-lathe',
    ]);
    $this->actingAs($user);

    $updatedListing = UpdateListingAction::run($user, $listing, [
        'title' => 'CNC Lathe',
        'description' => 'Updated industrial CNC lathe description.',
        'category' => 'Machinery',
        'status' => ListingStatus::Draft->value,
        'price' => 43000,
        'currency' => 'EUR',
        'country' => 'BE',
        'city' => 'Berlin',
    ]);

    expect($updatedListing->slug)->toBe('cnc-lathe');
});

test('adds a suffix when an updated title conflicts with another listing', function () {
    $user = User::factory()->create();
    $company = $user->company;
    Listing::factory()->for($company)->create(['slug' => 'cnc-lathe']);
    $listing = Listing::factory()->for($company)->create();
    $this->actingAs($user);

    $updatedListing = UpdateListingAction::run($user, $listing, [
        'title' => 'CNC Lathe',
        'description' => 'Updated industrial CNC lathe description.',
        'category' => 'Machinery',
        'status' => ListingStatus::Draft->value,
        'price' => 43000,
        'currency' => 'EUR',
        'country' => 'BE',
        'city' => 'Berlin',
    ]);

    expect($updatedListing->slug)->toBe('cnc-lathe-1');
});
