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
use Blackjack\Tests\Support\DummyInput;
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
        $input = new DummyInput(['1', 'Y', 'N']);
        $output = new SpyOutput();

        $game = new Game($deck, $input, $output, new Judge());
        $game->play();

        self::assertSame([
            'ブラックジャックを開始します。',
            'プレイヤーの人数を入力してください。(1-3)',
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
            'あなたは勝ちです！',
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
            new Card(Suit::SPADE, Rank::FIVE), // プレイヤーが引く → 合計25（バースト。引いた札と得点は表示してから終了）
        ]);
        $input = new DummyInput(['1', 'Y']);
        $output = new SpyOutput();

        $game = new Game($deck, $input, $output, new Judge());
        $game->play();

        self::assertSame([
            'ブラックジャックを開始します。',
            'プレイヤーの人数を入力してください。(1-3)',
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
            'あなたは負けです…。',
            'ブラックジャックを終了します。',
        ], $output->lines());
    }

    public function testTwoPlayersDealerStillPlaysWhenOnlyCpuSurvives(): void
    {
        // あなたはバーストするが、CPU1は生き残るのでディーラーはターンを続ける。
        $deck = new Deck([
            new Card(Suit::HEART, Rank::TEN),    // あなた1枚目
            new Card(Suit::SPADE, Rank::NINE),   // CPU1 1枚目
            new Card(Suit::DIAMOND, Rank::SIX),  // ディーラー1枚目（表）
            new Card(Suit::CLUB, Rank::NINE),    // あなた2枚目 → 合計19
            new Card(Suit::HEART, Rank::EIGHT),  // CPU1 2枚目 → 合計17（CPUは引かない。ターン中の出力なし）
            new Card(Suit::SPADE, Rank::FIVE),   // ディーラー2枚目（伏せ） → 合計11
            new Card(Suit::DIAMOND, Rank::TEN),  // あなたが引く → 合計29（バースト）
            new Card(Suit::CLUB, Rank::SEVEN),   // ディーラーが引く → 合計18
        ]);
        $input = new DummyInput(['2', 'Y']);
        $output = new SpyOutput();

        $game = new Game($deck, $input, $output, new Judge());
        $game->play();

        self::assertSame([
            'ブラックジャックを開始します。',
            'プレイヤーの人数を入力してください。(1-3)',
            'あなたの引いたカードはハートの10です。',
            'あなたの引いたカードはクラブの9です。',
            'CPU1の引いたカードはスペードの9です。',
            'CPU1の引いたカードはハートの8です。',
            'ディーラーの引いたカードはダイヤの6です。',
            'ディーラーの引いた2枚目のカードはわかりません。',
            'あなたの現在の得点は19です。',
            'カードを引きますか？（Y/N）',
            'あなたの引いたカードはダイヤの10です。',
            'あなたの現在の得点は29です。',
            'ディーラーの引いた2枚目のカードはスペードの5でした。',
            'ディーラーの現在の得点は11です。',
            'ディーラーの引いたカードはクラブの7です。',
            'ディーラーの現在の得点は18です。',
            'あなたの得点は29です。',
            'CPU1の得点は17です。',
            'ディーラーの得点は18です。',
            'あなたは負けです…。',
            'CPU1は負けです…。',
            'ブラックジャックを終了します。',
        ], $output->lines());
    }

    public function testCpuPlayerAutomaticallyHitsWithoutAskingHit(): void
    {
        // CPU1は「カードを引きますか？」を尋ねられず、17以上になるまで自動で引く（引くたびに札と得点は表示される）。
        $deck = new Deck([
            new Card(Suit::HEART, Rank::SEVEN),   // あなた1枚目
            new Card(Suit::SPADE, Rank::TWO),     // CPU1 1枚目
            new Card(Suit::DIAMOND, Rank::NINE),  // ディーラー1枚目（表）
            new Card(Suit::CLUB, Rank::EIGHT),    // あなた2枚目 → 合計15
            new Card(Suit::DIAMOND, Rank::THREE), // CPU1 2枚目 → 合計5
            new Card(Suit::CLUB, Rank::TWO),      // ディーラー2枚目（伏せ） → 合計11
            new Card(Suit::HEART, Rank::NINE),    // CPU1が引く → 合計14
            new Card(Suit::HEART, Rank::FIVE),    // CPU1が引く → 合計19（引くのをやめる）
            new Card(Suit::SPADE, Rank::SEVEN),   // ディーラーが引く → 合計18
        ]);
        $input = new DummyInput(['2', 'N']);
        $output = new SpyOutput();

        $game = new Game($deck, $input, $output, new Judge());
        $game->play();

        self::assertSame([
            'ブラックジャックを開始します。',
            'プレイヤーの人数を入力してください。(1-3)',
            'あなたの引いたカードはハートの7です。',
            'あなたの引いたカードはクラブの8です。',
            'CPU1の引いたカードはスペードの2です。',
            'CPU1の引いたカードはダイヤの3です。',
            'ディーラーの引いたカードはダイヤの9です。',
            'ディーラーの引いた2枚目のカードはわかりません。',
            'あなたの現在の得点は15です。',
            'カードを引きますか？（Y/N）',
            'CPU1の引いたカードはハートの9です。',
            'CPU1の現在の得点は14です。',
            'CPU1の引いたカードはハートの5です。',
            'CPU1の現在の得点は19です。',
            'ディーラーの引いた2枚目のカードはクラブの2でした。',
            'ディーラーの現在の得点は11です。',
            'ディーラーの引いたカードはスペードの7です。',
            'ディーラーの現在の得点は18です。',
            'あなたの得点は15です。',
            'CPU1の得点は19です。',
            'ディーラーの得点は18です。',
            'あなたは負けです…。',
            'CPU1は勝ちです！',
            'ブラックジャックを終了します。',
        ], $output->lines());
    }
}
