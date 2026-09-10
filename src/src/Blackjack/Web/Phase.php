<?php

declare(strict_types=1);

namespace Blackjack\Web;

/**
 * ゲームの進行フェーズ。
 *
 * コンソール版 Game::play() が「配札 → プレイヤーのターン → ディーラーのターン → 決着」
 * という一直線の処理だったものを、HTTP のリクエストをまたいで進められるよう
 * 「今どのフェーズか」を明示的に持たせるための enum。
 *
 * 遷移:
 *   PLAYER_TURN --(全プレイヤーが引き終える)--> DEALER_TURN --(ディーラーが引き終える)--> FINISHED
 *   ※ 生存者が誰もいなければ DEALER_TURN をスキップして FINISHED へ行ってもよい（実装者判断）
 */
enum Phase
{
    /** 各プレイヤー（人間＋CPU）が引くかどうかを決めている最中。 */
    case PLAYER_TURN;

    /** 全プレイヤーが引き終え、ディーラーが 17 以上になるまで引いている最中。 */
    case DEALER_TURN;

    /** 決着済み。手札・得点・勝敗をすべて公開してよい。 */
    case FINISHED;
}
