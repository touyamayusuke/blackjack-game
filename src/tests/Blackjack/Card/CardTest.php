<?php

declare(strict_types=1);

namespace Blackjack\Tests\Card;

use PHPUnit\Framework\TestCase;
use Blackjack\Card\Card;
use Blackjack\Card\Rank;
use Blackjack\Card\Suit;

final class CardTest extends TestCase
{
    public function testLabelCombinesSuitAndRank(): void
    {
        self::assertSame('ハートの7', (new Card(Suit::HEART, Rank::SEVEN))->label());
    }

    public function testPointDelegatesToRank(): void
    {
        self::assertSame(10, (new Card(Suit::SPADE, Rank::KING))->point());
    }
}
