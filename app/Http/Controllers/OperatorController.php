<?php

namespace App\Http\Controllers;

use App\Data\Models\Operator;
use App\Http\Requests\OperatorCreateRequest;
use App\Http\Resources\OperatorCollection;
use App\Http\Resources\OperatorResource;
use Illuminate\Http\Request;
use DB;

class OperatorController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return new OperatorCollection(Operator::all()->load('stations'));
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
    public function store(OperatorCreateRequest $request)
    {
        //
        $operator = new Operator();
        $operator->first_name = $request['first_name'];
        $operator->last_name = $request['last_name'];
        $operator->code = $request['code'];
        $operator->save();
        return new OperatorCollection(Operator::all());
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
        return new OperatorResource(Operator::find($id));
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
    public function update(OperatorCreateRequest $request, $id)
    {
        //
        $operator = Operator::find($id);
        $operator->first_name = $request['first_name'];
        $operator->last_name = $request['last_name'];
        $operator->code = $request['code'];
        $operator->save();
        return new OperatorCollection(Operator::all());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
        $operator = Operator::find($id);
        $operator->delete();
        return new OperatorCollection(Operator::all());
    }

}
