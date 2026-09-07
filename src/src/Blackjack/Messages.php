<?php

declare(strict_types=1);

namespace Blackjack;

/**
 * 画面に出す日本語の文言をここに集約する（表示だけを担当する純関数群）。
 */
final class Messages
{
    /**
     * 「ブラックジャックを開始します。」
     */
    public static function start(): string
    {
        return 'ブラックジャックを開始します。';
    }

    public static function askPlayerCount(): string
    {
        return 'プレイヤーの人数を入力してください。(1-3)';
    }

    /**
     * 「{name}の引いたカードは{card}です。」
     */
    public static function drewCard(string $name, string $card): string
    {
        return $name . 'の引いたカードは' . $card . 'です。';
    }

    /**
     * 「ディーラーの引いた2枚目のカードはわかりません。」
     */
    public static function hiddenSecondCard(string $name): string
    {
        return $name . 'の引いた2枚目のカードはわかりません。';
    }

    /**
     * 「ディーラーの引いた2枚目のカードは{card}でした。」
     */
    public static function revealedSecondCard(string $card): string
    {
        return 'ディーラーの引いた2枚目のカードは' . $card . 'でした。';
    }

    /**
     * 「{name}の現在の得点は{score}です。」
     */
    public static function currentScore(string $name, int $score): string
    {
        return $name . 'の現在の得点は' . $score . 'です。';
    }

    /**
     * 「カードを引きますか？（Y/N）」
     */
    public static function askHit(): string
    {
        return 'カードを引きますか？（Y/N）';
    }

    /**
     * 「{name}の得点は{score}です。」（決着時）
     */
    public static function finalScore(string $name, int $score): string
    {
        return $name . 'の得点は' . $score . 'です。';
    }

    /**
     * 「あなたの勝ちです！」など、勝敗の結果文。
     */
    public static function result(string $name, Outcome $outcome): string
    {
        return match ($outcome) {
            Outcome::WIN => $name . 'は勝ちです！',
            Outcome::LOSE => $name . 'は負けです…。',
            Outcome::DRAW => $name . 'は引き分けです。',
        };
    }

    /**
     * 「ブラックジャックを終了します。」
     */
    public static function end(): string
    {
        return 'ブラックジャックを終了します。';
    }
}
