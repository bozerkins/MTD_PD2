<?php
/**
 * Created by PhpStorm.
 * User: bogdans
 * Date: 17.20.1
 * Time: 01:01
 */

namespace App;


class Penalty
{
    /** @var  string */
    private $time;
    /** @var  PLayer */
    private $player;

    /**
     * @return string
     */
    public function getTime()
    {
        return $this->time;
    }

    /**
     * @param string $time
     */
    public function setTime($time)
    {
        $this->time = $time;
    }

    /**
     * @return PLayer
     */
    public function getPlayer()
    {
        return $this->player;
    }

    /**
     * @param PLayer $player
     */
    public function setPlayer($player)
    {
        $this->player = $player;
    }
}