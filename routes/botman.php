<?php
use App\Http\Controllers\BotManController;
use Illuminate\Support\Facades\Cookie;

$botman = resolve('botman');

$botman->hears('Hi', function ($bot) {
    $bot->reply('Hello! I\'m here to help, please type "bank" (whithout quotes please) to learn more');
});

$botman->hears('bank', BotManController::class.'@startBank');

if (Cookie::has('login_hash')) {
    $botman->hears('convert {amount} {currency1} to {currency2}', BotManController::class.'@convert');
    $botman->hears('my currency is {currency}', BotManController::class.'@setCurrency');
    $botman->hears('deposit {amount} {currency}', BotManController::class.'@deposit');
    $botman->hears('withdraw {amount}', BotManController::class.'@withdraw');
    $botman->hears('balance', BotManController::class.'@balance');
} else {
    $botman->hears('.*(convert|deposit|withdraw|balance).*', function ($bot) {
        $bot->reply('You need to access your account first, please <a href="'.url('/login').'">click here</a>');
    });
}

$botman->fallback(function($bot) {
    $bot->reply('I can\'t answer that yet... try "bank" without quotes to learn more about me!');
});