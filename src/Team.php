<?php

namespace App;

class Team
{
    /** @var  String */
    private $name;
    /** @var  Player[] */
    private $players = [];
    /**
     * @return String
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param String $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    public function addPlayer(Player $player)
    {
        $this->players[$player->getNumber()] = $player;
    }

    public function hasPlayer(Player $player)
    {
        return array_key_exists($player->getNumber(), $this->players);
    }

    public function getPlayer($number)
    {
        return $this->players[$number];
    }

    public function getPlayers()
    {
        return $this->players;
    }

}