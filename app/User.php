<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class User extends Model
{
    protected $table = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'email', 'password'];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = ['password', 'reset_password_token', 'reset_password_token_expiration', 'remember_token'];

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
}
