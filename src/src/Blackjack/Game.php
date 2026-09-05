<?php

declare(strict_types=1);

namespace Blackjack;

use Blackjack\Io\Input;
use Blackjack\Io\Output;
use Blackjack\Participant\Dealer;
use Blackjack\Participant\Player;

/**
 * ゲーム全体の進行役。配札・プレイヤーのターン・ディーラーのターン・勝敗表示をまとめる。
 */
final class Game
{
    public function __construct(
        private readonly Deck $deck,
        private readonly Input $input,
        private readonly Output $output,
        private readonly Judge $judge,
    ) {
    }

    /**
     * 1ゲームを最後まで進める。
     *
     * 流れ:
     *  1. プレイヤー→ディーラーの順に2枚ずつ配る（ディーラーの2枚目は伏せる）
     *  2. プレイヤーのターン（wantsToNewCard の間 引く。バーストで終了）
     *  3. プレイヤーがバーストしていなければ、伏せカードを公開しディーラーのターンへ
     *  4. Judge で勝敗を出し、両者の得点と結果を表示する
     */
    public function play(): void
    {
        $this->output->writeLine(Messages::start());

        $player = new Player('あなた', new Hand(), $this->input);
        $dealer = new Dealer(new Hand());

        $player->receive($this->deck->draw());
        $dealer->receive($this->deck->draw());
        $player->receive($this->deck->draw());
        $dealer->receive($this->deck->draw());

        $this->output->writeLine(Messages::drewCard($player->name(), $player->hand()->cards()[0]->label()));
        $this->output->writeLine(Messages::drewCard($player->name(), $player->hand()->cards()[1]->label()));
        $this->output->writeLine(Messages::drewCard($dealer->name(), $dealer->hand()->cards()[0]->label()));
        $this->output->writeLine(Messages::hiddenSecondCard());
        $this->output->writeLine(Messages::currentScore($player->name(), $player->score()));
        $this->output->writeLine(Messages::askHit());

        while ($player->wantsToNewCard()) {
            $card = $this->deck->draw();
            $player->receive($card);
            $this->output->writeLine(Messages::drewCard($player->name(), $card->label()));
            $this->output->writeLine(Messages::currentScore($player->name(), $player->score()));

            if ($player->isBust()) {
                break;
            }

            $this->output->writeLine(Messages::askHit());
        }

        if (!$player->isBust()) {
            $this->output->writeLine(Messages::revealedSecondCard($dealer->hand()->cards()[1]->label()));
            $this->output->writeLine(Messages::currentScore($dealer->name(), $dealer->score()));

            while ($dealer->wantsToNewCard()) {
                $card = $this->deck->draw();
                $dealer->receive($card);
                $this->output->writeLine(Messages::drewCard($dealer->name(), $card->label()));
                $this->output->writeLine(Messages::currentScore($dealer->name(), $dealer->score()));
            }
        }

        $outcome = $this->judge->decide($player, $dealer);
        $this->output->writeLine(Messages::finalScore($player->name(), $player->score()));
        $this->output->writeLine(Messages::finalScore($dealer->name(), $dealer->score()));
        $this->output->writeLine(Messages::result($outcome));
        $this->output->writeLine(Messages::end());
    }
}
