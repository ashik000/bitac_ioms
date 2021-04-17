<?php

namespace App\Data\Repositories\Interfaces;


interface RawQueryBuilderOutputInterface
{
    /**
     * @param $e bool
     *
     * @return bool
     */
    public function setEnableRawOutput($e);

    /**
     * @return bool
     */
    public function getRawOutputEnabled();

}