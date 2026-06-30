<?php

declare(strict_types=1);

namespace ApplicationTest\Repository;

use Application\Enum\Rating;
use Application\Model\Movie;
use Application\Model\Status;
use Application\Model\User;
use Application\Repository\StatusRepository;
use ApplicationTest\Traits\TestWithTransactionAndUser;
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
        $user = $this->getEntityManager()->getReference(User::class, 1001);
        $actual = $this->repository->getGraph(null, false);
        self::assertSame([
            [
                'name' => 'Need',
                'data' => [
                    [
                        1577919600000,
                        2,
                    ],
                ],
            ],
            [
                'name' => 'Bad',
                'data' => [
                    [
                        1578006000000,
                        1,
                    ],
                    [
                        1578092400000,
                        0,
                    ],
                ],
            ],
            [
                'name' => 'Ok',
                'data' => [
                    [
                        1577833200000,
                        1,
                    ],
                    [
                        1577919600000,
                        0,
                    ],
                ],
            ],
            [
                'name' => 'Excellent',
                'data' => [],
            ],
            [
                'name' => 'Favorite',
                'data' => [
                    [
                        1577919600000,
                        2,
                    ],
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
                    [
                        1578006000000,
                        1,
                    ],
                    [
                        1578092400000,
                        0,
                    ],
                ],
            ],
            [
                'name' => 'Ok',
                'data' => [
                    [
                        1577833200000,
                        1,
                    ],
                    [
                        1577919600000,
                        0,
                    ],
                ],
            ],
            [
                'name' => 'Excellent',
                'data' => [],
            ],
            [
                'name' => 'Favorite',
                'data' => [
                    [
                        1577919600000,
                        1,
                    ],
                ],
            ],
        ], $actual);

        $actual = $this->repository->getGraph($user, true);
        self::assertSame([
            [
                'name' => 'Need',
                'data' => [
                    [
                        1577833200000,
                        0,
                    ],
                    [
                        1577919600000,
                        0,
                    ],
                    [
                        1578006000000,
                        0,
                    ],
                    [
                        1578092400000,
                        0,
                    ],
                ],
            ],
            [
                'name' => 'Bad',
                'data' => [
                    [
                        1577833200000,
                        0,
                    ],
                    [
                        1577919600000,
                        0,
                    ],
                    [
                        1578006000000,
                        1,
                    ],
                    [
                        1578092400000,
                        0,
                    ],
                ],
            ],
            [
                'name' => 'Ok',
                'data' => [
                    [
                        1577833200000,
                        1,
                    ],
                    [
                        1577919600000,
                        0,
                    ],
                    [
                        1578006000000,
                        0,
                    ],
                    [
                        1578092400000,
                        0,
                    ],
                ],
            ],
            [
                'name' => 'Excellent',
                'data' => [
                    [
                        1577833200000,
                        0,
                    ],
                    [
                        1577919600000,
                        0,
                    ],
                    [
                        1578006000000,
                        0,
                    ],
                    [
                        1578092400000,
                        0,
                    ],
                ],
            ],
            [
                'name' => 'Favorite',
                'data' => [
                    [
                        1577833200000,
                        0,
                    ],
                    [
                        1577919600000,
                        1,
                    ],
                    [
                        1578006000000,
                        1,
                    ],
                    [
                        1578092400000,
                        1,
                    ],
                ],
            ],
        ], $actual);
    }
}
