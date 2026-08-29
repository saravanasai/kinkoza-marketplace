<?php

namespace Database\Factories;

use App\Enums\Country;
use App\Enums\Currency;
use App\Enums\ListingCategory;
use App\Enums\ListingStatus;
use App\Models\Company;
use App\Models\Listing;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Listing>
 */
class ListingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = fake()->sentence(4);

        return [
            'company_id' => Company::factory(),
            'title' => $title,
            'slug' => Str::slug($title).'-'.fake()->unique()->lexify('??????'),
            'description' => fake()->paragraphs(3, true),
            'category' => fake()->randomElement(ListingCategory::cases())->value,
            'status' => ListingStatus::Draft->value,
            'price' => fake()->numberBetween(1000, 250000),
            'currency' => fake()->randomElement(Currency::cases())->value,
            'country' => fake()->randomElement(Country::cases())->value,
            'city' => fake()->city(),
            'published_at' => null,
            'expires_at' => null,
        ];
    }

    public function published(): static
    {
        return $this->state(fn (): array => [
            'status' => ListingStatus::Published->value,
            'published_at' => now()->subDay(),
            'expires_at' => now()->addMonth(),
        ]);
    }

    public function draft(): static
    {
        return $this->state(fn (): array => [
            'status' => ListingStatus::Draft->value,
            'published_at' => null,
            'expires_at' => null,
        ]);
    }

    public function pendingReview(): static
    {
        return $this->state(fn (): array => [
            'status' => ListingStatus::PendingReview->value,
            'published_at' => null,
            'expires_at' => null,
        ]);
    }

    public function expired(): static
    {
        return $this->state(fn (): array => [
            'status' => ListingStatus::Expired->value,
            'published_at' => now()->subMonth(),
            'expires_at' => now()->subDay(),
        ]);
    }

    public function futurePublication(): static
    {
        return $this->state(fn (): array => [
            'status' => ListingStatus::Published->value,
            'published_at' => now()->addDay(),
            'expires_at' => now()->addMonth(),
        ]);
    }
}
