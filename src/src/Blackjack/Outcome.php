<?php

declare(strict_types=1);

namespace Blackjack;

/**
 * プレイヤーから見た勝負の結果。
 */
enum Outcome
{
    case WIN;
    case LOSE;
    case DRAW;
}
