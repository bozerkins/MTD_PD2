<?php

include __DIR__ . '/vendor/autoload.php';

$directory = new \RecursiveDirectoryIterator(__DIR__ . '/data');
$iterator = new \RecursiveIteratorIterator($directory);
$files = [];
foreach ($iterator as $info) {
    if (!preg_match("/\.json$/", $info->getPathname())) {
        continue;
    }
    $files[] = $info->getPathname();
}
$contents = [];
foreach($files as $file) {
    $contents[] = json_decode(utf8_encode(file_get_contents($file)), true);
}

$playsCollection = [];
$teamsCollection = [];
foreach($contents as $content) {
    $playObject = new \App\Play();
    $playObject->setDate($content["Spele"]["Laiks"]);
    $playObject->setPlace($content["Spele"]["Vieta"]);
    $playObject->setViewers($content["Spele"]["Skatitaji"]);
    $judgeObject = new \App\Judge();
    $judgeObject->setName($content["Spele"]['VT']["Vards"]);
    $judgeObject->setSurname($content["Spele"]['VT']["Uzvards"]);
    $playObject->setMainJudge($judgeObject);

    foreach($content["Spele"]['T'] as $judge) {
        $judgeObject = new \App\Judge();
        $judgeObject->setName($judge["Vards"]);
        $judgeObject->setSurname($judge["Uzvards"]);
        $playObject->addAdditionalJudge($judgeObject);
    }

    foreach($content["Spele"]["Komanda"] as $key => $team) {
        if (array_key_exists($team['Nosaukums'], $teamsCollection)) {
            $teamObject = $teamsCollection[$team['Nosaukums']];
        } else {
            $teamObject = new \App\Team();
            $teamObject->setName($team['Nosaukums']);
            $teamsCollection[$team['Nosaukums']] = $teamObject;
        }

        foreach($team["Speletaji"]["Speletajs"] as $player) {
            $playerObject = new \App\Player();
            $playerObject->setRole($player['Loma']);
            $playerObject->setNumber($player['Nr']);
            $playerObject->setSurname($player['Uzvards']);
            $playerObject->setName($player['Vards']);
            if (!$teamObject->hasPlayer($playerObject)) {
                $playerObject->setTeam($teamObject);
                $teamObject->addPlayer($playerObject);
            }
        }

        if ($team['Varti']) {
            if (!array_key_exists(0, $team['Varti']['VG'])) {
                $team['Varti']['VG'] = [$team['Varti']['VG']];
            }
            foreach($team['Varti']['VG'] as $goal) {
                $goalObject = new \App\Goal();
                $goalObject->setTime($goal['Laiks']);
                $goalObject->setType($goal['Sitiens']);
                $goalObject->setPlayer($teamObject->getPlayer($goal['Nr']));
                $playObject->addGoal($goalObject);
                if ($goal['P']) {
                    if (!array_key_exists(0, $goal['P'])) {
                        $goal['P'] = [$goal['P']];
                    }
                    foreach($goal['P'] as $support) {
                        $goalObject->addSupport($teamObject->getPlayer($support['Nr']));
                    }
                }
            }
        }
        if ($team['Sodi']) {
            if (!array_key_exists(0, $team['Sodi']['Sods'])) {
                $team['Sodi']['Sods'] = [$team['Sodi']['Sods']];
            }
            foreach($team['Sodi']['Sods'] as $penalty) {
                $penaltyObject = new \App\Penalty();
                $penaltyObject->setTime($penalty['Laiks']);
                $penaltyObject->setPlayer($teamObject->getPlayer($penalty['Nr']));
                $playObject->addPenalty($penaltyObject);
            }
        }

        if ($team['Mainas']) {
            if (!array_key_exists(0, $team['Mainas']['Maina'])) {
                $team['Mainas']['Maina'] = [$team['Mainas']['Maina']];
            }
            foreach($team['Mainas']['Maina'] as $change) {
                $changeObject = new \App\Change();
                $changeObject->setTime($change['Laiks']);
                $changeObject->setPlayerFrom($teamObject->getPlayer($change['Nr1']));
                $changeObject->setPlayerTo($teamObject->getPlayer($change['Nr2']));
                $playObject->addChange($changeObject);
            }
        }

        if ($key === 0) {
            $playObject->setTeamA($teamObject);
            foreach($team['Pamatsastavs']['Speletajs'] as $lead) {
                $playObject->addLeadA($teamObject->getPlayer($lead['Nr']));
            }
        } elseif ($key === 1) {
            $playObject->setTeamB($teamObject);
            foreach($team['Pamatsastavs']['Speletajs'] as $lead) {
                $playObject->addLeadB($teamObject->getPlayer($lead['Nr']));
            }
        } else {
            throw new \Exception('could not parse team number in the play');
        }

    }

    $playsCollection[] = $playObject;
}

$tournament = new \App\Tournament($playsCollection, $teamsCollection);

//dump($contents);
//dump($playsCollection);
//dump($teamsCollection);