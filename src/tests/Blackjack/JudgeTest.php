<?php

declare(strict_types=1);

namespace Blackjack\Tests;

use PHPUnit\Framework\TestCase;
use Blackjack\Card\Card;
use Blackjack\Card\Rank;
use Blackjack\Card\Suit;
use Blackjack\Hand;
use Blackjack\Judge;
use Blackjack\Outcome;
use Blackjack\Participant\Dealer;
use Blackjack\Participant\Player;
use Blackjack\Tests\Support\FakeInput;

final class JudgeTest extends TestCase
{
    public function testPlayerBustLoses(): void
    {
        $playerHand = new Hand();
        $playerHand->add(new Card(Suit::HEART, Rank::TEN));
        $playerHand->add(new Card(Suit::HEART, Rank::SIX));
        $playerHand->add(new Card(Suit::HEART, Rank::SEVEN)); // 10+6+7=23 → バースト
        $player = new Player('テストプレイヤー', $playerHand, new FakeInput([]));

        $dealerHand = new Hand();
        $dealerHand->add(new Card(Suit::SPADE, Rank::TEN));
        $dealerHand->add(new Card(Suit::SPADE, Rank::SEVEN)); // 17（バーストしていない）
        $dealer = new Dealer($dealerHand);

        $judge = new Judge();

        self::assertSame(Outcome::LOSE, $judge->decide($player, $dealer));
    }

    public function testDealerBustWhilePlayerAliveWins(): void
    {
        $playerHand = new Hand();
        $playerHand->add(new Card(Suit::HEART, Rank::TEN));
        $playerHand->add(new Card(Suit::HEART, Rank::SIX));
        $player = new Player('テストプレイヤー', $playerHand, new FakeInput([]));

        $dealerHand = new Hand();
        $dealerHand->add(new Card(Suit::SPADE, Rank::TEN));
        $dealerHand->add(new Card(Suit::SPADE, Rank::SIX));
        $dealerHand->add(new Card(Suit::SPADE, Rank::SEVEN));
        $dealer = new Dealer($dealerHand);

        $judge = new Judge();

        self::assertSame(Outcome::WIN, $judge->decide($player, $dealer));
    }

    public function testPlayerCloserToTwentyOneWins(): void
    {
        $playerHand = new Hand();
        $playerHand->add(new Card(Suit::HEART, Rank::TEN));
        $playerHand->add(new Card(Suit::HEART, Rank::SIX));
        $playerHand->add(new Card(Suit::HEART, Rank::FOUR));
        $player = new Player('テストプレイヤー', $playerHand, new FakeInput([]));

        $dealerHand = new Hand();
        $dealerHand->add(new Card(Suit::SPADE, Rank::TEN));
        $dealerHand->add(new Card(Suit::SPADE, Rank::SIX));
        $dealer = new Dealer($dealerHand);

        $judge = new Judge();

        self::assertSame(Outcome::WIN, $judge->decide($player, $dealer));
    }

    public function testDealerCloserToTwentyOneMakesPlayerLose(): void
    {
        $playerHand = new Hand();
        $playerHand->add(new Card(Suit::HEART, Rank::TEN));
        $playerHand->add(new Card(Suit::HEART, Rank::FIVE));
        $player = new Player('テストプレイヤー', $playerHand, new FakeInput([]));

        $dealerHand = new Hand();
        $dealerHand->add(new Card(Suit::SPADE, Rank::TEN));
        $dealerHand->add(new Card(Suit::SPADE, Rank::SIX));
        $dealer = new Dealer($dealerHand);

        $judge = new Judge();

        self::assertSame(Outcome::LOSE, $judge->decide($player, $dealer));
    }

    public function testEqualScoreIsDraw(): void
    {
        $playerHand = new Hand();
        $playerHand->add(new Card(Suit::HEART, Rank::TEN));
        $playerHand->add(new Card(Suit::HEART, Rank::FIVE));
        $player = new Player('テストプレイヤー', $playerHand, new FakeInput([]));

        $dealerHand = new Hand();
        $dealerHand->add(new Card(Suit::SPADE, Rank::TEN));
        $dealerHand->add(new Card(Suit::SPADE, Rank::FIVE));
        $dealer = new Dealer($dealerHand);

        $judge = new Judge();

        self::assertSame(Outcome::DRAW, $judge->decide($player, $dealer));
    }
}
