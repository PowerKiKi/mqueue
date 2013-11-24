<?php

/**
 * This script will search for source for needed movies that does not have sources yet
 */
require_once(__DIR__ . '/../public/index.php');

function movieProcessor(Closure $func, $movies, $sleep)
{
    $total = $movies->count();
    $count = 0;
    foreach ($movies as $movie) {
        echo '[' . str_pad(++$count, 5, ' ', STR_PAD_LEFT) . '/' . str_pad($total, 5, ' ', STR_PAD_LEFT) . "] " . $movie->getImdbUrl('akas') . "\t";
        flush();

        $func($movie);

        echo "\n";

        sleep($sleep);
    }

    return $total;
}

/**
 * Search source
 */
function searchSource()
{
    $movies = Default_Model_MovieMapper::findAllForSearch();
    $searcher = function(Default_Model_Movie $movie) {
        $searchEngine = new SearchEngine();

        $movie->fetchData(); // Refresh movie data to be sure we have latest available title
        $title = $movie->getTitle();
        $data = $searchEngine->search($title);
        $scores = $searchEngine->computeScores($title, $data);

        $best = @reset($scores);
        $movie->setSource($best);
        $movie->save();

        echo $movie->source ? : '[source not found]';
    };

    // 5 minutes pause between search, not to stress third-party servers
    $total = movieProcessor($searcher, $movies, 5 * 60);

    echo $total . " movie sources updated in database\n";
}

/**
 * Fetch movie data for those that need it
 * @param int $limit
 * @param int $sleep seconds to sleep between each movie
 */
function fetchMovieData($limit = null, $sleep = 0)
{
    $movies = Default_Model_MovieMapper::findAllForFetching($limit);
    $fetcher = function(Default_Model_Movie $movie) {
        $movie->fetchData();
        echo $movie->title;
    };

    $total = movieProcessor($fetcher, $movies, $sleep);

    echo $total . " movies updated in database\n";
}

// we only do things if this file were NOT included (otherwise, the file was included to access misc functions)
if (realpath(__FILE__) == realpath($_SERVER['SCRIPT_FILENAME'])) {
    // Clean up obsolete sources
    Default_Model_MovieMapper::deleteObsoleteSources();

    // Fetch movie data to update title and release date
    fetchMovieData(20, 1 * 60);

    // Search source for movies with release date
    searchSource();
}

