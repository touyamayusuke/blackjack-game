<?php

declare(strict_types=1);

namespace Blackjack\Tests\Support;

use Blackjack\Io\Output;

/**
 * テスト用の Output。出力された行をすべて溜め込み、後から検証できるようにする。
 */
final class SpyOutput implements Output
{
    private array $lines = [];

    public function writeLine(string $line): void
    {
        $this->lines[] = $line;
    }

    /**
     * これまでに出力された行を返す。
     */
    public function lines(): array
    {
        return $this->lines;
    }
}
