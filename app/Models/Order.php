<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    /**
     * @var array
     */
    protected $guarded = [];

    /**
     * NEW STATUS
     */
    const NEW_STATUS = 0;

    /**
     * TRANSPORT STATUS
     */
    const TRANSPORT_STATUS = 1;

    /**
     * DONE STATUS
     */
    const DONE_STATUS = 2;

    /**
     * CANCEL STATUS
     */
    const CANCEL_STATUS = 3;

    /**
     * @return HasMany
     */
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class, 'order_id', 'id');
    }
}
