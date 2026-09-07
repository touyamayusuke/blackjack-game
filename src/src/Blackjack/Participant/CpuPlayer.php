<?php

declare(strict_types=1);

namespace Blackjack\Participant;

/**
 * CPU が自動で操作する。
 * 得点が 17 未満の間はカードを引き続ける。
 */
final class CpuPlayer extends Participant
{
    public function wantsToNewCard(): bool
    {
        return $this->score() < 17;
    }
}
