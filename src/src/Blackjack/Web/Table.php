<?php

declare(strict_types=1);

namespace Blackjack\Web;

use Blackjack\Deck;
use Blackjack\Hand;
use Blackjack\Io\NullInput;
use Blackjack\Judge;
use Blackjack\Messages;
use Blackjack\Participant\CpuPlayer;
use Blackjack\Participant\Dealer;
use Blackjack\Participant\Participant;
use Blackjack\Participant\Player;

/**
 * Web 版ブラックジャックの「1ゲーム」を表す状態機械。
 *
 * コンソール版 {@see \Blackjack\Game} は play() の中で入力待ちをしていたため
 * HTTP には乗らなかった。ここでは「入力待ち」をなくし、外部（HTTP のアクション）から
 * hit() / stand() を呼ばれるたびに状態を1歩進める。
 *
 * - 生成時に人数分＋ディーラーへ2枚ずつ配り、PLAYER_TURN で先頭（人間）の手番にする
 * - hit() / stand() で人間の手番を進める。CPU とディーラーは自動で消化する
 * - view() で「いまフロントに見せてよい情報」だけを配列で返す
 */
final class Table
{
    private const HUMAN_NAME = 'あなた';
    private const MIN_PLAYERS = 1;
    private const MAX_PLAYERS = 3;

    /** 添字0が人間（Player）、以降は CPU1, CPU2...（CpuPlayer）。 @var list<Participant> */
    private array $players;

    private Dealer $dealer;

    private Phase $phase;

    /** PLAYER_TURN のとき「いま誰の手番か」を指す players の添字。 */
    private int $currentPlayerIndex = 0;

    /** 画面に出す文言を積んでいく。 @var list<string> */
    private array $log = [];

    /** 決着後の勝敗。FINISHED になるまで null。 @var list<array{name: string, outcome: string}>|null */
    private ?array $results = null;

    /**
     * ゲームを開始する（この時点で配札まで済ませる）。
     *
     * @param int $playerCount 人間を含むプレイヤーの人数（1〜3）。
     * @throws \InvalidArgumentException 人数が範囲外のとき。
     */
    public function __construct(
        private readonly Deck $deck,
        private readonly Judge $judge,
        int $playerCount,
    ) {
        if ($playerCount < self::MIN_PLAYERS || $playerCount > self::MAX_PLAYERS) {
            throw new \InvalidArgumentException(
                sprintf('プレイヤーの人数は %d〜%d で指定してください。', self::MIN_PLAYERS, self::MAX_PLAYERS),
            );
        }

        // 参加者を用意（添字0＝人間、以降＝CPU）
        $this->players = [new Player(self::HUMAN_NAME, new Hand(), new NullInput())];
        for ($i = 1; $i < $playerCount; $i++) {
            $this->players[] = new CpuPlayer('CPU' . $i, new Hand());
        }
        $this->dealer = new Dealer(new Hand());

        $this->log[] = Messages::start();

        // 配札: 全員に1枚 → ディーラーに1枚 → 全員に2枚目 → ディーラーに2枚目
        foreach ($this->players as $player) {
            $player->receive($this->deck->draw());
        }
        $this->dealer->receive($this->deck->draw());
        foreach ($this->players as $player) {
            $player->receive($this->deck->draw());
        }
        $this->dealer->receive($this->deck->draw());

        // 配ったカードをログへ（ディーラーの2枚目は伏せる）
        foreach ($this->players as $player) {
            $this->log[] = Messages::drewCard($player->name(), $player->hand()->cards()[0]->label());
            $this->log[] = Messages::drewCard($player->name(), $player->hand()->cards()[1]->label());
        }
        $this->log[] = Messages::drewCard($this->dealer->name(), $this->dealer->hand()->cards()[0]->label());
        $this->log[] = Messages::hiddenSecondCard($this->dealer->name());
        $this->log[] = Messages::currentScore(self::HUMAN_NAME, $this->players[0]->score());

        // 先頭は必ず人間なので、ここでは入力待ちに入るだけ
        $this->phase = Phase::PLAYER_TURN;
        $this->currentPlayerIndex = 0;
    }

    /**
     * いまの手番のプレイヤー（＝人間）がもう1枚引く。
     *
     * @throws \RuntimeException PLAYER_TURN でないとき。
     */
    public function hit(): void
    {
        if ($this->phase !== Phase::PLAYER_TURN) {
            throw new \RuntimeException('いまはカードを引けません。');
        }

        $player = $this->players[$this->currentPlayerIndex];
        $card = $this->deck->draw();
        $player->receive($card);
        $this->log[] = Messages::drewCard($player->name(), $card->label());
        $this->log[] = Messages::currentScore($player->name(), $player->score());

        if ($player->isBust()) {
            $this->advance();
        }
    }

    /**
     * いまの手番のプレイヤー（＝人間）がこれ以上引かない。手番を次へ渡す。
     *
     * @throws \RuntimeException PLAYER_TURN でないとき。
     */
    public function stand(): void
    {
        if ($this->phase !== Phase::PLAYER_TURN) {
            throw new \RuntimeException('いまは操作できません。');
        }

        $this->advance();
    }

    /**
     * フロントに返すスナップショット。JSON 化できる素の配列/スカラだけで構成する。
     *
     * @return array{
     *     phase: string,
     *     currentPlayer: string|null,
     *     participants: list<array{name: string, cards: list<string>, score: int|null, bust: bool}>,
     *     results: list<array{name: string, outcome: string}>|null,
     *     log: list<string>,
     * }
     */
    public function view(): array
    {
        // 人間の手番以外なら、ディーラーの伏せカードを公開してよい
        $revealDealer = $this->phase !== Phase::PLAYER_TURN;

        $participants = [];
        foreach ($this->players as $player) {
            $participants[] = [
                'name' => $player->name(),
                'cards' => $this->labelsOf($player),
                'score' => $player->score(),
                'bust' => $player->isBust(),
            ];
        }
        $participants[] = $this->dealerView($revealDealer);

        return [
            'phase' => $this->phase->name,
            'currentPlayer' => $this->phase === Phase::PLAYER_TURN
                ? $this->players[$this->currentPlayerIndex]->name()
                : null,
            'participants' => $participants,
            'results' => $this->results,
            'log' => $this->log,
        ];
    }

    /**
     * いまの手番を終え、次の手番へ移す。
     *
     * CPU は待たずに自動で引ききる。プレイヤーが尽きたらディーラーのターンへ。
     */
    private function advance(): void
    {
        $this->currentPlayerIndex++;

        while (isset($this->players[$this->currentPlayerIndex])) {
            $player = $this->players[$this->currentPlayerIndex];

            if (!$player instanceof CpuPlayer) {
                return; // 人間の手番: ここで入力待ちに戻る
            }

            $this->playOutAutomatically($player);
            $this->currentPlayerIndex++;
        }

        $this->phase = Phase::DEALER_TURN;
        $this->resolveDealer();
    }

    /**
     * ディーラーのターンを最後まで自動で消化し、決着させて FINISHED にする。
     */
    private function resolveDealer(): void
    {
        $someoneAlive = array_filter($this->players, static fn(Participant $p) => !$p->isBust()) !== [];

        if ($someoneAlive) {
            $this->log[] = Messages::revealedSecondCard($this->dealer->hand()->cards()[1]->label());
            $this->log[] = Messages::currentScore($this->dealer->name(), $this->dealer->score());
            $this->playOutAutomatically($this->dealer);
        }

        foreach ($this->players as $player) {
            $this->log[] = Messages::finalScore($player->name(), $player->score());
        }
        $this->log[] = Messages::finalScore($this->dealer->name(), $this->dealer->score());

        $this->results = [];
        foreach ($this->players as $player) {
            $outcome = $this->judge->decide($player, $this->dealer);
            $this->results[] = ['name' => $player->name(), 'outcome' => $outcome->name];
            $this->log[] = Messages::result($player->name(), $outcome);
        }

        $this->log[] = Messages::end();
        $this->phase = Phase::FINISHED;
    }

    /**
     * 参加者が「引く」と言う間、17以上になるまで引き続ける（CPU / ディーラー共通）。
     */
    private function playOutAutomatically(Participant $participant): void
    {
        while ($participant->wantsToNewCard()) {
            $card = $this->deck->draw();
            $participant->receive($card);
            $this->log[] = Messages::drewCard($participant->name(), $card->label());
            $this->log[] = Messages::currentScore($participant->name(), $participant->score());

            if ($participant->isBust()) {
                break;
            }
        }
    }

    /**
     * ディーラーの表示用データ。伏せ中は2枚目以降を "?"、得点は null にする。
     *
     * @return array{name: string, cards: list<string>, score: int|null, bust: bool}
     */
    private function dealerView(bool $reveal): array
    {
        if ($reveal) {
            return [
                'name' => $this->dealer->name(),
                'cards' => $this->labelsOf($this->dealer),
                'score' => $this->dealer->score(),
                'bust' => $this->dealer->isBust(),
            ];
        }

        $labels = [];
        foreach ($this->dealer->hand()->cards() as $index => $card) {
            $labels[] = $index === 0 ? $card->label() : '?';
        }

        return [
            'name' => $this->dealer->name(),
            'cards' => $labels,
            'score' => null,
            'bust' => false,
        ];
    }

    /**
     * 参加者の手札を表示用ラベルの配列にする。
     *
     * @return list<string>
     */
    private function labelsOf(Participant $participant): array
    {
        return array_map(
            static fn($card) => $card->label(),
            $participant->hand()->cards(),
        );
    }
}
