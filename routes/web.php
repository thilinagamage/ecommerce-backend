<?php

use App\Http\Controllers\Admin\Dashboard\OverviewController;
use App\Http\Controllers\Admin\User\UserController;
use Illuminate\Support\Facades\Route;


Route::middleware([])->group(function () {

  //  Route::middleware('permission:view_users')->get('/users', [UserController::class, 'index'])->name('users.index');
  // Route::middleware('permission:create_users')->get('/users/create', [UserController::class, 'create'])->name('users.create');
    // Route::middleware('permission:create_users')->post('/users', [UserController::class, 'store'])->name('users.store');
    // Route::middleware('permission:edit_users')->get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
    // Route::middleware('permission:edit_users')->put('/users/{user}', [UserController::class, 'update'])->name('users.update');
    // Route::middleware('permission:delete_users')->delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
});


Route::get('/users', [UserController::class, 'index'])->name('users.index');
Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
Route::post('/users/store', [UserController::class, 'store'])->name('users.store');
Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');



Route::get('/', function (OverviewController $controller) {
    return $controller->index();
})->name('admin.dashboard.index');
