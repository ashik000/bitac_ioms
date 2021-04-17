<?php


namespace App\Devices;

use App\Data\Models\Packet;
use Carbon\Carbon;
use Exception;


class ParsedProductionLogPacket
{
    /**
     * @var int
     */
    protected $deviceIdentifier;

    /**
     * @var int
     */
    protected $totalCount;

    /**
     * @var Carbon
     */
    protected $timestamp;

    protected $productionLogs;

    protected $downTimes;

    protected $cycleTime;

    protected $slowProductions;

    protected $parseErrors;

    protected $hasParseErrors = false;

    /**
     * @var Exception $exception
     */
    protected $exception;

    /**
     * @var Packet
     */
    protected $packet;

    public function __construct(int $deviceIdentifier, Carbon $timestamp, int $totalCount)
    {
        $this->deviceIdentifier = $deviceIdentifier;
        $this->timestamp        = $timestamp;
        $this->totalCount       = $totalCount;
    }

    public function setPayload(array $productionLogs, array $downTimes, array $slowProduction, array $parseErrors, Exception $exception = null)
    {
        $this->productionLogs  = $productionLogs;
        $this->downTimes       = $downTimes;
        $this->slowProductions = $slowProduction;
        $this->parseErrors     = $parseErrors;

        if (!empty($this->parseErrors)) {
            $this->hasParseErrors = true;
        }

        $this->exception = $exception;
    }

    /**
     * @return int
     */
    public function getDeviceIdentifier(): int
    {
        return $this->deviceIdentifier;
    }

    /**
     * @return int
     */
    public function getTotalCount(): int
    {
        return $this->totalCount;
    }

    /**
     * @return Carbon
     */
    public function getTimestamp(): Carbon
    {
        return $this->timestamp;
    }

    public function setException(Exception $exception)
    {
        $this->exception = $exception;
    }

    public function getExceptionStackTrace() {
        if ($this->exception) {
            return $this->exception->getTrace();
        }
        return 'Success';
    }


    /**
     * @throws Exception
     */
    public function throwIfException()
    {
        if ($this->exception) {
            throw $this->exception;
        }
    }

    public function getCycleTime()
    {
        return $this->cycleTime;
    }

    public function setCycleTime(int $cycleTime)
    {
        $this->cycleTime = $cycleTime;
    }
}
