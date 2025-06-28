<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Route::redirect('/', 'tasks');

Route::middleware(['auth', 'verified'])->group(function (): void {
    Volt::route('tasks', 'tasks')->name('tasks');
    Volt::route('entries', 'entries')->name('entries');
    Volt::route('meals', 'meals')->name('meals');
});

Route::middleware(['auth'])->group(function (): void {
    Route::redirect('settings', 'settings/profile');

    Volt::route('settings/profile', 'settings.profile')->name('settings.profile');
    Volt::route('settings/password', 'settings.password')->name('settings.password');
    Volt::route('settings/task-models', 'settings.task-models')->name('settings.task-models');
    Volt::route('settings/appearance', 'settings.appearance')->name('settings.appearance');
});

require __DIR__ . '/auth.php';
