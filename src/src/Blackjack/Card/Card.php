<?php

declare(strict_types=1);

namespace Blackjack\Card;

/**
 * 1枚のカードを表す不変の値オブジェクト。
 */
final readonly class Card
{
    public function __construct(
        public Suit $suit,
        public Rank $rank,
    ) {
    }

    /**
     * 表示用の文字列（例: 「ハートの7」）を返す。
     */
    public function label(): string
    {
        return $this->suit->label() . 'の' . $this->rank->label();
    }

    /**
     * カードの点数を返す（Rank に委譲）。
     */
    public function point(): int
    {
        return $this->rank->point();
    }

    /**
     * このカードが A (ACE) かどうかを返す（Rank に委譲）。
     */
    public function isAce(): bool
    {
        return $this->rank->isAce();
    }
}
