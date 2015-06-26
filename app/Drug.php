<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Drug extends Model
{
    protected $table = 'drugs';

    protected $fillable = [
        'label',
        'rxcui',
        'generic',
        'drug_forms',
        'generic_id',
        'indications',
        'prescription_type',
        'recalls',
        'description'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = ['created_at', 'updated_at', 'rxcui'];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'drug_forms' => 'array',
        'indications' => 'array',
        'recalls' => 'array'
    ];

    public function reviews()
    {
        return $this->hasMany('App\DrugReview');
    }

    public function sideEffects()
    {
        return $this->belongsToMany('App\DrugSideEffect');
    }

    public function alternatives()
    {
        return $this->belongsToMany('App\Drug', 'drug_alternative_drugs', 'drug_id', 'alternative_drug_id');
    }

    public function related()
    {
        return $this->belongsToMany('App\Drug', 'drug_related_drugs', 'drug_id', 'related_drug_id');
    }
}
