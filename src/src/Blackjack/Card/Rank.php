<?php

declare(strict_types=1);

namespace Blackjack\Card;

/**
 * トランプのランク（A, 2〜10, J, Q, K）。
 */
enum Rank
{
    case ACE;
    case TWO;
    case THREE;
    case FOUR;
    case FIVE;
    case SIX;
    case SEVEN;
    case EIGHT;
    case NINE;
    case TEN;
    case JACK;
    case QUEEN;
    case KING;

    /**
     * 表示用の文字列（例: 「A」「7」「Q」）を返す。
     */
    public function label(): string
    {
        return match ($this) {
            self::ACE => 'A',
            self::TWO => '2',
            self::THREE => '3',
            self::FOUR => '4',
            self::FIVE => '5',
            self::SIX => '6',
            self::SEVEN => '7',
            self::EIGHT => '8',
            self::NINE => '9',
            self::TEN => '10',
            self::JACK => 'J',
            self::QUEEN => 'Q',
            self::KING => 'K',
        };
    }

    /**
     * カードの点数を返す。
     * ステップ1: A=1、2〜9=書かれた数、10/J/Q/K=10。
     */
    public function point(): int
    {
        return match ($this) {
            self::ACE => 1,
            self::TWO => 2,
            self::THREE => 3,
            self::FOUR => 4,
            self::FIVE => 5,
            self::SIX => 6,
            self::SEVEN => 7,
            self::EIGHT => 8,
            self::NINE => 9,
            self::TEN, self::JACK, self::QUEEN, self::KING => 10,
        };
    }
}
