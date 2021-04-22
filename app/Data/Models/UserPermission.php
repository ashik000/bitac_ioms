<?php


namespace App\Data\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserPermission extends Model
{

    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'role_id'
    ];
}
