<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DrugRating extends Model
{
    protected $table = 'drug_ratings';

    public $timestamps = false;

    public function reviews()
    {
        return $this->hasMany('App\DrugReview', 'rating');
    }
}
