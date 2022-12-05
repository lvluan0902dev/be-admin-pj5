<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Blog extends Model
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
    public function blog_category()
    {
        return $this->belongsTo(BlogCategory::class, 'blog_category_id', 'id');
    }
}
