<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BlogCategory extends Model
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
    public function blogs()
    {
        return $this->hasMany(Blog::class, 'blog_category_id', 'id');
    }
}
