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

$factory->define(App\User::class, function (Faker\Generator $faker) {
    return [
        'username' => $faker->userName,
        'email' => $faker->email,
        'password' => Hash::make('123'),
        'age' => $faker->numberBetween(1, 85),
        'gender' => $faker->randomElement(['m', 'f'])
    ];
});

$factory->define(App\Drug::class, function (Faker\Generator $faker) {
    return [
        'label' => ucfirst($faker->word()),
        'description' => $faker->text(250),
        'rxcui' => $faker->ean8(),
        'generic' => ucfirst($faker->word()),
        'drug_forms' => [ucfirst($faker->word()), ucfirst($faker->word())],
        'generic_id' => $faker->numberBetween(1, 50),
        'prescription_type' => $faker->randomElement(['Prescription and OTC', 'Prescription', '-', 'OTC']), //TODO: see if we should make this a multi-pick instead
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
    return [
        'user_id' => $faker->numberBetween(1, 50),
        'age' => $faker->numberBetween(1, 85),
        'drug_id' => $faker->numberBetween(1, 50),
        'rating' => $faker->numberBetween(1, 3),
        'is_covered_by_insurance' => $faker->boolean(),
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
