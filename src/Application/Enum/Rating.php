<?php

namespace Application\Enum;

/**
 * A status rating.
 */
enum Rating: int
{
    case Nothing = 0;
    case Need = 1;
    case Bad = 2;
    case Ok = 3;
    case Excellent = 4;
    case Favorite = 5;

    public function name(): string
    {
        return match ($this) {
            self::Nothing => _tr('Not rated'),
            self::Need => _tr('Need'),
            self::Bad => _tr('Bad'),
            self::Ok => _tr('Ok'),
            self::Excellent => _tr('Excellent'),
            self::Favorite => _tr('Favorite'),
        };
    }

    /**
     * @return list<Rating>
     */
    public static function possibleChoices(bool $withNothing = false): array
    {
        return [
            ...($withNothing ? [self::Nothing] : []),
            self::Need,
            self::Bad,
            self::Ok,
            self::Excellent,
            self::Favorite,
        ];
    }

    /**
     * @return list<int>
     */
    public static function possibleValues(bool $withNothing = false): array
    {
        return array_map(fn (Rating $rating) => $rating->value, self::possibleChoices());
    }
}
