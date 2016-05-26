<?php
namespace AppBundle\PseudoEntity;

class WeeklyActivity
{
    private $speed;
    private $week;
    private $year;

    /**
     * @param mixed $speed
     */
    public function setSpeed($speed)
    {
        $this->speed = $speed;
    }

    /**
     * @param mixed $week
     */
    public function setWeek($week)
    {
        $this->week = $week;
    }

    /**
     * @param mixed $year
     */
    public function setYear($year)
    {
        $this->year = $year;
    }

}