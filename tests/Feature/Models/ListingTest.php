<?php

use App\Models\Listing;

beforeEach(function (): void {
    config()->set('scout.driver', 'collection');
});

test('makes only currently live listings searchable', function (string $state, bool $isSearchable) {
    $listing = Listing::factory()->{$state}()->create();

    expect($listing->shouldBeSearchable())->toBe($isSearchable);
})->with([
    'published listing' => ['published', true],
    'draft listing' => ['draft', false],
    'expired listing' => ['expired', false],
    'future listing' => ['futurePublication', false],
]);

test('builds a searchable document with listing attributes', function () {
    $listing = Listing::factory()->published()->create([
        'title' => 'CNC Lathe',
        'description' => 'Production-ready industrial CNC lathe.',
        'category' => 'Machinery',
        'country' => 'BE',
        'city' => 'Berlin',
        'price' => 42000,
    ]);

    expect($listing->toSearchableArray())->toMatchArray([
        'id' => (string) $listing->id,
        'title' => 'CNC Lathe',
        'description' => 'Production-ready industrial CNC lathe.',
        'category' => 'Machinery',
        'country' => 'BE',
        'city' => 'Berlin',
        'price' => 42000,
        'status' => 'Published',
        'published_at' => $listing->published_at->timestamp,
        'expires_at' => $listing->expires_at->timestamp,
        'created_at' => $listing->created_at->timestamp,
    ]);
});
