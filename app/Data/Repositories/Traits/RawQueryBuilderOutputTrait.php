<?php

namespace App\Data\Repositories\Traits;


trait RawQueryBuilderOutputTrait
{
    protected $outputRaw = false;

    /**
     * @param $e
     *
     * @return bool
     */
    public function setEnableRawOutput($e) {
        $this->outputRaw = $e;
        return $this->outputRaw;
    }

    /**
     * @return bool
     */
    public function getRawOutputEnabled()
    {
        return $this->outputRaw;
    }
}