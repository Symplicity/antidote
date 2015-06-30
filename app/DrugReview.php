<?php

namespace App;

use Carbon\Carbon as Carbon;
use Illuminate\Database\Eloquent\Model;

class DrugReview extends Model
{
    protected $table = 'drug_reviews';

    protected $hidden = [
        'updated_at',
        'pivot',
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

    public function rating()
    {
        return $this->belongsTo('App\DrugRating', 'rating');
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

    /** override this to get iso 8601 formatted string for output - move to common if required in other areas */
    public function getCreatedAtAttribute($value)
    {
        return Carbon::parse($value)->toIso8601String();
    }
}
