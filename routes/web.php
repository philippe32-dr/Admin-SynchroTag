<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\PuceController;

Route::get('/', function () {
    return view('welcome');
});

// Users
Route::get('/admin/users', [UserController::class, 'index'])->name('users.index');
Route::get('/admin/users/create', [UserController::class, 'create'])->name('users.create');
Route::post('/admin/users', [UserController::class, 'store'])->name('users.store');
Route::get('/admin/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
Route::put('/admin/users/{user}', [UserController::class, 'update'])->name('users.update');
Route::delete('/admin/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
Route::post('/admin/users/search', [UserController::class, 'search'])->name('users.search');

// Clients
Route::get('/admin/clients', [ClientController::class, 'index'])->name('clients.index');
Route::get('/admin/clients/create', [ClientController::class, 'create'])->name('clients.create');
Route::post('/admin/clients', [ClientController::class, 'store'])->name('clients.store');
Route::get('/admin/clients/{client}/edit', [ClientController::class, 'edit'])->name('clients.edit');
Route::put('/admin/clients/{client}', [ClientController::class, 'update'])->name('clients.update');
Route::post('/admin/clients/{client}/deactivate', [ClientController::class, 'deactivate'])->name('clients.deactivate');
Route::get('/admin/clients/{client}/show', [ClientController::class, 'show'])->name('clients.show');
Route::post('/admin/clients/{client}/puces/{puce}/remove', [ClientController::class, 'removePuce'])->name('clients.puces.remove');
Route::post('/admin/clients/search', [ClientController::class, 'search'])->name('clients.search');

// Puces
Route::get('/admin/puces', [PuceController::class, 'index'])->name('puces.index');
Route::get('/admin/puces/create', [PuceController::class, 'create'])->name('puces.create');
Route::post('/admin/puces', [PuceController::class, 'store'])->name('puces.store');
Route::get('/admin/puces/{puce}/edit', [PuceController::class, 'edit'])->name('puces.edit');
Route::put('/admin/puces/{puce}', [PuceController::class, 'update'])->name('puces.update');
Route::delete('/admin/puces/{puce}', [PuceController::class, 'destroy'])->name('puces.destroy');
Route::post('/admin/puces/{puce}/assign', [PuceController::class, 'assign'])->name('puces.assign');
Route::post('/admin/puces/{puce}/unassign', [PuceController::class, 'unassign'])->name('puces.unassign');
Route::post('/admin/puces/search', [PuceController::class, 'search'])->name('puces.search');

// KYC
Route::get('/admin/kyc', [App\Http\Controllers\KycController::class, 'index'])->name('kyc.index');
Route::get('/admin/kyc/create', [App\Http\Controllers\KycController::class, 'create'])->name('kyc.create');
Route::post('/admin/kyc', [App\Http\Controllers\KycController::class, 'store'])->name('kyc.store');
Route::get('/admin/kyc/{kyc}/show', [App\Http\Controllers\KycController::class, 'show'])->name('kyc.show');
Route::post('/admin/kyc/{kyc}/validate', [App\Http\Controllers\KycController::class, 'validateKyc'])->name('kyc.validate');
Route::post('/admin/kyc/{kyc}/reject', [App\Http\Controllers\KycController::class, 'reject'])->name('kyc.reject');
Route::post('/admin/kyc/search', [App\Http\Controllers\KycController::class, 'search'])->name('kyc.search');

// Dashboard d'administration
Route::get('/admin', [App\Http\Controllers\AdminController::class, 'dashboard'])->name('admin.dashboard');


Route::put('/clients/{client}', [App\Http\Controllers\ClientController::class, 'update'])->name('clients.update');
Route::post('/clients/{client}/puces/assign', [App\Http\Controllers\ClientController::class, 'assignPuces'])->name('clients.puces.assign');
Route::post('/clients/{client}/puces/remove', [App\Http\Controllers\ClientController::class, 'removePuces'])->name('clients.puces.remove');