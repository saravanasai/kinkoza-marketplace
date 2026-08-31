
<?php

use App\Enums\Country;
use App\Enums\ListingCategory;
use App\Livewire\Marketplace\Show;
use App\Models\Company;
use App\Models\Listing;
use App\Models\User;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Livewire;

beforeEach(function (): void {
    config()->set('scout.driver', 'typesense');
});

test('guests can browse the public marketplace shell', function () {
     /** @var \Tests\TestCase $this */

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

     /** @var \Tests\TestCase $this */
    $response = $this->actingAs($user)->get(route('home'));

    $response
        ->assertOk()
        ->assertSee('Open navigation menu')
        ->assertSee('Dashboard');
});

test('the public marketplace shows currently published listings', function () {
    $company = Company::factory()->create();

    $listing = Listing::factory()->published()->create([
        'company_id' => $company->id,
        'title' => 'CNC Milling Machine',
        'description' => 'Precision tool for industrial fabrication.',
        'city' => 'Paris',
    ]);

    Listing::factory()->draft()->create([
        'company_id' => $company->id,
        'title' => 'Draft listing should stay hidden',
    ]);

    Listing::factory()->expired()->create([
        'company_id' => $company->id,
        'title' => 'Expired listing should stay hidden',
    ]);

     /** @var \Tests\TestCase $this */
    $this->get(route('home'))
        ->assertOk()
        ->assertSeeText('CNC Milling Machine')
        ->assertSeeText('Paris')
        ->assertSeeText('Precision tool for industrial fabrication.')
        ->assertDontSeeText('Draft listing should stay hidden')
        ->assertDontSeeText('Expired listing should stay hidden');
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

     /** @var \Tests\TestCase $this */
    $this->get(route('marketplace.listings.show', $listing->slug))
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
    $viewer->load('company');
    $listing = Listing::factory()->published()->create();

    Livewire::actingAs($viewer)
        ->test(Show::class, ['marketplaceListing' => $listing])
        ->call('revealContact')
        ->assertSee('Email')
        ->assertSee($viewer->company->email)
        ->assertSee('Phone')
        ->assertSee($viewer->company->phone);

     /** @var \Tests\TestCase $this */
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
        ->test(Show::class, ['marketplaceListing' => $listing]);

    foreach (range(1, 10) as $attempt) {
        $component->call('revealContact');
    }

    $component
        ->call('revealContact')
        ->assertHasErrors('contact');

    /** @var \Tests\TestCase $this */
    $this->assertDatabaseCount('contact_reveals', 10);
});

test('public marketplace listings can be filtered by category', function () {
    Listing::factory()->published()->create([
        'title' => 'Machinery listing',
        'category' => ListingCategory::Machinery->value,
    ]);

    Listing::factory()->published()->create([
        'title' => 'Vehicle listing',
        'category' => ListingCategory::Vehicles->value,
    ]);

     /** @var \Tests\TestCase $this */
    $this->get(route('home', ['category' => ListingCategory::Machinery->value]))
        ->assertOk()
        ->assertSee('Machinery listing')
        ->assertDontSee('Vehicle listing');
});

test('public marketplace listings can be filtered by country and price range', function () {
    Listing::factory()->published()->create([
        'title' => 'Belgian Excavator',
        'country' => Country::BE->value,
        'price' => 45000,
    ]);

    Listing::factory()->published()->create([
        'title' => 'French Loader',
        'country' => Country::FR->value,
        'price' => 45000,
    ]);

    Listing::factory()->published()->create([
        'title' => 'Belgian Crane',
        'country' => Country::BE->value,
        'price' => 75000,
    ]);

     /** @var \Tests\TestCase $this */
    $this->get(route('home', [
        'country' => Country::BE->value,
        'minPrice' => 40000,
        'maxPrice' => 50000,
    ]))
        ->assertOk()
        ->assertSee('Belgian Excavator')
        ->assertDontSee('French Loader')
        ->assertDontSee('Belgian Crane');
});

test('public marketplace listings can be searched by keyword', function () {

    Listing::factory()->published()->create([
        'title' => 'Precision CNC Lathe',
        'description' => 'Production-ready machining equipment.',
    ]);

    Listing::factory()->published()->create([
        'title' => 'Forklift',
        'description' => 'Warehouse vehicle for heavy lifting.',
    ]);

     /** @var \Tests\TestCase $this */
    $this->get(route('home',['search' => 'CNC']))
        ->assertOk()
        ->assertSee('Precision CNC Lathe')
        ->assertDontSee('Forklift');
});

test('public marketplace listings paginate search results', function () {

    foreach (range(1, 13) as $number) {
        Listing::factory()->published()->create([
            'title' => 'Marketplace listing '.str_pad((string) $number, 2, '0', STR_PAD_LEFT),
        ]);
    }

     /** @var \Tests\TestCase $this */
    $this->get(route('home', ['page' => 2]))
        ->assertSee('Previous')
        ->assertSee('Next')
        ->assertDontSee('Go to page');
});

test('the active marketplace category can be cleared', function () {

    Listing::factory()->published()->create([
        'title' => 'Machinery listing',
        'category' => 'Machinery',
    ]);

    /** @var \Tests\TestCase $this */
    $this->get(route('home', ['category' => ListingCategory::Machinery->value]))
        ->assertOk()
        ->assertSee('Machinery')
        ->assertSee('Clear filter');
});

test('public marketplace listings can be sorted by newest first', function () {
    $olderTimestamp = now()->subDays(3);
    $newerTimestamp = now();

    Listing::factory()->published()->create([
        'title' => 'Older listing',
        'created_at' => $olderTimestamp,
        'published_at' => $olderTimestamp,
    ]);

    Listing::factory()->published()->create([
        'title' => 'Older listing 1',
        'created_at' => $olderTimestamp,
        'published_at' => $olderTimestamp,
    ]);

    Listing::factory()->published()->create([
        'title' => 'Newer listing',
        'created_at' => $newerTimestamp,
        'published_at' => $newerTimestamp,
    ]);

    /** @var \Tests\TestCase $this */
    $this->get(route('home', ['search' => 'listing', 'sort' => 'newest']))
        ->assertOk()
        ->assertSeeText('Newer listing')
        ->assertSeeText('Older listing 1')
        ->assertSeeText('Older listing');
});
