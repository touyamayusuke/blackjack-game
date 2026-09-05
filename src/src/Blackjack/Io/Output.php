<?php

declare(strict_types=1);

namespace Blackjack\Io;

/**
 * 出力の抽象。テストでは出力行を溜め込む偽実装を差し込む。
 */
interface Output
{
    /**
     * 1行出力する。
     */
    public function writeLine(string $line): void;
}
