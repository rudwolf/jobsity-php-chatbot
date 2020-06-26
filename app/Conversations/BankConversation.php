<?php

namespace App\Conversations;

use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Outgoing\Question;
use BotMan\BotMan\Messages\Outgoing\Actions\Button;
use BotMan\BotMan\Messages\Conversations\Conversation;
use Illuminate\Support\Facades\Auth;

class BankConversation extends Conversation
{
    /**
     * First question
     */
    public function askReason()
    {
        if (Auth::check()) {
            return $this->onlineUserReason();
        } else {
            return $this->offlineUserReason();
        }
    }

    /**
     * Anonymous User Answer
     */
    private function offlineUserReason()
    {
        $question = Question::create("Hi there! What do you need? (please click any button)")
            ->fallback('Unable to ask question')
            ->callbackId('ask_reason')
            ->addButtons([
                Button::create('Access My Account')->value('login'),
                Button::create('Create new Account')->value('register'),
            ]);

        return $this->ask($question, function (Answer $answer) {
            if ($answer->isInteractiveMessageReply()) {
                if ($answer->getValue() === 'login') {
                    $this->say('to access your account please <a href="'.url('/login').'">click here</a>');
                } else {
                    $this->say('to create a new account please <a href="'.url('/register').'">click here</a>.');
                }
            }
        });
    }

    /**
     * Anonymous User Answer
     */
    private function onlineUserReason()
    {
        $question = Question::create("Hi there! What do you need? (please click any button)")
            ->fallback('Unable to ask question')
            ->callbackId('ask_reason')
            ->addButtons([
                Button::create('Deposit')->value('deposit'),
                Button::create('Withdraw')->value('withdraw'),
                Button::create('Convert Currency')->value('convert'),
                Button::create('Balance')->value('balance'),
            ]);

        return $this->ask($question, function (Answer $answer) {
            if ($answer->isInteractiveMessageReply()) {
                switch ($answer->getValue()) {
                    case 'deposit':
                        $this->say('To deposit money type "deposit AMOUNT CURRENCY" without quotes, where amount is the value and currency is any valid currency like USD EUR CAD for example');
                        break;
                    case 'withdraw':
                        $this->say('To withdraw money type "withdraw AMOUNT CURRENCY" without quotes, where amount is the value and currency is any valid currency like USD EUR CAD for example');
                    break;
                    case 'convert':
                        $this->say('To convert money to other currency type "convert AMOUNT CURRENCY_FROM to CURRENCY_TO" without quotes, where amount is the value and currencies are the original currency and the desired destionation, you can use any valid currency like USD,EUR or CAD for example: "convert 20.5 USD to EUR"');
                        break;
                    default:
                        $this->say('Type "balance" without quotes to get your balance');
                        break;
                }
            }
        });
    }

    /**
     * Start the conversation
     */
    public function run()
    {
        $this->askReason();
    }
}
