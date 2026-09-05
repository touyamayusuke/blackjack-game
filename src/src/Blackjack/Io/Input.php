<?php

declare(strict_types=1);

namespace Blackjack\Io;

/**
 * 入力の抽象。テストでは偽実装を差し込む。
 */
interface Input
{
    /**
     * 1行読み取って返す（前後の空白は実装側で除去する想定）。
     */
    public function readLine(): string;
}
