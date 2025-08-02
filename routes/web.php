<?php

use App\Http\Controllers\OrderController;
use App\Http\Controllers\PDFController;
use App\Http\Controllers\ProductsController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\TiendaController;
use App\Http\Controllers\UnidadMedidaController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ExcelController;
use app\Http\Controllers\AuditController;
use App\http\Middleware\CheckRol;
use App\Models\User;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => User::count() === 0 ? true : false,
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

/* CONTROL DE TRAFICO HTTP */
Route::middleware(['auth', 'verified'])->group(function () {

    /* PARA TODOS LOS USUARIOS AUTENTICADOS */

    Route::controller(ExcelController::class)->group(function() {
        Route::post('/ObtenerHojasExcel', 'ObtenerHojasExcel')->name('ObtenerHojasExcel');
        Route::post('/importarProductosExcel', 'importarProductosExcel')->name('importarProductosExcel');
        Route::post('/importarTiendasExcel', 'importarTiendasExcel')->name('importarTiendasExcel');

    });
    
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
        Route::get('/order/{id}/edit', 'renderEdit')->name('show.edit');
        Route::get('/order/{idPedido}/{idTienda}/duplicate', 'renderDuplicate')->name('show.duplicate');
        Route::post('/orders', 'store')->name('order.store');
        Route::put('/editOrders', 'update')->name('order.update');
        Route::get('/orders', 'indexOrders')->name('index.orders');
        Route::get('/order/{id}/view', 'viewOrder')->name('view.order');
        //Route::get('/getProducts', 'getProducts')->name('order.getProducts');
    });
    // GenerarPDF
    Route::controller(PDFController::class)->group(function () {
        Route::post('/generatePDF', 'generatePDF')->name('generatePDF');
        Route::post('/order/{idPedido}/generatePDF', 'generarPDFPorPedido')->name('reporte.pedido');
    });

    // Reportes
    Route::controller(ReportController::class)->group(function () {
        Route::get('/reports', 'create')->name('reportes');
    });

    /* PARA ADMINISTRADORES */
    Route::middleware(CheckRol::class)->group(function () {

        // Administrar Usuarios
        Route::controller(UserController::class)->group(function() {
            Route::get('/users', 'index')->name('users');
            Route::get('/registerUser', 'create')->name('registro.user');
            Route::post('/users', 'store')->name('users.store');
            Route::get('/users/{id}/edit', 'renderEdit')->name('users.edit');
            Route::put('/usersEdit', 'update')->name('users.update');
            Route::put('/userDelete', 'deactivate')->name('user.deactivate');
        });

        // Administrar Tiendas
        Route::controller(TiendaController::class)->group(function() {
            Route::get('/stores', 'index')->name('stores');
            Route::get('/registerStore', 'create')->name('registro.store');
            Route::post('/stores', 'store')->name('stores.store');
            Route::get('/tiendas/{id}/edit', 'renderEdit')->name('stores.edit');
            Route::put('/storeEdit', 'update')->name('store.update');
            Route::put('/storeDelete', 'deactivate')->name('store.deactivate');
        });

        // Administrar Productos
        Route::controller(ProductsController::class)->group(function() {
            Route::get('/products', 'index')->name('products');
            Route::get('/registerProduct', 'create')->name('registro.product');
            Route::post('/products', 'store')->name('products.store');
            /*
            Route::get('/productos/{id}/edit', 'renderEdit')->name('product.edit');
            Route::put('/productEdit', 'update')->name('product.update');
            */
            Route::get('/configuration', 'renderEdit')->name('configuration');
            Route::put('/productEdit', 'update')->name('product.update');
            Route::put('/productActivate', 'activate')->name('product.activate');
            Route::put('/productDelete', 'deactivate')->name('product.deactivate');
            Route::get('/editMassiveProducts', 'renderEditMassive')->name('edit.massive.product');
        });
        
        // Administrar Unidades de Medida
        Route::controller(UnidadMedidaController::class)->group(function() {
            Route::get('/unidadMedida', 'index')->name('unidad.medida');
            Route::get('/registerUnidad', 'create')->name('registro.unidad');
            Route::post('/unidadMedida', 'store')->name('unidad.store');
            Route::get('/unidad/{id}/edit', 'renderEdit')->name('unidad.edit');
            Route::put('/unidadEdit', 'update')->name('unidad.update');
            Route::put('/unidadDelete', 'deactivate')->name('unidad.deactivate');
        });

        // Para ver auditorias
        Route::controller(AuditController::class)->group(function() {
            Route::get('/audits', 'index')->name('audits');
            Route::get('/audits/{id}', 'show')->name('audits.show');
        });
    });
});

require __DIR__.'/auth.php';
