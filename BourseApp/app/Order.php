<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    // define the fields that can be given through a request
    protected $fillable = [
        'share_id',
        'passedOn',
        'type',
        'price',
        'quantity',
        'totalPrice',
        'totalChargedPrice',
        'charges',
        'chargesPercent',
        'comment',
    ];

    // if some field should be casted for instance to boolean

    protected $casts = [
        'passedOn'  => 'datetime',
    ];

    protected $dates = ['created_at', 'updated_at'];

    public function share() 
    {
        return $this->belongsTo(Share::class);
    }
}
