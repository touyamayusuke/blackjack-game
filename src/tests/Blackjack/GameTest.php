<?php

declare(strict_types=1);

namespace Blackjack\Tests;

use PHPUnit\Framework\TestCase;
use Blackjack\Card\Card;
use Blackjack\Card\Rank;
use Blackjack\Card\Suit;
use Blackjack\Deck;
use Blackjack\Game;
use Blackjack\Judge;
use Blackjack\Tests\Support\FakeInput;
use Blackjack\Tests\Support\SpyOutput;

final class GameTest extends TestCase
{
    public function testPlaysFullGameWithFixedDeckAndInput(): void
    {
        // docs/要件.md のコンソール例と同じ状況を、山札の順番を固定して再現する。
        $deck = new Deck([
            new Card(Suit::HEART, Rank::SEVEN),  // プレイヤー1枚目
            new Card(Suit::DIAMOND, Rank::QUEEN), // ディーラー1枚目（表）
            new Card(Suit::CLUB, Rank::EIGHT),    // プレイヤー2枚目 → 合計15
            new Card(Suit::DIAMOND, Rank::TWO),   // ディーラー2枚目（伏せ）
            new Card(Suit::SPADE, Rank::FIVE),    // プレイヤーが引く → 合計20
            new Card(Suit::HEART, Rank::KING),    // ディーラーが引く → 合計22（バースト）
        ]);
        $input = new FakeInput(['Y', 'N']);
        $output = new SpyOutput();

        $game = new Game($deck, $input, $output, new Judge());
        $game->play();

        self::assertSame([
            'ブラックジャックを開始します。',
            'あなたの引いたカードはハートの7です。',
            'あなたの引いたカードはクラブの8です。',
            'ディーラーの引いたカードはダイヤのQです。',
            'ディーラーの引いた2枚目のカードはわかりません。',
            'あなたの現在の得点は15です。',
            'カードを引きますか？（Y/N）',
            'あなたの引いたカードはスペードの5です。',
            'あなたの現在の得点は20です。',
            'カードを引きますか？（Y/N）',
            'ディーラーの引いた2枚目のカードはダイヤの2でした。',
            'ディーラーの現在の得点は12です。',
            'ディーラーの引いたカードはハートのKです。',
            'ディーラーの現在の得点は22です。',
            'あなたの得点は20です。',
            'ディーラーの得点は22です。',
            'あなたの勝ちです！',
            'ブラックジャックを終了します。',
        ], $output->lines());
    }

    public function testPlayerBustEndsTheGameImmediately(): void
    {
        $deck = new Deck([
            new Card(Suit::SPADE, Rank::TEN),  // プレイヤー1枚目
            new Card(Suit::CLUB, Rank::NINE),  // ディーラー1枚目（表）
            new Card(Suit::SPADE, Rank::JACK), // プレイヤー2枚目 → 合計20
            new Card(Suit::CLUB, Rank::TWO),   // ディーラー2枚目（伏せ、公開されないはず）
            new Card(Suit::SPADE, Rank::FIVE), // プレイヤーが引く → 合計25（バースト）
        ]);
        $input = new FakeInput(['Y']);
        $output = new SpyOutput();

        $game = new Game($deck, $input, $output, new Judge());
        $game->play();

        self::assertSame([
            'ブラックジャックを開始します。',
            'あなたの引いたカードはスペードの10です。',
            'あなたの引いたカードはスペードのJです。',
            'ディーラーの引いたカードはクラブの9です。',
            'ディーラーの引いた2枚目のカードはわかりません。',
            'あなたの現在の得点は20です。',
            'カードを引きますか？（Y/N）',
            'あなたの引いたカードはスペードの5です。',
            'あなたの現在の得点は25です。',
            'あなたの得点は25です。',
            'ディーラーの得点は11です。',
            'あなたの負けです…。',
            'ブラックジャックを終了します。',
        ], $output->lines());
    }
}
