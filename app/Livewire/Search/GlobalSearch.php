<?php

namespace App\Livewire\Search;

use App\Models\Crop;
use App\Models\Employee;
use App\Models\Equipment;
use App\Models\Field;
use App\Models\InventoryItem;
use App\Models\Property;
use App\Models\Season;
use App\Models\Supplier;
use Livewire\Component;

class GlobalSearch extends Component
{
    public string $query = '';
    public bool $open = false;

    public function render()
    {
        $q = trim($this->query);
        $results = [];
        if (strlen($q) >= 2) {
            $results = [
                'Propriedades' => Property::where('name', 'like', "%{$q}%")->limit(5)->get()->map(fn ($m) => ['label' => $m->name, 'url' => route('properties.index')]),
                'Talhões' => Field::where('name', 'like', "%{$q}%")->limit(5)->get()->map(fn ($m) => ['label' => $m->name, 'url' => route('fields.index')]),
                'Safras' => Season::where('name', 'like', "%{$q}%")->limit(5)->get()->map(fn ($m) => ['label' => $m->name, 'url' => route('seasons.show', $m)]),
                'Produtos' => InventoryItem::where('name', 'like', "%{$q}%")->limit(5)->get()->map(fn ($m) => ['label' => $m->name, 'url' => route('inventory.index')]),
                'Funcionários' => Employee::where('name', 'like', "%{$q}%")->limit(5)->get()->map(fn ($m) => ['label' => $m->name, 'url' => route('employees.index')]),
                'Fornecedores' => Supplier::where('company_name', 'like', "%{$q}%")->limit(5)->get()->map(fn ($m) => ['label' => $m->company_name, 'url' => route('suppliers.index')]),
                'Máquinas' => Equipment::where('name', 'like', "%{$q}%")->limit(5)->get()->map(fn ($m) => ['label' => $m->name, 'url' => route('equipment.index')]),
                'Culturas' => Crop::where('name', 'like', "%{$q}%")->limit(5)->get()->map(fn ($m) => ['label' => $m->name, 'url' => route('crops.index')]),
            ];
        }

        return view('livewire.search.global-search', compact('results'));
    }
}
