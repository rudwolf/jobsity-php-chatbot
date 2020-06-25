<?php
use App\Http\Controllers\BotManController;

$botman = resolve('botman');

$botman->hears('Hi', function ($bot) {
    $bot->reply('Hello!');
});

$botman->hears('bank', BotManController::class.'@startBank');

$botman->fallback(function($bot) {
    $bot->reply('I can\'t answer that yet... try "bank" without quotes to start transactions!');
});