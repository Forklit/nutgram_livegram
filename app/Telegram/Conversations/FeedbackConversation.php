<?php

namespace App\Telegram\Conversations;

use App\Models\Chat;
use App\Models\User;
use Illuminate\Support\Facedec\DB;
use Psr\SimpleCache\InvalidArgumentException;
use SergiX44\Nutgram\Conversations\Conversation;
use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Attributes\ParseMode;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardButton;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardMarkup;

class FeedbackConversation extends Conversation
{
    protected ?string $feedback;
    protected $forwardFrom;
    protected $messageText;
    protected $message;
    protected bool $success = false;
    protected int $chat_id;
    protected int $message_id;
    protected $chatID;
    public int $from_id;
    public int $user_id;


    /**
     * Feedback commandasi shu funksiyada ishlaydi
     * @param Nutgram $bot
     * @throws InvalidArgumentException
     */
    public function start(Nutgram $bot): void
    {
        $message = $bot->sendMessage(message('feedback.ask'), [
            'reply_markup' => InlineKeyboardMarkup::make()
                ->addRow(InlineKeyboardButton::make(
                    text: trans('common.cancel'),
                    callback_data: 'feedback.cancel'
                )),
        ]);

        $this->chat_id = $message->chat->id;
        $this->message_id = $message->message_id;

        $this->next('getFeedback');

        stats('feedback', 'command');
    }

    /**
     * Feedback ni qabul qilish
     * @param Nutgram $bot
     * @throws InvalidArgumentException
     */
    public function getFeedback(Nutgram $bot): void
    {
        //bekor qillish
        $callBack =$bot->isCallbackQuery() && $bot->callbackQuery()->data;

        if ($callBack === 'feedback.cancel') {
            $bot->answerCallbackQuery();
            $this->end();
            stats('feedback.cancelled', 'feedback');

            return;
        }

        //check valid input
        if ($bot->message()?->text === null) {
            $bot->sendMessage(message('feedback.wrong'), [
                'parse_mode' => ParseMode::HTML,
            ]);
            $this->start($bot);

            return;
        }

        //get the input
        $this->feedback = $bot->message()?->text;

        //message ni guruhga yuborish
        $bot->sendMessage(message('feedback.received', [
            'from' => "{$bot->user()?->first_name} {$bot->user()?->last_name}",
            'username' => $bot->user()?->username,
            'user_id' => $bot->userId(),
            'message' => $this->feedback,
        ]), [
            'chat_id' => config('developer.id'),
        ]);
        
        $this->messageId = $bot->message()->message_id;
        $this->chatId = $bot->message()->chat->id;
        $this->chatID = Chat::latest()->value('chat_id');
        $bot->forwardMessage($this->chatID, "$this->chatId", "$this->messageId");
        $this->success = true;
        $this->end();


        stats('feedback.sent', 'feedback');
        $this->next('test');
    }

     

    // //feedback ni bekor qilish
    // public function closing(Nutgram $bot): void
    // {
    //     $bot->deleteMessage($this->chat_id, $this->message_id);

    //     if ($this->success) {
    //         $bot->sendMessage(message('feedback.thanks'));

    //         return;
    //     }

    //     $bot->sendMessage(message('feedback.cancelled'));

    // }

    
    //Feedback ga javob qaytarish
    public function test(Nutgram $bot): void
    {
        $this->message = $bot->message();
        $this->forwardFrom = $bot->message()->reply_to_message->forward_from->id;

        $this->messageText = $this->message->text;

        $bot->sendMessage($this->messageText, ['chat_id' => $this->forwardFrom]);
        $this->end();
    }


    

    
    
    




    

}

    



