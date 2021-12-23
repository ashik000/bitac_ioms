<?php

namespace App\Data\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Team extends Model
{
    use SoftDeletes;
    protected $fillable = ['name'];

    public function stations()
    {
        return $this->belongsToMany(Station::class, 'station_team')
            ->as('meta')
            ->whereNull('station_team.deleted_at')
            ->whereNull('station_team.end_time')
            ->withPivot([
                'id',
                'start_time',
                'end_time'
            ]);
    }
}
