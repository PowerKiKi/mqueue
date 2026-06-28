<?php

namespace ApplicationTest\Model;

use Application\Model\Movie;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class MovieTest extends TestCase
{
    #[DataProvider('providerExtractId')]
    public function testExtractId(int|string|null $input, $expected): void
    {
        self::assertSame($expected, Movie::extractId($input));
    }

    public static function providerExtractId(): iterable
    {
        yield [null, null];
        yield [123, '0000123'];
        yield [1234567890, '1234567890'];
        yield ['123', '0000123'];
        yield ['abc', null];
    }

    public function testSetId(): void
    {
        $movie = new Movie();
        self::assertNull($movie->id);
        $movie->setId(12);
        self::assertSame(12, $movie->id);
    }

    public function testGetImdbUrl(): void
    {
        $movie = new Movie();
        $movie->setId(12);
        self::assertSame('https://www.imdb.com/title/tt0000012/', $movie->getImdbUrl());
    }

    #[DataProvider('providerSetSource')]
    public function testSetSource(array|false $source, array $expected): void
    {
        $movie = new Movie();
        $movie->setSource($source);
        self::assertSame(
            $expected,
            [
                'searchCount' => $movie->searchCount,
                'identity' => $movie->identity,
                'quality' => $movie->quality,
                'score' => $movie->score,
                'source' => $movie->source,
            ],
        );
    }

    public static function providerSetSource(): iterable
    {
        yield [
            [
                'identity' => 10,
                'quality' => 20,
                'score' => 0,
                'link' => 'https://example.com',
            ],
            [
                'searchCount' => 1,
                'identity' => 0,
                'quality' => 0,
                'score' => 0,
                'source' => null,
            ],
        ];

        yield [
            [
                'identity' => 10,
                'quality' => 20,
                'score' => 30,
                'link' => 'https://example.com',
            ],
            [
                'searchCount' => 1,
                'identity' => 10,
                'quality' => 20,
                'score' => 30,
                'source' => 'https://example.com',
            ],
        ];
    }
}
