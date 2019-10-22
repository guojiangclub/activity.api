<?php

use Faker\Factory;

$faker = Factory::create('zh_CN');

$factory->defineAs(\GuojiangClub\Activity\Core\Models\Activity::class, 'activity', function () use ($faker) {
    return [
        'title' => $faker->words(3, true),
        'subtitle' => $faker->words(3, true),
        'content' => $faker->text(),
        'img' => 'http://i1.piimg.com/4851/0456a672e003a875.jpg',
        'address' => $faker->words(3, true),
        'starts_at' => $faker->dateTimeBetween('-2 month', '+3 months'),
        'ends_at' => $faker->dateTimeBetween('-1 month', '+4 months'),
        'entry_end_at' => $faker->dateTimeBetween('-1 month', '+4 months')
    ];
});

$factory->defineAs(\GuojiangClub\Activity\Core\Models\City::class, 'city', function () use ($faker) {
    return [
        'name' => '长沙',
        'img' => 'http://i1.piimg.com/4851/0456a672e003a875.jpg',
        'province' => 430000,
        'city' => 430100,
        'area' => 430105
    ];
});

$factory->defineAs(\GuojiangClub\Activity\Core\Models\Payment::class, 'payment', function () use ($faker) {
    return [
        'title' => $faker->word,
        'limit' => $faker->randomDigitNotNull
    ];
});