<?php

namespace App\Http\Controllers;

use BotMan\BotMan\BotMan;
use Illuminate\Http\Request;
use App\Conversations\BankConversation;
use App\Library\AmdorenApiLibrary;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Account;
use App\AccountLog;
use Log;

class BotManController extends Controller
{
    /**
     * Place your BotMan logic here.
     */
    public function handle()
    {
        $botman = app('botman');
        $botman->listen();
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function tinker()
    {
        return view('tinker');
    }

    /**
     * Loaded through routes/botman.php
     * @param  BotMan $bot
     */
    public function startBank(BotMan $bot)
    {
        $bot->startConversation(new BankConversation());
    }

    public function convert(BotMan $bot, $amount, $currency1, $currency2)
    {
        $conversion = AmdorenApiLibrary::convertCurrency($currency1,$currency2,$amount);
        $valid_currency1 = array_key_exists($currency1, Account::$currencies);
        $valid_currency2 = array_key_exists($currency2, Account::$currencies);
        if ($conversion['error'] == 0 && $valid_currency1 && $valid_currency1) {
            $bot->reply($currency1 .' '.$amount.' is worth '.$currency2.' '.$this->round_down($conversion['amount']));
        } else {
            if ($conversion['error'] !== 0)
                Log::error('Convertion oopsie! '.$conversion['error'].' '.$conversion['error_message']);
            $bot->reply('There was an error, please try again or type "bank" without quotes to learn more.');
        }
    }

    public function setCurrency(BotMan $bot, $currency)
    {
        $email = Auth::user()->email;
        $account = Account::firstOrCreate(['email' => $email]);
        if (isset($account->currency) && !is_null($account->currency)) {
            // user already have a currency set, reply to him with his account currency
            $bot->reply("Hey you! Your account currency was already set to $account->currency before, can't change it anymore, sorry!");
            return;
        } else {
            $valid_currency = array_key_exists($currency, Account::$currencies);
            if ($valid_currency) {
                $account->currency = $currency;
                $account->save();
                $bot->reply("Hey you! Your account currency is $account->currency now! Type \"bank\" without quotes to know more!");
                return;
            } else {
                $bot->reply("Well, almost there! $currency is not a valid currency, please use a valid one.");
                return;
            }
        }
    }

    public function deposit(BotMan $bot, $amount, $currency)
    {
        $email = Auth::user()->email;
        $account = Account::firstOrCreate(['email' => $email]);
        if (isset($account->currency) && !is_null($account->currency)) {
            if ($currency !== $account->currency) {
                $conversion = AmdorenApiLibrary::convertCurrency($currency,$account->currency,$amount);
                if ($conversion['error'] == 0) {
                    $deposit = $this->round_down($conversion['amount']);
                } else {
                    Log::error('Convertion oopsie! '.$conversion['error'].' '.$conversion['error_message']);
                    $bot->reply('There was an error, please try again or type "bank" without quotes to learn more.');
                    return;
                }
            } else {
                $deposit = $this->round_down($amount);
            }
            $account->balance += $deposit;
            $account->save();
            $this->log($email,"Deposited $amount $currency");
            $bot->reply("Thank you! $amount $currency deposited. Your balance now is $account->balance $account->currency");
            return;
        } else {
            $bot->reply('There was an error, you need to set your account currency first! To do it type "my currency is CURRENCY" without quotes, where CURRENCY is any valid currency like USD, CAD or EUR.');
            return;
        }
    }

    public function withdraw(BotMan $bot, $amount) {
        try {
            $account = Account::where('email', '=', Auth::user()->email)->firstOrFail();
            $amount = $this->round_down(floatval($amount));
            $balance = $this->round_down($account->balance);
            if ($balance >= $amount) {
                $result = $account->balance - $amount;
                $account->balance = $result;
                $account->save();
                $this->log(Auth::user()->email,"Withdrawn $amount $account->currency");
                $bot->reply("Thank you! $amount $account->currency withdrawn. Your balance now is $account->balance $account->currency");
                return;
            } else {
                // user doesn't have enouth money!
                $bot->reply("You can't withdraw $amount from your account, you only have $account->balance $account->currency in your account");
                return;
            }
        } catch (\ModelNotFoundException $m) {
            $bot->reply('There was an error, account empty, please deposit something first. please try again or type "bank" without quotes to learn more.');
            return;
        }
    }

    public function log($email, $message) {
        $log = new AccountLog;
        $log->email = $email;
        $log->log_entry = $message;
        $log->save();
    }

    function round_down($number, $precision = 2)
    {
        $fig = (int) str_pad('1', $precision, '0');
        return (floor($number * $fig) / $fig);
    }
}
