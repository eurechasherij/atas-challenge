<?php

namespace Database\Factories;

use App\Models\XeroToken;
use Illuminate\Database\Eloquent\Factories\Factory;

class XeroTokenFactory extends Factory
{
    protected $model = XeroToken::class;

    public function definition()
    {
        return [
            'user_id' => fn() => \App\Models\User::factory(),
            'access_token' => $this->faker->sha256,
            'refresh_token' => $this->faker->sha256,
            'tenant_id' => $this->faker->uuid,
            'expires_in' => 3600,
            'fetched_at' => now(),
        ];
    }
}
