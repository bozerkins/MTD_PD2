<?php
/**
 * Created by PhpStorm.
 * User: bogdans
 * Date: 17.20.1
 * Time: 01:05
 */

namespace App;


class Change
{
    /** @var  string */
    private $time;
    /** @var  PLayer */
    private $playerFrom;
    /** @var  Player */
    private $playerTo;

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
    public function getPlayerFrom()
    {
        return $this->playerFrom;
    }

    /**
     * @param PLayer $playerFrom
     */
    public function setPlayerFrom($playerFrom)
    {
        $this->playerFrom = $playerFrom;
    }

    /**
     * @return Player
     */
    public function getPlayerTo()
    {
        return $this->playerTo;
    }

    /**
     * @param Player $playerTo
     */
    public function setPlayerTo($playerTo)
    {
        $this->playerTo = $playerTo;
    }
}