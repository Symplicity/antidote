<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DrugIndication extends Model
{
    protected $table = 'drug_indications';

    protected $hidden = ['pivot'];

    public $timestamps = false;

    protected $fillable = [
        'value',
    ];

    public function drugs()
    {
        return $this->belongsToMany('App\Drug');
    }
}
