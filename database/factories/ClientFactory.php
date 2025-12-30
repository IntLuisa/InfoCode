<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Client>
 */
class ClientFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'document' => fake()->optional()->numerify('###########'),
            'email' => fake()->optional()->safeEmail(),
            'phone' => fake()->optional()->phoneNumber(),
            'address' => fake()->optional()->streetAddress(),
            'number' => fake()->optional()->buildingNumber(),
            'complement' => fake()->optional()->secondaryAddress(),
            'district' => fake()->optional()->citySuffix(),
            'state' => fake()->optional()->stateAbbr(),
            'city' => fake()->optional()->city(),
            'zip_code' => fake()->optional()->sentence(1),
            'origin' => fake()->optional()->randomElement(['Instagram', 'Site', 'Indicação', 'Prospecção']),
        ];
    }
}
