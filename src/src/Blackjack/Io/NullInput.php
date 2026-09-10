<?php

declare(strict_types=1);

namespace Blackjack\Io;

/**
 * 何も入力を返さない Input。
 *
 * Web 版では人間プレイヤーの「引く / 引かない」は HTTP リクエスト（hit / stand）で
 * 決まるので、Player::wantsToNewCard() は呼ばれない。その Player を組み立てるために
 * 形だけ渡すのがこのクラス。もし読み取られたら設計ミスなので例外にする。
 */
final class NullInput implements Input
{
    public function readLine(): string
    {
        throw new \LogicException(
            'NullInput は読み取れません。Web 版の人間の操作は hit / stand で受け取ります。',
        );
    }
}
