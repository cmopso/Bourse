<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Share extends Model
{
    // define the fields that can be given through a request
    protected $fillable = [
        'name',
        'codeISIN',
        'type',
    ];

    // if some field should be casted for instance to boolean

    /*protected $casts = [
    
    ];*/

    protected $dates = ['created_at', 'updated_at'];

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function priceShares()
    {
        return $this->hasMany(PriceShares::class);
    }
}
