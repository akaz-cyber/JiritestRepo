<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmail{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password','role','photo','status','provider','provider_id',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function orders(){
        return $this->hasMany('App\Models\Order');
    }

    public function addresses() {
    return $this->hasMany(\App\Models\Address::class);
    }

    public function defaultAddress() {
        return $this->hasOne(\App\Models\Address::class)->where('is_default', true);
        }

    public function hasVerifiedEmail()
    {
        $fresh = $this->fresh();

        if (in_array($fresh->role, ['admin', 'super_admin'])) {
            return true;
        }

        return !is_null($fresh->email_verified_at);
    }
}
