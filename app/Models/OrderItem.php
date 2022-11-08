<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class OrderItem extends Pivot
{
    function order()
    {
        return $this->belongsTo(Order::class);
    }
    
    function item()
    {
        return $this->belongsTo(Item::class);
    }
}
