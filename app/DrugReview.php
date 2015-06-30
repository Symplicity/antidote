<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DrugReview extends Model
{
    protected $table = 'drug_reviews';

    protected $hidden = ['updated_at', 'pivot', 'drug_id', 'user_id'];

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function drug()
    {
        return $this->belongsTo('App\Drug');
    }

    public function rating()
    {
        return $this->belongsTo('App\DrugRating', 'rating');
    }

    public function sideEffects()
    {
        return $this->belongsToMany('App\DrugSideEffect');
    }
}
