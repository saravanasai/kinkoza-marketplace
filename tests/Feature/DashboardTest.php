<?php

use App\Models\Company;
use App\Models\ContactReveal;
use App\Models\Listing;
use App\Models\User;

test('guests are redirected to the login page', function () {
    $response = $this->get(route('dashboard'));
    $response->assertRedirect(route('login'));
});

test('authenticated users can visit the dashboard', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $response = $this->get(route('dashboard'));
    $response->assertOk();
});

test('the dashboard shows contact reveals for the sellers listings only', function () {
    $seller = User::factory()->create();
    $ownListing = Listing::factory()->for($seller->company)->create();
    $foreignListing = Listing::factory()->for(Company::factory())->create();

    ContactReveal::factory()->count(2)->for($ownListing)->create();
    ContactReveal::factory()->for($foreignListing)->create();

    $this->actingAs($seller)
        ->get(route('dashboard'))
        ->assertSee('Contact reveals')
        ->assertSee('2');
});
