<?php

namespace Database\Factories;

use App\Models\InventoryCategory;
use App\Models\Organization;
use Illuminate\Database\Eloquent\Factories\Factory;

class InventoryCategoryFactory extends Factory
{
    protected $model = InventoryCategory::class;

    public function definition(): array
    {
        return ['organization_id' => Organization::factory(), 'name' => 'Categoria '.$this->faker->word, 'type' => 'insumo'];
    }
}
