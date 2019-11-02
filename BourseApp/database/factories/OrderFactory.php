<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Order;
use Faker\Generator as Faker;

$factory->define(Order::class, function (Faker $faker) {

    $type = array('buy','sale','dividend', 'other');
    $price = $faker->randomfloat($nbMaxDecimals = 3, $min = 10, $max = 100);
    $quantity = $faker->numberBetween($min = 1, $max = 100);
    $charges = $faker->randomfloat($nbMaxDecimals = 3, $min = 0, $max = 10);

    return [
        'share_id'      => factory(\App\Share::class),
        'passedOn'      => $faker->dateTime(),
        'type'          => $type[array_rand($type)], 
        'price'         => $price,
        'quantity'      => $quantity,
        'totalPrice'    => $price * $quantity,
        'totalChargedPrice' => $price * $quantity + $charges,
        'charges'       => $charges,
        'chargesPercent' => $charges / ($price * $quantity + $charges),
        'comment'       => $faker->sentence(10),
    ];
});
