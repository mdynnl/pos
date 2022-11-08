<?php

use App\Http\Controllers\PaymentController;
use App\Http\Controllers\OrderController;
use App\Models\Item;
use App\Models\Order;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->group(function () {

    Route::get('/orders', [OrderController::class, 'index'])->name('orders');
    Route::get('/orders/create', [OrderController::class, 'create'])->name('orders.create');
    Route::get('/', function () {
        if (auth()->user()->is_admin) {
            return Redirect::route('dashboard');
        } else {
            return Redirect::route('orders.create');
        }
    });


    Route::get('/orders/{order}/edit', [OrderController::class, 'edit'])->name('order.edit');
    Route::put('/orders/{order}/payment', [OrderController::class, 'payment'])->name('order.payment');
    Route::put('/orders/{order}/cancel', [OrderController::class, 'cancel'])->name('order.cancel');
    Route::put('/orders/{order}/complete', [OrderController::class, 'complete'])->name('order.complete');
    Route::get('/orders/{order}/download', [OrderController::class, 'download'])->name('order.download');
    Route::delete('/orders/{order}', [OrderController::class, 'destroy'])->name('order.destroy');

    Route::post('/orders/{order}/items/', [OrderController::class, 'storeItem'])->name('items.store');
    Route::put('/orders/{order}/items/{orderItem}', [OrderController::class, 'updateItem'])->name('item.update');


    Route::get('/dashboard', function () {
        $orders = Order::all();
        $ordersByDate = $orders->groupBy(fn ($o) => $o->created_at->toDateString());
        return inertia('Dashboard', [
            'orders' => [
                'all' => $orders,
                'byDate' => $ordersByDate,
            ],
            'total' => [
                'all' => $orders->sum('total'),
                'byDate' => $ordersByDate->map->sum('total'),
            ],
            'items' => Item::all(),
        ]);
    })->name('dashboard')->middleware(['admin']);
});


require __DIR__ . '/auth.php';
