<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductEximController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use Spatie\Health\Http\Controllers\HealthCheckResultsController;

Route::get('health', HealthCheckResultsController::class)->name('health');
Route::view('/', 'auth.login');
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth'])->name('dashboard');

// Profie group route
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Route group user
Route::controller(UserController::class)
    ->prefix('users')
    ->middleware('auth')
    ->group(function () {
        Route::get('/', 'index')->name('users.index');
    });

// Catalogue group route
Route::controller(CategoryController::class)
    ->prefix('catalogue')
    ->middleware('auth')
    ->group(function () {
        Route::get('/', 'index')->name('catalogue.index');
        Route::get('/category/{id}', 'show_category')->name('catalogue.category');
    });

// Product group route
Route::controller(ProductController::class)
    ->prefix('products')
    ->middleware(['auth', 'verified'])
    ->group(function () {
        Route::get('/', 'index')->name('products.index');
        Route::get('/create', 'create')->name('products.create');
        Route::post('/create', 'store')->name('products.store');
        Route::get('/details/{id}', 'show')->name('products.show');
        Route::get('/edit/{id}', 'edit')->name('products.edit');
        Route::put('/updates/{id}', 'update')->name('products.update');
        Route::delete('/delete/{id}', 'destroy')->name('products.delete');
        Route::get('import', 'import')->name('products.import');
        Route::post('/', 'importstore')->name('products.importstore');
        Route::get('export', 'export')->name('products.export');
    });

// Exim (export & import) controller
Route::get('importproduct', [ProductEximController::class, 'import'])->name('exim.import');
Route::get('exportproduct', [ProductEximController::class, 'export'])->name('exim.export');
Route::post('product', [ProductEximController::class, 'importstore'])->name('exim.store');

require __DIR__.'/auth.php';
