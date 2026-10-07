<?php

namespace Database\Factories;

use App\Models\Organization;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class OrganizationFactory extends Factory
{
    protected $model = Organization::class;

    public function definition(): array
    {
        $name = 'Fazenda '.$this->faker->lastName;

        return ['name' => $name, 'slug' => Str::slug($name).'-'.$this->faker->unique()->randomNumber(4), 'active' => true, 'plan' => 'essencial'];
    }
}
