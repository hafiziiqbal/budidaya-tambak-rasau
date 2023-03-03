<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\JaringController;
use App\Http\Controllers\KategoriControler;
use App\Http\Controllers\KolamController;
use App\Http\Controllers\PembelianController;
use App\Http\Controllers\Produk\PakanController;
use App\Http\Controllers\ProdukController;
use App\Http\Controllers\SupplierController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('home');
});

Route::group(['middleware' => 'guest'], function () {
    Route::get('/login', [AuthController::class, 'login'])->name('login');

    Route::post('/login', [AuthController::class, 'authenticate'])->name('login.auth');
});

Route::group(['middleware' => 'auth'], function () {
    Route::get('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/dashboard', function () {
        return view('dashboard')->with(['title' => 'DASHBOARD']);
    })->name('dashboard');

    Route::group(['prefix' => 'supplier'], function () {
        Route::get('/', [SupplierController::class, 'index'])->name('supplier');
        Route::get('/create', [SupplierController::class, 'create'])->name('supplier.create');
        Route::get('/{id}/edit', [SupplierController::class, 'edit'])->name('supplier.edit');
        Route::get('/delete/{id}', [SupplierController::class, 'destroy'])->name('supplier.destory');

        Route::post('/', [SupplierController::class, 'store'])->name('supplier.store');
        Route::post('/{id}/update', [SupplierController::class, 'update'])->name('supplier.update');
        Route::post('/datatable', [SupplierController::class, 'datatable'])->name('supplier.datatable');
    });

    Route::group(['prefix' => 'kategori'], function () {
        Route::get('/', [KategoriControler::class, 'index'])->name('kategori');
        Route::get('/create', [KategoriControler::class, 'create'])->name('kategori.create');
        Route::get('/{id}/edit', [KategoriControler::class, 'edit'])->name('kategori.edit');
        Route::get('/delete/{id}', [KategoriControler::class, 'destroy'])->name('kategori.destory');

        Route::post('/', [KategoriControler::class, 'store'])->name('kategori.store');
        Route::post('/{id}/update', [KategoriControler::class, 'update'])->name('kategori.update');
        Route::post('/datatable', [KategoriControler::class, 'datatable'])->name('kategori.datatable');
    });

    Route::group(['prefix' => 'kolam'], function () {
        Route::get('/', [KolamController::class, 'index'])->name('kolam');
        Route::get('/create', [KolamController::class, 'create'])->name('kolam.create');
        Route::get('/{id}/edit', [KolamController::class, 'edit'])->name('kolam.edit');
        Route::get('/delete/{id}', [KolamController::class, 'destroy'])->name('kolam.destory');

        Route::post('/', [KolamController::class, 'store'])->name('kolam.store');
        Route::post('/{id}/update', [KolamController::class, 'update'])->name('kolam.update');
        Route::post('/datatable', [KolamController::class, 'datatable'])->name('kolam.datatable');
    });

    Route::group(['prefix' => 'jaring'], function () {
        Route::get('/', [JaringController::class, 'index'])->name('jaring');
        Route::get('/create', [JaringController::class, 'create'])->name('jaring.create');
        Route::get('/{id}/edit', [JaringController::class, 'edit'])->name('jaring.edit');
        Route::get('/delete/{id}', [JaringController::class, 'destroy'])->name('jaring.destory');

        Route::post('/', [JaringController::class, 'store'])->name('jaring.store');
        Route::post('/{id}/update', [JaringController::class, 'update'])->name('jaring.update');
        Route::post('/datatable', [JaringController::class, 'datatable'])->name('jaring.datatable');
    });

    Route::group(['prefix' => 'pembelian'], function () {
        Route::get('/', [PembelianController::class, 'index'])->name('pembelian');
        Route::get('/contoh', [PembelianController::class, 'contoh'])->name('pembelian.contoh');
        Route::get('/{id}/show', [PembelianController::class, 'show'])->name('pembelian.show');
        Route::get('/create', [PembelianController::class, 'create'])->name('pembelian.create');
        Route::get('/{id}/edit', [PembelianController::class, 'edit'])->name('pembelian.edit');
        Route::get('/delete/{id}', [PembelianController::class, 'destroy'])->name('pembelian.destory');

        Route::post('/', [PembelianController::class, 'store'])->name('pembelian.store');
        Route::post('/{id}/update', [PembelianController::class, 'update'])->name('pembelian.update');
        Route::post('/datatable', [PembelianController::class, 'datatable'])->name('pembelian.datatable');
    });

    Route::group(['prefix' => 'produk'], function () {
        Route::get('/', [ProdukController::class, 'index'])->name('produk');
        Route::get('/create', [ProdukController::class, 'create'])->name('produk.create');
        Route::get('/{id}/edit', [ProdukController::class, 'edit'])->name('produk.edit');
        Route::get('/delete/{id}', [ProdukController::class, 'destroy'])->name('produk.destory');

        Route::post('/', [ProdukController::class, 'store'])->name('produk.store');
        Route::post('/{id}/update', [ProdukController::class, 'update'])->name('produk.update');
        Route::post('{id}/datatable', [ProdukController::class, 'datatable'])->name('produk.datatable');
    });
});
