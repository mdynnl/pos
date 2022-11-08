<?php

namespace App\Http\Controllers;

use App\Enums\OrderStatus;
use App\Models\Item;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
{
    function create()
    {
        return Redirect::route('order.edit', [
            'order' => Order::firstWhere('status', '!=', OrderStatus::Complete)
                ?? Order::create()
        ]);
    }

    function index()
    {
        $orders = Order::all();
        return inertia('Orders/Index', [
            'orders' => $orders,
        ]);
    }

    function download(Order $order)
    {
        return app()
            ->make('dompdf.wrapper')
            ->loadView('pdf', ['order' => $order])
            ->stream();
    }

    function payment(Order $order)
    {
        $order->update(['status' => OrderStatus::Payment]);
    }

    function cancel(Order $order)
    {
        $order->update(['status' => OrderStatus::Ongoing]);
    }
    function complete(Order $order)
    {
        ['paid' => $paid] = request()->validate(['paid' => 'integer']);

        if ($paid < $order->total) {
            request()->validate([
                'paid' => function ($attr, $value, $fail) {
                    $fail("Payment not enough");
                }
            ]);
        } else {
            $order->update([
                'status' => OrderStatus::Complete,
                'paid' => $paid,
            ]);
        }
    }


    function edit(Order $order)
    {
        return inertia('Orders/Edit', [
            'allItems' => Item::all(),
            'order' => $order->load('items'),
        ]);
    }

    function storeItem(Order $order)
    {
        $item = Item::firstWhere(request()->validate(['code' => 'size:8']));

        if (!$item) {
            request()->validate([
                'code' => function ($attr, $value, $fail) {
                    $fail('Invalid item code');
                }
            ]);
        }

        $this->updateOrderItem(
            $order
                ->order_items()
                ->firstOrNew(
                    ['item_id' => $item->id],
                    ['quantity' => 0]
                )
        );
    }

    function updateOrderItem(OrderItem $orderItem, $by = 1)
    {
        $orderItem->quantity += $by;
        if ($orderItem->quantity <= 0) {
            if ($orderItem->id) {
                $orderItem->delete();
            }
        } else {
            $orderItem->save();
        }
    }

    function updateItem(Order $order, OrderItem $orderItem)
    {
        $this->updateOrderItem(
            $orderItem,
            request()->validate(['by' => ['integer']])['by']
        );
    }

    function destroy()
    {
    }
}
