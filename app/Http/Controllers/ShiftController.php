<?php

namespace App\Http\Controllers;

use App\Data\Models\Shift;
use App\Http\Requests\ShiftCreateRequest;
use App\Http\Resources\ShiftCollection;
use App\Http\Resources\ShiftResource;
use Illuminate\Http\Request;

class ShiftController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return ShiftCollection
     */
    public function index(Request $request){
        return new ShiftCollection(Shift::all());
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(){

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param ShiftCreateRequest $request
     * @return ShiftCollection
     */
    public function store(ShiftCreateRequest $request)
    {
        $shift = new Shift();
        $shift-> name = $request['name'];
        $shift-> start_time = $request['start_time'];
        $shift-> end_time = $request['end_time'];
        $shift->save();
        return new ShiftCollection(Shift::all());
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return ShiftResource
     */
    public function show($id){
        return new ShiftResource(Shift::find($id));
    }

//    /**
//     * Show the form for editing the specified resource.
//     *
//     * @param  int  $id
//     */
//    public function edit($id){
//
//    }

    /**
     * Update the specified resource in storage.
     *
     * @param ShiftCreateRequest $request
     * @param  int $id
     * @return ShiftCollection
     */
    public function update(ShiftCreateRequest $request, $id){
        $shift = Shift::find($id);
        $shift-> name = $request['name'];
        $shift-> start_time = $request['start_time'];
        $shift-> end_time = $request['end_time'];
        $shift->save();
        return new ShiftCollection(Shift::all());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return ShiftCollection
     */
    public function destroy($id){
        $shift = Shift::find($id);
        $shift->delete();
        return new ShiftCollection(Shift::all());
    }
}
