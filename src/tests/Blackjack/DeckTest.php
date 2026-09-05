<?php

declare(strict_types=1);

namespace Blackjack\Tests;

use PHPUnit\Framework\TestCase;
use Blackjack\Deck;

final class DeckTest extends TestCase
{
    public function testStandardDeckHasFiftyTwoUniqueCards(): void
    {
        $deck = new Deck();
        $cards = $deck->cards();
        self::assertCount(52, $cards);
    }

    public function testDrawReturnsTopCardAndReducesCount(): void
    {
        $deck = new Deck();
        $deck->draw();
        self::assertCount(51, $deck->cards());
    }

    public function testDrawOnEmptyDeckThrows(): void
    {
        $cards = [];
        $emptyDeck = new Deck($cards);
        $this->expectException(\RuntimeException::class);
        $emptyDeck->draw();
    }
}
