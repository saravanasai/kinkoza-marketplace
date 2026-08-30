<?php

use App\Models\Company;
use App\Models\Listing;
use App\Models\User;
use Tests\TestCase;

test('guests are redirected from the show listing page', function () {
    /** @var TestCase $this */
    $listing = Listing::factory()->create();

    $this->get(route('listings.show', $listing))->assertRedirect(route('login'));
});

test('a seller can view their own listing show page', function () {
    /** @var TestCase $this */
    $seller = User::factory()->create();
    $listing = Listing::factory()->for($seller->company)->create([
        'title' => 'Alpha CNC Milling Machine',
        'description' => 'Heavy-duty milling machine for precision jobs.',
    ]);

    $this->actingAs($seller)
        ->get(route('listings.show', $listing))
        ->assertOk()
        ->assertSee($listing->title)
        ->assertSee($listing->description)
        ->assertDontSee('Save listing')
        ->assertDontSee('Delete listing');
});

test('a seller cannot view another company listing show page', function () {
    /** @var TestCase $this */
    $seller = User::factory()->create();
    $foreignListing = Listing::factory()->for(Company::factory())->create();

    $this->actingAs($seller)
        ->get(route('listings.show', $foreignListing))
        ->assertNotFound();
});
