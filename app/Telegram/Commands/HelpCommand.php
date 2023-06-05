<?php

namespace App\Telegram\Commands;

use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Attributes\ParseMode;

class HelpCommand
{
    public function __invoke(Nutgram $bot): void
    {
        $bot->sendMessage('help', function (Nutgram $bot) {
            return $bot->sendMessage('I can help you with connecting devs');
        })->description('Help message');
    }
}
