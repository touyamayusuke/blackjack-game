<?php

declare(strict_types=1);

namespace Blackjack;

use Blackjack\Card\Card;
use Blackjack\Card\Rank;

/**
 * 手札。カードの集まりと、その得点・バースト判定を受け持つ。
 */
final class Hand
{
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
     */
    public function cards(): array
    {
        return $this->cards;
    }

    /**
     * 手札の合計得点を返す。
     * ステップ2: A は1点または11点のうち、合計が21以内で最大となる方で数える。
     *
     * 実装のヒント（貪欲法）:
     *  1. 全カードの基準点（A は1点として Card::point() が返す値）を合計し、
     *     同時に手札に含まれる A の枚数を数える。
     *  2. A の枚数分だけ、「+10点しても合計が21以下に収まるなら+10する
     *     （＝その A を11点扱いに昇格させる）」を1枚ずつ試す。
     *  3. 最終的な合計を返す。
     */
    public function score(): int
    {
        $score = 0;
        $aceCount = 0;

        foreach ($this->cards as $card) {
            $score += $card->point();
            if ($card->isAce()) {
                $aceCount++;
            }
        }

        while ($aceCount > 0 && $score + 10 <= 21) {
            $score += 10;
            $aceCount--;
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
