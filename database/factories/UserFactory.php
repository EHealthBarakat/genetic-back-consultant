<?php

namespace Database\Factories;

use App\Enums\UserGenderEnum;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'first_name' => fake()->firstName,
            'last_name' => fake()->lastName,
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'mobile' => fake('fa_IR')->mobileNumber,
            'mobile_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
            'gender_enum' =>  fake()->randomElement(array_column(UserGenderEnum::cases(),'value')),
            'birthday' => fake()->dateTimeBetween('-50 years', '-18 years')->format('Y-m-d'),
            'is_active' => true,
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
