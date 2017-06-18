<?php

/**
 * This script fetch data from IMDb for all movies currently in our database and save it.
 * It will overwrite existing data (update).
 */
require_once __DIR__ . '/search_movie_source.php';

fetchMovieData(null, 3);
