<?php

namespace App\Http\Controllers;

use App\Data\Models\DowntimeReason;
use App\Data\Models\DowntimeReasonGroup;
use App\Data\Repositories\DowntimeReasonRepository;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

class DowntimeReasonGroupController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response()->json(DowntimeReasonGroup::orderBy('name', 'asc')->get(),200);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $downtime_reason_group = new DowntimeReasonGroup();
        $downtime_reason_group['name'] = $request['name'];
        $downtime_reason_group->save();
        return response()->json(DowntimeReasonGroup::orderBy('name', 'asc')->get(),200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id){
        return response()->json(DowntimeReasonGroup::find($id),200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $downtime_reason_group = DowntimeReasonGroup::find($id);
        $downtime_reason_group['name'] = $request['name'];
        $downtime_reason_group->save();
        return response()->json(DowntimeReasonGroup::orderBy('name', 'asc')->get(),200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $downtimeReasonGroup = DowntimeReasonGroup::find($id);

        $checkUsage = DowntimeReason::where('reason_group_id', $downtimeReasonGroup['id'])->get();

        if (count($checkUsage) > 0) {
            // throw error on group_id found on reason table
            return response()->json(DowntimeReasonGroup::all(),200);
        }
        else {
            $downtimeReasonGroup->delete();
            return response()->json(DowntimeReasonGroup::all(),200);
        }
    }
}
