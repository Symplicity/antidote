<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DrugSideEffect extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'drug_side_effects';

    public $timestamps = false;

    public function drugs()
    {
        return $this->belongsToMany('App\Drug');
    }
}
