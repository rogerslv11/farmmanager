<?php

namespace App\Jobs;

use App\Models\AgriculturalActivity;
use App\Models\MaintenanceRecord;
use App\Models\User;
use App\Notifications\GenericFarmAlert;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class CheckOperationalAlerts implements ShouldQueue
{
    use Queueable;

    public function handle(): void
    {
        $overdueActivities = AgriculturalActivity::withoutGlobalScopes()->overdue()->with('organization')->get()->groupBy('organization_id');
        foreach ($overdueActivities as $orgId => $activities) {
            $users = User::where('organization_id', $orgId)->whereIn('role', ['admin', 'gestor', 'agronomo'])->get();
            foreach ($users as $user) {
                $user->notify(new GenericFarmAlert('Atividades atrasadas', "{$activities->count()} atividade(s) agrícola(s) em atraso.", route('activities.index'), 'activity_overdue'));
            }
        }

        $overdueMaint = MaintenanceRecord::withoutGlobalScopes()->overdue()->with('equipment')->get()->groupBy('organization_id');
        foreach ($overdueMaint as $orgId => $records) {
            $users = User::where('organization_id', $orgId)->whereIn('role', ['admin', 'gestor'])->get();
            foreach ($users as $user) {
                $user->notify(new GenericFarmAlert('Manutenção atrasada', "{$records->count()} manutenção(ões) em atraso.", route('maintenance.index'), 'maintenance_overdue'));
            }
        }
    }
}
