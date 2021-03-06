<?php

use Inertia\Inertia;
use App\Models\Order;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Application;
use App\Http\Controllers\OrderController;

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

// Route::get('/', function () {
//     return Inertia::render('Welcome', [
//         'canLogin' => Route::has('login'),
//         'canRegister' => Route::has('register'),
//         'laravelVersion' => Application::VERSION,
//         'phpVersion' => PHP_VERSION,
//     ]);
// });
Route::redirect('/','/logowanie');



Route::middleware(['auth', 'verified'])->group(function () {

    Route::get('/panel', function () {

        return Inertia::render('Dashboard',[
            'newOrders'=>Order::where('status','Nowe')->count(),
            'ConfirmOrders'=>Order::where('status','Potwierdzone')->count(),
            'InvoiceOrders'=>Order::where('status','Zafakturowane')->count()
        ]);
    })->name('dashboard');

    Route::controller(OrderController::class)->group(function () {
        Route::get('/zamowieniaPobierz', 'getOrders')->name('orders.get');
        Route::get('/zamowienia', 'index')->name('orders');
        Route::get('/zamowienie/{order}/faktura', 'invoice')->name('orders.single.invoice');
        Route::get('/zamowienie/{order}/faktura/podglad', 'invoiceShow')->name('orders.single.invoice.show');
        Route::post('/zamowienie/{order}/faktura', 'invoiceSend')->name('orders.single.invoice.post');
        Route::get('/zamowienie/{order}', 'show')->name('orders.single');
        Route::patch('/zamowienie/aktualizacja/{id}', 'update')->name('orders.single.update');
    });
});




require __DIR__.'/auth.php';
