<?php
/**
 * Created by PhpStorm.
 * User: bogdans
 * Date: 17.20.1
 * Time: 00:25
 */

namespace App;


class Play
{
    /** @var  string */
    private $date;
    /** @var  int */
    private $viewers;
    /** @var  string */
    private $place;
    /** @var  Judge */
    private $mainJudge;
    /** @var Judge[] */
    private $additionalJudges = [];

    /** @var  Team */
    private $teamA;
    /** @var  Team */
    private $teamB;

    /** @var  Player[] */
    private $leadsA;
    /** @var  Player[] */
    private $leadsB;

    /** @var Change[] */
    private $changes = [];
    /** @var Goal[] */
    private $goals = [];
    /** @var Penalty[] */
    private $penalties = [];

    /**
     * @return string
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @param string $date
     */
    public function setDate($date)
    {
        $this->date = $date;
    }

    /**
     * @return int
     */
    public function getViewers()
    {
        return $this->viewers;
    }

    /**
     * @param int $viewers
     */
    public function setViewers($viewers)
    {
        $this->viewers = $viewers;
    }

    /**
     * @return string
     */
    public function getPlace()
    {
        return $this->place;
    }

    /**
     * @param string $place
     */
    public function setPlace($place)
    {
        $this->place = $place;
    }

    /**
     * @return Team
     */
    public function getTeamA()
    {
        return $this->teamA;
    }

    /**
     * @param Team $teamA
     */
    public function setTeamA($teamA)
    {
        $this->teamA = $teamA;
    }

    /**
     * @return Team
     */
    public function getTeamB()
    {
        return $this->teamB;
    }

    /**
     * @param Team $teamB
     */
    public function setTeamB($teamB)
    {
        $this->teamB = $teamB;
    }

    /**
     * @return Judge
     */
    public function getMainJudge()
    {
        return $this->mainJudge;
    }

    /**
     * @param Judge $mainJudge
     */
    public function setMainJudge($mainJudge)
    {
        $this->mainJudge = $mainJudge;
    }

    /**
     * @param Judge $additionalJudge
     */
    public function addAdditionalJudge($additionalJudge)
    {
        $this->additionalJudges[] = $additionalJudge;
    }

    public function getAdditionalJudges()
    {
        return $this->additionalJudges;
    }

    /**
     * @param Goal $goal
     */
    public function addGoal($goal)
    {
        $this->goals[] = $goal;
    }

    /**
     * @param Player $leadA
     */
    public function addLeadA($leadA)
    {
        $this->leadsA[] = $leadA;
    }

    /**
     * @param Player $leadB
     */
    public function addLeadB($leadB)
    {
        $this->leadsB[] = $leadB;
    }

    /**
     * @param Penalty $penalty
     */
    public function addPenalty($penalty)
    {
        $this->penalties[] = $penalty;
    }

    /**
     * @param Change $change
     */
    public function addChange($change)
    {
        $this->changes[] = $change;
    }

    /**
     * @param string $team
     * @return bool
     */
    public function hasTeam($team)
    {
        return $this->getTeamA()->getName() === $team ||
            $this->getTeamB()->getName() === $team
            ;
    }

    /**
     * @param Player $player
     * @return bool
     */
    public function hasPlayer(Player $player)
    {
        return $this->getTeamA()->hasPlayer($player) ||
            $this->getTeamB()->hasPlayer($player);
    }

    public function hasJudge(Judge $judge)
    {
        if ($this->getMainJudge()->getName() . ' ' . $this->getMainJudge()->getSurname()
            === $judge->getName() . ' ' . $judge->getSurname()) {
            return true;
        }
        foreach($this->additionalJudges as $addJudge) {
            if ($addJudge->getName() . ' ' . $addJudge->getSurname()
                === $judge->getName() . ' ' . $judge->getSurname()) {
                return true;
            }
        }
        return false;
    }

    public function getJudgePenalties(Judge $judge)
    {
        if ($this->hasJudge($judge)) {
            return count($this->penalties);
        }
        return 0;
    }

    public function getPlayerPenalties(Player $player)
    {
        $penalties = 0;
        foreach($this->penalties as $penalty) {
            $penalties += $penalty->getPlayer()->getNumber() === $player->getNumber();
        }
        return $penalties;
    }

    public function getDefenderGoalsLost(Player $player)
    {
        $leads = [];
        if ($this->getTeamA()->hasPlayer($player)) {
            $leads = $this->leadsA;
        }
        if ($this->getTeamB()->hasPlayer($player)) {
            $leads = $this->leadsB;
        }
        $isLead = false;
        foreach($leads as $lead) {
            if ($lead->getNumber() === $player->getNumber()) {
                $isLead = true;
                break;
            }
        }
        $steps = [];
        if ($isLead) {
            $steps[] = [
                'type' => 'in',
                'time' => '00:00'
            ];
        }
        foreach($this->changes as $change) {
            if ($change->getPlayerFrom()->getNumber() === $player->getNumber()) {
                $steps[] = [
                    'type' => 'out',
                    'time' => $change->getTime()
                ];
            }
            if ($change->getPlayerTo()->getNumber() === $player->getNumber()) {
                $steps[] = [
                    'type' => 'in',
                    'time' => $change->getTime()
                ];
            }
        }
        $lastStep = end($steps);
        if ($lastStep && $lastStep['type'] === 'in') {
            $steps[] = [
                'type' => 'out',
                'time' => $this->isOvertime() ? $this->getMaxGoalTime() : '60:00'
            ];
        }
        $intervals = [];
        $c = 0;
        foreach($steps as $key => $step) {
            if ($step['type'] === 'in') {
                $intervals[$c] = [];
                $intervals[$c]['in'] = $step['time'];
            }
            if ($step['type'] === 'out') {
                $intervals[$c]['out'] = $step['time'];
                $c++;
            }
        }
        $goals = 0;
        foreach($this->goals as $goal) {
            if ($goal->getPlayer()->getTeam() === $player->getTeam()) {
                continue;
            }
            foreach($intervals as $interval) {
                if ($goal->getTime() < $interval['out'] && $goal->getTime() > $interval['in']) {
                    $goals++;
                }
            }
        }
        return $goals;
    }

    /**
     * @param Player $player
     * @return int
     */
    public function getPlayerGoals(Player $player)
    {
        $goals = 0;
        foreach($this->goals as $goal) {
            $goals += $goal->getPlayer()->getNumber() === $player->getNumber();
        }
        return $goals;
    }

    /**
     * @param Player $player
     * @return int
     */
    public function getPlayerGoalSupports(Player $player)
    {
        $goals = 0;
        foreach($this->goals as $goal) {
            foreach($goal->getSupports() as $support) {
                /** @var Player $support */
                $goals += $support->getNumber() === $player->getNumber();
            }
        }
        return $goals;
    }

    /**
     * @param string $team
     * @return int
     */
    public function getTeamPoints($team)
    {
        $won = $this->getWonTeam()->getName() === $team;
        $overtime = $this->isOvertime();
        return $won
            ? (
                $overtime ? 3 : 5
            )
            : (
                $overtime ? 2 : 1
            )
        ;
    }

    public function isOvertime()
    {
        $maxTime = $this->getMaxGoalTime();
        return $maxTime
            ? intval(explode(':', $maxTime)[0]) > 60
            : false;
    }

    public function getMaxGoalTime()
    {
        return max(array_map(function(Goal $goal) {
            return $goal->getTime();
        }, $this->goals));
    }

    /**
     * @return Team
     */
    public function getWonTeam()
    {
        $teamGoals = [];
        foreach($this->goals as $goal) {
            $tn = $goal->getPlayer()->getTeam()->getName();
            if (!array_key_exists($tn, $teamGoals)) {
                $teamGoals[$tn] = [
                    'goals' => 0,
                    'team' => $goal->getPlayer()->getTeam()
                ];
            }
            $teamGoals[$tn]['goals']++;
        }
        $max = max(array_map(function($item){return $item['goals'];}, $teamGoals));
        $team = array_search($max, array_map(function($item){return $item['goals'];}, $teamGoals));
        return $teamGoals[$team]['team'];
    }

    public function getGoalsWon($team)
    {
        $count = 0;
        foreach($this->goals as $goal) {
            $tn = $goal->getPlayer()->getTeam()->getName();
            $count += ($tn === $team);
        }
        return $count;
    }

    public function getGoalsLost($team)
    {
        $count = 0;
        foreach($this->goals as $goal) {
            $tn = $goal->getPlayer()->getTeam()->getName();
            $count += ($tn !== $team);
        }
        return $count;
    }
}