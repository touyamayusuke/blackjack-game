<?php

declare(strict_types=1);

namespace Blackjack\Tests\Web;

use PHPUnit\Framework\TestCase;
use Blackjack\Card\Card;
use Blackjack\Card\Rank;
use Blackjack\Card\Suit;
use Blackjack\Deck;
use Blackjack\Judge;
use Blackjack\Web\Table;

/**
 * Table（Web 版の状態機械）のテスト。
 *
 * 方針は GameTest と同じ:
 *  - Deck に固定順のカード配列を渡して決定論的にする
 *  - view() が返す配列を assertSame で丸ごと突き合わせる
 *
 * 配札順（Table 実装と合わせること）:
 *   players[0] 1枚目 → dealer 1枚目 → players[0] 2枚目 → dealer 2枚目
 *   （複数人なら players 全員に1枚 → dealer 1枚 → players 全員に2枚目 → dealer 2枚目）
 *
 * TDD の順序: 上から1メソッドずつ「テストを書く → Table を実装 → 緑」で進める。
 */
final class TableTest extends TestCase
{
    /**
     * 生成直後: 各自2枚配られ、PLAYER_TURN で人間の手番。
     * ディーラーの2枚目は '?'、ディーラーの score は null で伏せられている。
     */
    public function testInitialDealPutsTwoCardsEachAndHidesDealerHoleCard(): void
    {
        self::markTestIncomplete('未実装');
    }

    /**
     * hit(): 現在プレイヤーに1枚増え、得点が更新され、手番は変わらない（バーストしない限り）。
     */
    public function testHitAddsCardToCurrentPlayer(): void
    {
        self::markTestIncomplete('未実装');
    }

    /**
     * hit() でバーストしたら、その手番は終了して次へ進む。
     * 人間1人なら DEALER_TURN or FINISHED へ。
     */
    public function testHitToBustEndsThatPlayersTurn(): void
    {
        self::markTestIncomplete('未実装');
    }

    /**
     * stand(): 引かずに手番を次へ渡す。
     */
    public function testStandMovesToNextTurn(): void
    {
        self::markTestIncomplete('未実装');
    }

    /**
     * CPU プレイヤーは hit/stand を待たず、17 以上になるまで自動で引いてから次へ。
     * （playerCount=2 で人間が stand した直後に CPU1 が自動消化される様子を検証）
     */
    public function testCpuPlayerHitsAutomaticallyUntilSeventeen(): void
    {
        self::markTestIncomplete('未実装');
    }

    /**
     * 生存者がいれば、DEALER_TURN でディーラーが 17 以上まで自動で引き、
     * 伏せカードが公開される。
     */
    public function testDealerDrawsUntilSeventeenWhenSomeoneSurvives(): void
    {
        self::markTestIncomplete('未実装');
    }

    /**
     * 全プレイヤーがバーストしたら、ディーラーは引かずに FINISHED。
     */
    public function testDealerDoesNotDrawWhenEveryoneBusts(): void
    {
        self::markTestIncomplete('未実装');
    }

    /**
     * FINISHED: 全カード・全得点が公開され、results が Judge::decide() と一致する。
     */
    public function testFinishedViewRevealsEverythingAndReportsResults(): void
    {
        self::markTestIncomplete('未実装');
    }

    /**
     * FINISHED 後に hit()/stand() を呼ぶと例外。
     */
    public function testActionsAfterFinishThrow(): void
    {
        self::markTestIncomplete('未実装');
    }

    /**
     * 人数が 1〜3 の範囲外なら生成時に例外。
     */
    public function testInvalidPlayerCountThrows(): void
    {
        self::markTestIncomplete('未実装');
    }

    /**
     * 固定順デッキを作るヘルパ（必要になったら中身を埋める）。
     *
     * @param list<Card> $cards
     */
    private function fixedDeck(array $cards): Deck
    {
        return new Deck($cards);
    }
}
