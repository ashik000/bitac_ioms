<?php

namespace App\Data\Models;

use Carbon\Traits\Timestamp;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MachineStatus extends Model
{
    //
    use SoftDeletes;
    use Timestamp;
    protected $table = 'machine_status';
}
