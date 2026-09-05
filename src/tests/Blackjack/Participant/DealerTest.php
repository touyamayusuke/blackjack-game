<?php

declare(strict_types=1);

namespace Blackjack\Tests\Participant;

use PHPUnit\Framework\TestCase;
use Blackjack\Participant\Dealer;
use Blackjack\Hand;
use Blackjack\Card\Card;
use Blackjack\Card\Rank;
use Blackjack\Card\Suit;

final class DealerTest extends TestCase
{
    public function testWantsToNewCardBelowSeventeen(): void
    {
        $hand = new Hand();
        $hand->add(new Card(Suit::HEART, Rank::TEN));
        $hand->add(new Card(Suit::HEART, Rank::SIX));
        $dealer = new Dealer($hand);
        self::assertTrue($dealer->wantsToNewCard());
    }

    public function testDoesNotWantToNewCardAtSeventeen(): void
    {
        $hand = new Hand();
        $hand->add(new Card(Suit::HEART, Rank::TEN));
        $hand->add(new Card(Suit::HEART, Rank::SEVEN));
        $dealer = new Dealer($hand);
        self::assertFalse($dealer->wantsToNewCard());
    }

    public function testDoesNotWantToNewCardAboveSeventeen(): void
    {
        $hand = new Hand();
        $hand->add(new Card(Suit::HEART, Rank::TEN));
        $hand->add(new Card(Suit::HEART, Rank::EIGHT));
        $dealer = new Dealer($hand);
        self::assertFalse($dealer->wantsToNewCard());
    }
}
