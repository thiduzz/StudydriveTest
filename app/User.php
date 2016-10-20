<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Thomaswelton\LaravelGravatar\Facades\Gravatar;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password','avatar_url','active','activation_token'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token','activation_token'
    ];

    public function toArray()
    {
        $array = parent::toArray();
        $array['avatar_url'] = $this->getAvatarLinkAttribute();
        return $array;
    }

    public function getAvatarLinkAttribute()
    {
        if($this->avatar_url != null)
        {
            return 'https://s3-'.env('S3_REGION').'.amazonaws.com/'.env('S3_BUCKET').'/'.$this->avatar_url;
        }
        return '';
    }
}
