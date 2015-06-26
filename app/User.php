<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;
use DateTime;
use DateInterval;

class User extends Model
{
    protected $table = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['username', 'email', 'password'];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = ['email', 'password', 'reset_password_token', 'reset_password_token_expiration'];

    public function reviews()
    {
        return $this->hasMany('App\DrugReview');
    }

    public function setEmailAttribute($value)
    {
        $this->attributes['email'] = Crypt::encrypt($value);
    }

    public function getEmailAttribute($value)
    {
        return Crypt::decrypt($value);
    }

    public function setAgeAttribute($value)
    {
        $now = new DateTime();
        $rough_birth_date = $now->sub(new DateInterval("P" . $value . "Y"));
        $this->attributes['age'] = $rough_birth_date;
    }

    public function getAgeAttribute($value)
    {
        if (is_scalar($value)) {
            $value = new DateTime($value);
        }
        $age = $value->diff(new DateTime);
        return $age->y;
    }
}
