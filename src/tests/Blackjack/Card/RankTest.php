<?php

declare(strict_types=1);

namespace Blackjack\Tests\Card;

use PHPUnit\Framework\TestCase;
use Blackjack\Card\Rank;

final class RankTest extends TestCase
{
    public function testNumberCardsScoreTheirFaceValue(): void
    {
        self::assertSame(2, Rank::TWO->point());
        self::assertSame(3, Rank::THREE->point());
        self::assertSame(4, Rank::FOUR->point());
        self::assertSame(5, Rank::FIVE->point());
        self::assertSame(6, Rank::SIX->point());
        self::assertSame(7, Rank::SEVEN->point());
        self::assertSame(8, Rank::EIGHT->point());
        self::assertSame(9, Rank::NINE->point());
    }

    public function testTenAndFaceCardsScoreTen(): void
    {
        self::assertSame(10, Rank::TEN->point());
        self::assertSame(10, Rank::JACK->point());
        self::assertSame(10, Rank::QUEEN->point());
        self::assertSame(10, Rank::KING->point());
    }

    public function testAceBasePointIsOne(): void
    {
        self::assertSame(1, Rank::ACE->point());
    }

    public function testIsAceReturnsTrueOnlyForAce(): void
    {
        self::assertTrue(Rank::ACE->isAce());
        self::assertFalse(Rank::TWO->isAce());
        self::assertFalse(Rank::TEN->isAce());
        self::assertFalse(Rank::JACK->isAce());
        self::assertFalse(Rank::QUEEN->isAce());
        self::assertFalse(Rank::KING->isAce());
    }

    public function testLabelReturnsDisplayString(): void
    {
        self::assertSame('A', Rank::ACE->label());
        self::assertSame('7', Rank::SEVEN->label());
        self::assertSame('10', Rank::TEN->label());
        self::assertSame('J', Rank::JACK->label());
        self::assertSame('Q', Rank::QUEEN->label());
        self::assertSame('K', Rank::KING->label());
    }
}
