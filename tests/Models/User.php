<?php

namespace Padosoft\Laravel\ActivitylogExtended\Test\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\CausesActivity;
use Illuminate\Contracts\Auth\Authenticatable;

class User extends Model implements Authenticatable
{
    use CausesActivity;

    protected $table = 'users';

    protected $guarded = [];

    protected $fillable = ['id', 'name'];

    /**
     * Get the name of the unique identifier for the user.
     *
     * @return string
     */
    public function getAuthIdentifierName()
    {
        return 'id';
    }

    /**
     * Get the unique identifier for the user.
     *
     * @return mixed
     */
    public function getAuthIdentifier()
    {
        $name = $this->getAuthIdentifierName();

        return $this->attributes[$name];
    }

    /**
     * Get the password for the user.
     *
     * @return string
     */
    public function getAuthPassword()
    {
        return $this->attributes['password'];
    }

    /**
     * Get the token value for the "remember me" session.
     *
     * @return string
     */
    public function getRememberToken()
    {
        return 'token';
    }

    /**
     * Set the token value for the "remember me" session.
     *
     * @param string $value
     */
    public function setRememberToken($value)
    {
    }

    /**
     * Get the column name for the "remember me" token.
     *
     * @return string
     */
    public function getRememberTokenName()
    {
        return 'tokenName';
    }

    public function articles()
    {
        return $this->hasMany(Article::class);
    }

    public function latestArticle()
    {
        return $this->hasOne(Article::class)->latest()->limit(1);
    }
}
