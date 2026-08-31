<?php

use App\Actions\Listings\CreateListingAction;
use App\Enums\ListingStatus;
use App\Models\Listing;
use App\Models\User;
use Illuminate\Validation\ValidationException;

beforeEach(function (): void {
    config()->set('scout.driver', 'collection');
});

test('creates a unique slug and publication window for a published listing', function () {
    $user = User::factory()->create();
    $company = $user->company;
    Listing::factory()->for($company)->create(['slug' => 'cnc-lathe']);
    $publishedAt = now()->startOfMinute();
    $this->travelTo($publishedAt);
    $this->actingAs($user);

    $listing = CreateListingAction::run($user, [
        'title' => 'CNC Lathe',
        'description' => 'Production-ready industrial CNC lathe.',
        'category' => 'Machinery',
        'status' => ListingStatus::Published->value,
        'price' => 42000,
        'currency' => 'EUR',
        'country' => 'BE',
        'city' => 'Berlin',
    ]);

    $this->assertModelExists($listing);

    expect($listing->slug)->toBe('cnc-lathe-1')
        ->and($listing->published_at)->toEqual($publishedAt)
        ->and($listing->expires_at)->toEqual($publishedAt->copy()->addMonth());
});

test('leaves a draft listing outside the publication window', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $listing = CreateListingAction::run($user, [
        'title' => 'Warehouse Racking',
        'description' => 'Heavy-duty racking for commercial warehouses.',
        'category' => 'Machinery',
        'status' => ListingStatus::Draft->value,
        'price' => 9000,
        'currency' => 'EUR',
        'country' => 'FR',
        'city' => 'Lyon',
    ]);

    expect($listing->published_at)->toBeNull()
        ->and($listing->expires_at)->toBeNull();
});

test('rejects more than four listing images before persisting a listing', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    CreateListingAction::run($user, [
        'title' => 'Hydraulic Press',
        'description' => 'Industrial hydraulic press for production lines.',
        'category' => 'Machinery',
        'status' => ListingStatus::Draft->value,
        'price' => 18000,
        'currency' => 'EUR',
        'country' => 'BE',
        'city' => 'Brussels',
    ], ['one', 'two', 'three', 'four', 'five']);
})->throws(ValidationException::class);
