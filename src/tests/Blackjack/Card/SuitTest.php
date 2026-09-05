<?php

declare(strict_types=1);

namespace Blackjack\Tests\Card;

use PHPUnit\Framework\TestCase;
use Blackjack\Card\Suit;

final class SuitTest extends TestCase
{
    public function testLabelReturnsJapaneseName(): void
    {
        self::assertSame('ハート', Suit::HEART->label());
        self::assertSame('クラブ', Suit::CLUB->label());
        self::assertSame('ダイヤ', Suit::DIAMOND->label());
        self::assertSame('スペード', Suit::SPADE->label());
    }
}
