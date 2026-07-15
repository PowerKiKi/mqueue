<?php

declare(strict_types=1);

namespace ApplicationTest\Repository;

use Application\Enum\Rating;
use Application\Model\Movie;
use Application\Model\Status;
use Application\Model\User;
use Application\Repository\StatusRepository;
use ApplicationTest\Traits\TestWithTransactionAndUser;
use Cake\Chronos\Chronos;
use PHPUnit\Framework\TestCase;

class StatusRepositoryTest extends TestCase
{
    use TestWithTransactionAndUser {
        setUp as traitSetupWithTransaction;
    }

    private StatusRepository $repository;

    protected function setUp(): void
    {
        $this->traitSetupWithTransaction();
        $this->repository = _em()->getRepository(Status::class);
    }

    public function testGetStatistics(): void
    {
        $user = $this->getEntityManager()->getReference(User::class, 1001);
        $actual = $this->repository->getStatistics($user);

        self::assertSame([
            'total' => 3,
            'rated' => 1,
            0 => 2,
            1 => 0,
            2 => 0,
            3 => 0,
            4 => 0,
            5 => 1,
        ], $actual);
    }

    public function testGetActivityQuery(): void
    {
        $user = $this->getEntityManager()->getReference(User::class, 1001);
        $movie = $this->getEntityManager()->getReference(Movie::class, 96446);

        $actual = $this->repository->getActivityQuery($user)->getResult();
        self::assertSame([
            2004,
            2003,
            2002,
            2001,
        ], $this->toIds($actual));

        $actual = $this->repository->getActivityQuery($movie)->getResult();
        self::assertSame([
            2002,
            2005,
            2007,
            2001,
        ], $this->toIds($actual));

        $actual = $this->repository->getActivityQuery(null)->getResult();
        self::assertSame([
            2004,
            2003,
            2002,
            2005,
            2006,
            2007,
            2001,
        ], $this->toIds($actual));

    }

    public function testSet(): void
    {
        $user = $this->getEntityManager()->getReference(User::class, 1001);
        $movie = $this->getEntityManager()->getReference(Movie::class, 96446);

        $status = $this->repository->set($movie, $user, Rating::Bad);
        self::assertSame($movie, $status->movie);
        self::assertSame($user, $status->user);
        self::assertSame(Rating::Bad, $status->rating);
        self::assertNotNull($status->id);
    }

    public function testGetAllByMoviesAndUser(): void
    {
        $user = $this->getEntityManager()->getReference(User::class, 1001);
        $actual = $this->repository->getAllByMoviesAndUser([], null);
        self::assertSame([], $actual);

        $actual = $this->repository->getAllByMoviesAndUser([96446, 123], null);
        self::assertSame([
            96446 => null,
            123 => null,
        ], $this->toIds($actual));

        $actual = $this->repository->getAllByMoviesAndUser([96446, 123], $user);
        self::assertSame([
            96446 => 2002,
            123 => null,
        ], $this->toIds($actual));

        $actual = $this->repository->getAllByMoviesAndUser(['096446'], $user);
        self::assertSame([
            96446 => 2002,
        ], $this->toIds($actual));
    }

    public function testGetOneByMovieAndUser(): void
    {
        $user = $this->getEntityManager()->getReference(User::class, 1001);

        $status = $this->repository->getOneByMovieAndUser(96446, $user);
        self::assertSame(96446, $status->movie->id);
        self::assertSame($user, $status->user);
        self::assertSame(Rating::Favorite, $status->rating);
        self::assertNotNull($status->id);

        $status = $this->repository->getOneByMovieAndUser(123, $user);
        self::assertSame(123, $status->movie->id);
        self::assertSame($user, $status->user);
        self::assertSame(Rating::Nothing, $status->rating);
        self::assertNull($status->id);
    }

    public function testGetGraph(): void
    {
        $d1 = $this->epoch('2020-01-01');
        $d2 = $this->epoch('2020-01-02');
        $d3 = $this->epoch('2020-01-03');
        $d4 = $this->epoch('2020-01-04');

        $user = $this->getEntityManager()->getReference(User::class, 1001);
        $actual = $this->repository->getGraph(null, false);
        self::assertSame([
            [
                'name' => 'Need',
                'data' => [
                    [$d2, 2],
                ],
            ],
            [
                'name' => 'Bad',
                'data' => [
                    [$d3, 1],
                    [$d4, 0],
                ],
            ],
            [
                'name' => 'Ok',
                'data' => [
                    [$d1, 1],
                    [$d2, 0],
                ],
            ],
            [
                'name' => 'Excellent',
                'data' => [],
            ],
            [
                'name' => 'Favorite',
                'data' => [
                    [$d2, 2],
                ],
            ],
        ], $actual);

        $actual = $this->repository->getGraph($user, false);
        self::assertSame([
            [
                'name' => 'Need',
                'data' => [],
            ],
            [
                'name' => 'Bad',
                'data' => [
                    [$d3, 1],
                    [$d4, 0],
                ],
            ],
            [
                'name' => 'Ok',
                'data' => [
                    [$d1, 1],
                    [$d2, 0],
                ],
            ],
            [
                'name' => 'Excellent',
                'data' => [],
            ],
            [
                'name' => 'Favorite',
                'data' => [
                    [$d2, 1],
                ],
            ],
        ], $actual);

        $actual = $this->repository->getGraph($user, true);
        self::assertSame([
            [
                'name' => 'Need',
                'data' => [
                    [$d1, 0],
                    [$d2, 0],
                    [$d3, 0],
                    [$d4, 0],
                ],
            ],
            [
                'name' => 'Bad',
                'data' => [
                    [$d1, 0],
                    [$d2, 0],
                    [$d3, 1],
                    [$d4, 0], ],
            ],
            [
                'name' => 'Ok',
                'data' => [
                    [$d1, 1],
                    [$d2, 0],
                    [$d3, 0],
                    [$d4, 0],
                ],
            ],
            [
                'name' => 'Excellent',
                'data' => [
                    [$d1, 0],
                    [$d2, 0],
                    [$d3, 0],
                    [$d4, 0],
                ],
            ],
            [
                'name' => 'Favorite',
                'data' => [
                    [$d1, 0],
                    [$d2, 1],
                    [$d3, 1],
                    [$d4, 1],
                ],
            ],
        ], $actual);
    }

    private function epoch(string $s): int
    {
        return new Chronos($s)->timestamp * 1000;
    }
}
