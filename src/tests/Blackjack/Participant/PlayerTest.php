<?php

declare(strict_types=1);

namespace Blackjack\Tests\Participant;

use PHPUnit\Framework\TestCase;
use Blackjack\Participant\Player;
use Blackjack\Hand;
use Blackjack\Tests\Support\FakeInput;

final class PlayerTest extends TestCase
{
    public function testWantsToNewCardWhenInputIsYes(): void
    {
        $player = new Player('テストプレイヤー', new Hand(), new FakeInput(['Y']));
        self::assertTrue($player->wantsToNewCard());
    }

    public function testWantsToNewCardWhenInputIsLowercaseYes(): void
    {
        $player = new Player('テストプレイヤー', new Hand(), new FakeInput(['y']));
        self::assertTrue($player->wantsToNewCard());
    }

    public function testDoesNotWantToNewCardWhenInputIsNo(): void
    {
        $player = new Player('テストプレイヤー', new Hand(), new FakeInput(['N']));
        self::assertFalse($player->wantsToNewCard());
    }
}
