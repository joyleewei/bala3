<?php

use Faker\Generator as Faker;

$factory->define(App\Models\Reply::class, function (Faker $faker) {
    // 随机取一个月以内的时间
    $time = $faker->dateTimeThisMonth();
    return [
        'content' => $faker->sentence(),
        'updated_at' => $time,
        'created_at' => $time,
    ];
});
