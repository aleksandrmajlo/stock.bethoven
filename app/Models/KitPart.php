<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KitPart extends Model
{
    protected $fillable = [
        'kit_id',
        'sequence_number',
        'title',
        'barcode',
        'purchase_price',
        'sale_price',
        'status',
        'printed_at',
    ];

    protected $casts = [
        'printed_at' => 'datetime',
    ];

    public function kit()
    {
        return $this->belongsTo(Kit::class);
    }
}
