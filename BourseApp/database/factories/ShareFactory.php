<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Share;
use Faker\Generator as Faker;
use Carbon\Carbon;

$factory->define(Share::class, function (Faker $faker) {

    $type = array('share','tracker','fund','indice');

    return [
        'name'          => $faker->unique()->name,
        'codeISIN'      => $faker->unique()->randomNumber($nbDigits = NULL, $strict = false),
        'type'          => $type[array_rand($type)],
        ];
});
