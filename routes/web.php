<?php

use App\Http\Livewire\Ec20BankComp;
use App\Http\Livewire\Ec20BankDetailComp;
use Illuminate\Support\Facades\Route;



Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth'])->name('dashboard');


Route::get('/banks', Ec20BankComp::class)->name('banks.index');
Route::get('/banks/create', Ec20BankDetailComp::class)->name('banks.create');
Route::get('/banks/{id}/edit', Ec20BankDetailComp::class)->name('banks.edit');




require __DIR__.'/auth.php';
