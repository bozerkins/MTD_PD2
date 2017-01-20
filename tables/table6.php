<?php

require __DIR__ . '/../db.php';

$table = [];
$headers = [
    'vieta sarakstā pēc kārtas',
    'tiesneša vārds un uzvārds',
    'sodu skaits',
];

$judges = $tournament->getJudges();
foreach($judges as $judge) {
    $row = [];
    $row['credentials'] = $judge->getName() . ' ' . $judge->getSurname();
    $row['penalties'] = $tournament->getJudgePenalties($judge);
    $table[] = $row;
}
usort($table, function($a, $b) {
    return $a['penalties'] > $b['penalties'] ? -1 : 1;
});

$c = 0;
$table = array_map(function($item) use (&$c) {
    return array_merge(['number' => ++$c], $item);
}, $table);

$table = array_slice($table, 0, 10);
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
    <h2>turnīra "stingrāko" tiesnešu saraksts</h2>
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
