<?php

/**
 * This script fetch all data from IMDb and save it in our database.
 * It will overwrite existing data (update).
 */


require_once('../public/index.php');

$movies = Default_Model_MovieMapper::fetchAll();
foreach ($movies as $movie)
{
	echo $movie->getImdbUrl(). "\t";
	$movie->title = null;
	$title = $movie->getTitle();
	echo $title . "\n";
}

echo count($movies) . " movies updated in database\n";