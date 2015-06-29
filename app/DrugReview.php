<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DrugReview extends Model
{
    protected $table = 'drug_reviews';

    protected $hidden = [
        'updated_at',
        'drug_id',
        'user_id'
    ];

    protected $appends = [
        'upvotes',
        'downvotes'
    ];

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function drug()
    {
        return $this->belongsTo('App\Drug');
    }

    public function votes()
    {
        return $this->hasMany('App\DrugReviewVote');
    }

    public function sideEffects()
    {
        return $this->belongsToMany('App\DrugSideEffect');
    }

    public function getUpvotesAttribute()
    {
        return $this->votes()->where('vote', 1)->get()->count();
    }

    public function getDownvotesAttribute()
    {
        return $this->votes()->where('vote', -1)->get()->count();
    }
}
