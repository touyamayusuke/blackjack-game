<?php

declare(strict_types=1);

namespace Blackjack\Participant;

use Blackjack\Hand;
use Blackjack\Io\Input;

/**
 * 人間のプレイヤー。カードを引くかどうかは入力で決める。
 */
final class Player extends Participant
{
    public function __construct(
        string $name,
        Hand $hand,
        private readonly Input $input,
    ) {
        parent::__construct($name, $hand);
    }

    public function wantsToNewCard(): bool
    {
        $answer = $this->input->readLine();
        return strtolower($answer) === 'y';
    }
}
