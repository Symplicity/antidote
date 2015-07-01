<?php

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| Here you may define all of your model factories. Model factories give
| you a convenient way to create models for testing and seeding your
| database. Just tell the factory how a default model should look.
|
*/

$factory->define(App\DrugSideEffect::class, function (Faker\Generator $faker) {
    return [
        'value' => $faker->unique()->word()
    ];
});

$factory->define(App\DrugIndication::class, function (Faker\Generator $faker) {
    return [
        'value' => $faker->unique()->word()
    ];
});

$factory->define(App\User::class, function (Faker\Generator $faker) {
    return [
        'username' => $faker->userName,
        'email' => $faker->email,
        'password' => Hash::make('123'),
        'age' => $faker->numberBetween(18, 85),
        'gender' => $faker->randomElement(['m', 'f'])
    ];
});

$factory->define(App\Drug::class, function (Faker\Generator $faker) {
    return [
        'rxcui' => $faker->ean8(),
        'type' => $faker->randomElement(['brand', 'generic']),
        'label' => ucfirst($faker->word()),
        'generic' => ucfirst($faker->word()),
        'generic_id' => $faker->numberBetween(1, 50),
        'description' => $faker->paragraph(3),
        'drug_forms' => [ucfirst($faker->word()), ucfirst($faker->word())],
        'recalls' => [
            [
                'number' => $faker->ean8(),
                'date' => $faker->date($format = 'Ymd', $max = 'now'), // '20140827'
                'recall' => $faker->text(250),
                'lots' => 'Lot Number: ' . $faker->ean8() . ', Exp ' . $faker->date($format = 'm / d / Y', $max = 'now')//9 / 30 / 2014'
            ]
        ]
    ];
});

$factory->define(App\DrugReview::class, function (Faker\Generator $faker) {
    $upvotes = $faker->numberBetween(0, 50);
    return [
        'user_id' => $faker->numberBetween(1, 50),
        'drug_id' => $faker->numberBetween(1, 50),
        'age' => $faker->numberBetween(18, 85),
        'gender' => $faker->randomElement(['m', 'f']),
        'rating' => $faker->numberBetween(1, 3),
        'is_covered_by_insurance' => $faker->boolean(),
        'upvotes_cache' => $upvotes,
        'downvotes_cache' => $faker->numberBetween($upvotes, 50) - $upvotes,
        'comment' => $faker->text(250)
    ];
});

$factory->define(App\DrugSideEffect::class, function (Faker\Generator $faker) {
    return [
        'value' => $faker->unique()->word()
    ];
});

$factory->define(App\DrugIndication::class, function (Faker\Generator $faker) {
    return [
        'value' => $faker->unique()->word()
    ];
});

$factory->define(App\DrugReviewVote::class, function (Faker\Generator $faker) {
    return [
        'vote' => $faker->randomElement([-1, 1])
    ];
});
