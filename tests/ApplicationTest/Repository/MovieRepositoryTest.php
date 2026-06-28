<?php

namespace ApplicationTest\Repository;

use Application\Model\Movie;
use Application\Repository\MovieRepository;
use Application\Service\Sorting;
use ApplicationTest\Traits\TestWithTransactionAndUser;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class MovieRepositoryTest extends TestCase
{
    use TestWithTransactionAndUser {
        setUp as traitSetupWithTransaction;
    }

    private MovieRepository $repository;

    protected function setUp(): void
    {
        $this->traitSetupWithTransaction();
        $this->repository = _em()->getRepository(Movie::class);
    }

    public function testGetOrCreate(): void
    {
        $newMovie = $this->repository->getOrCreate(1);
        self::assertSame('https://www.imdb.com/title/tt0000001/', $newMovie->getImdbUrl());

        $existingMovie = $this->repository->getOrCreate(96446);
        self::assertSame('Willow', $existingMovie->title);
    }

    public function testGetAllForSearch(): void
    {
        $actual = $this->repository->getAllForSearch();
        self::assertSame([96446], $this->toIds($actual));
    }

    public function testGetAllForFetching(): void
    {
        $actual = $this->repository->getAllForFetching();
        $ids = $this->toIds($actual);
        sort($ids);
        self::assertSame([96446, 103064, 28650488], $ids);

        $actual = $this->repository->getAllForFetching(200);
        $ids = $this->toIds($actual);
        sort($ids);
        self::assertSame([96446, 103064, 28650488], $ids);
    }

    #[DataProvider('providerGetFilteredQuery')]
    public function testGetFilteredQuery(array $filters, Sorting $sorting, array $expected): void
    {
        $actual = $this->repository->getFilteredQuery($filters, $sorting)->getResult();
        self::assertSame($expected, $this->toIds($actual));
    }

    public static function providerGetFilteredQuery(): iterable
    {
        $sorting1 = new Sorting([], ['movie.title']);
        $sorting2 = new Sorting([], ['date']);
        $sorting3 = new Sorting([], ['movie.dateSearch']);
        $sorting4 = new Sorting([], ['status0.rating']);

        yield 'sort by title' => [
            [],
            $sorting1,
            [103064, 28650488, 96446],
        ];

        yield 'sort by date' => [
            [],
            $sorting2,
            [103064, 28650488, 96446],
        ];

        yield 'sort by search date' => [
            [],
            $sorting3,
            [96446, 28650488, 103064],
        ];

        yield 'filter by status' => [
            [
                'filter1' => [
                    'user' => 1001,
                    'condition' => 'is',
                    'status' => [1, 5],
                    'title' => '',
                    'withSource' => false,
                ],
            ],
            $sorting1,
            [96446],
        ];

        yield 'sort by status' => [
            [
                'filter1' => [
                    'user' => 1001,
                    'condition' => 'is',
                    'status' => [0, 1, 2, 3, 4, 5],
                    'title' => '',
                    'withSource' => false,
                ]],
            $sorting4,
            [28650488, 103064, 96446],
        ];

        yield 'filter by NOT status' => [
            [
                'filter1' => [
                    'user' => 1001,
                    'condition' => 'isnot',
                    'status' => [1, 5],
                    'title' => '',
                    'withSource' => false,
                ],
            ],
            $sorting1,
            [103064, 28650488],
        ];

        yield 'filter by title' => [
            [
                'filter1' => [
                    'user' => 1001,
                    'condition' => 'is',
                    'status' => [0, 1, 2, 3, 4, 5],
                    'title' => 'will',
                    'withSource' => false,
                ],
            ],
            $sorting1,
            [96446],
        ];

        yield 'filter by source' => [
            [
                'filter1' => [
                    'user' => 1001,
                    'condition' => 'is',
                    'status' => [0, 1, 2, 3, 4, 5],
                    'title' => '',
                    'withSource' => true,
                ],
            ],
            $sorting1,
            [103064],
        ];

        yield 'filter by multiple users' => [
            [
                'filter1' => [
                    'user' => 1002,
                    'condition' => 'is',
                    'status' => [1],
                ],
                'filter2' => [
                    'user' => 1003,
                    'condition' => 'is',
                    'status' => [1],
                ],
            ],
            $sorting1,
            [96446],
        ];
    }

    public function testDeleteObsoleteSources(): void
    {
        $this->repository->deleteObsoleteSources();
        self::assertSame(0, $this->getEntityManager()->getConnection()->fetchOne('SELECT COUNT(*) FROM movie WHERE source IS NOT NULL'));
    }
}
