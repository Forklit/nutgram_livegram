<?php

namespace App\Telegram\Commands;

use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Attributes\ParseMode;

class StartCommand
{
    public function __invoke(Nutgram $bot): void
    {
        $bot->sendMessage('start', function (Nutgram $bot) {
            return $bot->sendMessage('Hello, type something to Smart software developers!');
        })->description('The start command!');
    }
}