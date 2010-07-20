<?php

/**
 * This script fetch data from IMDb for all movies currently in our database and save it.
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