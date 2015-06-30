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

    protected static function boot()
    {
        parent::boot();
        static::flushModelEvents();
    }

    /** workaround for this issue with unit testing model events:  https://github.com/laravel/framework/issues/1181 */
    public static function flushModelEvents()
    {
        static::created(function (DrugReviewVote $drug_review_vote) {
            //add to votes cache on drug review model
            if ($drug_review_vote->vote == 1) {
                DrugReview::find($drug_review_vote->drug_review_id)->increment('upvotes_cache');
            } else {
                DrugReview::find($drug_review_vote->drug_review_id)->increment('downvotes_cache');
            }
        });

        static::updated(function (DrugReviewVote $drug_review_vote) {
            //update votes cache on drug review model
            if ($drug_review_vote->vote == 1) {
                $drug_review = DrugReview::find($drug_review_vote->drug_review_id);
                $drug_review->increment('upvotes_cache');
                $drug_review->decrement('downvotes_cache');
            } else {
                $drug_review = DrugReview::find($drug_review_vote->drug_review_id);
                $drug_review->increment('downvotes_cache');
                $drug_review->decrement('upvotes_cache');
            }
        });
    }
}
