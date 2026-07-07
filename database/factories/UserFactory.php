<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    protected static ?string $password;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
            'role' => User::ROLE_USER,
            // Approved by default so factory users can use the app in tests.
            'status' => User::STATUS_APPROVED,
            'approved_at' => now(),
        ];
    }

    /** Always give the user a profile row, mirroring real registration. */
    public function configure(): static
    {
        return $this->afterCreating(function (User $user) {
            // Create via the relation without reading $user->profile first, which
            // would cache a null relation on the in-memory instance.
            $user->profile()->create(['sender_name' => $user->name]);
        });
    }

    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    public function pending(): static
    {
        return $this->state(fn () => ['status' => User::STATUS_PENDING, 'approved_at' => null]);
    }

    public function admin(): static
    {
        return $this->state(fn () => ['role' => User::ROLE_ADMIN, 'status' => User::STATUS_APPROVED]);
    }
}
