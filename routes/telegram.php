<?php

/** @var Nutgram $bot */

use App\Enums\ExceptionType;
use App\Telegram\Commands\AboutCommand;
use App\Telegram\Commands\CancelCommand;
use App\Telegram\Commands\HelpCommand;
use App\Telegram\Commands\PrivacyCommand;
use App\Telegram\Commands\StartCommand;
use App\Telegram\Commands\StatsCommand;
use App\Telegram\Conversations\DonateConversation;
use App\Telegram\Conversations\FeedbackConversation;
use App\Telegram\Conversations\SettingsConversation;
use App\Telegram\Handlers\DocumentHandler;
use App\Telegram\Handlers\ExceptionsHandler;
use App\Telegram\Handlers\PhotoHandler;
use App\Telegram\Handlers\PreCheckoutQueryHandler;
use App\Telegram\Handlers\StickerHandler;
use App\Telegram\Handlers\SuccessfulPaymentHandler;
use App\Telegram\Handlers\UpdateChatStatusHandler;
use App\Telegram\Middleware\CheckMaintenance;
use App\Telegram\Middleware\CheckOffline;
use App\Telegram\Middleware\CollectChat;
use App\Telegram\Middleware\SetLocale;
use App\Telegram\Middleware\Throttle;
use SergiX44\Nutgram\Nutgram;
use App\Models\Chat;
use App\Models\Statistic;
use App\Models\Message;
/*
|--------------------------------------------------------------------------
| Global middlewares
|--------------------------------------------------------------------------
*/

$bot->middleware(CollectChat::class);
$bot->middleware(SetLocale::class);
$bot->middleware(Throttle::class);
$bot->middleware(CheckMaintenance::class);
$bot->middleware(CheckOffline::class);

/*
|--------------------------------------------------------------------------
| Bot handlers
|--------------------------------------------------------------------------
*/

$bot->onMyChatMember(UpdateChatStatusHandler::class);
$bot->onSticker(StickerHandler::class);
$bot->onDocument(DocumentHandler::class);
$bot->onPhoto(PhotoHandler::class);
$bot->onPreCheckoutQuery(PreCheckoutQueryHandler::class);
$bot->onSuccessfulPayment(SuccessfulPaymentHandler::class);

/*
|--------------------------------------------------------------------------
| Bot commands
|--------------------------------------------------------------------------
*/

$bot->onCommand('start', function (Nutgram $bot) {
    return $bot->sendMessage('Hello, type something to Smart software developers!');
})->description('The start command!');
$bot->onCommand('help', function (Nutgram $bot) {
    return $bot->sendMessage('I can help you with connecting devs');
})->description('Help message');
$bot->onCommand('settings', SettingsConversation::class)->description('Bot Settings');



$bot->onCommand('stats', StatsCommand::class)->description('Show bot statistics');
$bot->onCommand('feedback', FeedbackConversation::class)->description('Send a feedback about the bot');
$bot->onCommand('about', AboutCommand::class)->description('About the bot');
$bot->onCommand('privacy', PrivacyCommand::class)->description('Privacy Policy');
$bot->onCommand('cancel', CancelCommand::class)->description('Close a conversation or a keyboard');

/*
|--------------------------------------------------------------------------
| Exception handlers
|--------------------------------------------------------------------------
*/

$bot->onApiError(...ExceptionType::USER_BLOCKED->toNutgramException());
$bot->onApiError(...ExceptionType::USER_DEACTIVATED->toNutgramException());
$bot->onApiError(...ExceptionType::SAME_CONTENT->toNutgramException());
$bot->onApiError(...ExceptionType::MSG_TO_EDIT_NOT_FOUND->toNutgramException());
$bot->onApiError(...ExceptionType::MSG_TO_DELETE_NOT_FOUND->toNutgramException());
$bot->onApiError(...ExceptionType::WRONG_FILE_ID->toNutgramException());

$bot->onApiError([ExceptionsHandler::class, 'api']);
$bot->onException([ExceptionsHandler::class, 'global']);



//telegram.php

$bot->onMessage(function (Nutgram $bot) {
    $message = $bot->message();
    if ($message->reply_to_message !== null) {
        // Log::debug($message->forward_from_chat->id);
        var_dump($message->reply_to_message->message_thread_id);
        $bot->sendMessage($message->getText(), ['chat_id' =>$this->$bot]);
    }
    /*$message = $bot->sendMessage($bot->message()->getText(), [
        'chat_id' => 469614167,
//        'from_chat_id' => 1305723427,
//        'message_id' => (int) $bot->messageId()
    ])->message_id;*/
//    Log::info($message. "\n\n !!!!!!!!!!!!!");
//    var_dump($message);
//    $bot->forwardMessage(1305723427, 469614167, $message);
});

