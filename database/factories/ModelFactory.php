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
        'name' => $faker->name,
        'email' => $faker->email,
        'password' => Hash::make('123'),
        'age' => $faker->numberBetween(1, 85),
        'gender' => $faker->randomElement(['m', 'f'])
    ];
});

$factory->define(App\Drug::class, function (Faker\Generator $faker) {
    return [
        'ndc' => $faker->ean8(),
        'label' => ucfirst($faker->word()),
        'description' => $faker->text(250),
        'rxcui' => $faker->ean8(),
        'generic' => ucfirst($faker->word()),
        'drug_forms' => [ucfirst($faker->word()), ucfirst($faker->word())]
    ];
});

$factory->define(App\DrugReview::class, function (Faker\Generator $faker) {
    return [
        'user_id' => $faker->numberBetween(1, 50),
        'drug_id' => $faker->numberBetween(1, 50),
        'rating' => $faker->numberBetween(1, 3),
        'is_covered_by_insurance' => $faker->boolean(),
        'comment' => $faker->text(250)
    ];
});
