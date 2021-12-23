<?php

namespace App\Data\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StationTeam extends Model
{
    use SoftDeletes;

    public function team() {
        return $this->belongsTo(Team::Class);
    }

    public function station() {
        return $this->belongsTo(Station::Class);
    }

    public function scopeActive($query)
    {
        return $query->whereNull('end_time');
    }
}
