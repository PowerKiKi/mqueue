<?php

/**
 * This script will search for source for needed movies that does not have sources yet
 */

require_once(__DIR__ . '/../public/index.php');

$searchEngine = new SearchEngine();

$movies = Default_Model_MovieMapper::findAllForSearch();
$count = 0;
$total = $movies->count();

foreach ($movies as $movie)
{	
	echo '[' . str_pad(++$count, 5, ' ', STR_PAD_LEFT) . '/' . str_pad($total, 5, ' ', STR_PAD_LEFT) . "] " . $movie->getImdbUrl('akas'). "\t";
	flush();
	
	$title = $movie->getTitle();
	$data = $searchEngine->search($title);
	$scores = $searchEngine->computeScores($title, $data);
	
	$best = @reset($scores);
	$movie->setSource($best);
	$movie->save();
	
	echo $movie->source . "\n";
	
	sleep(5 * 60); // 5 minutes pause between search, not to stress third-party servers
}

echo $total . " movie sources updated in database\n";
