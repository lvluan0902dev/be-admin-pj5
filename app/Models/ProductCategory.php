<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductCategory extends Model
{
    /**
     * @var array
     */
    protected $guarded = [];

    /**
     * ACTIVE STATUS
     */
    const ACTIVE_STATUS = 1;

    /**
     * INACTIVE STATUS
     */
    const INACTIVE_STATUS = 0;

    /**
     * @return HasMany
     */
    public function products()
    {
        return $this->hasMany(Product::class, 'product_category_id', 'id');
    }
}
