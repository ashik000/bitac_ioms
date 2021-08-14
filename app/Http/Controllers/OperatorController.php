<?php

namespace App\Http\Controllers;

use App\Data\Models\Operator;
use App\Data\Repositories\OperatorRepository;
use App\Http\Requests\OperatorCreateRequest;
use App\Http\Resources\OperatorCollection;
use App\Http\Resources\OperatorResource;
use Illuminate\Http\Request;
use DB;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

class OperatorController extends Controller
{
    /**
     * @var OperatorRepository
     */
    private $operatorRepository;

    public function __construct(OperatorRepository $operatorRepository)
    {
        $this->operatorRepository = $operatorRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $operators = $this->operatorRepository->fetchAllOperator('asc');
        return new OperatorCollection($operators);
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
        $checkStore = $this->operatorRepository->storeOperator($request);
        if(!$checkStore) throw new BadRequestException();
        $operators = $this->operatorRepository->fetchAllOperator('asc');
        return new OperatorCollection($operators);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
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
        $checkUpdate = $this->operatorRepository->updateOperator($request, $id);
        if(!$checkUpdate) throw new BadRequestException();
        $operators = $this->operatorRepository->fetchAllOperator('asc');
        return new OperatorCollection($operators);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $checkDelete = $this->operatorRepository->deleteOperator($id);
        if(!$checkDelete) throw new BadRequestException();
        $operators = $this->operatorRepository->fetchAllOperator('asc');
        return new OperatorCollection($operators);
    }

}
