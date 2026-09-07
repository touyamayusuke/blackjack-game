<?php

declare(strict_types=1);

namespace Blackjack;

use Blackjack\Card\Card;
use Blackjack\Card\Rank;
use Blackjack\Card\Suit;

/**
 * 山札。52枚のカードを保持し、上から引いていく。
 */
final class Deck
{
    private array $cards;

    public function __construct(?array $cards = null)
    {
        // （null なら Suit::cases() × Rank::cases() で52枚生成）
        if ($cards === null) {
            $this->cards = [];
            foreach (Suit::cases() as $suit) {
                foreach (Rank::cases() as $rank) {
                    $this->cards[] = new Card($suit, $rank);
                }
            }
        } else {
            $this->cards = $cards;
        }
    }

    /**
     * 山札をシャッフルする。
     */
    public function shuffle(): void
    {
        shuffle($this->cards);
    }

    /**
     * 山札の一番上から1枚引く。空の場合は例外を投げる。
     */
    public function draw(): Card
    {
        if (empty($this->cards)) {
            throw new \RuntimeException('山札が空です');
        }
        return array_shift($this->cards);
    }

    /**
     * 残り枚数を返す。
     */
    public function count(): int
    {
        return count($this->cards);
    }

    /**
     * 現在の山札を返す。
     */
    public function cards(): array
    {
        return $this->cards;
    }
}
