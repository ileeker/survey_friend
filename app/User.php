<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\History;
use App\Best;
use App\Black;
use App\InnovateSub;
use App\OpinionetworkSub;
use App\Remark;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
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

    public function histories()
    {
        return $this->hasMany(History::class);
    }

    public function bests()
    {
        return $this->hasMany(Best::class);
    }

    public function blacks()
    {
        return $this->hasMany(Black::class);
    }

    public function innovate_subs()
    {
        return $this->hasMany(InnovateSub::class);
    }

    public function opinionetwork_subs()
    {
        return $this->hasMany(OpinionetworkSub::class);
    }

    public function remarks()
    {
        return $this->hasMany(Remark::class);
    }

}
