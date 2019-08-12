<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */
use App\Models\Product\Product;
use Faker\Generator as Faker;

$factory->define(Product::class, function (Faker $faker) {
    return [
        'name' => $name = $faker->company,
        'slug'=> str_slug($name),
        'price' => random_int(10,100)
    ];
});
