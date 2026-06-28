<?php

namespace Application\Service;

use Application\Model\Movie;

class Processor
{
    /**
     * Process a movie and pause between each of them.
     *
     * @param callable(Movie): mixed $callback
     * @param Movie[] $movies
     * @param int $seconds seconds to sleep between each movie
     */
    private function process(callable $callback, array $movies, int $seconds): int
    {
        $total = count($movies);
        $count = 0;
        foreach ($movies as $movie) {
            echo '[' . mb_str_pad((string) ++$count, 5, ' ', STR_PAD_LEFT) . '/' . mb_str_pad((string) $total, 5, ' ', STR_PAD_LEFT) . '] ' . $movie->getImdbUrl() . "\t";
            flush();

            $callback($movie);

            echo "\n";

            sleep($seconds);
        }

        return $total;
    }

    /**
     * Search source for movies with release date.
     */
    public function searchSource(): void
    {
        $movies = _em()->getRepository(Movie::class)->getAllForSearch();
        $searcher = function (Movie $movie): void {
            $searchEngine = new SearchEngine();

            $movie->fetchData(); // Refresh movie data to be sure we have latest available title
            $title = $movie->title;
            $data = $searchEngine->search($title);
            $scores = $searchEngine->computeScores($title, $data);

            $best = @reset($scores);
            $movie->setSource($best);
            _em()->flush();

            echo $movie->source ?: '[source not found]';
        };

        // 5 minutes pause between search, not to stress third-party servers
        $total = $this->process($searcher, $movies, 5 * 60);

        echo $total . " movie sources updated in database\n";
    }

    /**
     * Fetch movie data to update title and release date for those that need it.
     *
     * @param int $seconds seconds to sleep between each movie
     */
    public function fetchMovieData(?int $limit, int $seconds = 0): void
    {
        $movies = _em()->getRepository(Movie::class)->getAllForFetching($limit);
        $fetcher = function (Movie $movie): void {
            $movie->fetchData();
            echo $movie->title;
        };

        $total = $this->process($fetcher, $movies, $seconds);

        echo $total . " movies updated in database\n";
    }
}
