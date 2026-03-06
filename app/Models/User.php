<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;
use PDO;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\Casts\Attribute;

class User extends Authenticatable
{
    use HasApiTokens, HasRoles, SoftDeletes;

    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory;
    use HasProfilePhoto;
    use Notifiable;
    use TwoFactorAuthenticatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    // protected $fillable = [
    //     'name',
    //     'email',
    //     'password',
    // ];
    protected $guarded=[];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'profile_photo_url',
        'subscription_start_date', 'subscription_end_date'
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function setPasswordAttribute($value){
           $this->attributes['password']=Hash::make($value);
    }

    public function subscriptions(){
        return $this->hasMany(Subscription::class,'user_id');
    }
    
    public function latestSubscription()
   {
     return $this->hasOne(Subscription::class, 'user_id')->latestOfMany();
   }

// Custom accessors
  public function getSubscriptionStartDateAttribute()
  {
    return $this->latestSubscription?->start_date;
  }

  public function getSubscriptionEndDateAttribute()
  {
    return $this->latestSubscription?->end_date;
  }
  
//   protected function occupationFlag(): Attribute
//   {
//         return Attribute::get(function ($value) {
//               if (is_null($value)) {
//                 return null;
//             }

//             return $value == 0 ? 'farmer' : 'seeder';
//         });
//   }
  
//   protected $appends = ['subscription_start_date', 'subscription_end_date'];
//   protected $hidden = ['latest_subscription'];
   protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
        'latestSubscription'
    ];
}
