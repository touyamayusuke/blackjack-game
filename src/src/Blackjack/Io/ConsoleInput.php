<?php

declare(strict_types=1);

namespace Blackjack\Io;

/**
 * 標準入力から1行読み取る Input 実装。
 */
final class ConsoleInput implements Input
{
    public function readLine(): string
    {
        $line = fgets(STDIN);
        if ($line === false) {
            throw new \RuntimeException('標準入力の読み取りに失敗しました。');
        }
        return trim($line);
    }
}
