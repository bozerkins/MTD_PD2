<?php

require __DIR__ . '/../db.php';

$table = [];
$teams = $tournament->getTeamsNames();
$headers = [
    'komandas vieta tabulā pēc kārtas',
    'nosaukums',
    'iegūto punktu skaits',
    'uzvaru un zaudējumu skaits pamatlaikā',
    'uzvaru un zaudējumu skaits papildlaikā',
    'spēlēs gūto un zaudēto vārtu skaits'
];
foreach($teams as $team) {
    $row = [];
    $row['team'] = $team;
    $row['points'] = $tournament->getTeamPoints($team);
    $row['result'] = $tournament->getWins($team) . ':' . $tournament->getLosses($team);
    $row['result_overtime'] = $tournament->getWinsOvertime($team) . ':' . $tournament->getLossesOvertime($team);
    $row['result_goals'] = $tournament->getGoalsWon($team) . ':' . $tournament->getGoalsLost($team);
    $table[] = $row;
}

usort($table, function($a, $b) {
    return $a['points'] > $b['points'] ? -1 : 1;
});

$c = 0;
$table = array_map(function($item) use (&$c) {
    return array_merge(['number' => ++$c], $item);
}, $table);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <meta name="description" content="">
    <meta name="author" content="">

    <!-- Bootstrap core CSS -->
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet">

</head>

<body>

<div class="container">
    <h2>turnīra tabula</h2>
    <table class="table table-bordered">
        <tr>
            <?php foreach($headers as $header) : ?>
                <th><?=$header; ?></th>
            <?php endforeach; ?>
        </tr>
        <?php foreach($table as $row) : ?>
            <tr>
                <?php foreach($row as $value) : ?>
                    <td><?=$value; ?></td>
                <?php endforeach; ?>
            </tr>
        <?php endforeach; ?>
    </table>
</div> <!-- /container -->


</body>
</html>
