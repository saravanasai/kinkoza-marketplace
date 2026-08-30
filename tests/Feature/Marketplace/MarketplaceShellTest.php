<?php

use App\Enums\ListingStatus;
use App\Livewire\Marketplace\Show;
use App\Models\Company;
use App\Models\Listing;
use App\Models\User;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Livewire;

test('guests can browse the public marketplace shell', function () {
    $response = $this->get(route('home'));

    $response
        ->assertOk()
        ->assertSee('Kinkoza')
        ->assertSee('Over 1 million verified listings')
        ->assertSee('Source commercial assets with confidence')
        ->assertSee('Built for commercial trade')
        ->assertSee('Contact us')
        ->assertSee('Careers')
        ->assertSee('Open navigation menu')
        ->assertSee('Log in')
        ->assertSee('Create account');
});

test('authenticated users see the dashboard link in the public shell', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('home'));

    $response
        ->assertOk()
        ->assertSee('Open navigation menu')
        ->assertSee('Dashboard');
});

test('the public marketplace shows currently published listings', function () {
    $publishedListing = Listing::factory()->published()->create([
        'title' => 'CNC Milling Machine',
        'description' => 'Precision tool for industrial fabrication.',
        'city' => 'Paris',
    ]);

    Listing::factory()->create([
        'title' => 'Draft listing should stay hidden',
        'status' => ListingStatus::Draft->value,
    ]);

    Listing::factory()->expired()->create([
        'title' => 'Expired listing should stay hidden',
    ]);

    $this->get(route('home'))
        ->assertOk()
        ->assertSee('CNC Milling Machine')
        ->assertSee('Paris')
        ->assertSee('Precision tool for industrial fabrication.')
        ->assertDontSee('Draft listing should stay hidden')
        ->assertDontSee('Expired listing should stay hidden');
});

test('guests can view a published public listing detail', function () {
    $listing = Listing::factory()->published()->create([
        'title' => 'Industrial CNC Lathe',
        'description' => 'Heavy-duty lathe ready for production runs.',
        'city' => 'Berlin',
        'price' => 42000,
    ]);

    Listing::factory()->expired()->create([
        'title' => 'Expired listing should stay hidden',
    ]);

    $this->get(route('marketplace.listings.show', $listing))
        ->assertOk()
        ->assertSee('Industrial CNC Lathe')
        ->assertSee('Heavy-duty lathe ready for production runs.')
        ->assertSee('Berlin')
        ->assertSee('42,000')
        ->assertSee('Log in to view contact details')
        ->assertDontSee('sales@example.com');
});

test('authenticated visitors can reveal a live listing contact and create an audit record', function () {
    $viewer = User::factory()->create();
    $company = Company::factory()->create([
        'contact_email' => 'sales@example.com',
        'contact_phone' => '+33 1 23 45 67 89',
    ]);
    $listing = Listing::factory()->published()->for($company)->create();

    Livewire::actingAs($viewer)
        ->test(Show::class, ['listing' => $listing])
        ->call('revealContact')
        ->assertSee('Email')
        ->assertSee('sales@example.com')
        ->assertSee('Phone')
        ->assertSee('+33 1 23 45 67 89');

    $this->assertDatabaseHas('contact_reveals', [
        'listing_id' => $listing->id,
        'user_id' => $viewer->id,
    ]);
});

test('contact reveals are rate limited per user', function () {
    $viewer = User::factory()->create();
    $listing = Listing::factory()->published()->create();
    $rateLimitKey = 'contact-reveal:'.$viewer->id;

    RateLimiter::clear($rateLimitKey);

    $component = Livewire::actingAs($viewer)
        ->test(Show::class, ['listing' => $listing]);

    foreach (range(1, 10) as $attempt) {
        $component->call('revealContact');
    }

    $component
        ->call('revealContact')
        ->assertHasErrors('contact');

    $this->assertDatabaseCount('contact_reveals', 10);
});

test('public listing details include a gallery area for listing images', function () {
    $listing = Listing::factory()->published()->create([
        'title' => 'Industrial CNC Lathe',
        'description' => 'Heavy-duty lathe ready for production runs.',
        'city' => 'Berlin',
        'price' => 42000,
    ]);

    $this->get(route('marketplace.listings.show', $listing))
        ->assertOk()
        ->assertSee('Gallery')
        ->assertSee('listing-gallery');
});

test('public marketplace listings can be filtered by category', function () {
    Listing::factory()->published()->create([
        'title' => 'Machinery listing',
        'category' => 'Machinery',
    ]);

    Listing::factory()->published()->create([
        'title' => 'Vehicle listing',
        'category' => 'Vehicles',
    ]);

    $this->get(route('home', ['category' => 'Machinery']))
        ->assertOk()
        ->assertSee('Machinery listing')
        ->assertDontSee('Vehicle listing');
});

test('public marketplace listings can be filtered by country and price range', function () {
    Listing::factory()->published()->create([
        'title' => 'Belgian Excavator',
        'country' => 'BE',
        'price' => 45000,
    ]);

    Listing::factory()->published()->create([
        'title' => 'French Loader',
        'country' => 'FR',
        'price' => 45000,
    ]);

    Listing::factory()->published()->create([
        'title' => 'Belgian Crane',
        'country' => 'BE',
        'price' => 75000,
    ]);

    $this->get(route('home', [
        'country' => 'BE',
        'minPrice' => 40000,
        'maxPrice' => 50000,
    ]))
        ->assertOk()
        ->assertSee('Belgian Excavator')
        ->assertDontSee('French Loader')
        ->assertDontSee('Belgian Crane');
});

test('public marketplace listings can be searched by keyword', function () {
    config()->set('scout.driver', 'collection');

    Listing::factory()->published()->create([
        'title' => 'Precision CNC Lathe',
        'description' => 'Production-ready machining equipment.',
    ]);

    Listing::factory()->published()->create([
        'title' => 'Forklift',
        'description' => 'Warehouse vehicle for heavy lifting.',
    ]);

    $this->get(route('home', ['search' => 'CNC']))
        ->assertOk()
        ->assertSee('Precision CNC Lathe')
        ->assertDontSee('Forklift');
});

test('public marketplace listings paginate search results', function () {
    config()->set('scout.driver', 'collection');

    foreach (range(1, 13) as $number) {
        Listing::factory()->published()->create([
            'title' => 'Marketplace listing '.str_pad((string) $number, 2, '0', STR_PAD_LEFT),
        ]);
    }

    $this->get(route('home', ['page' => 2]))
        ->assertSee('Marketplace listing 01')
        ->assertDontSee('Marketplace listing 02');
});

test('the active marketplace category can be cleared', function () {
    Listing::factory()->published()->create([
        'title' => 'Machinery listing',
        'category' => 'Machinery',
    ]);

    $this->get(route('home', ['category' => 'Machinery']))
        ->assertOk()
        ->assertSee('Machinery')
        ->assertSee('Clear filter');
});
