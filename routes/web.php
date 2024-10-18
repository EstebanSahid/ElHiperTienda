<?php

use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use App\http\Middleware\CheckRol;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

/* CONTROL DE TRAFICO HTTP */
Route::middleware(['auth', 'verified'])->group(function () {
    /* PARA TODOS LOS USUARIOS AUTENTICADOS */
    Route::get('/getProducts', [OrderController::class, 'getProducts'])->name('order.getProducts');

    // Perfil
    Route::controller(ProfileController::class)->group(function() {
        Route::get('/profile', 'edit')->name('profile.edit');
        Route::patch('/profile', 'update')->name('profile.update');
        Route::delete('/profile', 'destroy')->name('profile.destroy');
    });

    // Ordenes
    Route::controller(OrderController::class)->group(function () {
        Route::get('/dashboard', 'index')->name('dashboard');
        Route::get('/order/{id}/create', 'create')->name('order.create');
        Route::post('/orders', 'store')->name('order.store');
    });

    /* PARA ADMINISTRADORES */

    // Administrar Usuarios
    Route::middleware(CheckRol::class)->group(function () {
        Route::controller(UserController::class)->group(function() {
            Route::get('/users', 'index')->name('users');
            Route::get('/registerUser', 'create')->name('registro.user');
            Route::post('/users', 'store')->name('users.store');
        });
    });
    
    /*
    Route::controller(UserController::class)->group(function() {
        Route::get('/users', 'index')->name('users')->middleware(CheckRol::class);
        Route::get('/registerUser', 'create')->name('registro.user')->middleware(CheckRol::class);
        Route::post('/users', 'store')->name('users.store')->middleware(CheckRol::class);
    });
    */


    // Reportes
    Route::get('/reports', function () {
        return Inertia::render('Reports/Reports');
    })->name('reportes');


    

    // Administracion Tiendas
    Route::get('/stores', function() {
        return Inertia::render('Admin/Stores/Stores');
    })->name('stores');

    // Administracion de Produtos
    Route::get('/products', function() {
        return Inertia::render(('Products/Products'));
    })->name('products');

});

require __DIR__.'/auth.php';
