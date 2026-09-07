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
     * カードの基準点を返す。A の基準点は1点。
     * ステップ2: A を11点として扱ってよいかどうかは手札全体に依存するため、
     * その判断（1点/11点のどちらを採用するか）は Hand::score() 側が行う。
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

    /**
     * このランクが A (ACE) かどうかを返す。
     * ステップ2: Hand::score() が「A を11点に昇格できるか」を判断する際に使う。
     */
    public function isAce(): bool
    {
        return $this === self::ACE;
    }
}
