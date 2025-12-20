<?php

namespace Database\Factories;

use App\Models\Country;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        // Get or create a test country (Ghana is created in the countries migration)
        $country = Country::firstOrCreate(
            ['name' => 'Ghana'],
            [
                'id' => (string) Str::uuid(),
                'country_code' => 'GH',
                'tel_code' => '+233',
                'is_phone_number_required_during_onboarding' => false,
                'is_phone_number_default_verification_medium' => false,
            ]
        );

        return [
            'uuid' => (string) Str::uuid(),
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'phone_number' => '+233' . $this->faker->numerify('#########'),
            'country_id' => $country->id,
            'email_verified_at' => now(),
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function unverified()
    {
        return $this->state(function (array $attributes) {
            return [
                'email_verified_at' => null,
            ];
        });
    }
}
