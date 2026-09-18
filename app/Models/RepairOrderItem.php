<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RepairOrderItem extends Model
{
    protected $fillable = [
        'repair_order_id',
        'kit_part_id',
        'barcode',
        'title',
        'purchase_price',
        'sale_price',
    ];

    public function order()
    {
        return $this->belongsTo(RepairOrder::class, 'repair_order_id');
    }

    public function part()
    {
        return $this->belongsTo(KitPart::class, 'kit_part_id');
    }
}
