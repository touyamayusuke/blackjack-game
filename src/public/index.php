<?php

declare(strict_types=1);

/**
 * Web 版ブラックジャックの入口 兼 JSON API。
 *
 * 起動:
 *   php -S localhost:8000 -t public
 *
 * 振り分け:
 *   - /style.css, /app.js など「実在するファイル」  … ビルトインサーバが直接配る（ここには来ない）
 *   - /api/... で始まるパス                        … このファイルが JSON で応答する
 *   - それ以外（/ など）                            … index.html をそのまま返す
 *
 * エンドポイント:
 *   POST /api/games                {"playerCount": 1..3}   新規ゲーム。view を返す
 *   GET  /api/games/current                                現在の view（無ければ 404）
 *   POST /api/games/current/hit                            1枚引いて view（不正な操作は 422）
 *   POST /api/games/current/stand                          引かずに手番を進めて view
 *
 * ステータス:
 *   200 正常（view の JSON）
 *   400 リクエストが不正（人数が範囲外、JSON 壊れ）
 *   404 進行中のゲームが無い / 未知のパス
 *   422 いまできない操作（決着後の hit など）
 *
 * ※ /api/... は Table と SessionGameStore を実装するまで 500（未実装）になる。
 *    「/ を開くと index.html が出る」ところまでは、この時点で動く。
 */

require __DIR__ . '/../vendor/autoload.php';

use Blackjack\Deck;
use Blackjack\Judge;
use Blackjack\Web\SessionGameStore;
use Blackjack\Web\Table;

session_start();

/**
 * どこかで拾い損ねた例外は JSON の 500 にして返す。
 * （フロントは常に JSON が返る前提でいられる。ブラウザで押して試すときのデバッグが楽になる）
 */
set_exception_handler(static function (\Throwable $e): void {
    http_response_code(500);
    header('Content-Type: application/json; charset=UTF-8');
    echo json_encode(
        ['error' => $e->getMessage(), 'where' => $e->getFile() . ':' . $e->getLine()],
        JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES,
    );
});

/**
 * 連想配列を JSON にして返し、その場で処理を終了する。
 */
function respond_json(array $payload, int $status = 200): never
{
    http_response_code($status);
    header('Content-Type: application/json; charset=UTF-8');
    echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

/**
 * リクエストボディ（JSON）を連想配列で返す。空なら空配列、壊れていたら 400 で終了。
 *
 */
function read_json_body(): array
{
    $raw = file_get_contents('php://input');
    if ($raw === false || $raw === '') {
        return [];
    }

    $data = json_decode($raw, true);
    if (!is_array($data)) {
        respond_json(['error' => 'リクエストボディが不正な JSON です。'], 400);
    }

    return $data;
}

/**
 * セッションから進行中ゲームを取り出す。無ければ 404 で終了する。
 */
function load_game_or_404(SessionGameStore $store): Table
{
    $table = $store->load();
    if ($table === null) {
        respond_json(['error' => '進行中のゲームがありません。まず新規ゲームを開始してください。'], 404);
    }

    return $table;
}

// --- リクエストの解釈 ------------------------------------------------------

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';

// --- API 以外はフロント（index.html）を返す ------------------------------

if (!str_starts_with($path, '/api/')) {
    header('Content-Type: text/html; charset=UTF-8');
    readfile(__DIR__ . '/index.html');
    exit;
}

// --- ここから /api/... のルーティング ------------------------------------

$store = new SessionGameStore();

// 新規ゲーム
if ($method === 'POST' && $path === '/api/games') {
    $body = read_json_body();
    $playerCount = (int)($body['playerCount'] ?? 1);

    $deck = new Deck();
    $deck->shuffle();

    try {
        $table = new Table($deck, new Judge(), $playerCount);
    } catch (\InvalidArgumentException $e) {
        respond_json(['error' => $e->getMessage()], 400);
    }

    $store->clear();
    $store->save($table);
    respond_json($table->view());
}

// 現在の状態
if ($method === 'GET' && $path === '/api/games/current') {
    respond_json(load_game_or_404($store)->view());
}

// 1枚引く
if ($method === 'POST' && $path === '/api/games/current/hit') {
    $table = load_game_or_404($store);

    try {
        $table->hit();
    } catch (\RuntimeException $e) {
        respond_json(['error' => $e->getMessage()], 422);
    }

    $store->save($table);
    respond_json($table->view());
}

// 引かずに手番を進める
if ($method === 'POST' && $path === '/api/games/current/stand') {
    $table = load_game_or_404($store);

    try {
        $table->stand();
    } catch (\RuntimeException $e) {
        respond_json(['error' => $e->getMessage()], 422);
    }

    $store->save($table);
    respond_json($table->view());
}

// どれにも当たらなかった
respond_json(['error' => 'そのエンドポイントはありません。'], 404);
