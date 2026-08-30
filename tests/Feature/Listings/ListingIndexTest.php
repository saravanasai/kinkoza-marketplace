<?php

use App\Enums\Country;
use App\Enums\ListingCategory;
use App\Enums\ListingStatus;
use App\Models\Company;
use App\Models\Listing;
use App\Models\User;
use Tests\TestCase;

test('guests are redirected from the seller listings page', function () {
    /** @var TestCase $this */
    $this->get(route('listings.index'))->assertRedirect(route('login'));
});

test('a seller only sees listings for their own company', function () {
    /** @var TestCase $this */
    $sellerA = User::factory()->create();
    $companyA = $sellerA->company;
    $companyB = Company::factory()->create();

    $ownListing = Listing::factory()->for($companyA)->create([
        'title' => 'Alpha CNC Milling Machine',
        'status' => ListingStatus::Published->value,
        'category' => ListingCategory::Machinery->value,
        'country' => Country::FR->value,
    ]);

    $foreignListing = Listing::factory()->for($companyB)->create([
        'title' => 'Beta Packaging Line',
        'status' => ListingStatus::Published->value,
        'category' => ListingCategory::Machinery->value,
        'country' => Country::FR->value,
    ]);

    $this->actingAs($sellerA)
        ->get(route('listings.index'))
        ->assertOk()
        ->assertSee($ownListing->title)
        ->assertSee(route('listings.edit', $ownListing), false)
        ->assertSee(route('listings.show', $ownListing), false)
        ->assertDontSee($foreignListing->title);
});

test('title search remains scoped to the sellers company', function () {
    /** @var TestCase $this */
    $sellerA = User::factory()->create();
    $companyA = $sellerA->company;
    $companyB = Company::factory()->create();

    $ownListing = Listing::factory()->for($companyA)->create([
        'title' => 'Alpha Packaging Line',
    ]);

    $foreignListing = Listing::factory()->for($companyB)->create([
        'title' => 'Beta Packaging Line',
    ]);

    $this->actingAs($sellerA)
        ->get(route('listings.index', ['search' => 'Packaging']))
        ->assertOk()
        ->assertSee($ownListing->title)
        ->assertDontSee($foreignListing->title);
});

test('status category and country filters remain scoped to the sellers company', function () {
    /** @var TestCase $this */
    $sellerA = User::factory()->create();
    $companyA = $sellerA->company;
    $companyB = Company::factory()->create();

    $matchingListing = Listing::factory()->for($companyA)->create([
        'title' => 'Alpha Machinery',
        'status' => ListingStatus::Published->value,
        'category' => ListingCategory::Machinery->value,
        'country' => Country::FR->value,
    ]);

    $foreignMatchingListing = Listing::factory()->for($companyB)->create([
        'title' => 'Beta Machinery',
        'status' => ListingStatus::Published->value,
        'category' => ListingCategory::Machinery->value,
        'country' => Country::FR->value,
    ]);

    $this->actingAs($sellerA)
        ->get(route('listings.index', [
            'status' => ListingStatus::Published->value,
            'category' => ListingCategory::Machinery->value,
            'country' => Country::FR->value,
        ]))
        ->assertOk()
        ->assertSee($matchingListing->title)
        ->assertDontSee($foreignMatchingListing->title);
});

test('seller listings paginate', function () {
    /** @var TestCase $this */
    $sellerA = User::factory()->create();
    $companyA = $sellerA->company;
    $companyB = Company::factory()->create();

    $firstListing = Listing::factory()->for($companyA)->create([
        'title' => 'Alpha Listing 01',
    ]);

    for ($index = 2; $index <= 11; $index++) {
        Listing::factory()->for($companyA)->create([
            'title' => 'Alpha Listing '.str_pad((string) $index, 2, '0', STR_PAD_LEFT),
        ]);
    }

    Listing::factory()->for($companyB)->create([
        'title' => 'Foreign Listing',
    ]);

    $this->actingAs($sellerA)
        ->get(route('listings.index', ['page' => 2]))
        ->assertOk()
        ->assertSee($firstListing->title);
});
