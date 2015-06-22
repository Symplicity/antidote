<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Drug extends Model
{
    protected $table = 'drugs';

    public function reviews()
    {
        return $this->hasMany('App\DrugReview');
    }

    public function sideEffects()
    {
        return $this->belongsToMany('App\DrugSideEffect');
    }
}
