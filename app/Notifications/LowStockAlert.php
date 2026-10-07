<?php

namespace App\Notifications;

use App\Models\InventoryItem;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class LowStockAlert extends Notification
{
    use Queueable;

    public function __construct(public InventoryItem $item) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'type' => 'low_stock',
            'title' => 'Estoque baixo',
            'message' => "O produto \"{$this->item->name}\" está abaixo do mínimo ({$this->item->current_stock} {$this->item->unit}).",
            'url' => route('inventory.index'),
            'item_id' => $this->item->id,
        ];
    }
}
