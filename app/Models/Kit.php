<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kit extends Model
{
    protected $fillable = [
        'title',
        'purchase_price',
        'sale_price',
        'status',
    ];

    public function parts()
    {
        return $this->hasMany(KitPart::class);
    }
}
