<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\BibitController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\JaringController;
use App\Http\Controllers\KategoriControler;
use App\Http\Controllers\KolamController;
use App\Http\Controllers\PakanController;
use App\Http\Controllers\PanenController;
use App\Http\Controllers\PembagianBibitController;
use App\Http\Controllers\PembagianPakanController;
use App\Http\Controllers\PembelianController;
use App\Http\Controllers\PemberianPakanController;
use App\Http\Controllers\PenjualanController;
use App\Http\Controllers\ProdukController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\TongController;

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
    return redirect()->route('dashboard')->with(['title' => 'DASHBOARD']);
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
        Route::get('/{id}/edit', [PembelianController::class, 'edit'])->name('pembelian.edit');
        Route::get('/{id}/edit-json', [PembelianController::class, 'editJson'])->name('pembelian.edit.json');
        Route::post('/', [PembelianController::class, 'store'])->name('pembelian.store');
        Route::post('/{id}/edit', [PembelianController::class, 'update'])->name('pembelian.update');
        Route::post('/datatable', [PembelianController::class, 'datatable'])->name('pembelian.datatable');

        // Route::get('/contoh', [PembelianController::class, 'contoh'])->name('pembelian.contoh');
        Route::get('/{id}/show', [PembelianController::class, 'show'])->name('pembelian.show');
        Route::get('/create', [PembelianController::class, 'create'])->name('pembelian.create');

        Route::get('/delete/{id}', [PembelianController::class, 'destroy'])->name('pembelian.destory');

        Route::group(['prefix' => 'detail'], function () {
            Route::post('/', [PembelianController::class, 'storeDetail'])->name('pembelian.store.detail');
            Route::post('/{id}/edit', [PembelianController::class, 'updateDetail'])->name('pembelian.update.detail');
            Route::get('/delete/{id}', [PembelianController::class, 'destroyDetail'])->name('pembelian.destory.detail');
        });
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

    Route::group(['prefix' => 'tong'], function () {
        Route::get('/', [TongController::class, 'index'])->name('tong');
        Route::get('/create', [TongController::class, 'create'])->name('tong.create');
        Route::get('/{id}/edit', [TongController::class, 'edit'])->name('tong.edit');
        Route::get('/delete/{id}', [TongController::class, 'destroy'])->name('tong.destory');
        Route::get('/contoh', [TongController::class, 'contoh'])->name('tong.contoh');
        Route::post('/', [TongController::class, 'store'])->name('tong.store');
        Route::post('/{id}/update', [TongController::class, 'update'])->name('tong.update');
        Route::post('/datatable', [TongController::class, 'datatable'])->name('tong.datatable');
    });

    Route::group(['prefix' => 'customer'], function () {
        Route::get('/', [CustomerController::class, 'index'])->name('customer');
        Route::get('/create', [CustomerController::class, 'create'])->name('customer.create');
        Route::get('/{id}/edit', [CustomerController::class, 'edit'])->name('customer.edit');
        Route::get('/delete/{id}', [CustomerController::class, 'destroy'])->name('customer.destory');

        Route::post('/', [CustomerController::class, 'store'])->name('customer.store');
        Route::post('/{id}/update', [CustomerController::class, 'update'])->name('customer.update');
        Route::post('/datatable', [CustomerController::class, 'datatable'])->name('customer.datatable');
    });

    Route::group(['prefix' => 'pembagian-bibit'], function () {
        Route::get('/', [PembagianBibitController::class, 'index'])->name('pembagian.bibit');
        Route::get('/create', [PembagianBibitController::class, 'create'])->name('pembagian.bibit.create');
        Route::get('/contoh', [PembagianBibitController::class, 'contoh'])->name('pembagian.bibit.contoh');
        Route::get('/{id}/edit', [PembagianBibitController::class, 'edit'])->name('pembagian.bibit.edit');
        Route::get('/{id}/edit-json', [PembagianBibitController::class, 'editJson'])->name('pembagian.bibit.edit.json');
        Route::get('/{id}/show', [PembagianBibitController::class, 'show'])->name('pembagian.bibit.show');
        Route::get('/delete/{id}', [PembagianBibitController::class, 'destroy'])->name('pembagian.bibit.destory');

        Route::post('/', [PembagianBibitController::class, 'store'])->name('pembagian.bibit.store');
        Route::post('/sortir', [PembagianBibitController::class, 'storeSortir'])->name('pembagian.bibit.store.sortir');
        Route::post('/{id}/update', [PembagianBibitController::class, 'update'])->name('pembagian.bibit.update');
        Route::post('/datatable', [PembagianBibitController::class, 'datatable'])->name('pembagian.bibit.datatable');
        Route::post('/datatable/detail', [PembagianBibitController::class, 'datatableDetail'])->name('pembagian.bibit.datatable.detail');

        Route::group(['prefix' => 'detail'], function () {
            Route::post('/', [PembagianBibitController::class, 'storeDetail'])->name('pembagian.bibit.store.detail');
            Route::post('/{id}/edit', [PembagianBibitController::class, 'updateDetail'])->name('pembagian.bibit.update.detail');
            Route::get('/delete/{id}', [PembagianBibitController::class, 'destroyDetail'])->name('pembagian.bibit.destory.detail');
        });
    });

    Route::group(['prefix' => 'pembagian-pakan'], function () {
        Route::get('/', [PembagianPakanController::class, 'index'])->name('pembagian.pakan');
        Route::get('/create', [PembagianPakanController::class, 'create'])->name('pembagian.pakan.create');
        Route::get('/contoh', [PembagianPakanController::class, 'contoh'])->name('pembagian.pakan.contoh');
        Route::get('/{id}/edit', [PembagianPakanController::class, 'edit'])->name('pembagian.pakan.edit');
        Route::get('/{id}/edit-json', [PembagianPakanController::class, 'editJson'])->name('pembagian.pakan.edit.json');
        Route::get('/delete/{id}', [PembagianPakanController::class, 'destroy'])->name('pembagian.pakan.destory');
        Route::get('/{id}/show', [PembagianPakanController::class, 'show'])->name('pembagian.pakan.show');

        Route::post('/', [PembagianPakanController::class, 'store'])->name('pembagian.pakan.store');
        Route::post('/{id}/update', [PembagianPakanController::class, 'update'])->name('pembagian.pakan.update');
        Route::post('/datatable', [PembagianPakanController::class, 'datatable'])->name('pembagian.pakan.datatable');
        Route::post('/datatable/detail', [PembagianPakanController::class, 'datatableDetail'])->name('pembagian.pakan.datatable.detai;');

        Route::group(['prefix' => 'detail'], function () {
            Route::post('/', [PembagianPakanController::class, 'storeDetail'])->name('pembagian.pakan.store.detail');
            Route::post('/{id}/edit', [PembagianPakanController::class, 'updateDetail'])->name('pembagian.pakan.update.detail');
            Route::get('/delete/{id}', [PembagianPakanController::class, 'destroyDetail'])->name('pembagian.pakan.destory.detail');
        });
    });

    Route::group(['prefix' => 'pemberian-pakan'], function () {
        Route::get('/', [PemberianPakanController::class, 'index'])->name('pemberian.pakan');
        Route::get('/create', [PemberianPakanController::class, 'create'])->name('pemberian.pakan.create');
        Route::get('/pembagian-bibit-by-tong/{id}', [PemberianPakanController::class, 'getBagiBibitByTong']);
        // Route::get('/contoh', [PemberianPakanController::class, 'contoh'])->name('pemberian.pakan.contoh');
        Route::get('/{id}/edit', [PemberianPakanController::class, 'edit'])->name('pemberian.pakan.edit');
        // Route::get('/{id}/edit-json', [PemberianPakanController::class, 'editJson'])->name('pemberian.pakan.edit.json');
        Route::get('/delete/{id}', [PemberianPakanController::class, 'destroy'])->name('pemberian.pakan.destory');

        Route::post('/', [PemberianPakanController::class, 'store'])->name('pemberian.pakan.store');
        Route::post('/{id}/update', [PemberianPakanController::class, 'update'])->name('pemberian.pakan.update');
        Route::post('/datatable', [PemberianPakanController::class, 'datatable'])->name('pemberian.pakan.datatable');
    });

    Route::group(['prefix' => 'panen'], function () {
        Route::get('/', [PanenController::class, 'index'])->name('panen');
        Route::get('/create', [PanenController::class, 'create'])->name('panen.create');
        // Route::get('/contoh', [PanenController::class, 'contoh'])->name('panen.contoh');
        Route::get('/{id}/edit', [PanenController::class, 'edit'])->name('panen.pakan.edit');
        Route::get('/{id}/edit-json', [PanenController::class, 'editJson'])->name('panen.edit.json');
        Route::get('/delete/{id}', [PanenController::class, 'destroy'])->name('panen.destory');
        Route::get('/{id}/show', [PanenController::class, 'show'])->name('panen.show');

        Route::post('/', [PanenController::class, 'store'])->name('panen.store');
        Route::post('/{id}/update', [PanenController::class, 'update'])->name('panen.update');
        Route::post('/datatable', [PanenController::class, 'datatable'])->name('panen.datatable');

        Route::group(['prefix' => 'detail'], function () {
            Route::post('/', [PanenController::class, 'storeDetail'])->name('panen.store.detail');
            Route::post('/{id}/edit', [PanenController::class, 'updateDetail'])->name('panen.update.detail');
            Route::get('/delete/{id}', [PanenController::class, 'destroyDetail'])->name('panen.destory.detail');
        });
    });

    Route::group(['prefix' => 'penjualan'], function () {
        Route::get('/', [PenjualanController::class, 'index'])->name('jual');
        Route::get('/create', [PenjualanController::class, 'create'])->name('jual.create');
        Route::get('/{id}/edit', [PenjualanController::class, 'edit'])->name('jual.pakan.edit');
        Route::get('/{id}/edit-json', [PenjualanController::class, 'editJson'])->name('jual.edit.json');
        Route::get('/delete/{id}', [PenjualanController::class, 'destroy'])->name('jual.destory');
        Route::get('/{id}/show', [PenjualanController::class, 'show'])->name('jual.show');

        Route::post('/', [PenjualanController::class, 'store'])->name('jual.store');
        Route::post('/{id}/update', [PenjualanController::class, 'update'])->name('jual.update');
        Route::post('/datatable', [PenjualanController::class, 'datatable'])->name('jual.datatable');

        Route::group(['prefix' => 'detail'], function () {
            Route::post('/', [PenjualanController::class, 'storeDetail'])->name('jual.store.detail');
            Route::post('/{id}/edit', [PenjualanController::class, 'updateDetail'])->name('jual.update.detail');
            Route::get('/delete/{id}', [PenjualanController::class, 'destroyDetail'])->name('jual.destory.detail');
        });
    });

    Route::group(['prefix' => 'bibit'], function () {
        Route::get('/', [BibitController::class, 'index'])->name('bibit');
        Route::get('/contoh', [BibitController::class, 'contoh'])->name('bibit.contoh');
        Route::get('/create', [BibitController::class, 'create'])->name('bibit.create');
        Route::get('/{id}/edit', [BibitController::class, 'edit'])->name('bibit.edit');
        Route::get('/delete/{id}', [BibitController::class, 'destroy'])->name('bibit.destory');

        Route::post('/', [BibitController::class, 'store'])->name('bibit.store');
        Route::post('/{id}/update', [BibitController::class, 'update'])->name('bibit.update');
        Route::post('/datatable', [BibitController::class, 'datatable'])->name('bibit.datatable');
    });

    Route::group(['prefix' => 'pakan'], function () {
        Route::get('/', [PakanController::class, 'index'])->name('pakan');
        Route::get('/contoh', [PakanController::class, 'contoh'])->name('pakan.contoh');
        Route::get('/create', [PakanController::class, 'create'])->name('pakan.create');
        Route::get('/{id}/edit', [PakanController::class, 'edit'])->name('pakan.edit');
        Route::get('/delete/{id}', [PakanController::class, 'destroy'])->name('pakan.destory');

        Route::post('/', [PakanController::class, 'store'])->name('pakan.store');
        Route::post('/{id}/update', [PakanController::class, 'update'])->name('pakan.update');
        Route::post('/datatable', [PakanController::class, 'datatable'])->name('pakan.datatable');
    });
});
