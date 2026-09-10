<?php

declare(strict_types=1);

namespace Blackjack\Web;

/**
 * 進行中の Table を PHP のセッションに置く GameStore 実装。
 *
 * 仕組み:
 *  - $_SESSION に入れた値は、リクエスト終了時に PHP が自動でファイルへ保存し、
 *    次のリクエストの session_start() で自動で復元してくれる。
 *  - よってここでは $_SESSION[キー] に Table を出し入れするだけでよい
 *    （serialize / unserialize を自分で書く必要はない）。
 *
 * 前提:
 *  - 呼び出し側（public/index.php）で session_start() 済みであること。
 *  - session_start() より前に Table クラスがオートロード可能であること
 *    （index.php は require autoload → session_start の順なので OK）。
 *
 * 1セッションにつき同時に持てるゲームは1つ。
 */
final class SessionGameStore implements GameStore
{
    private const SESSION_KEY = 'blackjack_game';

    /**
     * 保存中のゲームを返す。無ければ（または壊れていれば）null。
     */
    public function load(): ?Table
    {
        $game = $_SESSION[self::SESSION_KEY] ?? null;

        // 何も無い / 別物が入っていた場合に備えて型を確認してから返す
        return $game instanceof Table ? $game : null;
    }

    /**
     * ゲームを保存する（既存があれば上書き）。
     */
    public function save(Table $table): void
    {
        $_SESSION[self::SESSION_KEY] = $table;
    }

    /**
     * 保存中のゲームを破棄する。
     */
    public function clear(): void
    {
        unset($_SESSION[self::SESSION_KEY]);
    }
}
