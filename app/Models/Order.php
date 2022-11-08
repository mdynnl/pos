<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Order extends Model
{
    protected $fillable = ['status', 'paid'];
    protected $appends = ['total', 'change', 'remaining'];
    use HasFactory;
    function items()
    {
        return $this->belongsToMany(Item::class, OrderItem::class, 'order_id', 'item_id')->withPivot('id', 'quantity');
    }

    function order_items()
    {
        return $this->hasMany(OrderItem::class);
    }

    function getRemainingAttribute()
    {
        return round(max(0, $this->total - $this->paid), 2);
    }
    function getChangeAttribute()
    {
        return round(max($this->paid - $this->total, 0), 2);
    }

    function getTotalAttribute($total)
    {
        $total = DB::table('orders')
            ->leftJoin('order_item', 'order_item.order_id', '=', 'orders.id')
            ->leftJoin('items', 'items.id', '=', 'order_item.item_id')
            ->where('orders.id', $this->id)
            ->sum(DB::raw('items.price * order_item.quantity'));

        return round($total, 2);
    }

    function getPaidAttribute($value)
    {
        return round($value, 2);
    }
}
