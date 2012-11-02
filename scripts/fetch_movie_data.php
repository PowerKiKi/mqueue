<?php

/**
 * This script fetch data from IMDb for all movies currently in our database and save it.
 * It will overwrite existing data (update).
 */


require_once(__DIR__ . '/../public/index.php');

$movies = Default_Model_MovieMapper::fetchAll();
$count = 0;
$total = $movies->count();
foreach ($movies as $movie)
{	
	echo '[' . str_pad(++$count, 5, ' ', STR_PAD_LEFT) . '/' . str_pad($total, 5, ' ', STR_PAD_LEFT) . "] " . $movie->getImdbUrl('akas'). "\t";
	$movie->title = null;
	$title = $movie->getTitle();
	echo $title . "\n";
}

echo $total . " movies updated in database\n";