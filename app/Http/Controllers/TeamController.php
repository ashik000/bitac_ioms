<?php

namespace App\Http\Controllers;

use App\Data\Models\Team;
use Illuminate\Http\Request;

class TeamController extends Controller
{
    public function index(Request $request){
        return Team::all();
    }
}
