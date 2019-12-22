<?php

namespace App;

use App\Share;

use Illuminate\Database\Eloquent\Model;

class PriceShares extends Model
{
    // define the fields that can be given through a request
    protected $fillable = [
        'date',
        'share_id',
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

    public function share()
    {
        return $this->belongsTo(Share::class);
    }
}
