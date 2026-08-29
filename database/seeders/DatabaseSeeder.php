<?php

namespace Database\Seeders;

use App\Enums\Country;
use App\Enums\Currency;
use App\Enums\KybStatus;
use App\Enums\ListingCategory;
use App\Enums\ListingStatus;
use App\Models\Company;
use App\Models\Listing;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $companyA = Company::factory()->create([
            'name' => 'Seller Alpha SARL',
            'registration_number' => 'FR123456789',
            'vat_number' => 'FRFR123456789',
            'country' => Country::FR->value,
            'city' => 'Lyon',
            'contact_email' => 'hello@seller-alpha.test',
            'contact_phone' => '+33 1 40 00 00 01',
            'kyb_status' => KybStatus::Verified->value,
        ]);

        $companyB = Company::factory()->create([
            'name' => 'Seller Beta BV',
            'registration_number' => 'BE987654321',
            'vat_number' => 'BEBE987654321',
            'country' => Country::BE->value,
            'city' => 'Brussels',
            'contact_email' => 'hello@seller-beta.test',
            'contact_phone' => '+32 2 40 00 00 02',
            'kyb_status' => KybStatus::Verified->value,
        ]);

        $userA = User::factory()->forCompany($companyA)->create([
            'name' => 'Seller A',
            'email' => 'seller-a@example.com',
        ]);

        $userB = User::factory()->forCompany($companyB)->create([
            'name' => 'Seller B',
            'email' => 'seller-b@example.com',
        ]);

        $listings = [
            [
                'company_id' => $companyA->id,
                'title' => 'Alpha CNC Milling Machine',
                'slug' => 'alpha-cnc-milling-machine',
                'description' => 'High-precision CNC milling machine for production environments.',
                'category' => ListingCategory::Machinery->value,
                'status' => ListingStatus::Published->value,
                'price' => 185000,
                'currency' => Currency::EUR->value,
                'country' => Country::FR->value,
                'city' => 'Lyon',
                'published_at' => now()->subDays(2),
                'expires_at' => now()->addDays(28),
            ],
            [
                'company_id' => $companyA->id,
                'title' => 'Alpha Delivery Van Fleet',
                'slug' => 'alpha-delivery-van-fleet',
                'description' => 'Fleet of low-mileage delivery vans ready for regional distribution.',
                'category' => ListingCategory::Vehicles->value,
                'status' => ListingStatus::Draft->value,
                'price' => 72000,
                'currency' => Currency::GBP->value,
                'country' => Country::BE->value,
                'city' => 'Brussels',
                'published_at' => null,
                'expires_at' => null,
            ],
            [
                'company_id' => $companyA->id,
                'title' => 'Alpha Warehouse Leasehold',
                'slug' => 'alpha-warehouse-leasehold',
                'description' => 'Commercial warehouse leasehold in a logistics corridor.',
                'category' => ListingCategory::CommercialProperty->value,
                'status' => ListingStatus::PendingReview->value,
                'price' => 410000,
                'currency' => Currency::EUR->value,
                'country' => Country::LU->value,
                'city' => 'Luxembourg City',
                'published_at' => null,
                'expires_at' => null,
            ],
            [
                'company_id' => $companyA->id,
                'title' => 'Alpha Software Brand Rights',
                'slug' => 'alpha-software-brand-rights',
                'description' => 'Intangible asset package including brand and software rights.',
                'category' => ListingCategory::IntangibleAssets->value,
                'status' => ListingStatus::Expired->value,
                'price' => 125000,
                'currency' => Currency::GBP->value,
                'country' => Country::FR->value,
                'city' => 'Lille',
                'published_at' => now()->subMonths(2),
                'expires_at' => now()->subDays(2),
            ],
            [
                'company_id' => $companyB->id,
                'title' => 'Beta Packaging Line',
                'slug' => 'beta-packaging-line',
                'description' => 'Automated packaging line with modular stations.',
                'category' => ListingCategory::Machinery->value,
                'status' => ListingStatus::Published->value,
                'price' => 260000,
                'currency' => Currency::GBP->value,
                'country' => Country::BE->value,
                'city' => 'Antwerp',
                'published_at' => now()->subDay(),
                'expires_at' => now()->addDays(20),
            ],
            [
                'company_id' => $companyB->id,
                'title' => 'Beta Utility Truck Bundle',
                'slug' => 'beta-utility-truck-bundle',
                'description' => 'Two utility trucks suitable for field operations.',
                'category' => ListingCategory::Vehicles->value,
                'status' => ListingStatus::Published->value,
                'price' => 98000,
                'currency' => Currency::EUR->value,
                'country' => Country::LU->value,
                'city' => 'Esch-sur-Alzette',
                'published_at' => now()->subHours(8),
                'expires_at' => now()->addWeeks(3),
            ],
            [
                'company_id' => $companyB->id,
                'title' => 'Beta Office Building Option',
                'slug' => 'beta-office-building-option',
                'description' => 'Office building option with flexible acquisition terms.',
                'category' => ListingCategory::CommercialProperty->value,
                'status' => ListingStatus::Draft->value,
                'price' => 1500000,
                'currency' => Currency::EUR->value,
                'country' => Country::FR->value,
                'city' => 'Paris',
                'published_at' => null,
                'expires_at' => null,
            ],
            [
                'company_id' => $companyB->id,
                'title' => 'Beta Trademark Portfolio',
                'slug' => 'beta-trademark-portfolio',
                'description' => 'Trademark portfolio across the Benelux region.',
                'category' => ListingCategory::IntangibleAssets->value,
                'status' => ListingStatus::PendingReview->value,
                'price' => 210000,
                'currency' => Currency::GBP->value,
                'country' => Country::LU->value,
                'city' => 'Luxembourg City',
                'published_at' => null,
                'expires_at' => null,
            ],
            [
                'company_id' => $companyB->id,
                'title' => 'Beta Future Production Site',
                'slug' => 'beta-future-production-site',
                'description' => 'Production site scheduled for publication after final review.',
                'category' => ListingCategory::CommercialProperty->value,
                'status' => ListingStatus::Published->value,
                'price' => 890000,
                'currency' => Currency::EUR->value,
                'country' => Country::BE->value,
                'city' => 'Ghent',
                'published_at' => now()->addDay(),
                'expires_at' => now()->addMonth(),
            ],
        ];

        foreach ($listings as $listing) {
            Listing::query()->create($listing);
        }
    }
}
