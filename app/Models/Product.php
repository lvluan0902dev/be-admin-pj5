<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
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
     * @return BelongsTo
     */
    public function product_category()
    {
        return $this->belongsTo(ProductCategory::class, 'product_category_id', 'id');
    }

    /**
     * @return BelongsTo
     */
    public function product_brand()
    {
        return $this->belongsTo(ProductBrand::class, 'product_brand_id', 'id');
    }

    /**
     * @return HasMany
     */
    public function product_images()
    {
        return $this->hasMany(ProductImage::class, 'product_id', 'id');
    }

    /**
     * @return HasMany
     */
    public function product_options()
    {
        return $this->hasMany(ProductOption::class, 'product_id', 'id');
    }
}
