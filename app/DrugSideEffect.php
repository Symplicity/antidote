<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DrugSideEffect extends Model
{
    protected $table = 'drug_side_effects';

    public $timestamps = false;

    public function drugs()
    {
        return $this->belongsToMany('App\Drug');
    }
}