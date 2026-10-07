<?php

namespace Database\Factories;

use App\Models\Organization;
use App\Models\Property;
use Illuminate\Database\Eloquent\Factories\Factory;

class PropertyFactory extends Factory
{
    protected $model = Property::class;

    public function definition(): array
    {
        return [
            'organization_id' => Organization::factory(),
            'name' => 'Fazenda '.$this->faker->city,
            'city' => $this->faker->city,
            'state' => 'GO',
            'total_area' => $this->faker->randomFloat(2, 50, 3000),
            'productive_area' => $this->faker->randomFloat(2, 40, 2500),
            'status' => 'ativa',
        ];
    }
}
