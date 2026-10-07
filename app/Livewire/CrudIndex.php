<?php

namespace App\Livewire;

use Livewire\Component;

abstract class CrudIndex extends Component
{
    public string $search = '';
    public string $statusFilter = '';
    public string $sortField = 'id';
    public string $sortDir = 'desc';
    public int $perPage = 12;
    public bool $showModal = false;
    public bool $showDelete = false;
    public ?int $editingId = null;
    public ?int $deletingId = null;
    public string $layout = 'cards';

    public function updatingSearch(): void { $this->resetPage(); }
    public function updatingStatusFilter(): void { $this->resetPage(); }

    public function sortBy(string $field): void
    {
        if ($this->sortField === $field) {
            $this->sortDir = $this->sortDir === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDir = 'asc';
        }
    }

    public function create(): void
    {
        $this->resetForm();
        $this->editingId = null;
        $this->showModal = true;
    }

    public function edit(int $id): void
    {
        $this->editingId = $id;
        $this->fillForm($id);
        $this->showModal = true;
    }

    public function confirmDelete(int $id): void
    {
        $this->deletingId = $id;
        $this->showDelete = true;
    }

    protected function notify(string $message, string $type = 'success'): void
    {
        $this->dispatch('notify', message: $message, type: $type);
    }

    abstract protected function resetForm(): void;
    abstract protected function fillForm(int $id): void;
}
