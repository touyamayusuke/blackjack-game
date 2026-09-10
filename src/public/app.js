"use strict";

/**
 * フロントの制御。
 *
 * やることは1つだけ:「API を呼ぶ → 返ってきた view で画面を作り直す」。
 * ゲームの状態はサーバ（セッション）が持つので、ここでは覚えておかない。
 *
 * view の形（Table::view() が返すもの）:
 *   {
 *     phase: 'PLAYER_TURN' | 'DEALER_TURN' | 'FINISHED',
 *     currentPlayer: 'あなた' | null,
 *     participants: [ { name, cards: string[], score: number|null, bust: boolean }, ... ],
 *     results: null | [ { name, outcome: 'WIN'|'LOSE'|'DRAW' } ],
 *     log: string[]
 *   }
 */

/** document.getElementById の短縮。 */
const $ = (id) => document.getElementById(id);

const OUTCOME_TEXT = { WIN: "勝ち", LOSE: "負け", DRAW: "引き分け" };

/**
 * API を呼ぶ。成功なら JSON を返し、失敗なら Error を投げる。
 *
 * @param {"GET"|"POST"} method
 * @param {string} path
 * @param {object} [body]  指定すると JSON として送る
 */
async function api(method, path, body) {
  const res = await fetch(path, {
    method,
    headers: body === undefined ? {} : { "Content-Type": "application/json" },
    body: body === undefined ? undefined : JSON.stringify(body),
  });

  // index.php は常に JSON を返す作りなので、まず JSON として読む。
  const data = await res.json().catch(() => ({}));

  if (!res.ok) {
    throw new Error(data.error || `通信エラー (${res.status})`);
  }
  return data;
}

/** エラー文言を画面上部に出す。 */
function showError(message) {
  const el = $("error");
  el.textContent = message;
  el.hidden = false;
}

/** エラー表示を消す。 */
function clearError() {
  $("error").hidden = true;
}

/**
 * view を受け取って画面全体を作り直す。
 */
function render(view) {
  // --- ログ ---
  $("log").textContent = view.log.join("\n");

  // --- 対戦テーブル（毎回まっさらから組み直す）---
  const table = $("table");
  table.replaceChildren();

  for (const p of view.participants) {
    const row = document.createElement("div");
    row.className = "participant" + (p.bust ? " participant--bust" : "");

    const name = document.createElement("div");
    name.className = "participant__name";
    // score が null（ディーラーの伏せ中）なら数字を出さない
    name.textContent = p.score === null ? p.name : `${p.name}（${p.score}）`;
    row.appendChild(name);

    const hand = document.createElement("div");
    hand.className = "hand";
    for (const label of p.cards) {
      const card = document.createElement("span");
      card.className = "card" + (label === "?" ? " card--hidden" : "");
      card.textContent = label;
      hand.appendChild(card);
    }
    row.appendChild(hand);

    table.appendChild(row);
  }
  table.hidden = false;

  // --- 操作ボタン ---
  // 押していいのは「自分の手番」だけ
  const canAct = view.phase === "PLAYER_TURN" && view.currentPlayer === "あなた";
  $("actions").hidden = view.phase === "FINISHED";
  $("hit").disabled = !canAct;
  $("stand").disabled = !canAct;

  // --- 結果 ---
  if (view.phase === "FINISHED" && Array.isArray(view.results)) {
    const body = $("result-body");
    body.replaceChildren();
    for (const r of view.results) {
      const line = document.createElement("p");
      line.className = "result-line result-line--" + r.outcome.toLowerCase();
      line.textContent = `${r.name}：${OUTCOME_TEXT[r.outcome] ?? r.outcome}`;
      body.appendChild(line);
    }
    $("result").hidden = false;
  } else {
    $("result").hidden = true;
  }
}

/** 「ゲーム開始」: 新規ゲームを作って画面へ。 */
async function startGame() {
  clearError();
  try {
    const playerCount = Number($("player-count").value);
    const view = await api("POST", "/api/games", { playerCount });
    $("setup").hidden = true;
    render(view);
  } catch (e) {
    showError(e.message);
  }
}

/** hit / stand の共通処理。 */
async function sendAction(path) {
  clearError();
  try {
    render(await api("POST", path));
  } catch (e) {
    showError(e.message);
  }
}

/** 「もう一度」: セットアップ画面に戻すだけ（次の「開始」で新規ゲームになる）。 */
function backToSetup() {
  clearError();
  $("result").hidden = true;
  $("table").hidden = true;
  $("actions").hidden = true;
  $("setup").hidden = false;
}

window.addEventListener("DOMContentLoaded", async () => {
  $("start").addEventListener("click", startGame);
  $("hit").addEventListener("click", () => sendAction("/api/games/current/hit"));
  $("stand").addEventListener("click", () => sendAction("/api/games/current/stand"));
  $("restart").addEventListener("click", backToSetup);

  // 進行中のゲームがあれば続きから表示（無ければ 404 なのでセットアップのまま）
  try {
    const view = await api("GET", "/api/games/current");
    $("setup").hidden = true;
    render(view);
  } catch {
    /* 進行中ゲームなし: 何もしない */
  }
});
