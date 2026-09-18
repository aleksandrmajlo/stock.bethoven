<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RepairOrder extends Model
{
    protected $fillable = [
        'client_name',
        'client_phone',
        'car_info',
        'master_name',
        'total_amount',
        'status',
        'paid_at',
        'confirmed_by',
    ];

    protected $casts = [
        'paid_at' => 'datetime',
    ];

    public function items()
    {
        return $this->hasMany(RepairOrderItem::class);
    }
}
