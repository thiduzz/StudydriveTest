<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Thomaswelton\LaravelGravatar\Facades\Gravatar;

/**
 * This model is the basis of the whole app - the user is the representation of one row in the users table
 *
 * Class User
 * @package App
 * @author  Thiago Mello
 * @see     \Illuminate\Foundation\Auth\User
 * @since   1.0
 */
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

    /**
     * Override the avatar_url value to format it to the file repository url format
     * @return array
     */
    public function toArray()
    {
        $array = parent::toArray();
        $array['avatar_url'] = $this->getAvatarLinkAttribute();
        return $array;
    }

    /**
     * Format the avatar_url parameter
     * @return string
     */
    public function getAvatarLinkAttribute()
    {
        if($this->avatar_url != null)
        {
            return 'https://s3-'.env('S3_REGION').'.amazonaws.com/'.env('S3_BUCKET').'/'.$this->avatar_url;
        }
        return '';
    }

    public function activate()
    {
        $this->active = 1;
        $this->save();
        return true;
    }
}
