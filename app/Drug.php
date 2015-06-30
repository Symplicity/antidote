<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Drug extends Model
{
    protected $table = 'drugs';

    /**
     * Any attributes listed in the $appends property will automatically
     * be included in the array or JSON form of the model,
     * provided that you've added the appropriate accessor
     *
     * @var array
     */
    protected $appends = [
        'effectiveness_percentage',
        'insurance_coverage_percentage',
        'total_reviews'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = ['created_at', 'updated_at', 'pivot', 'rxcui'];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'drug_forms' => 'array',
        'recalls' => 'array'
    ];

    public function generic()
    {
        return $this->hasOne('App\Drug', 'generic_id');
    }

    public function alternatives()
    {
        return $this->belongsToMany('App\Drug', 'drug_alternatives', 'drug_id', 'alternative_id');
    }

    public function related()
    {
        return $this->belongsToMany('App\Drug', 'drug_related', 'drug_id', 'related_id');
    }

    public function indications()
    {
        return $this->belongsToMany('App\DrugIndication');
    }

    public function sideEffects()
    {
        return $this->belongsToMany('App\DrugSideEffect');
    }

    public function prescriptionTypes()
    {
        return $this->belongsToMany('App\DrugPrescriptionType');
    }

    public function reviews()
    {
        return $this->hasMany('App\DrugReview');
    }

    public function getEffectivenessPercentageAttribute()
    {
        $effectiveness = 0;

        $reviews = $this->getTotalReviewsAttribute();
        if (!empty($reviews)) {
            $effective = $this->reviews()->where('rating', '3')->get()->count();
            $efectiveness = round($effective * 100 / $reviews);
        }

        return $effectiveness;
    }

    public function getInsuranceCoveragePercentageAttribute()
    {
        $coverage = 0;

        $reviews = $this->getTotalReviewsAttribute();
        if (!empty($reviews)) {
            $covered = $this->reviews()->where('is_covered_by_insurance', '1')->get()->count();
            $coverage = round($covered * 100 / $reviews);
        }

        return $coverage;
    }

    public function getTotalReviewsAttribute()
    {
        return $this->reviews()->get()->count();
    }
}
