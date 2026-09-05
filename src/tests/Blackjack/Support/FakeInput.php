<?php

declare(strict_types=1);

namespace Blackjack\Tests\Support;

use Blackjack\Io\Input;

/**
 * テスト用の Input。あらかじめ積んだ応答を readLine() で順に返す。
 */
final class FakeInput implements Input
{
    /**
     * @param list<string> $responses readLine() が先頭から順に返す応答
     */
    public function __construct(private array $responses = [])
    {
    }

    public function readLine(): string
    {
        if ($this->responses === []) {
            throw new \RuntimeException('FakeInput has no more queued responses.');
        }

        return array_shift($this->responses);
    }
}
