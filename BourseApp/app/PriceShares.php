<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PriceShares extends Model
{
    // define the fields that can be given through a request
    protected $fillable = [
        'date',
        'open',
        'highest',
        'lowest',
        'close',
        'volume',
    ];

    // if some field should be casted for instance to boolean

    protected $casts = [
        'date'  => 'datetime',
    ];

    protected $dates = ['created_at', 'updated_at'];

}
