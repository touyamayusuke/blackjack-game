<?php

declare(strict_types=1);

namespace Blackjack;

use Blackjack\Card\Card;

/**
 * 手札。カードの集まりと、その得点・バースト判定を受け持つ。
 */
final class Hand
{
    /** @var list<Card> */
    private array $cards = [];

    /**
     * 手札にカードを1枚加える。
     */
    public function add(Card $card): void
    {
        $this->cards[] = $card;
    }

    /**
     * 現在の手札を返す。
     *
     * @return list<Card>
     */
    public function cards(): array
    {
        return $this->cards;
    }

    /**
     * 手札の合計得点を返す。
     * ステップ1: 単純合計（A は 1 点）。
     */
    public function score(): int
    {
        $score = 0;
        foreach ($this->cards as $card) {
            $score += $card->point();
        }
        return $score;
    }

    /**
     * 21 を超えているか。
     */
    public function isBust(): bool
    {
        return $this->score() > 21;
    }
}
