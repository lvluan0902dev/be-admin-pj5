<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    /**
     * @var array
     */
    protected $guarded = [];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function product_category()
    {
        return $this->belongsTo(ProductCategory::class, 'product_category_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function product_brand()
    {
        return $this->belongsTo(ProductBrand::class, 'product_brand_id', 'id');
    }
}
