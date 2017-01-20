<?php

$dirs = scandir(__DIR__ . '/tables');
$dirs = array_filter($dirs, function($item) {
    return $item !== '.' && $item !== '..';
});
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

    <h2>tables</h2>
    <?php foreach($dirs as $dir) : ?>
        <a href="tables/<?=$dir; ?>"><?=$dir; ?></a><br>
    <?php endforeach; ?>

</div> <!-- /container -->


</body>
</html>
