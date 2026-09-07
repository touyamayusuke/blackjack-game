<?php

declare(strict_types=1);

namespace Blackjack\Tests\Participant;

use PHPUnit\Framework\TestCase;
use Blackjack\Participant\CpuPlayer;
use Blackjack\Hand;
use Blackjack\Card\Card;
use Blackjack\Card\Rank;
use Blackjack\Card\Suit;

final class CpuPlayerTest extends TestCase
{
    public function testWantsToNewCardBelowSeventeen(): void
    {
        $hand = new Hand();
        $hand->add(new Card(Suit::HEART, Rank::TEN));
        $hand->add(new Card(Suit::HEART, Rank::SIX));
        $cpuPlayer = new CpuPlayer('CPU1', $hand);
        self::assertTrue($cpuPlayer->wantsToNewCard());
    }

    public function testDoesNotWantToNewCardAtSeventeen(): void
    {
        $hand = new Hand();
        $hand->add(new Card(Suit::HEART, Rank::TEN));
        $hand->add(new Card(Suit::HEART, Rank::SEVEN));
        $cpuPlayer = new CpuPlayer('CPU1', $hand);
        self::assertFalse($cpuPlayer->wantsToNewCard());
    }

    public function testDoesNotWantToNewCardAboveSeventeen(): void
    {
        $hand = new Hand();
        $hand->add(new Card(Suit::HEART, Rank::TEN));
        $hand->add(new Card(Suit::HEART, Rank::EIGHT));
        $cpuPlayer = new CpuPlayer('CPU1', $hand);
        self::assertFalse($cpuPlayer->wantsToNewCard());
    }

    public function testNameIsTheGivenName(): void
    {
        $cpuPlayer = new CpuPlayer('CPU2', new Hand());
        self::assertSame('CPU2', $cpuPlayer->name());
    }
}
