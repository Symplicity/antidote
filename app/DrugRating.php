<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DrugRating extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'drug_ratings';

    public $timestamps = false;
}
