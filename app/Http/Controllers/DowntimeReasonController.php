<?php

namespace App\Http\Controllers;

use App\Data\Models\Downtime;
use App\Data\Models\DowntimeReason;
use App\Http\Requests\DowntimeReasonCreateRequest;
use App\Http\Resources\DowntimeLineData;
use App\Http\Resources\DowntimeReasonCollection;
use App\Http\Resources\DowntimeReasonResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DowntimeReasonController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return DowntimeReasonCollection
     */
    public function index()
    {
        $downtimeReasons = DowntimeReason::all();
        $downtimeReasons->load('downtimeReasonGroup');
        return new DowntimeReasonCollection($downtimeReasons);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return void
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(DowntimeReasonCreateRequest $request)
    {
        $downtime_reason                    = new DowntimeReason();
        $downtime_reason['reason_group_id'] = $request['reason_group_id'];
        $downtime_reason['name']            = $request['name'];
        $downtime_reason['type']            = $request['type'];
        $downtime_reason->save();
        return new DowntimeReasonCollection(DowntimeReason::all());
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return new DowntimeReasonResource(DowntimeReason::find($id));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return void
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return DowntimeReasonCollection
     */
    public function update(Request $request, $id)
    {
        $downtime_reason                    = DowntimeReason::find($id);
        $downtime_reason['reason_group_id'] = $request['reason_group_id'];
        $downtime_reason['name']            = $request['name'];
        $downtime_reason['type']            = $request['type'];
        $downtime_reason->save();
        return new DowntimeReasonCollection(DowntimeReason::all());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return DowntimeReasonCollection
     */
    public function destroy($id)
    {
        $downtime_reason = DowntimeReason::find($id);
        $downtime_reason->delete();
        return new DowntimeReasonCollection(DowntimeReason::all());
    }

    public function assignDowntimeReason(Request $request) {
        $downtime = Downtime::find($request->input('downtimeId'));

        if (empty($downtime)) {
            return response()->json(["code" => 404], 404);
        }

        $downtime->reason_id = $request->input('downtimeReasonId');
        $downtime->save();

        return new DowntimeLineData($downtime);
    }

    public function removeDowntimeReason(Request $request) {
        $downtime = Downtime::find($request->input('downtimeId'));

        if (empty($downtime)) {
            return response()->json(["code" => 404], 404);
        }

        $downtime->reason_id = null;
        $downtime->save();

        return new DowntimeLineData($downtime);
    }
}
