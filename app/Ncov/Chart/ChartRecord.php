<?php

namespace App\Ncov\Chart;

use Carbon\CarbonInterface;

class ChartRecord
{
    /**
     * @var mixed
     */
    private $value;

    /**
     * @var CarbonInterface
     */
    private $date;

    /**
     * ChartRecord constructor.
     *
     * @param $value
     * @param CarbonInterface $date
     */
    public function __construct($value, CarbonInterface $date)
    {
        $this->value = $value;
        $this->date = $date;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param mixed $value
     */
    public function setValue($value): void
    {
        $this->value = $value;
    }

    /**
     * @return CarbonInterface
     */
    public function getDate(): CarbonInterface
    {
        return $this->date;
    }

    /**
     * @param CarbonInterface $date
     */
    public function setDate(CarbonInterface $date): void
    {
        $this->date = $date;
    }

}
