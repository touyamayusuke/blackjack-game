<?php

declare(strict_types=1);

namespace Blackjack\Card;

/**
 * トランプのスート（ハート / クラブ / ダイヤ / スペード）。
 */
enum Suit
{
    case HEART;
    case CLUB;
    case DIAMOND;
    case SPADE;

    /**
     * 表示用の日本語名（例: 「ハート」）を返す。
     */
    public function label(): string
    {
        return match ($this) {
            self::HEART => 'ハート',
            self::CLUB => 'クラブ',
            self::DIAMOND => 'ダイヤ',
            self::SPADE => 'スペード',
        };
    }
}
