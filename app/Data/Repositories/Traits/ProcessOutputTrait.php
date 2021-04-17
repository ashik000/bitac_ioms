<?php

namespace App\Data\Repositories\Traits;


use App\Data\Repositories\Interfaces\PaginatedResultInterface;
use App\Data\Repositories\Interfaces\RawQueryBuilderOutputInterface;

trait ProcessOutputTrait
{
    /**
     * @param $query
     *
     * @return mixed
     */
    public function output($query)
    {
        if (($this instanceof RawQueryBuilderOutputInterface) && $this->getRawOutputEnabled()) {
            return $query;
        }
        else {
            if (($this instanceof PaginatedResultInterface) && $this->isEnablePagination()) {
                return $query->paginate($this->getResultsPerPage());
            }
            else {
                return $query->get();
            }
        }
    }
}