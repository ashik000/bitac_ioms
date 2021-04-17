<?php

namespace App\Data\Repositories\Traits;

trait PaginatedOutputTrait
{
    /**
     * @var int Number of Results to show per page
     */
    private $resultsPerPage = 30;

    /**
     * @var bool Enable/Disable paginated result
     */
    private $enablePagination = true;

    /**
     * @return int
     */
    public function getResultsPerPage()
    {
        return $this->resultsPerPage;
    }

    /**
     * @param int $resultsPerPage
     *
     * @return PaginatedOutputTrait
     */
    public function setResultsPerPage($resultsPerPage)
    {
        $this->resultsPerPage = $resultsPerPage;

        return $this;
    }

    /**
     * @return boolean
     */
    public function isEnablePagination()
    {
        return $this->enablePagination;
    }

    /**
     * @param boolean $enablePagination
     *
     * @return PaginatedOutputTrait
     */
    public function setEnablePagination($enablePagination)
    {
        $this->enablePagination = $enablePagination;

        return $this;
    }
}