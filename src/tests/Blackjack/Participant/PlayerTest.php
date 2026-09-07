<?php

declare(strict_types=1);

namespace Blackjack\Tests\Participant;

use PHPUnit\Framework\TestCase;
use Blackjack\Participant\Player;
use Blackjack\Hand;
use Blackjack\Tests\Support\DummyInput;

final class PlayerTest extends TestCase
{
    public function testWantsToNewCardWhenInputIsYes(): void
    {
        $player = new Player('テストプレイヤー', new Hand(), new DummyInput(['Y']));
        self::assertTrue($player->wantsToNewCard());
    }

    public function testWantsToNewCardWhenInputIsLowercaseYes(): void
    {
        $player = new Player('テストプレイヤー', new Hand(), new DummyInput(['y']));
        self::assertTrue($player->wantsToNewCard());
    }

    public function testDoesNotWantToNewCardWhenInputIsNo(): void
    {
        $player = new Player('テストプレイヤー', new Hand(), new DummyInput(['N']));
        self::assertFalse($player->wantsToNewCard());
    }
}
