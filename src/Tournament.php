<?php
/**
 * Created by PhpStorm.
 * User: bogdans
 * Date: 17.20.1
 * Time: 01:22
 */

namespace App;

class Tournament
{
    /** @var  Play[] */
    private $plays;
    /** @var Team[] */
    private $teams;

    public function __construct(array $plays, array $teams)
    {
        $this->plays = $plays;
        $this->teams = array_values($teams);
    }

    /**
     * @return string[]
     */
    public function getTeamsNames()
    {
        return array_map(function(Team $team) {
            return $team->getName();
        }, $this->teams);
    }

    /**
     * @return Player[]
     */
    public function getPlayers()
    {
        return array_reduce($this->teams, function($result, Team $team) {
            return array_merge($result, $team->getPlayers());
        }, []);
    }

    /**
     * @return Judge[]
     */
    public function getJudges()
    {
        $judgesMix = array_reduce($this->plays, function($result, Play $play) {
            return array_merge($result, $play->getAdditionalJudges(), [$play->getMainJudge()]);
        }, []);

        $judges = [];
        foreach($judgesMix as $judge) {
            /** @var Judge $judge */
            $key = $judge->getName() . ' ' . $judge->getSurname();
            if (!array_key_exists($key, $judges)) {
                $judges[$key] = $judge;
            }
        }
        return array_values($judges);
    }

    public function getJudgePenalties(Judge $judge)
    {
        $plays = array_filter($this->plays, function(Play $play) use ($judge) {
            return $play->hasJudge($judge);
        });
        $penalties = 0;
        foreach($plays as $play) {
            $penalties += $play->getJudgePenalties($judge);
        }
        return $penalties;
    }

    public function getPlayerPenalties(Player $player)
    {
        $penalties = 0;
        $plays = array_filter($this->plays, function(Play $play) use ($player) {
            return $play->hasPlayer($player);
        });
        foreach($plays as $play) {
            $penalties += $play->getPlayerPenalties($player);
        }
        return $penalties;
    }

    /**
     * @return Player[]
     */
    public function getPlayersDefenders()
    {
        return array_filter($this->getPlayers(), function(Player $player) {
            return $player->getRole() === 'V';
        });
    }

    public function getDefenderGoalsLostPerPlay(Player $player)
    {
        $plays = array_filter($this->plays, function(Play $play) use ($player) {
            return $play->hasPlayer($player);
        });

        $goalsCnt = 0;
        $playsCnt = 0;
        foreach($plays as $play) {
            $goalsLost = $play->getDefenderGoalsLost($player);
            if ($goalsLost) {
                $goalsCnt += $goalsLost;
                $playsCnt++;
            }
        }
        return $playsCnt ? $goalsCnt / $playsCnt : 0;
    }

    /**
     * @param Player $player
     * @return int
     */
    public function getPlayerGoals(Player $player)
    {
        $plays = array_filter($this->plays, function(Play $play) use ($player) {
            return $play->hasPlayer($player);
        });
        $goals = 0;
        foreach($plays as $play) {
            $goals += $play->getPlayerGoals($player);
        }
        return $goals;
    }

    /**
     * @param Player $player
     * @return int
     */
    public function getPlayerGoalSupports(Player $player)
    {
        $plays = array_filter($this->plays, function(Play $play) use ($player) {
            return $play->hasPlayer($player);
        });
        $goals = 0;
        foreach($plays as $play) {
            $goals += $play->getPlayerGoalSupports($player);
        }
        return $goals;
    }

    /**
     * @param String $team
     * @return int
     */
    public function getTeamPoints($team)
    {
        $plays = array_filter($this->plays, function(Play $play) use ($team) {
            return $play->hasTeam($team);
        });
        $points = 0;
        foreach($plays as $play) {
            $points += $play->getTeamPoints($team);
        }
        return $points;
    }

    public function getPlaysAmount($team)
    {
        $plays = array_filter($this->plays, function(Play $play) use ($team) {
            return $play->hasTeam($team);
        });

        return count($plays);
    }

    public function getWins($team)
    {
        $plays = array_filter($this->plays, function(Play $play) use ($team) {
            return $play->hasTeam($team);
        });
        return array_sum(array_map(function(Play $play) use ($team) {
            return $play->getWonTeam()->getName() === $team && !$play->isOvertime();
        }, $plays));
    }

    public function getWinsOvertime($team)
    {
        $plays = array_filter($this->plays, function(Play $play) use ($team) {
            return $play->hasTeam($team);
        });
        return array_sum(array_map(function(Play $play) use ($team) {
            return $play->getWonTeam()->getName() === $team && $play->isOvertime();
        }, $plays));
    }

    public function getLosses($team)
    {
        $plays = array_filter($this->plays, function(Play $play) use ($team) {
            return $play->hasTeam($team);
        });
        return array_sum(array_map(function(Play $play) use ($team) {
            return $play->getWonTeam()->getName() !== $team && !$play->isOvertime();
        }, $plays));
    }

    public function getLossesOvertime($team)
    {
        $plays = array_filter($this->plays, function(Play $play) use ($team) {
            return $play->hasTeam($team);
        });
        return array_sum(array_map(function(Play $play) use ($team) {
            return $play->getWonTeam()->getName() !== $team && $play->isOvertime();
        }, $plays));
    }

    public function getGoalsWon($team)
    {
        $plays = array_filter($this->plays, function(Play $play) use ($team) {
            return $play->hasTeam($team);
        });
        return array_sum(array_map(function(Play $play) use ($team) {
            return $play->getGoalsWon($team);
        }, $plays));
    }

    public function getGoalsLost($team)
    {
        $plays = array_filter($this->plays, function(Play $play) use ($team) {
            return $play->hasTeam($team);
        });
        return array_sum(array_map(function(Play $play) use ($team) {
            return $play->getGoalsLost($team);
        }, $plays));
    }
}