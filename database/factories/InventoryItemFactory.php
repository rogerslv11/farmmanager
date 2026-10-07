<?php

namespace Database\Factories;

use App\Models\InventoryCategory;
use App\Models\InventoryItem;
use App\Models\Organization;
use Illuminate\Database\Eloquent\Factories\Factory;

class InventoryItemFactory extends Factory
{
    protected $model = InventoryItem::class;

    public function definition(): array
    {
        return [
            'organization_id' => Organization::factory(),
            'inventory_category_id' => InventoryCategory::factory(),
            'name' => 'Produto '.$this->faker->word,
            'unit' => 'un',
            'current_stock' => 100,
            'minimum_stock' => 20,
            'average_cost' => 50,
            'status' => 'ativo',
        ];
    }
}
