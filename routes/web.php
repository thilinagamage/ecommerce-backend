<?php

use App\Http\Controllers\Admin\Dashboard\OverviewController;
use App\Http\Controllers\Admin\Products\AttributeController;
use App\Http\Controllers\Admin\Products\CategoryController;
use App\Http\Controllers\Admin\Products\ProductController;
use App\Http\Controllers\Admin\Products\ProductVariationController;
use App\Http\Controllers\Admin\Products\TagController;
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

Route::prefix('users')->controller(UserController::class)->group(function() {
    Route::get('/', [UserController::class, 'index'])->name('users.index');
    Route::get('/create', [UserController::class, 'create'])->name('users.create');
    Route::post('/store', [UserController::class, 'store'])->name('users.store');
    Route::get('/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
    Route::put('/{user}', [UserController::class, 'update'])->name('users.update');
    Route::delete('/{user}', [UserController::class, 'destroy'])->name('users.destroy');
});



Route::prefix('categories')->controller(CategoryController::class)->group(function() {
    Route::get('/', 'index')->name('products.categories.index');
    Route::get('/create', 'create')->name('products.categories.create');
    Route::post('/store', 'store')->name('products.categories.store');
    Route::get('/{category}/edit','edit')->name('products.categories.edit');
    Route::put('/{category}', 'update')->name('products.categories.update');
    Route::delete('/{category}', 'destroy')->name('products.categories.destroy');
    Route::get('/{category}}','show')->name('products.categories.show');

});

Route::prefix('products')->controller(ProductController::class)->group(function() {

    Route::get('/','index')->name('products.index');
    Route::get('/create', 'create')->name('products.create');
    Route::post('/store', 'store')->name('products.store');
    Route::get('/{id}/edit','edit')->name('products.edit');
    Route::put('/{id}','update')->name('products.update');
    Route::get('products/{id}','show')->name('products.show');
    Route::delete('/{id}','destroy')->name('products.destroy');
    Route::delete('/products/gallery-image/{image}','deleteGalleryImage')->name('products.gallery-image.delete');

});

Route::prefix('products/{product}')->group(function () {

    Route::post('variations/generate',
        [ProductVariationController::class, 'generate']
    )->name('products.variations.generate');

    Route::delete('variations/{variation}',
        [ProductVariationController::class, 'destroy']
    )->name('products.variations.destroy');

    Route::post('variations/{variation}/toggle',
        [ProductVariationController::class, 'toggle']
    )->name('products.variations.toggle');

    Route::put('variations/{variation}',
        [ProductVariationController::class, 'update']
    )->name('products.variations.update');

    Route::delete('variations-cleanup',
        [ProductVariationController::class, 'cleanup']
    )->name('products.variations.cleanup');
});


Route::prefix('attributes')->controller(AttributeController::class)->group(function() {
    Route::get('/','index')->name('products.attributes.index');
    Route::get('/create','create')->name('products.attributes.create');
    Route::post('/store', 'store')->name('products.attributes.store');
    Route::get('/{attribute}/edit', 'edit')->name('products.attributes.edit');
    Route::put('/{attribute}','update')->name('products.attributes.update');
    Route::delete('/{destroy}','destroy')->name('products.attributes.destroy');
});

Route::prefix('tags')->controller(TagController::class)->group(function() {
    Route::get('/', 'index')->name('products.tags.index');
    Route::get('/create','create')->name('products.tags.create');
    Route::post('/store', 'store')->name('products.tags.store');
    Route::get('/{id}/edit', 'edit')->name('products.tags.edit');
    Route::put('/{id}/update', 'update')->name('products.tags.update');
    Route::delete('/{tag}', 'destroy')->name('products.tags.destroy');


});



    Route::post('products/{product}/variations/generate', [ProductVariationController::class, 'generate'])->name('products.variations.generate');
    Route::patch('variations/{variation}',[ProductVariationController::class, 'update'])->name('variations.update');
    Route::patch('variations/{variation}/toggle',[ProductVariationController::class, 'toggle'])->name('variations.toggle');






Route::get('/', function (OverviewController $controller) {
    return $controller->index();
})->name('admin.dashboard.index');
