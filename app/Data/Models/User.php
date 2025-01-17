<?php

namespace App\Data\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

/**
 * App\Data\Models\User
 *
 * @property int $id
 * @property string $name
 * @property string $email
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $notifications
 * @property-read int|null $notifications_count
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Data\Models\User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Data\Models\User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Data\Models\User query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Data\Models\User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Data\Models\User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Data\Models\User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Data\Models\User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Data\Models\User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Data\Models\User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Data\Models\User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Data\Models\User whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'remember_token'
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

    public function roles() {
        return $this->belongsToMany(Role::class, 'user_permissions');
    }
}
