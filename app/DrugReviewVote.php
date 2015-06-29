<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DrugReviewVote extends Model
{
    protected $table = 'drug_review_votes';

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function drugReview()
    {
        return $this->belongsTo('App\DrugReview');
    }

    public function setVoteAttribute($value)
    {
        //restrict values to only these two
        $this->attributes['vote'] = ($value == -1) ? -1 : 1;
    }
}
