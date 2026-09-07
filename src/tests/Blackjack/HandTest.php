<?php

declare(strict_types=1);

namespace Blackjack\Tests;

use PHPUnit\Framework\TestCase;
use Blackjack\Card\Card;
use Blackjack\Card\Rank;
use Blackjack\Card\Suit;
use Blackjack\Hand;

final class HandTest extends TestCase
{
    public function testAddAppendsCard(): void
    {
        $hand = new Hand();
        $card1 = new Card(Suit::HEART, Rank::SEVEN);
        $card2 = new Card(Suit::SPADE, Rank::KING);

        $hand->add($card1);
        $hand->add($card2);

        self::assertSame([$card1, $card2], $hand->cards());
    }

    public function testScoreSumsCardPoints(): void
    {
        $hand = new Hand();
        $hand->add(new Card(Suit::HEART, Rank::SEVEN)); // 7 points
        $hand->add(new Card(Suit::SPADE, Rank::KING)); // 10 points

        self::assertSame(17, $hand->score());
    }

    public function testIsBustWhenOverTwentyOne(): void
    {
        $hand = new Hand();
        $hand->add(new Card(Suit::HEART, Rank::TEN)); // 10 points
        $hand->add(new Card(Suit::SPADE, Rank::KING)); // 10 points
        $hand->add(new Card(Suit::DIAMOND, Rank::TWO)); // 2 points

        self::assertTrue($hand->isBust());
    }

    public function testIsNotBustAtTwentyOneOrBelow(): void
    {
        $hand = new Hand();
        $hand->add(new Card(Suit::HEART, Rank::TEN)); // 10 points
        $hand->add(new Card(Suit::SPADE, Rank::SEVEN)); // 7 points
        $hand->add(new Card(Suit::DIAMOND, Rank::FOUR)); // 4 points

        self::assertFalse($hand->isBust());
    }

    public function testAceCountsAsElevenWhenItFitsUnderTwentyOne(): void
    {
        $hand = new Hand();
        $hand->add(new Card(Suit::HEART, Rank::ACE));
        $hand->add(new Card(Suit::SPADE, Rank::KING));

        self::assertSame(21, $hand->score());
    }

    public function testAceCountsAsOneWhenElevenWouldBust(): void
    {
        $hand = new Hand();
        $hand->add(new Card(Suit::HEART, Rank::ACE));
        $hand->add(new Card(Suit::SPADE, Rank::KING));
        $hand->add(new Card(Suit::DIAMOND, Rank::FIVE));

        self::assertSame(16, $hand->score());
    }

    public function testOnlyOneAceCountsAsElevenWhenTwoAcesPresent(): void
    {
        $hand = new Hand();
        $hand->add(new Card(Suit::HEART, Rank::ACE));
        $hand->add(new Card(Suit::SPADE, Rank::ACE));
        $hand->add(new Card(Suit::DIAMOND, Rank::NINE));

        self::assertSame(21, $hand->score());
    }

    public function testAllAcesCountAsOneWhenElevenWouldAlwaysBust(): void
    {
        $hand = new Hand();
        $hand->add(new Card(Suit::HEART, Rank::ACE));
        $hand->add(new Card(Suit::SPADE, Rank::ACE));
        $hand->add(new Card(Suit::DIAMOND, Rank::ACE));
        $hand->add(new Card(Suit::CLUB, Rank::ACE));
        $hand->add(new Card(Suit::HEART, Rank::KING));

        self::assertSame(14, $hand->score());
    }
}
