<?php

namespace App\Livewire\Audit;

use App\Models\AuditLog;
use Livewire\Component;
use Livewire\WithPagination;

class AuditIndex extends Component
{
    use WithPagination;

    public string $search = '';

    public function render()
    {
        $items = AuditLog::with('user')
            ->when($this->search, fn ($q) => $q->where('description', 'like', "%{$this->search}%"))
            ->orderByDesc('id')->paginate(20);

        return view('livewire.audit.index', compact('items'));
    }
}
