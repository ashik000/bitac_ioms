<?php

namespace App\Http\Controllers;

use App\Data\Models\Operator;
use App\Data\Models\Team;
use DB;
use Illuminate\Http\Request;

class TeamController extends Controller
{
    public function index(Request $request){
        return Team::all();
    }

    public function getOperatorsOfTeam(Request $request){
        $team_id = $request['team_id'];
        return DB::table('team_operator')
            ->join('operators', 'operators.id', '=', 'team_operator.operator_id')
            ->where('team_operator.team_id', '=', $team_id)
            ->get();
    }

    public function store(Request $request){
        return Team::create([
            'name' => $request['name']
        ]);
    }

    public function updateTeamOperators(Request $request){
        DB::table('team_operator')->where('team_id', '=', $request['team_id'])->delete();
        $array = [];
        foreach ($request['operator_ids'] as $operator_id ){
            array_push($array,[
                'team_id' => $request['team_id'],
                'operator_id' => $operator_id
                ]);
        }
        DB::table('team_operator')->insert($array);
        return response()-> json("Success",200);
    }
}
