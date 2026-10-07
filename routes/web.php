<?php

use App\Http\Controllers\AuthController;
use App\Models\Property;
use App\Models\Season;
use Illuminate\Support\Facades\Route;

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.attempt');
Route::post('/login/demo', [AuthController::class, 'demoLogin'])->name('login.demo');

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/', fn () => view('dashboard'))->name('dashboard');

    $m = [
        'properties' => 'properties', 'fields' => 'fields', 'crops' => 'crops',
        'plantings' => 'plantings', 'seasons' => 'seasons', 'activities' => 'activities',
        'production' => 'production', 'harvests' => 'harvests', 'inventory' => 'inventory',
        'purchases' => 'purchases', 'suppliers' => 'suppliers', 'equipment' => 'equipment',
        'maintenance' => 'maintenance', 'employees' => 'employees', 'customers' => 'customers',
        'sales' => 'sales', 'expenses' => 'expenses', 'reports' => 'reports',
    ];
    foreach ($m as $name => $module) {
        Route::get("/{$name}", fn () => view('generic', ['title' => ucfirst($name), 'component' => $name]))->name("{$name}.index")->middleware("module:{$module}");
    }

    Route::get('/properties/{property}', function (Property $property) {
        abort_unless($property->organization_id === auth()->user()->organization_id, 403);
        return view('properties.show', ['property' => $property->load(['fields.currentPlanting.crop', 'equipment', 'employees', 'expenses'])]);
    })->name('properties.show')->middleware('module:properties');

    Route::get('/seasons/{season}', function (Season $season) {
        abort_unless($season->organization_id === auth()->user()->organization_id, 403);
        return view('generic', ['title' => $season->name, 'component' => 'season-dashboard', 'season' => $season]);
    })->name('seasons.show')->middleware('module:seasons');

    Route::get('/notifications', fn () => view('notifications.index'))->name('notifications.index');
    Route::get('/audit', fn () => view('generic', ['title' => 'Auditoria', 'component' => 'audit']))->name('audit.index')->middleware('module:reports');
    Route::get('/profile', [AuthController::class, 'showProfile'])->name('profile');
    Route::put('/profile', [AuthController::class, 'updateProfile'])->name('profile.update');
    Route::put('/profile/password', [AuthController::class, 'updatePassword'])->name('profile.password');
});
