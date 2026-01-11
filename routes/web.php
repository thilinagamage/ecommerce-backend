<?php

use App\Http\Controllers\Admin\Dashboard\OverviewController;
use App\Http\Controllers\Admin\Products\CategoryController;
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

Route::prefix('categories')->controller(CategoryController::class)->group(function() {
    Route::get('/', 'index')->name('products.categories.index');
    Route::get('/create', 'create')->name('products.categories.create');
    Route::post('/store', 'store')->name('products.categories.store');
    Route::get('/{category}/edit','edit')->name('products.categories.edit');
    Route::put('/{category}', 'update')->name('products.categories.update');
    Route::delete('/{category}', 'destroy')->name('products.categories.destroy');
    Route::get('/{category}}','show')->name('products.categories.show');

});

Route::get('/', function (OverviewController $controller) {
    return $controller->index();
})->name('admin.dashboard.index');
