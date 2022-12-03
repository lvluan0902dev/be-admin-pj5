<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Faq extends Model
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
}
