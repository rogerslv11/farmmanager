<?php

namespace App\Jobs;

use App\Models\InventoryItem;
use App\Models\User;
use App\Notifications\LowStockAlert;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class CheckStockAlerts implements ShouldQueue
{
    use Queueable;

    public function handle(): void
    {
        InventoryItem::withoutGlobalScopes()->lowStock()->chunk(100, function ($items) {
            foreach ($items as $item) {
                $users = User::where('organization_id', $item->organization_id)->whereIn('role', ['admin', 'gestor', 'estoquista'])->get();
                foreach ($users as $user) {
                    $already = $user->notifications()->where('data->item_id', $item->id)->where('data->type', 'low_stock')->whereNull('read_at')->exists();
                    if (! $already) {
                        $user->notify(new LowStockAlert($item));
                    }
                }
            }
        });
    }
}
