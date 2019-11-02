<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\PriceShares;
use Faker\Generator as Faker;

$factory->define(PriceShares::class, function (Faker $faker) {

    $type = array('buy','sale','dividend', 'other');
    $open = $faker->randomfloat($nbMaxDecimals = 3, $min = 0, $max = 100);
    $highest = $faker->randomfloat($nbMaxDecimals = 3, $min = 0, $max = 100);
    $lowest = $faker->randomfloat($nbMaxDecimals = 3, $min = 0, $max = 100);
    $close =  $faker->randomfloat($nbMaxDecimals = 3, $min = 0, $max = 100);

    return [
        'share_id'      => factory(\App\Share::class),
        'date'          => $faker->dateTime(),
        'open'          => $open,
        'highest'       => max($open, $highest, $lowest, $close), 
        'lowest'        => min($open, $highest, $lowest, $close), 
        'close'         => $close,
        'volume'        => $faker->numberBetween($min = 1, $max = 100000),        
    ];
});
