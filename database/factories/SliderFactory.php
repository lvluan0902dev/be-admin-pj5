<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Model;
use Faker\Generator as Faker;

$factory->define(\App\Models\Slider::class, function (Faker $faker) {
    return [
        'title' => $faker->text(30),
        'content' => $faker->text(50),
        'link' => $faker->text(30),
        'image_name' => $faker->text(30),
        'image_path' => $faker->imageUrl(),
        'status' => $faker->numberBetween(0, 1)
    ];
});
