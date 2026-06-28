<?php

/**
 * This script fetch data from IMDb for all movies currently in our database and save it.
 * It will overwrite existing data (update).
 */

use Application\Service\Processor;

require_once __DIR__ . '/../public/index.php';

$processor = new Processor();
$processor->fetchMovieData(null, 3);
