<?php

namespace Database\Factories;

use App\Enums\Country;
use App\Enums\KybStatus;
use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Company>
 */
class CompanyFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->company();

        return [
            'name' => $name,
            'registration_number' => strtoupper(fake()->bothify('##??######')),
            'vat_number' => strtoupper(fake()->bothify('??########')),
            'country' => fake()->randomElement(Country::cases())->value,
            'city' => fake()->city(),
            'contact_email' => Str::slug($name).'@example.test',
            'contact_phone' => fake()->phoneNumber(),
            'kyb_status' => KybStatus::Verified->value,
        ];
    }
}
