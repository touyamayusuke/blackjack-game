<?php

declare(strict_types=1);

namespace Blackjack\Tests\Support;

use Blackjack\Io\Input;

/**
 * テスト用の Input。あらかじめ積んだ応答を readLine() で順に返す。
 */
final class DummyInput implements Input
{
    public function __construct(private array $responses = [])
    {
    }

    public function readLine(): string
    {
        if ($this->responses === []) {
            throw new \RuntimeException('DummyInput has no more queued responses.');
        }

        return array_shift($this->responses);
    }
}
