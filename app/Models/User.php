<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'name',
        'username',
        'age',
        'email',
        'password',
        'mobile_number',
        'provider_id',
        'code_expiry',
        'verification_code',
        'phone_number',
        'device_type',
        'device_token',
        'fcm_token',
        'image',
        'country',
        'state',
        'city',
        'user',
        'zip_code',
        'trial_end',
        'trial_start',
        'country_code',
        'account_id',
        'verify_status'

    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }
    public function getJWTCustomClaims()
    {
        return [];
    }
    public function bankDetails()
    {
        return $this->hasOne(BankDetails::class);
    }
    public function follow(){
        return $this->hasone(Follow::class,'trainer_id');
    }
    public function buildmyworkout(){
        return $this->hasMany(BuildMyWorkoutModel::class);
    }
    public function shareworkout(){
        return $this->hasMany(ShareWorkout::class,'client_id');
    }
    public function note(){
        return $this->hasMany(Note::class);
    }
    public function share_note(){

        return $this->hasMany(ShareNote::class,'client_id');

    }
    public function parentnote(){
        return $this->hasMany(ParentNote::class);
    }
    public function userprefrence(){
        return $this->hasMany(UserPrefrence::class)->select('id','user_id','type_specialties_id');;
    }
    public function newsfeed(){
        return $this->hasMany(NewsFeed::class);
    }
}
