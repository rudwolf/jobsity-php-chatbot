<h1 align="center">Jobsity Chatbot Challenge</h1>

## About this project

This was made as a test for a Jobsity developer position using BotMan Studio + Laravel Framework

This project requires a standard linux/windows machine with apache/nginx server and mysql for the database, also, needs php7.1 and composer to be installed.

To install do the following:

- After cloning, run composer install
- if .env file isn't present, run in the root of the project folder `composer post-root-package-install` and after that `php artisan key:generate`
- Create a database, configure the credentials on the .env file
- run `php artisan migrate`
- run `php artisan serve` and access the url as given in the command line

This service uses AMDOREN API, to get a key go to https://www.amdoren.com/developer/ and make a free account, after that, set your key on the ENV file like this

    AMDO_KEY="KEY SECRET GOES HERE"

type 'bank' on the chat to start using it.