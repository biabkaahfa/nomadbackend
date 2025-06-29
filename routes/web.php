<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Profils\ProfilsController;
use App\Http\Controllers\Permissions\PermissionsController;
use App\Http\Controllers\Compagnies\CompagniesController;
use App\Http\Controllers\Trajets\TrajetsController;
use Illuminate\Support\Facades\Route;
//ProfilsController   PermissionsController
Route::get('/user', [UserController::class, 'index'])->name('users.index');

Route::resource('profils',ProfilsController::class);
Route::resource('permissions', PermissionsController::class);
Route::resource('compagnies', CompagniesController::class);

Route::resource('trajets', TrajetsController::class);

Route::middleware(['auth'])->group(function () {
    Route::post('logout', [AuthController::class, 'logout'])->name('logout');
    // Route::resource('users', UserController::class)
    //     ->names('users');
});

Route::middleware(['guest'])->group(function () {
    Route::get('login', [AuthController::class, 'loginPage'])->name('login');
    Route::post('login', [AuthController::class, 'authenticate']);
});
 
        
    
   
    Route::resource('users', UserController::class);
    //  // Routes supplémentaires pour les utilisateurs
    Route::patch('users/{user}/toggle-status', [UserController::class, 'toggleStatus'])
         ->name('users.toggleStatus');
    
    Route::get('users/search', [UserController::class, 'search'])
         ->name('users.search');
 // Routes pour la gestion des utilisateurs
   