<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DrugSideEffect extends Model
{
    protected $table = 'drug_side_effects';

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = ['created_at', 'updated_at', 'pivot'];

    protected $fillable = ['value'];

    public function drugs()
    {
        return $this->belongsToMany('App\Drug');
    }

    public function drugReviews()
    {
        return $this->belongsToMany('App\DrugReview');
    }
}
