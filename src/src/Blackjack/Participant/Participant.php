<?php

declare(strict_types=1);

namespace Blackjack\Participant;

use Blackjack\Card\Card;
use Blackjack\Hand;

/**
 * ゲーム参加者（プレイヤー / ディーラー）の共通の親。
 */
abstract class Participant
{
    public function __construct(
        private readonly string $name,
        private readonly Hand $hand,
    ) {
    }

    /**
     * 表示用の名前を返す。
     */
    public function name(): string
    {
        return $this->name;
    }

    /**
     * この参加者の手札を返す。
     */
    public function hand(): Hand
    {
        return $this->hand;
    }

    /**
     * カードを1枚受け取る（手札に加える）。
     */
    public function receive(Card $card): void
    {
        $this->hand->add($card);
    }

    /**
     * 現在の得点を返す（手札に委譲）。
     */
    public function score(): int
    {
        return $this->hand->score();
    }

    /**
     * バーストしているか。
     */
    public function isBust(): bool
    {
        return $this->hand->isBust();
    }

    /**
     * もう1枚引くかどうか。実装は各サブクラスに任せる。
     */
    abstract public function wantsToNewCard(): bool;
}
