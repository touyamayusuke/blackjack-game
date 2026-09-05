<?php

declare(strict_types=1);

namespace Blackjack\Io;

/**
 * 標準出力へ書き出す Output 実装。
 */
final class ConsoleOutput implements Output
{
    public function writeLine(string $line): void
    {
        echo $line . PHP_EOL;
    }
}
