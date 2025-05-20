<?php

use App\Livewire\CreateTicket;
use App\Livewire\EditTicket;
use App\Livewire\ListTickets;
use App\Livewire\UserLogin;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('login', UserLogin::class)->name('login');

Route::group([
    'middleware' => 'auth',
    'prefix' => 'tickets',
    'as' => 'tickets.',
], function()
{
    Route::get('/', ListTickets::class)->name('index');

    Route::get('create', CreateTicket::class)->name('create');

    Route::get('{ticket}/edit', EditTicket::class)->name('edit');
});
