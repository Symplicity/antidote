<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Drug extends Model
{
    protected $table = 'drugs';

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = ['created_at', 'updated_at'];

    public function reviews()
    {
        return $this->hasMany('App\DrugReview');
    }

    public function sideEffects()
    {
        return $this->belongsToMany('App\DrugSideEffect');
    }
}
