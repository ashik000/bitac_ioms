<?php


namespace App\Data\Repositories;

use App\Data\Models\Operator;
use App\Data\Repositories\Interfaces\PaginatedResultInterface;
use App\Data\Repositories\Interfaces\RawQueryBuilderOutputInterface;
use App\Data\Repositories\Traits\PaginatedOutputTrait;
use App\Data\Repositories\Traits\ProcessOutputTrait;
use App\Data\Repositories\Traits\RawQueryBuilderOutputTrait;

class OperatorRepository implements PaginatedResultInterface, RawQueryBuilderOutputInterface
{
    use ProcessOutputTrait, PaginatedOutputTrait, RawQueryBuilderOutputTrait;

    public function fetchAllOperator($orderBy) {
        return Operator::orderBy('first_name', $orderBy)->get();
    }

    public function storeOperator($request) {
        $operator = new Operator();
        $operator['first_name'] = $request['first_name'];
        $operator['last_name'] = $request['last_name'];
        $operator['code'] = $request['code'];
        $check = $operator->save();
        if ($check) {
            return true;
        }
        return false;
    }

    public function updateOperator($request, $id) {
        $operator = Operator::find($id);
        $operator['first_name'] = $request['first_name'];
        $operator['last_name'] = $request['last_name'];
        $operator['code'] = $request['code'];
        $check = $operator->save();
        if ($check) {
            return true;
        }
        return false;
    }

    public function deleteOperator($id) {
        $operator = Operator::find($id);
        $check = $operator->delete();
        if ($check) {
            return true;
        }
        return false;
    }

}
