<?php
/**
 * Created by PhpStorm.
 * User: bogdans
 * Date: 17.20.1
 * Time: 00:50
 */

namespace App;


class Goal
{
    /** @var  string */
    private $time;
    /** @var  Player */
    private $player;
    /** @var  string */
    private $type;
    /** @var Player */
    private $supports = [];

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
     * @return Player
     */
    public function getPlayer()
    {
        return $this->player;
    }

    /**
     * @param Player $player
     */
    public function setPlayer($player)
    {
        $this->player = $player;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @param Player $support
     */
    public function addSupport($support)
    {
        $this->supports[] = $support;
    }

    public function getSupports()
    {
        return $this->supports;
    }
}