<?php

declare(strict_types=1);

namespace Blackjack;

use Blackjack\Participant\Participant;

/**
 * プレイヤーとディーラーの手札から勝敗を判定する。
 */
final class Judge
{
    /**
     * プレイヤーから見た結果を返す。
     * バーストは即負け。どちらも 21 以内なら 21 に近い方が勝ち。同点は引き分け。
     */
    public function decide(Participant $player, Participant $dealer): Outcome
    {
        if ($player->isBust()) {
            return Outcome::LOSE;
        }
        if ($dealer->isBust()) {
            return Outcome::WIN;
        }
        if ($player->score() > $dealer->score()) {
            return Outcome::WIN;
        }
        if ($player->score() < $dealer->score()) {
            return Outcome::LOSE;
        }
        return Outcome::DRAW;
    }
}
