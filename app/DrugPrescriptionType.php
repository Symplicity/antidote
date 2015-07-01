<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DrugPrescriptionType extends Model
{
    protected $table = 'drug_prescription_types';

    public $timestamps = false;

    protected $hidden = ['pivot'];

    public function drugs()
    {
        return $this->belongsToMany('App\Drug');
    }
}
