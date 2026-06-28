<?php

/**
 * This script will search for source for needed movies that does not have sources yet.
 */

use Application\Model\Movie;
use Application\Service\Processor;

require_once __DIR__ . '/../public/index.php';

// Clean up obsolete sources
_em()->getRepository(Movie::class)->deleteObsoleteSources();

$processor = new Processor();
$processor->fetchMovieData(20, 1 * 60);
$processor->searchSource();
