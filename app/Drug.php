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
        'prescription_type',
        'recalls',
        'description'
    ];

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
    protected $hidden = ['created_at', 'updated_at', 'rxcui', 'pivot'];

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

    public function indications()
    {
        return $this->belongsToMany('App\DrugIndication');
    }

    public function alternatives()
    {
        return $this->belongsToMany('App\Drug', 'drug_alternative_drugs', 'drug_id', 'alternative_drug_id');
    }

    public function related()
    {
        return $this->belongsToMany('App\Drug', 'drug_related_drugs', 'drug_id', 'related_drug_id');
    }

    public function getEffectivenessPercentageAttribute()
    {
        $reviews = $this->getTotalReviewsAttribute();
        if (!empty($reviews)) {
            return round(count($this->reviews()->where('rating', '3')->get()) / $this->getTotalReviewsAttribute(), 2);
        }
        return 0;
    }

    public function getInsuranceCoveragePercentageAttribute()
    {
        $reviews = $this->getTotalReviewsAttribute();
        if (!empty($reviews)) {
            return round(count($this->reviews()->where('is_covered_by_insurance', '1')->get()) / $this->getTotalReviewsAttribute(), 2);
        }
        return 0;
    }

    public function getTotalReviewsAttribute()
    {
        return count($this->reviews()->get());
    }
}
