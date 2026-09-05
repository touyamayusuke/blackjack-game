<?php

declare(strict_types=1);

namespace Blackjack\Participant;

use Blackjack\Hand;

/**
 * ディーラー。CPU が自動で操作する。
 * 得点が 17 未満の間はカードを引き続ける。
 */
final class Dealer extends Participant
{
    public function __construct(Hand $hand)
    {
        parent::__construct('ディーラー', $hand);
    }

    public function wantsToNewCard(): bool
    {
        return $this->score() < 17;
    }
}
