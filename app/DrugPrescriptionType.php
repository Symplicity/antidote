<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DrugPrescriptionType extends Model
{
    protected $table = 'drug_prescription_types';

    public $timestamps = false;

    public function drugs()
    {
        return $this->belongsToMany('App\Drug');
    }
}
