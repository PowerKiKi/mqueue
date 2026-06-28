<?php

namespace Application\Repository;

use Application\Enum\Rating;
use Application\Model\Movie;
use Application\Model\Status;
use Application\Service\Sorting;
use Doctrine\ORM\Id\AssignedGenerator;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Query;
use Doctrine\ORM\Query\Expr\Join;

/**
 * @extends AbstractRepository<Movie>
 */
class MovieRepository extends AbstractRepository
{
    public function getOrCreate(int $id): Movie
    {
        $movie = $this->findOneById($id);

        if (!$movie) {
            $movie = new Movie();
            $movie->setId($id);

            // Exceptionally allow manually set ID
            $metadata = _em()->getClassMetaData(Movie::class);
            $metadata->setIdGenerator(new AssignedGenerator());
            $metadata->setIdGeneratorType(ClassMetadata::GENERATOR_TYPE_NONE);

            _em()->persist($movie);
            $movie->title = '';
            _em()->flush();
        }

        return $movie;
    }

    /**
     * Returns movies for search.
     *
     * @return Movie[]
     */
    public function getAllForSearch(): array
    {
        $qb = $this->createQueryBuilder('movie')
            ->innerJoin(Status::class, 'status', Join::WITH, 'movie.id = status.movie')
            ->andWhere('status.isLatest = 1')
            ->andWhere('status.rating = :rating')
            ->andWhere('movie.source IS NULL')
            ->andWhere("movie.dateSearch IS NULL OR movie.dateSearch < DATE_SUB(NOW(), movie.searchCount, 'MONTH')") // Search for same movie with an incrementally longer interval (1 month, 2 month, 3 month, etc.)
            ->andWhere('movie.startYear IS NOT NULL') // Movie must be released ...
            ->andWhere("movie.startYear < DATE_SUB(NOW(), 1, 'MONTH')") // ...at least released one month ago, or longer
            ->addGroupBy('movie.id')
            ->addOrderBy('COUNT(movie.id)', 'DESC') // First, order by popularity, so get the most needed first
            ->addOrderBy('RAND()', 'ASC') // Then, randomize a little bit so we don't always look for the same movies
            ->setParameter('rating', Rating::Need)
            ->setMaxResults(5);

        return $qb->getQuery()->getResult();
    }

    /**
     * Returns movies for data fetching.
     *
     * @return Movie[]
     */
    public function getAllForFetching(?int $limit = null): array
    {
        $qb = $this->createQueryBuilder('movie')
            ->andWhere("movie.dateUpdate IS NULL OR movie.dateUpdate < DATE_SUB(NOW(), 1, 'MONTH')") // Don't fetch data for movies, more than once a month
            ->addOrderBy('RAND()'); // Randomize order, so we don't watch only old movies

        if ($limit) {
            $qb->setMaxResults($limit);
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * Returns a query filtered according to parameters. This query may be used with paginator.
     */
    public function getFilteredQuery(array $filters, Sorting $sorting): Query
    {
        $select = $this->createQueryBuilder('movie');

        $i = 0;
        $maxDate = '';
        $filtersDone = [];
        foreach ($filters as $key => $filter) {
            if (!($filter['user'] ?? false)) {
                continue;
            }

            $filterUniqueId = $filter['user'];
            if (!preg_match('/^filter\d+$/', $key) || in_array($filterUniqueId, $filtersDone, true)) {
                continue;
            }

            $filtersDone[] = $filterUniqueId;
            $showNoRating = in_array(0, $filter['status'], true);
            $alias = 'status' . $i++;
            $allowNull = ' OR ' . $alias . '.user IS NULL';
            if ($filter['condition'] === 'is') {
                $condition = '';
                $allowNull = $showNoRating ? $allowNull : '';
            } else {
                $condition = 'NOT ';
                $allowNull = !$showNoRating ? $allowNull : '';
            }

            $paramUser = 'user' . $i;
            $select->leftJoin(Status::class, $alias, Join::WITH, '(movie.id = ' . $alias . '.movie AND ' . $alias . ".user = :$paramUser)" . $allowNull);
            $select->setParameter($paramUser, $filter['user']);

            $select->andWhere($alias . '.isLatest = 1 OR ' . $alias . '.isLatest IS NULL');

            // Filter by status
            $paramRating = 'rating' . $i;
            $select->andWhere($alias . '.rating ' . $condition . "IN (:$paramRating)" . $allowNull);
            $select->setParameter($paramRating, $filter['status']);

            // Filter by title
            $title = $filter['title'] ?? null;
            if ($title) {
                $id = Movie::extractId($title) ?? -1;
                $titles = explode(' ', mb_trim($title));
                foreach ($titles as $y => $part) {
                    if (!$part) {
                        continue;
                    }

                    $paramSearch = 'search' . $i . '_' . $y;
                    $paramId = 'id' . $i . '_' . $y;
                    $select->andWhere("movie.title LIKE :$paramSearch OR movie.id = :$paramId");
                    $select->setParameter($paramSearch, '%' . $part . '%');
                    $select->setParameter($paramId, $id);
                }
            }

            // Filter by presence of source
            if ($filter['withSource'] ?? null) {
                $select->andWhere('movie.source IS NOT NULL');
            }

            if ($maxDate) {
                $maxDate = "IF($alias.dateUpdate IS NULL OR $alias.dateUpdate < $maxDate, $maxDate, $alias.dateUpdate)";
            } else {
                $maxDate = "$alias.dateUpdate";
            }
        }

        $key = $sorting->validKey;
        if ($key === 'date') {
            $key = $maxDate ?: 'movie.title';
        }

        $select->addOrderBy($key, $sorting->validOrder);

        return $select->getQuery();
    }

    /**
     * Delete obsolete sources for all movies.
     * An obsolete source is either a source older than 3 months, or
     * a source which is not needed anymore (nobody need the movie anymore).
     */
    public function deleteObsoleteSources(): void
    {
        $db = $this->getEntityManager()->getConnection();
        $update = 'UPDATE movie SET date_update = date_update, date_search = NULL, search_count = 0, identity = 0, quality = 0, score = 0, source = NULL';

        // Delete sources older than 6 months
        $db->executeStatement($update . ' WHERE `source` IS NOT NULL AND `date_search` < DATE_SUB(NOW(), INTERVAL 6 MONTH)');

        // Delete non-needed sources
        $db->executeStatement($update . ' WHERE `movie`.`id` NOT IN (SELECT `status`.`movie_id` FROM `status` WHERE `rating` = :rating AND `is_latest`)', ['rating' => Rating::Need->value]);
    }
}
