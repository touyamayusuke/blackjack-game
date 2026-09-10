<?php

declare(strict_types=1);

namespace Blackjack\Web;

/**
 * 進行中の Table を HTTP リクエストをまたいで保存・復元するための抽象。
 *
 * コンソール版には無かった関心事。1プロセスで完結していたゲームを、
 * 「POST /hit のたびに別リクエスト」で進めるために必要になる。
 *
 * 実装の候補:
 *  - {@see SessionGameStore}         … $_SESSION に serialize して置く。1セッション1ゲーム。まずはこれ。
 *  - （発展）イベントソーシング版      … {seed, playerCount, actions[]} だけ保存し、
 *                                       毎回シード付き Deck を作り直して actions を再適用して復元する。
 *
 * テストでは配列に持つだけの偽実装を差し込めるよう、インターフェースにしておく。
 */
interface GameStore
{
    /**
     * 現在保存されているゲームを返す。無ければ null。
     */
    public function load(): ?Table;

    /**
     * ゲームを保存する（既存があれば上書き）。
     */
    public function save(Table $table): void;

    /**
     * 保存中のゲームを破棄する（「新規ゲーム」開始時などに使う）。
     */
    public function clear(): void;
}
