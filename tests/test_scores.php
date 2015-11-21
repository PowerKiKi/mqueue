<?php

/**
 * This script is used to test score computing algorithm on a batch of movies and their sources
 */
require_once __DIR__ . '/../public/index.php';

$searchEngine = new \mQueue\Service\SearchEngine();

// Clean up
$destination = __DIR__ . '/data/current/';
$cmd = 'rm -rf ' . escapeshellarg($destination);
`$cmd`;
@mkdir($destination);

// For each file of sources, compute scores
$bests = [];
$found = 0;
foreach (glob(__DIR__ . '/data/sources/*') as $path) {
    echo '.';
    $title = str_replace('mqueue_', '', pathinfo($path, PATHINFO_BASENAME));

    $content = file_get_contents($path);
    $sources = $searchEngine->parse($content);

    // Compute score and save the result on disk
    $scores = $searchEngine->computeScores($title, $sources);
    file_put_contents($destination . $title, var_export($scores, true));

    // Keep the best source for global statistics
    $best = reset($scores);
    $bests[$title] = $best;
    if ($best && $best['score']) {
        ++$found;
    }
}

// Output global statistics
array_unshift($bests, [
    'found' => $found,
    'not found' => count($bests) - $found,
    'total' => count($bests),
]);
file_put_contents($destination . '/_all', var_export($bests, true));
