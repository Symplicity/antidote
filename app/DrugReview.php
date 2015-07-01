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
        'user_id',
        'upvotes_cache',
        'downvotes_cache'
    ];

    protected $appends = [
        'upvotes',
        'downvotes'
    ];

    protected $casts = [
        'upvotes_cache' => 'int',
        'downvotes_cache' => 'int'
    ];

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
        if (isset($this->attributes['upvotes_cache'])) {
            return $this->attributes['upvotes_cache'];
        }
        return 0;
    }

    public function getDownvotesAttribute()
    {
        if (isset($this->attributes['downvotes_cache'])) {
            return $this->attributes['downvotes_cache'];
        }
        return 0;
    }

    /** override this to get iso 8601 formatted string for output - move to common if required in other areas */
    public function getCreatedAtAttribute($value)
    {
        return Carbon::parse($value)->toIso8601String();
    }
}
