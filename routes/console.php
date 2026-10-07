<?php

use App\Jobs\CheckOperationalAlerts;
use App\Jobs\CheckStockAlerts;
use Illuminate\Support\Facades\Schedule;

Schedule::job(new CheckStockAlerts)->dailyAt('07:00');
Schedule::job(new CheckOperationalAlerts)->dailyAt('07:15');
