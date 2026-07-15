<?php

namespace Application\Repository;

use Application\Enum\Rating;
use Application\Model\Movie;
use Application\Model\Status;
use Application\Model\User;
use Cake\Chronos\Chronos;
use DateTimeZone;
use Doctrine\ORM\Query;
use Doctrine\ORM\Query\Expr\Join;

/**
 * @extends AbstractRepository<Status>
 */
class StatusRepository extends AbstractRepository
{
    /**
     * Define the status for a movie-user tuple. If an existing status exists and
     * is very recent, it will be updated, otherwise a new status will be created.
     *
     * IMPORTANT: This is the only allowed way to modify status.
     */
    public function set(Movie $movie, User $user, Rating $rating): Status
    {
        $connection = $this->getEntityManager()->getConnection();
        $connection->beginTransaction();

        // Find out if a very recent status exists to be replaced, so user can change their mind "quickly"
        $timeLimit = Chronos::now()->subMinutes(5)->toDateTimeString();
        $qb = $this->createQueryBuilder('status')
            ->andWhere('status.user = :user')
            ->andWhere('status.movie = :movie')
            ->andWhere('status.dateUpdate > :timeLimit')
            ->setParameter('user', $user->id)
            ->setParameter('movie', $movie->id)
            ->setParameter('timeLimit', $timeLimit);

        $status = $qb->getQuery()->getOneOrNullResult();

        // Otherwise, create a brand new one and set all existing one as "old"
        if (!$status) {
            $status = new Status();
            $this->getEntityManager()->persist($status);
            $status->user = $user;
            $status->movie = $movie;
            $status->isLatest = true;

            // Here we must set dateUpdate to itself to avoid auto-update of the timestamp field by MySql
            $connection->executeStatement(
                'UPDATE `status` SET is_latest = 0, date_update = date_update WHERE user_id = :user AND movie_id = :movie',
                [
                    'user' => $user->id,
                    'movie' => $movie->id,
                ],
            );
        }

        $status->rating = $rating;

        _em()->flush();

        $connection->commit();

        return $status;
    }

    /**
     * Find a status by its user and movie. If not found it will be created (but not saved).
     */
    public function getOneByMovieAndUser(int|string $id, ?User $user): Status
    {
        $statuses = $this->getAllByMoviesAndUser([$id], $user);

        return reset($statuses);
    }

    /**
     * Returns an array of Status containing all statuses for specified ids
     * (if they don't exist in database, they will be created with default values but not saved).
     *
     * @param (int|string)[] $idMovies
     *
     * @return Status[]
     */
    public function getAllByMoviesAndUser(array $idMovies, ?User $user): array
    {
        $idMovies = array_map(fn (string|int $id) => (int) $id, $idMovies);
        $idMovies = array_unique($idMovies);

        $statuses = [];
        if (!count($idMovies)) {
            return $statuses;
        }

        // Do not hit database if we know there won't be any result anyway
        if ($user) {
            $qb = $this->createQueryBuilder('status')
                ->andWhere('status.user = :user')
                ->setParameter('user', $user)
                ->andWhere('status.movie IN (:movies)')
                ->setParameter('movies', $idMovies)
                ->andWhere('status.isLatest = 1');

            /** @var Status[] $records */
            $records = $qb->getQuery()->getResult();

            foreach ($records as $record) {
                $statuses[$record->movie->id] = $record;
            }
        }

        // Fill non-existing statuses in databases
        foreach ($idMovies as $id) {
            if (!array_key_exists($id, $statuses)) {
                $status = new Status();
                if ($user) {
                    $status->user = $user;
                }
                $status->movie = $this->getEntityManager()->getReference(Movie::class, $id);
                $statuses[$id] = $status;
            }
        }

        return $statuses;
    }

    /**
     * Build statistic for the given user.
     *
     * @return array statistics
     */
    public function getStatistics(User $user): array
    {
        $sql = <<<SQL
            SELECT IFNULL(status.rating, 0) AS rating, COUNT(IFNULL(status.rating, 0)) AS count
            FROM status
            RIGHT JOIN movie ON movie.id = status.movie_id AND status.user_id = :user
            WHERE status.is_latest = 1 OR status.is_latest IS NULL
            GROUP BY IFNULL(status.rating, 0)
            SQL;

        $records = $this->getEntityManager()->getConnection()->fetchAllAssociative($sql, ['user' => $user->id]);

        // Set all count to 0
        $result = [
            'total' => 0,
            'rated' => 0,
            Rating::Nothing->value => 0,
        ];
        foreach (Rating::possibleChoices() as $rating) {
            $result[$rating->value] = 0;
        }

        // Fetch real counts
        foreach ($records as $row) {
            $result[$row['rating']] = $row['count'];
            if ((int) $row['rating'] !== Rating::Nothing->value) {
                $result['rated'] += $row['count'];
            }
            $result['total'] += $row['count'];
        }

        return $result;
    }

    /**
     * Build statistic for the given user.
     *
     * @return array statistics
     */
    public function getGraph(?User $user, bool $percent): array
    {
        $qb = $this->getEntityManager()->getConnection()->createQueryBuilder()
            ->select('status.rating, status.user_id, status.movie_id, status.date_update')
            ->from('status')
            ->addOrderBy('status.date_update');

        if ($user) {
            $qb->andWhere('status.user_id = :user')
                ->setParameter('user', $user->id);
        }

        $records = $qb->fetchAllAssociative();

        // Set all count to 0
        $cumulatedStatuses = [Rating::Nothing->value => 0];
        $graphData = [];
        foreach (Rating::possibleChoices() as $rating) {
            $cumulatedStatuses[$rating->value] = 0;
            $graphData[$rating->value] = [];
        }

        // Fetch real counts
        $lastStatuses = [];
        foreach ($records as $row) {
            $rating = $row['rating'];
            $userId = $row['user_id'];
            $movieId = $row['movie_id'];

            // Add new status
            ++$cumulatedStatuses[$rating];
            $changed = [$rating];

            // Substract old status
            if (isset($lastStatuses[$userId][$movieId])) {
                --$cumulatedStatuses[$lastStatuses[$userId][$movieId]];
                $changed[] = $lastStatuses[$userId][$movieId];
            }
            $lastStatuses[$userId][$movieId] = $rating;

            $time = new Chronos($row['date_update']);
            $time = $time->setTimezone(new DateTimeZone('GMT'));
            $epoch = (int) $time->format('U') * 1000;

            // If we are in percent mode, we need all status for each timestamp
            if ($percent) {
                $changed = Rating::possibleValues();
            }

            // Keep for the graph only the changed values (and overwrite previous value if it happened at exactly the same time)
            foreach ($changed as $val) {
                $graphData[$val][$epoch] = [
                    $epoch,
                    $cumulatedStatuses[$val],
                ];
            }
        }

        // Format everything in a more output friendly way
        $result = [];
        foreach (Rating::possibleChoices() as $rating) {
            $result[] = [
                'name' => $rating->name(),
                'data' => array_values($graphData[$rating->value]),
            ];
        }

        return $result;
    }

    /**
     * Returns the query to get activity for either the whole system, or a specific user, or a specific movie.
     */
    public function getActivityQuery(null|Movie|User $item): Query
    {
        $qb = $this->createQueryBuilder('status')
            ->addSelect('movie')
            ->addSelect('user')
            ->innerJoin('status.movie', 'movie', Join::WITH)
            ->innerJoin('status.user', 'user', Join::WITH)
            ->addOrderBy('status.dateUpdate', 'DESC')
            ->addOrderBy('status.id', 'ASC');

        if ($item instanceof User) {
            $qb->andWhere('status.user = :user')
                ->setParameter('user', $item);
        } elseif ($item instanceof Movie) {
            $qb->andWhere('status.movie = :movie')
                ->setParameter('movie', $item);
        }

        return $qb->getQuery();
    }
}
